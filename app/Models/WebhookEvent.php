<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model
{
    protected $fillable = [
        'notification_id',
        'event_type',
        'entity_id',
        'matched_subscription_id',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'description',
        'amount',
        'invoice_number',
        'arb_status',
        'response_code',
        'signature_valid',
        'source_ip',
        'received_at',
        'payload',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'signature_valid' => 'boolean',
        'received_at'     => 'datetime',
        'payload'         => 'array',
    ];

    public function matchedSubscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'matched_subscription_id');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Display helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Authorize.Net response code → human label.
     * Only relevant for transaction-style payment events.
     */
    public function responseCodeLabel(): ?string
    {
        return match ((string) $this->response_code) {
            '1' => 'Approved',
            '2' => 'Declined',
            '3' => 'Error',
            '4' => 'Held for Review',
            ''  => null,
            default => $this->response_code ? 'Code ' . $this->response_code : null,
        };
    }

    /**
     * High-level category for color coding in the UI.
     * Returns: 'payment' | 'subscription' | 'profile' | 'customer' | 'other'
     */
    public function category(): string
    {
        $t = (string) $this->event_type;
        if (str_starts_with($t, 'net.authorize.payment.'))                       return 'payment';
        if (str_starts_with($t, 'net.authorize.customer.subscription.'))         return 'subscription';
        if (str_starts_with($t, 'net.authorize.customer.paymentProfile.'))       return 'profile';
        if (str_starts_with($t, 'net.authorize.customer.'))                      return 'customer';
        return 'other';
    }

    /**
     * Status semantic: success | failed | warning | info | refund.
     * Used for badge color in the table.
     */
    public function statusKind(): string
    {
        return match ($this->event_type) {
            // Success
            'net.authorize.payment.authcapture.created',
            'net.authorize.payment.authorization.created',
            'net.authorize.payment.capture.created',
            'net.authorize.payment.priorAuthCapture.created',
            'net.authorize.payment.fraud.approved',
            'net.authorize.customer.subscription.created',
            'net.authorize.customer.created',
            'net.authorize.customer.paymentProfile.created' => 'success',

            // Refund / void
            'net.authorize.payment.refund.created',
            'net.authorize.payment.void.created' => 'refund',

            // Failed / terminated / deleted
            'net.authorize.payment.fraud.declined',
            'net.authorize.customer.subscription.failed',
            'net.authorize.customer.subscription.cancelled',
            'net.authorize.customer.subscription.terminated',
            'net.authorize.customer.deleted',
            'net.authorize.customer.paymentProfile.deleted' => 'failed',

            // Pending / warning
            'net.authorize.payment.fraud.held',
            'net.authorize.customer.subscription.suspended',
            'net.authorize.customer.subscription.expiring',
            'net.authorize.customer.subscription.expired'   => 'warning',

            // Informational / neutral
            default => 'info',
        };
    }

    /**
     * Returns ['label' => 'Approved', 'class' => 'bg-green-100 text-green-800']
     */
    public function statusBadge(): array
    {
        $kind = $this->statusKind();

        $label = match (true) {
            // Add response code suffix for payment authorizations/captures
            $kind === 'success' && str_starts_with($this->event_type, 'net.authorize.payment.')
                && $this->responseCodeLabel() !== null => $this->responseCodeLabel(),
            $kind === 'success' => 'Success',
            $kind === 'refund'  => 'Refunded',
            $kind === 'failed'  => 'Failed',
            $kind === 'warning' => 'Pending',
            default             => 'Info',
        };

        $class = match ($kind) {
            'success' => 'bg-green-100 text-green-800',
            'refund'  => 'bg-orange-100 text-orange-800',
            'failed'  => 'bg-red-100 text-red-800',
            'warning' => 'bg-amber-100 text-amber-800',
            default   => 'bg-slate-100 text-slate-700',
        };

        return ['label' => $label, 'class' => $class];
    }

    /**
     * Composite display name — falls back to email then to "—".
     */
    public function customerDisplayName(): string
    {
        $name = trim(($this->customer_first_name ?? '') . ' ' . ($this->customer_last_name ?? ''));
        if ($name !== '') return $name;
        if ($this->customer_email) return $this->customer_email;
        return '—';
    }

    /**
     * Build a plain-English description from event_type + payload + matched sub.
     * Static so the controller can call it at insert time.
     */
    public static function describeEvent(
        string $eventType,
        array $payload,
        ?string $firstName = null,
        ?string $lastName = null
    ): string {
        $name = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
        if ($name === '') $name = 'unknown customer';

        $amount = data_get($payload, 'payload.authAmount')
                ?? data_get($payload, 'payload.amount');
        $amountStr = $amount !== null ? '$' . number_format((float) $amount, 2) : '';

        $invoice = data_get($payload, 'payload.invoiceNumber');
        $invoiceStr = $invoice ? " (invoice {$invoice})" : '';

        return match ($eventType) {
            'net.authorize.payment.authcapture.created'
                => trim("Payment of {$amountStr} captured successfully for {$name}{$invoiceStr}"),
            'net.authorize.payment.authorization.created'
                => trim("Payment authorization of {$amountStr} for {$name}{$invoiceStr}"),
            'net.authorize.payment.capture.created'
                => trim("Capture of {$amountStr} for {$name}{$invoiceStr}"),
            'net.authorize.payment.priorAuthCapture.created'
                => trim("Prior-auth capture of {$amountStr} for {$name}{$invoiceStr}"),
            'net.authorize.payment.refund.created'
                => trim("Refund of {$amountStr} issued for {$name}{$invoiceStr}"),
            'net.authorize.payment.void.created'
                => "Payment voided for {$name}{$invoiceStr}",
            'net.authorize.payment.fraud.approved'
                => "Fraud review approved for {$name}{$invoiceStr}",
            'net.authorize.payment.fraud.declined'
                => "Fraud review declined for {$name}{$invoiceStr}",
            'net.authorize.payment.fraud.held'
                => "Payment held for fraud review — {$name}{$invoiceStr}",

            'net.authorize.customer.subscription.created'
                => "Subscription started for {$name}",
            'net.authorize.customer.subscription.updated'
                => "Subscription updated for {$name}",
            'net.authorize.customer.subscription.failed'
                => "Recurring payment failed for {$name}",
            'net.authorize.customer.subscription.suspended'
                => "Subscription suspended for {$name}",
            'net.authorize.customer.subscription.cancelled'
                => "Subscription cancelled for {$name}",
            'net.authorize.customer.subscription.expired'
                => "Subscription expired for {$name}",
            'net.authorize.customer.subscription.expiring'
                => "Subscription expiring soon for {$name}",
            'net.authorize.customer.subscription.terminated'
                => "Subscription terminated for {$name}",

            'net.authorize.customer.created'
                => 'Customer profile created' . ($firstName ? " for {$name}" : ''),
            'net.authorize.customer.updated'
                => 'Customer profile updated' . ($firstName ? " for {$name}" : ''),
            'net.authorize.customer.deleted'
                => 'Customer profile deleted' . ($firstName ? " for {$name}" : ''),
            'net.authorize.customer.paymentProfile.created'
                => 'Payment method added' . ($firstName ? " for {$name}" : ''),
            'net.authorize.customer.paymentProfile.updated'
                => 'Payment method updated' . ($firstName ? " for {$name}" : ''),
            'net.authorize.customer.paymentProfile.deleted'
                => 'Payment method removed' . ($firstName ? " for {$name}" : ''),

            default => $eventType,
        };
    }
}
