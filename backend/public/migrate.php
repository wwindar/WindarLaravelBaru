<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Artisan;

/**
 * ⚠️  FILE INI UNTUK SEKALI PAKAI SAJA — HAPUS SEGERA SETELAH MIGRATION BERHASIL!
 *
 * Cara pakai:
 *   1. Upload seluruh folder backend ke hosting.
 *   2. Akses URL ini sekali lewat browser:
 *      https://api.namadomainmu.infinityfreeapp.com/migrate.php
 *   3. Jika berhasil, halaman akan menampilkan "Migration selesai".
 *   4. SEGERA HAPUS file ini dari File Manager hosting — ini celah keamanan!
 *
 * ⛔  Jangan pernah biarkan file ini tersisa di server produksi.
 */

// Kunci sederhana supaya tidak bisa diakses sembarang orang.
// Ganti nilai ini dan kirimkan lewat query string: ?kunci=NILAI_RAHASIAMU
define('MIGRATION_KEY', 'ganti-kunci-rahasia-ini');

if (! isset($_GET['kunci']) || $_GET['kunci'] !== MIGRATION_KEY) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1><p>Akses ditolak. Sertakan ?kunci=... yang benar.</p>';
    exit;
}

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

// Jalankan migration
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();

    echo '<pre style="font-family:monospace;background:#1a1a2e;color:#e2e2e2;padding:20px;border-radius:8px;">';
    echo "✅ Migration selesai!\n\n";
    echo htmlspecialchars($output);
    echo '</pre>';
    echo '<p style="color:red;font-weight:bold;">⚠️  SEGERA HAPUS FILE INI DARI SERVER SEKARANG!</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<pre style="color:red;">';
    echo "❌ Migration gagal:\n\n";
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
}
