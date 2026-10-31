<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\DosenTulisanController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\MahasiswaTulisanController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\PublicTulisanController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// Auth Endpoints
// =========================================================================
Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// =========================================================================
// Publik — Tanpa Login
// =========================================================================
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/agenda', [AgendaController::class, 'index']);
Route::get('/agenda/{slug}', [AgendaController::class, 'show']);

// Tulisan terbit
Route::get('/tulisan', [PublicTulisanController::class, 'index']);
Route::get('/tulisan/{slug}', [PublicTulisanController::class, 'show']);
Route::post('/tulisan/{id}/lapor', [PublicTulisanController::class, 'lapor']);

// Berita & Pengumuman terbit
Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{slug}', [BeritaController::class, 'show']);

// =========================================================================
// Mahasiswa — Penulis Tulisan
// =========================================================================
Route::middleware(['auth:sanctum', 'role:mahasiswa'])->prefix('mahasiswa')->group(function (): void {
    Route::get('/tulisan', [MahasiswaTulisanController::class, 'index']);
    Route::post('/tulisan', [MahasiswaTulisanController::class, 'store']);
    Route::get('/tulisan/{id}', [MahasiswaTulisanController::class, 'show']);
    Route::put('/tulisan/{id}', [MahasiswaTulisanController::class, 'update']);
    Route::delete('/tulisan/{id}', [MahasiswaTulisanController::class, 'destroy']);
    Route::post('/tulisan/{id}/ajukan', [MahasiswaTulisanController::class, 'ajukan']);
});

// Notifikasi (semua user yang login)
Route::middleware('auth:sanctum')->prefix('notifikasi')->group(function (): void {
    Route::get('/', [NotifikasiController::class, 'index']);
    Route::patch('/{id}/baca', [NotifikasiController::class, 'baca']);
    Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua']);
});

// =========================================================================
// Dosen — Antrean & Moderasi
// =========================================================================
Route::middleware(['auth:sanctum', 'role:dosen'])->prefix('dosen')->group(function (): void {
    Route::get('/antrean', [DosenTulisanController::class, 'antrean']);
    Route::post('/tulisan/{id}/setujui', [DosenTulisanController::class, 'setujui']);
    Route::post('/tulisan/{id}/tolak', [DosenTulisanController::class, 'tolak']);
    Route::post('/tulisan/{id}/revisi', [DosenTulisanController::class, 'revisi']);
    Route::get('/laporan', [DosenTulisanController::class, 'laporan']);
    Route::patch('/laporan/{id}/status', [DosenTulisanController::class, 'updateStatusLaporan']);
});
