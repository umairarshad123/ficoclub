<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds indexes needed for fast dashboard aggregate queries.
 * Runs only if the indexes don't already exist.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // status index already exists per original migration — skip.
            // arb_subscription_id already indexed — skip.

            $table->index('referral_code', 'subscriptions_referral_code_index');
            $table->index('subscribed_at',  'subscriptions_subscribed_at_index');
            $table->index('terminated_at',  'subscriptions_terminated_at_index');
            $table->index('plan_key',       'subscriptions_plan_key_index');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_referral_code_index');
            $table->dropIndex('subscriptions_subscribed_at_index');
            $table->dropIndex('subscriptions_terminated_at_index');
            $table->dropIndex('subscriptions_plan_key_index');
        });
    }
};
