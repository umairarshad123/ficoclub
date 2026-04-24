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
        'amount',
        'invoice_number',
        'arb_status',
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
}
