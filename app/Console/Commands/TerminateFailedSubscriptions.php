<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TerminateFailedSubscriptions
 *
 * Runs daily (via scheduler).
 * Finds all subscriptions with status = 'past_due'
 * where grace_period_ends_at <= now().
 * Marks them 'terminated' and fires GHL termination webhook.
 *
 * Schedule entry in app/Console/Kernel.php:
 *   $schedule->command('subscriptions:terminate-failed')->dailyAt('02:00');
 */
class TerminateFailedSubscriptions extends Command
{
    protected $signature   = 'subscriptions:terminate-failed';
    protected $description = 'Terminate past_due subscriptions whose 7-day grace period has expired.';

    public function handle(): int
    {
        $this->info('Checking for expired grace periods...');

        Log::info('TerminateFailedSubscriptions: starting run', [
            'now' => now()->toIso8601String(),
        ]);

        $expired = Subscription::where('status', 'past_due')
            ->whereNotNull('grace_period_ends_at')
            ->where('grace_period_ends_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No subscriptions to terminate.');
            Log::info('TerminateFailedSubscriptions: no subscriptions to terminate');
            return self::SUCCESS;
        }

        $this->info('Found ' . $expired->count() . ' subscription(s) to terminate.');

        foreach ($expired as $subscription) {

            Log::info('Terminating subscription', [
                'subscription_id'    => $subscription->id,
                'email'              => $subscription->email,
                'plan_key'           => $subscription->plan_key,
                'arb_subscription_id'=> $subscription->arb_subscription_id,
                'grace_period_ended' => $subscription->grace_period_ends_at,
            ]);

            // Mark terminated
            $subscription->update([
                'status'        => 'terminated',
                'terminated_at' => now(),
            ]);

            // Log the event
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'terminated',
                'payload'         => [
                    'reason'             => 'grace_period_expired',
                    'grace_period_ended' => $subscription->grace_period_ends_at,
                    'terminated_at'      => now()->toIso8601String(),
                ],
                'note' => 'Auto-terminated: grace period expired after ' . $subscription->failed_payment_count . ' failed payment(s).',
            ]);

            // Fire GHL termination webhook
            $this->fireGhlTerminationWebhook($subscription);

            $this->line('  ✓ Terminated: ' . $subscription->email . ' (ID: ' . $subscription->id . ')');
        }

        $this->info('Done. Terminated ' . $expired->count() . ' subscription(s).');

        Log::info('TerminateFailedSubscriptions: run complete', [
            'terminated_count' => $expired->count(),
        ]);

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GHL TERMINATION WEBHOOK
    // Fires to GHL_SUBSCRIPTION_TERMINATED_WEBHOOK_URL
    // Set this in your .env file.
    // In GHL, use this webhook to tag the contact, stop sequences, revoke access.
    // ─────────────────────────────────────────────────────────────────────────
    private function fireGhlTerminationWebhook(Subscription $subscription): void
    {
        $url = config('services.ghl.subscription_terminated_webhook_url');

        if (!$url) {
            Log::warning('GHL subscription_terminated_webhook_url not set (check GHL_SUBSCRIPTION_TERMINATED_WEBHOOK_URL in .env)', [
                'subscription_id' => $subscription->id,
            ]);
            return;
        }

        $payload = [
            // ── Contact identifiers ──────────────────────────────────────────
            'first_name'           => $subscription->first_name,
            'last_name'            => $subscription->last_name,
            'email'                => $subscription->email,
            'phone'                => $subscription->phone,
            'address'              => $subscription->address,
            'city'                 => $subscription->city,
            'state'                => $subscription->state,
            'zip'                  => $subscription->zip,

            // ── Subscription details ─────────────────────────────────────────
            'plan'                 => $subscription->plan_label,
            'plan_key'             => $subscription->plan_key,
            'recurring_amount'     => $subscription->recurring_amount,
            'invoice_number'       => $subscription->invoice_number,
            'arb_subscription_id'  => $subscription->arb_subscription_id,

            // ── Termination context ───────────────────────────────────────────
            'failed_payment_count' => $subscription->failed_payment_count,
            'first_failed_at'      => optional($subscription->first_failed_at)->toIso8601String(),
            'grace_period_ends_at' => optional($subscription->grace_period_ends_at)->toIso8601String(),
            'terminated_at'        => optional($subscription->terminated_at)->toIso8601String(),

            // ── Source tag — identifies this webhook in GHL ───────────────────
            'source'               => '850_fico_subscription_terminated',
            'tags'                 => ['subscription-terminated', 'access-revoked', 'payment-failed'],
        ];

        Log::info('Firing GHL SUBSCRIPTION_TERMINATED webhook', [
            'subscription_id' => $subscription->id,
            'email'           => $subscription->email,
        ]);

        try {
            Http::timeout(15)->post($url, $payload);
            Log::info('GHL SUBSCRIPTION_TERMINATED webhook fired successfully', [
                'subscription_id' => $subscription->id,
                'url'             => $url,
            ]);
        } catch (\Throwable $e) {
            Log::error('GHL SUBSCRIPTION_TERMINATED webhook failed', [
                'subscription_id' => $subscription->id,
                'url'             => $url,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}