<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gantikan tabel karya lama dengan riwayat_status_tulisan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('karya');

        Schema::create('riwayat_status_tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tulisan_id')->constrained('tulisan')->cascadeOnDelete();
            $table->string('status', 30);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_tulisan');
    }
};
