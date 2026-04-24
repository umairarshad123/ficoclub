<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('ssn_encrypted')->nullable();
            $table->date('dob')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('annual_income', 50)->nullable();
            $table->string('own_or_rent', 50)->nullable();
            $table->string('monthly_housing', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 50)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('years_at_address', 50)->nullable();
            $table->string('sc_email')->nullable();
            $table->text('sc_password_encrypted')->nullable();
            $table->string('sc_ssn_last4', 10)->nullable();
            $table->enum('funding_type', ['personal','business','both'])->nullable();
            $table->string('biz_entity_type')->nullable();
            $table->string('biz_name')->nullable();
            $table->string('biz_phone', 30)->nullable();
            $table->string('biz_email')->nullable();
            $table->string('biz_website')->nullable();
            $table->string('biz_incorp_state', 50)->nullable();
            $table->string('biz_has_directors', 10)->nullable();
            $table->string('biz_annual_revenue', 50)->nullable();
            $table->string('biz_has_financials', 10)->nullable();
            $table->string('biz_address')->nullable();
            $table->string('biz_city')->nullable();
            $table->string('biz_state', 50)->nullable();
            $table->string('biz_zip', 10)->nullable();
            $table->string('final_phone', 30)->nullable();
            $table->string('final_email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('sheets_pushed')->default(false);
            $table->boolean('ghl_pushed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('leads');
    }
};