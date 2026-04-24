<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Services\AuthorizeNetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncSubscriptionsCommand extends Command
{
    protected $signature = 'subs:sync {--dry-run : Preview only, no DB writes}';
    protected $description = 'Reconcile subscription statuses with Authorize.Net';

    public function handle(AuthorizeNetService $authnet): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Fetching Auth.net subscriptions...');
        $arbSubs = $authnet->getAllSubscriptions();
        $this->info(sprintf('Got %d subscription records', count($arbSubs)));

        if (empty($arbSubs)) {
            $this->error('Zero subscriptions returned. Check API credentials.');
            return self::FAILURE;
        }

        $arbById = [];
        foreach ($arbSubs as $sub) {
            $arbById[(string) $sub['id']] = $sub;
        }

        $stats = ['ok' => 0, 'updated' => 0, 'orphaned' => 0, 'errors' => 0];

        foreach ($arbById as $arbId => $arbSub) {
            try {
                $expected = $this->mapArbStatus($arbSub['status'] ?? 'unknown');
                $local = Subscription::where('arb_subscription_id', $arbId)->first();

                if (! $local) {
                    $this->warn("  [MISSING] ARB $arbId not in local DB");
                    continue;
                }

                if ($local->status !== $expected) {
                    $this->line(sprintf(
                        '  [UPDATE] %s %s (%s): %s -> %s',
                        $local->first_name, $local->last_name, $arbId,
                        $local->status, $expected
                    ));

                    if (! $dryRun) {
                        $this->applyStatusChange($local, $expected, $arbSub);
                    }
                    $stats['updated']++;
                } else {
                    $stats['ok']++;
                }
            } catch (\Throwable $e) {
                $this->error("  [ERROR] $arbId: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        $localActive = Subscription::whereIn('status', ['active', 'past_due'])
            ->whereNotNull('arb_subscription_id')
            ->get();

        foreach ($localActive as $local) {
            if (! isset($arbById[(string) $local->arb_subscription_id])) {
                $this->warn(sprintf(
                    '  [ORPHAN] %s %s — not in Auth.net, terminating',
                    $local->first_name, $local->last_name
                ));
                if (! $dryRun) {
                    $this->applyStatusChange($local, 'terminated', ['note' => 'Not in Auth.net list']);
                }
                $stats['orphaned']++;
            }
        }

        $this->info('SYNC COMPLETE');
        $this->info(sprintf('  In sync:   %d', $stats['ok']));
        $this->info(sprintf('  Updated:   %d', $stats['updated']));
        $this->info(sprintf('  Orphaned:  %d', $stats['orphaned']));
        $this->info(sprintf('  Errors:    %d', $stats['errors']));

        if ($dryRun) {
            $this->warn('DRY RUN - no changes written');
        }

        return $stats['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function mapArbStatus(string $arbStatus): string
    {
        return match (strtolower($arbStatus)) {
            'active'    => 'active',
            'suspended' => 'past_due',
            default     => 'terminated',
        };
    }

    private function applyStatusChange(Subscription $sub, string $newStatus, array $payload): void
    {
        $transitionedToTerminated = false;

        DB::transaction(function () use ($sub, $newStatus, $payload, &$transitionedToTerminated) {
            $old = $sub->status;
            $sub->status = $newStatus;

            if ($newStatus === 'terminated' && ! $sub->terminated_at) {
                $sub->terminated_at = now();
            }

            $sub->save();

            SubscriptionEvent::create([
                'subscription_id' => $sub->id,
                'event_type'      => $newStatus === 'terminated' ? 'terminated' : 'manual_note',
                'payload'         => $payload,
                'note'            => sprintf('Sync job: %s -> %s', $old, $newStatus),
            ]);

            if ($newStatus === 'terminated' && $old !== 'terminated') {
                $transitionedToTerminated = true;
            }
        });

        if ($transitionedToTerminated) {
            $this->fireGhlTerminationWebhook($sub->fresh(), $payload);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Fire GHL termination webhook from the sync job.
    //
    // Mirrors the payload shape used by AuthorizeNetWebhookController and
    // TerminateFailedSubscriptions so GHL sees identical fields regardless of
    // which code path terminated the sub.
    //
    // Per-subscription 24h cache dedupe prevents double-firing in the rare race
    // where both the webhook and the sync flip the same sub within the window.
    // ─────────────────────────────────────────────────────────────────────────
    private function fireGhlTerminationWebhook(Subscription $sub, array $payload): void
    {
        $url = config('services.ghl.subscription_terminated_webhook_url');

        if (!$url) {
            Log::warning('[SubsSync] GHL subscription_terminated_webhook_url not configured — skipping', [
                'subscription_id' => $sub->id,
            ]);
            return;
        }

        $dedupeKey = 'ghl_terminated_fired_sub_' . $sub->id;
        if (Cache::has($dedupeKey)) {
            Log::info('[SubsSync] GHL termination webhook already fired recently — skipping duplicate', [
                'subscription_id' => $sub->id,
            ]);
            return;
        }

        $body = [
            'first_name'           => $sub->first_name,
            'last_name'            => $sub->last_name,
            'email'                => $sub->email,
            'phone'                => $sub->phone,
            'address'              => $sub->address,
            'city'                 => $sub->city,
            'state'                => $sub->state,
            'zip'                  => $sub->zip,
            'plan'                 => $sub->plan_label,
            'plan_key'             => $sub->plan_key,
            'recurring_amount'     => $sub->recurring_amount,
            'invoice_number'       => $sub->invoice_number,
            'arb_subscription_id'  => $sub->arb_subscription_id,
            'failed_payment_count' => $sub->failed_payment_count,
            'first_failed_at'      => optional($sub->first_failed_at)->toIso8601String(),
            'grace_period_ends_at' => optional($sub->grace_period_ends_at)->toIso8601String(),
            'terminated_at'        => optional($sub->terminated_at)->toIso8601String(),
            'reason'               => 'sync_reconciliation',
            'source'               => '850_fico_subscription_terminated',
            'tags'                 => ['subscription-terminated', 'sync-reconciled'],
        ];

        Log::info('[SubsSync] Firing GHL SUBSCRIPTION_TERMINATED webhook', [
            'subscription_id' => $sub->id,
            'email'           => $sub->email,
        ]);

        try {
            $response = Http::timeout(15)->post($url, $body);
            $ok = $response->successful();
            $ctx = [
                'subscription_id' => $sub->id,
                'status'          => $response->status(),
                'body_preview'    => substr((string) $response->body(), 0, 500),
            ];
            if ($ok) {
                Log::info('[SubsSync] GHL SUBSCRIPTION_TERMINATED webhook fired', $ctx);
                Cache::put($dedupeKey, true, now()->addHours(24));
            } else {
                Log::error('[SubsSync] GHL SUBSCRIPTION_TERMINATED webhook returned non-success status', $ctx);
            }
        } catch (\Throwable $e) {
            Log::error('[SubsSync] GHL SUBSCRIPTION_TERMINATED webhook failed', [
                'subscription_id' => $sub->id,
                'error'           => $e->getMessage(),
                'exception'       => get_class($e),
            ]);
        }
    }
}