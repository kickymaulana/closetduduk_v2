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
        Schema::create('aturan_penolakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cacat_id')->constrained('cacat')->cascadeOnDelete();
            $table->foreignId('dep_toleransi')->constrained('departemen')->cascadeOnDelete();
            $table->foreignId('dep_buang')->constrained('departemen')->cascadeOnDelete();
            $table->foreignId('dep_pemeriksa')->constrained('departemen')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_penolakan');
    }
};
