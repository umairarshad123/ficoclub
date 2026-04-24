<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                  ->nullable()
                  ->constrained('subscriptions')
                  ->nullOnDelete();

            // Authorize.Net transaction identifiers
            $table->string('transaction_id')->nullable()->index();
            $table->string('invoice_number')->nullable()->index();

            // Money
            $table->decimal('amount', 10, 2);

            // type: initial | recurring | refund | void
            $table->string('type', 20)->index();

            // status: captured | refunded | voided | failed
            $table->string('status', 20)->index();

            // Raw eventType as received from Auth.net, for traceability
            $table->string('event_type_raw')->nullable();

            // When Auth.net says the money moved
            $table->timestamp('charged_at')->nullable()->index();

            // Raw webhook payload for forensic/audit
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->index(['subscription_id', 'charged_at'], 'payments_sub_charged_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
