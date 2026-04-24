<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('biz_has_directors', 100)->change();
            $table->string('biz_has_financials', 100)->change();
            $table->string('own_or_rent', 100)->change();
            $table->string('employment_status', 255)->change();
            $table->string('biz_entity_type', 255)->change();
        });
    }

    public function down(): void {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('biz_has_directors', 10)->change();
            $table->string('biz_has_financials', 10)->change();
            $table->string('own_or_rent', 50)->change();
            $table->string('employment_status', 100)->change();
            $table->string('biz_entity_type', 100)->change();
        });
    }
};