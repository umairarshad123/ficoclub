<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local mirror of every Commas transaction (website checkout AND sales from
 * GHL funnels / payment links), synced by `php artisan commas:sync`.
 * Powers the Commas dashboard + Commas Sales page.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('commas_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('commas_id')->unique();                 // numeric transaction id from Commas
            $table->timestamp('transaction_date')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_phone', 40)->nullable();
            $table->string('product_id', 40)->nullable()->index(); // product hashid
            $table->string('product_title')->nullable();
            $table->decimal('amount', 10, 2)->default(0);           // gross charged (incl. any surcharge)
            $table->decimal('fee_amount', 10, 2)->default(0);       // Commas fee
            $table->decimal('net_amount', 10, 2)->default(0);       // payout to the business
            $table->decimal('refunded_amount', 10, 2)->default(0);       // returned to the customer
            $table->decimal('refund_cost', 10, 2)->default(0);           // what the refunds cost the business (incl. fees)
            $table->unsignedSmallInteger('refund_count')->default(0);
            $table->string('payment_type', 40)->nullable();         // upfront | auto_renew | …
            $table->timestamp('fund_release_on')->nullable();
            $table->boolean('fund_released')->default(false)->index();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commas_transactions');
    }
};
