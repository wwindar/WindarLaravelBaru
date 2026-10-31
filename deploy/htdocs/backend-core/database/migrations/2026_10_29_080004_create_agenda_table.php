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
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('judul');
            $table->enum('jenis', ['lomba', 'bedah_buku', 'open_mic', 'workshop']);
            $table->longText('deskripsi');
            $table->string('lokasi');
            $table->dateTime('mulai_pada');
            $table->dateTime('selesai_pada');
            $table->unsignedInteger('kuota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
