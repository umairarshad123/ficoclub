<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per Commas checkout attempt. Created BEFORE the customer pays
 * (holds contact info, plan, consent, referral), then marked paid exactly
 * once by whichever confirmation path gets there first: browser confirm,
 * payment.succeeded webhook, or the commas:reconcile job.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('checkout_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('invoice_number')->unique();

            // Plan snapshot at order time (price authority = config/plans.php)
            $table->string('plan_key');
            $table->string('plan_label');
            $table->decimal('amount', 10, 2);
            $table->string('commas_product_id')->nullable();

            // Customer
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->string('state', 10);
            $table->string('zip', 20);
            $table->string('referral_code', 50)->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('agreed_terms_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // pending → paid | mismatch (paid, but product/amount didn't match the plan)
            $table->string('status', 20)->default('pending')->index();

            // Commas identifiers — each source reports a different one
            $table->string('commas_session_id')->nullable();
            $table->string('client_transaction_ref')->nullable();          // SDK checkout:success transactionId (untrusted until verified)
            $table->string('commas_transaction_id')->nullable()->unique(); // numeric transaction id from the API
            $table->string('commas_payment_id')->nullable()->unique();     // ORD-XXXX-XXXX-XXXX from webhooks
            $table->string('commas_buyer_id')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('confirmed_via', 20)->nullable();               // browser | webhook | reconcile
            $table->timestamp('paid_at')->nullable()->index();
            $table->text('mismatch_reason')->nullable();

            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->timestamp('notified_at')->nullable();                  // GHL + Meta fired

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_orders');
    }
};
