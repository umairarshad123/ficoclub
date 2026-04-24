<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();

            // Auth.net notificationId — null tolerated for malformed/forged traffic
            $table->string('notification_id')->nullable()->unique();

            $table->string('event_type')->index();

            // Auth.net payload.id — transaction id OR ARB subscription id depending on event
            $table->string('entity_id')->nullable()->index();

            // Populated when this event maps to a local subscription
            $table->foreignId('matched_subscription_id')
                  ->nullable()
                  ->constrained('subscriptions')
                  ->nullOnDelete();

            // Lightweight parsed fields for fast dashboard queries
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('arb_status')->nullable();

            // Signature verification result at the moment of receipt
            // null  = could not verify (key or header missing)
            // true  = matched
            // false = mismatch (captured for forensic trail)
            $table->boolean('signature_valid')->nullable()->index();

            $table->string('source_ip', 45)->nullable();

            $table->timestamp('received_at')->nullable()->index();

            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['event_type', 'received_at'], 'webhook_events_type_received_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
