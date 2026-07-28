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
        Schema::table('user_memorized_ayahs', function (Blueprint $table): void {
            $table->string('status')->nullable()->after('memorized_at');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_memorized_ayahs', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'status']);
            $table->dropColumn('status');
        });
    }
};
