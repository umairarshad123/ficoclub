<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lead_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('file_key', 50);
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->char('download_token', 64)->unique();
            $table->text('download_url');
            $table->timestamps();
            $table->index('lead_id');
            $table->index('file_key');
        });
    }

    public function down(): void {
        Schema::dropIfExists('lead_files');
    }
};