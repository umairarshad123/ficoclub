<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A Commas checkout attempt. See the create_checkout_orders_table migration
 * for the lifecycle; App\Services\EnrollmentFulfillment owns the transitions.
 */
class CheckoutOrder extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_MISMATCH = 'mismatch';

    protected $fillable = [
        'uuid', 'invoice_number',
        'plan_key', 'plan_label', 'amount', 'commas_product_id',
        'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'state', 'zip',
        'referral_code', 'marketing_opt_in', 'agreed_terms_at',
        'ip_address', 'user_agent',
        'status',
        'commas_session_id', 'client_transaction_ref',
        'commas_transaction_id', 'commas_payment_id', 'commas_buyer_id',
        'paid_amount', 'confirmed_via', 'paid_at', 'mismatch_reason',
        'subscription_id', 'notified_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'marketing_opt_in' => 'boolean',
        'agreed_terms_at'  => 'datetime',
        'paid_at'          => 'datetime',
        'notified_at'      => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CheckoutOrder $order) {
            $order->uuid ??= (string) Str::uuid();
            // Random suffix — the old 'INV-' . time() collided on same-second checkouts.
            $order->invoice_number ??= 'INV-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        });
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /** The customer array the GHL webhook + onboarding cache have always used. */
    public function customerPayload(): array
    {
        return [
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'city'          => $this->city,
            'state'         => $this->state,
            'zip'           => $this->zip,
            'plan_key'      => $this->plan_key,
            'plan_label'    => $this->plan_label,
            'amount'        => number_format((float) $this->amount, 2, '.', ''),
            'referral_code' => $this->referral_code,
        ];
    }
}
