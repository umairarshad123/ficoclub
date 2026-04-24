<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pre-extracted fields denormalised onto webhook_events at insert time so the
 * dashboard can render rows without re-parsing the JSON blob on every request.
 *
 *   - response_code         e.g. "1" (Approved), "2" (Declined), "3", "4"
 *   - customer_first_name   copied from matched subscription, snapshot in time
 *   - customer_last_name    same
 *   - customer_email        same
 *   - description           pre-computed plain-English summary
 *
 * Customer fields are intentionally a snapshot — if the subscription email
 * changes later, historical webhook rows still show the email at the moment
 * the event arrived. That's the correct audit-log behavior.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('response_code', 8)->nullable()->after('arb_status');
            $table->string('customer_first_name', 100)->nullable()->after('matched_subscription_id');
            $table->string('customer_last_name', 100)->nullable()->after('customer_first_name');
            $table->string('customer_email', 150)->nullable()->after('customer_last_name');
            $table->string('description', 500)->nullable()->after('customer_email');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropColumn([
                'response_code',
                'customer_first_name',
                'customer_last_name',
                'customer_email',
                'description',
            ]);
        });
    }
};
