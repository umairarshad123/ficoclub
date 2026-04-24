<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banned_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('reason')->default('Honeypot triggered');
            $table->text('user_agent')->nullable();
            $table->timestamp('banned_at');
            $table->timestamps();
            
            $table->index('ip_address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banned_ips');
    }
};