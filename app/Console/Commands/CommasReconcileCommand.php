<?php

namespace App\Console\Commands;

use App\Models\CheckoutOrder;
use App\Services\CommasService;
use App\Services\EnrollmentFulfillment;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Safety net for lost webhooks. Commas delivers webhooks at most once with no
 * retries, so every 10 minutes we pull recent transactions and fulfill any
 * checkout order that was paid but never confirmed.
 *
 * The transactions list carries no metadata, so a transaction is matched to an
 * order by buyer email + product + time window. Orders already confirmed by
 * the webhook (which knows the ORD- id but not the numeric id) get the numeric
 * id attached instead, so the same payment can never fulfill a second order.
 *
 *   php artisan commas:reconcile            # commit
 *   php artisan commas:reconcile --dry-run  # preview
 */
class CommasReconcileCommand extends Command
{
    protected $signature   = 'commas:reconcile {--days=3 : Look-back window} {--dry-run : Preview only}';
    protected $description = 'Fulfill paid Commas checkouts whose confirmation webhook was missed';

    public function handle(CommasService $commas, EnrollmentFulfillment $fulfillment): int
    {
        if (! $commas->isConfigured()) {
            $this->line('COMMAS_API_KEY not set — nothing to reconcile.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $since  = now()->subDays((int) $this->option('days'));
        $stats  = ['seen' => 0, 'known' => 0, 'attached' => 0, 'fulfilled' => 0, 'unmatched' => 0];

        foreach ($this->recentTransactions($commas, $since) as $txn) {
            $stats['seen']++;

            $txnId   = (string) data_get($txn, 'id');
            $email   = strtolower((string) data_get($txn, 'fan.email', ''));
            $product = (string) (data_get($txn, 'product.id') ?? data_get($txn, 'service.id') ?? '');
            $paidAt  = Carbon::parse(data_get($txn, 'transaction_date'));

            if ($txnId === '' || CheckoutOrder::where('commas_transaction_id', $txnId)->exists()) {
                $stats['known']++;
                continue;
            }

            // Already confirmed by webhook (ORD id only) → just attach the numeric id.
            $confirmed = CheckoutOrder::whereIn('status', [CheckoutOrder::STATUS_PAID, CheckoutOrder::STATUS_MISMATCH])
                ->whereNull('commas_transaction_id')
                ->where('email', $email)
                ->where('commas_product_id', $product)
                ->whereBetween('paid_at', [$paidAt->copy()->subMinutes(30), $paidAt->copy()->addMinutes(30)])
                ->orderBy('paid_at')
                ->first();

            if ($confirmed) {
                $stats['attached']++;
                if (! $dryRun) {
                    $confirmed->update(['commas_transaction_id' => $txnId]);
                }
                continue;
            }

            // Paid but never confirmed → the most recent pending order from that buyer for that product.
            $pending = CheckoutOrder::where('status', CheckoutOrder::STATUS_PENDING)
                ->where('email', $email)
                ->where('commas_product_id', $product)
                ->whereBetween('created_at', [$paidAt->copy()->subDay(), $paidAt->copy()->addMinutes(5)])
                ->orderByDesc('created_at')
                ->first();

            if (! $pending) {
                $stats['unmatched']++;   // not a website checkout (or not ours) — webhook records those
                continue;
            }

            $this->warn(sprintf('  [MISSED] %s — %s $%s (txn %s)', $pending->invoice_number, $email, data_get($txn, 'amount'), $txnId));
            $stats['fulfilled']++;

            if ($dryRun) {
                continue;
            }

            $didFulfill = $fulfillment->markPaid($pending, [
                'product_id'     => $product,
                'amount'         => data_get($txn, 'amount'),
                'email'          => $email,
                'transaction_id' => $txnId,
                'buyer_id'       => data_get($txn, 'fan.id'),
                'raw'            => ['source' => 'reconcile', 'transaction' => $txn],
            ], 'reconcile');

            if ($didFulfill) {
                $fulfillment->notify($pending->refresh());
            }
        }

        $this->info(sprintf(
            'Commas reconcile: %d seen · %d already linked · %d ids attached · %d %s · %d not website orders',
            $stats['seen'], $stats['known'], $stats['attached'],
            $stats['fulfilled'], $dryRun ? 'would fulfill' : 'fulfilled',
            $stats['unmatched']
        ));

        return self::SUCCESS;
    }

    /** Pages through newest-first transactions until they're older than $since. */
    private function recentTransactions(CommasService $commas, Carbon $since): \Generator
    {
        for ($page = 1; $page <= 10; $page++) {
            $result = $commas->listTransactions($page, 100);

            foreach ($result['transactions'] as $txn) {
                $date = data_get($txn, 'transaction_date');
                if ($date && Carbon::parse($date)->lt($since)) {
                    return;
                }
                yield $txn;
            }

            if (! $result['has_more']) {
                return;
            }
        }
    }
}
