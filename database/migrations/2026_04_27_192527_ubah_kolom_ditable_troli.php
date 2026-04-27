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
        Schema::table('troli', function (Blueprint $table) {
            $table->renameColumn('invoice', 'nomor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('troli', function (Blueprint $table) {
            // Kembalikan namanya jika migrasi di-rollback
            $table->renameColumn('nomor', 'invoice');
        });
    }
};
