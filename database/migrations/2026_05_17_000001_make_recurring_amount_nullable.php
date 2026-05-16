<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One-time plans (onetime / couples / vip) carry no monthly recurring
     * amount, so recurring_amount must accept NULL.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('recurring_amount', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('recurring_amount', 10, 2)->nullable(false)->default(0)->change();
        });
    }
};
