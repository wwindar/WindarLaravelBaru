<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom profil ke tabel users:
 * - bio      : biografi/deskripsi diri singkat
 * - angkatan : tahun angkatan mahasiswa (opsional)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('username');
            }
            if (! Schema::hasColumn('users', 'angkatan')) {
                $table->string('angkatan', 10)->nullable()->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('bio');
            $table->dropColumnIfExists('angkatan');
        });
    }
};

