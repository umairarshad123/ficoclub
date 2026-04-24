<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'plan_key',
        'plan_label',
        'amount',
        'recurring_amount',
        'invoice_number',
        'transaction_id',
        'auth_code',
        'arb_subscription_id',
        'customer_profile_id',
        'customer_payment_profile_id',
        'referral_code',
        'status',
        'subscribed_at',
        'next_billing_date',
        'failed_payment_count',
        'first_failed_at',
        'grace_period_ends_at',
        'terminated_at',
    ];

    protected $casts = [
        'subscribed_at'        => 'datetime',
        'next_billing_date'    => 'datetime',
        'first_failed_at'      => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'terminated_at'        => 'datetime',
        'amount'               => 'decimal:2',
        'recurring_amount'     => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function events(): HasMany
    {
        return $this->hasMany(SubscriptionEvent::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Total cash collected for this subscription, net of refunds/voids.
     * Falls back to the initial charge amount if the payments table is empty
     * (e.g. legacy rows that predate the payments hydration).
     */
    public function lifetimeRevenue(): float
    {
        $fromPayments = (float) $this->payments()
            ->whereIn('type', ['initial', 'recurring'])
            ->whereIn('status', ['captured'])
            ->sum('amount');

        $refunded = (float) $this->payments()
            ->whereIn('type', ['refund', 'void'])
            ->sum('amount');

        $net = $fromPayments - $refunded;

        if ($fromPayments === 0.0) {
            // No hydrated payments yet — fall back to the known initial charge
            return (float) ($this->amount ?? 0);
        }

        return $net;
    }

    public function paymentsCount(): int
    {
        return (int) $this->payments()
            ->whereIn('type', ['initial', 'recurring'])
            ->where('status', 'captured')
            ->count();
    }


    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
