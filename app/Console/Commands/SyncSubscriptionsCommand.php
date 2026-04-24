<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Services\AuthorizeNetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($sub, $newStatus, $payload) {
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
        });
    }
}