<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Illuminate\Console\Command;

/**
 * BackfillPaymentsCommand
 *
 * One-off, operator-invoked reconciliation that seeds the `payments` table from
 * historical data that predates its existence:
 *
 *   1. Every Subscription row becomes its own "initial" payment row (using
 *      subscription.amount, transaction_id, invoice_number, subscribed_at).
 *   2. Every SubscriptionEvent with event_type='payment_recovered' that carries
 *      a transId + authAmount in its payload becomes a "recurring" payment row.
 *
 * Safe to re-run — dedup is on (transaction_id, type). Rows created by live
 * webhooks are never overwritten.
 *
 * Usage:
 *   php artisan payments:backfill --dry-run   # preview only
 *   php artisan payments:backfill             # commit
 */
class BackfillPaymentsCommand extends Command
{
    protected $signature   = 'payments:backfill {--dry-run : Preview counts; do not insert}';
    protected $description = 'Backfill the payments table from existing subscriptions + subscription_events';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info($dryRun ? 'DRY RUN — no rows will be written' : 'Writing rows...');

        $initialCreated = 0;
        $initialSkipped = 0;
        $recurringCreated = 0;
        $recurringSkipped = 0;

        // 1) Initial payments — one per subscription with a known transaction_id
        Subscription::whereNotNull('transaction_id')
            ->orderBy('id')
            ->chunk(200, function ($subs) use (&$initialCreated, &$initialSkipped, $dryRun) {
                foreach ($subs as $sub) {
                    $exists = Payment::where('transaction_id', $sub->transaction_id)
                        ->where('type', 'initial')
                        ->exists();
                    if ($exists) {
                        $initialSkipped++;
                        continue;
                    }

                    if (!$dryRun) {
                        Payment::create([
                            'subscription_id' => $sub->id,
                            'transaction_id'  => $sub->transaction_id,
                            'invoice_number'  => $sub->invoice_number,
                            'amount'          => $sub->amount,
                            'type'            => 'initial',
                            'status'          => 'captured',
                            'event_type_raw'  => 'backfill.initial',
                            'charged_at'      => $sub->subscribed_at ?? $sub->created_at,
                            'raw_payload'     => ['source' => 'backfill', 'note' => 'Synthesized from subscription row'],
                        ]);
                    }
                    $initialCreated++;
                }
            });

        // 2) Recurring payments — from payment_recovered events (their payloads
        //    carry the recurring authcapture payload).
        SubscriptionEvent::where('event_type', 'payment_recovered')
            ->orderBy('id')
            ->chunk(200, function ($events) use (&$recurringCreated, &$recurringSkipped, $dryRun) {
                foreach ($events as $ev) {
                    $p = $ev->payload ?? [];
                    $transId    = data_get($p, 'payload.id') ?? data_get($p, 'id');
                    $amount     = data_get($p, 'payload.authAmount') ?? data_get($p, 'authAmount');
                    $invoice    = data_get($p, 'payload.invoiceNumber') ?? data_get($p, 'invoiceNumber');
                    $chargedAt  = $ev->created_at;

                    if (!$transId || $amount === null) {
                        $recurringSkipped++;
                        continue;
                    }

                    $exists = Payment::where('transaction_id', $transId)
                        ->where('type', 'recurring')
                        ->exists();
                    if ($exists) {
                        $recurringSkipped++;
                        continue;
                    }

                    if (!$dryRun) {
                        Payment::create([
                            'subscription_id' => $ev->subscription_id,
                            'transaction_id'  => (string) $transId,
                            'invoice_number'  => $invoice ? (string) $invoice : null,
                            'amount'          => (float) $amount,
                            'type'            => 'recurring',
                            'status'          => 'captured',
                            'event_type_raw'  => 'backfill.recurring',
                            'charged_at'      => $chargedAt,
                            'raw_payload'     => $p,
                        ]);
                    }
                    $recurringCreated++;
                }
            });

        $this->info(sprintf('Initial: %d %s, %d already present',
            $initialCreated, $dryRun ? 'would create' : 'created', $initialSkipped));
        $this->info(sprintf('Recurring: %d %s, %d already present or missing data',
            $recurringCreated, $dryRun ? 'would create' : 'created', $recurringSkipped));

        if ($dryRun) {
            $this->warn('DRY RUN — run without --dry-run to commit.');
        }

        return self::SUCCESS;
    }
}
