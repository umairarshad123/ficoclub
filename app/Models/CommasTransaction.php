<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Local mirror of a Commas transaction — see App\Services\CommasTransactionSync. */
class CommasTransaction extends Model
{
    protected $fillable = [
        'commas_id', 'transaction_date',
        'customer_name', 'customer_email', 'customer_phone',
        'product_id', 'product_title',
        'amount', 'fee_amount', 'net_amount', 'refunded_amount', 'refund_cost', 'refund_count',
        'payment_type', 'fund_release_on', 'fund_released', 'raw',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'fund_release_on'  => 'datetime',
        'fund_released'    => 'boolean',
        'amount'           => 'decimal:2',
        'fee_amount'       => 'decimal:2',
        'net_amount'       => 'decimal:2',
        'refunded_amount'  => 'decimal:2',
        'refund_cost'      => 'decimal:2',
        'raw'              => 'array',
    ];

    /** Product hashids the website checkout sells (config/plans.php). */
    public static function websiteProductIds(): array
    {
        return collect(config('plans.plans', []))
            ->pluck('commas_product_id')
            ->filter()
            ->values()
            ->all();
    }

    public function isWebsiteSale(): bool
    {
        return in_array($this->product_id, self::websiteProductIds(), true);
    }

    public function scopeBetween(Builder $q, $start, $end): Builder
    {
        return $q->whereBetween('transaction_date', [$start, $end]);
    }
}
