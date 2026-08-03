<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quran_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id')->unique();
            $table->string('display_name');
            $table->string('translator')->nullable();
            $table->string('translator_foreign')->nullable();
            $table->string('language_code', 10)->index();
            $table->string('file_url');
            $table->string('file_name');
            $table->string('save_to')->nullable();
            $table->string('download_type')->nullable();
            $table->unsignedInteger('minimum_version')->default(0);
            $table->unsignedInteger('current_version')->default(0);
            $table->timestamp('remote_last_modified')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_translations');
    }
};
