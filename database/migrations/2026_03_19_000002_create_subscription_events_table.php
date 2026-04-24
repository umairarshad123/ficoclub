<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                  ->constrained('subscriptions')
                  ->onDelete('cascade');

            // Event type: payment_failed | payment_recovered | terminated | ghl_notified | manual_note
            $table->string('event_type')->index();

            // Raw Authorize.Net event data (JSON)
            $table->json('payload')->nullable();

            // Optional human-readable note
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_events');
    }
};
