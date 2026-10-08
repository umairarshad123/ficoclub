<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tag every money / webhook row with the processor it came from, so
 * Authorize.Net history and Commas activity can live side by side.
 * Existing rows are all Authorize.Net, hence the default.
 */
return new class extends Migration {
    public function up(): void
    {
        foreach (['subscriptions', 'payments', 'webhook_events'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->string('provider', 20)->default('authorize_net')->index();
            });
        }

        Schema::table('webhook_events', function (Blueprint $table) {
            $table->timestamp('processed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropColumn('processed_at');
        });

        foreach (['subscriptions', 'payments', 'webhook_events'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                $table->dropIndex($tbl . '_provider_index');
                $table->dropColumn('provider');
            });
        }
    }
};
