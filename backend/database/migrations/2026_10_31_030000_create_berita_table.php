<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // admin/dosen yang nulis
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->enum('status', ['draf', 'terbit'])->default('draf');
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();

            $table->index(['status', 'diterbitkan_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
