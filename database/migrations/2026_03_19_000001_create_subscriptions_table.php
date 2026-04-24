<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Customer identity
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();

            // Plan info
            $table->string('plan_key');          // silver | gold | platinum
            $table->string('plan_label');
            $table->decimal('amount', 10, 2);    // initial charge amount
            $table->decimal('recurring_amount', 10, 2); // monthly recurring

            // Authorize.Net identifiers
            $table->string('invoice_number')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('auth_code')->nullable();
            $table->string('arb_subscription_id')->nullable()->index(); // ARB sub ID
            $table->string('customer_profile_id')->nullable();
            $table->string('customer_payment_profile_id')->nullable();

            // Referral
            $table->string('referral_code')->nullable();

            // Subscription lifecycle
            // active | past_due | terminated | cancelled
            $table->string('status')->default('active')->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();

            // Failed payment tracking
            $table->unsignedTinyInteger('failed_payment_count')->default(0);
            $table->timestamp('first_failed_at')->nullable(); // when grace period starts
            $table->timestamp('grace_period_ends_at')->nullable(); // 7 days after first failure
            $table->timestamp('terminated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
