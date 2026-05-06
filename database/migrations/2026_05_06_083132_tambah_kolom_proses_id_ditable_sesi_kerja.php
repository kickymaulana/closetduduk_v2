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
        Schema::table('sesi_kerja', function (Blueprint $table) {
            $table->foreignId('proses_id')->nullable()->after('shift_id')->constrained('proses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_kerja', function (Blueprint $table) {
            $table->dropForeign(['proses_id']);
            $table->dropColumn(['proses_id']);
        });
    }
};
