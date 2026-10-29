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
        Schema::create('karya', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nomor_kiriman')->nullable()->unique();
            $table->string('judul');
            $table->enum('jenis', ['puisi', 'cerpen', 'esai']);
            $table->longText('isi');
            $table->string('nama_penulis');
            $table->string('email');
            $table->string('status', 30)->default('dikirim');
            $table->text('catatan_redaksi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karya');
    }
};

