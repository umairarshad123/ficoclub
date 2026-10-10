<?php

namespace App\Services;

use App\Models\CommasTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Mirrors every Commas transaction into commas_transactions.
 *
 * The account's whole history is small (tens–hundreds of rows), so each run
 * re-reads all pages: refunds and payout releases on older transactions are
 * picked up without any incremental bookkeeping. Used by `commas:sync` (every
 * 10 min) and the dashboard's "Sync now" button.
 */
class CommasTransactionSync
{
    public const LAST_SYNC_KEY = 'commas_transactions_last_sync';

    private const MAX_PAGES = 50; // 5,000 transactions — raise if the account outgrows it

    public function __construct(private CommasService $commas)
    {
    }

    /** @return array{seen:int, created:int, updated:int} */
    public function run(): array
    {
        $stats = ['seen' => 0, 'created' => 0, 'updated' => 0];

        for ($page = 1; $page <= self::MAX_PAGES; $page++) {
            $result = $this->commas->listTransactions($page, 100);

            foreach ($result['transactions'] as $txn) {
                $id = (string) data_get($txn, 'id', '');
                if ($id === '') {
                    continue;
                }
                $stats['seen']++;

                $row = CommasTransaction::updateOrCreate(['commas_id' => $id], $this->attributes($txn));
                $row->wasRecentlyCreated ? $stats['created']++ : ($row->wasChanged() ? $stats['updated']++ : null);
            }

            if (! $result['has_more']) {
                break;
            }
        }

        Cache::forever(self::LAST_SYNC_KEY, now()->toIso8601String());
        Log::info('[Commas] Transactions synced', $stats);

        return $stats;
    }

    public static function lastSyncedAt(): ?Carbon
    {
        $at = Cache::get(self::LAST_SYNC_KEY);

        return $at ? Carbon::parse($at) : null;
    }

    private function attributes(array $txn): array
    {
        $refunds = collect(data_get($txn, 'refunds', []))->filter(fn ($r) => is_array($r));
        $release = data_get($txn, 'servicePayment.fund_release_on');

        return [
            'transaction_date' => ($d = data_get($txn, 'transaction_date')) ? Carbon::parse($d)->utc() : null,
            'customer_name'    => data_get($txn, 'fan.name'),
            'customer_email'   => ($e = data_get($txn, 'fan.email')) ? strtolower(trim($e)) : null,
            'customer_phone'   => data_get($txn, 'fan.phone'),
            'product_id'       => data_get($txn, 'product.id') ?? data_get($txn, 'service.id'),
            'product_title'    => data_get($txn, 'product.title') ?? data_get($txn, 'service.title'),
            'amount'           => (float) data_get($txn, 'amount', 0),
            'fee_amount'       => (float) data_get($txn, 'fee_amount', 0),
            'net_amount'       => (float) data_get($txn, 'net_amount', 0),
            'refunded_amount'  => round($refunds->sum(fn ($r) => abs((float) ($r['amount'] ?? 0))), 2),
            'refund_cost'      => round($refunds->sum(fn ($r) => abs((float) ($r['refund_cost'] ?? $r['amount'] ?? 0))), 2),
            'refund_count'     => $refunds->count(),
            'payment_type'     => data_get($txn, 'servicePayment.payment_type'),
            'fund_release_on'  => $release ? Carbon::parse($release, 'UTC') : null,
            'fund_released'    => (bool) data_get($txn, 'servicePayment.fund_released', false),
            'raw'              => $txn,
        ];
    }
}
