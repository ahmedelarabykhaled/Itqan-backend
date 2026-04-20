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
        Schema::create('user_memorized_ayahs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedInteger('surah_id')->index();
            $table->unsignedInteger('ayah_number');
            $table->timestamp('memorized_at');
            $table->timestamps();

            $table->unique(['user_id', 'surah_id', 'ayah_number']);
            $table->index(['user_id', 'memorized_at']);
            $table->index(['user_id', 'surah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memorized_ayahs');
    }
};
