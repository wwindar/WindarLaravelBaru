<?php

use App\Http\Controllers\Api\AdminAgendaController;
use App\Http\Controllers\Api\AdminArtikelController;
use App\Http\Controllers\Api\AdminKaryaController;
use App\Http\Controllers\Api\AdminKategoriController;
use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KaryaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\KirimanController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// Auth Endpoints (Login / Logout Redaksi)
// =========================================================================
Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// =========================================================================
// Public Endpoints (Portal Komunitas Aksara Kampus)
// =========================================================================

// Kategori
Route::get('/kategori', [KategoriController::class, 'index']);

// Berita & Blog (Artikel)
Route::get('/artikel', [ArtikelController::class, 'index']);
Route::get('/artikel/{slug}', [ArtikelController::class, 'show']);

// Karya Sastra Terbit (Puisi, Cerpen, Esai)
Route::get('/karya', [KaryaController::class, 'index']);
Route::get('/karya/{slug}', [KaryaController::class, 'show']);

// Pengiriman & Pelacakan Karya oleh Penulis
Route::post('/kiriman', [KirimanController::class, 'store']);
Route::get('/kiriman/{nomor_kiriman}', [KirimanController::class, 'show']);
Route::put('/kiriman/{nomor_kiriman}', [KirimanController::class, 'update']);

// Agenda Kegiatan Sastra Kampus
Route::get('/agenda', [AgendaController::class, 'index']);
Route::get('/agenda/{slug}', [AgendaController::class, 'show']);

// =========================================================================
// Redaksi Endpoints (Protected by X-Admin-Token header)
// =========================================================================
Route::middleware('admin.token')->prefix('admin')->group(function (): void {
    // CRUD Artikel (Berita & Blog)
    Route::get('/artikel', [AdminArtikelController::class, 'index']);
    Route::post('/artikel', [AdminArtikelController::class, 'store']);
    Route::get('/artikel/{id}', [AdminArtikelController::class, 'show']);
    Route::match(['put', 'patch'], '/artikel/{id}', [AdminArtikelController::class, 'update']);
    Route::delete('/artikel/{id}', [AdminArtikelController::class, 'destroy']);

    // CRUD Agenda Sastra
    Route::get('/agenda', [AdminAgendaController::class, 'index']);
    Route::post('/agenda', [AdminAgendaController::class, 'store']);
    Route::get('/agenda/{id}', [AdminAgendaController::class, 'show']);
    Route::match(['put', 'patch'], '/agenda/{id}', [AdminAgendaController::class, 'update']);
    Route::delete('/agenda/{id}', [AdminAgendaController::class, 'destroy']);

    // CRUD Kategori
    Route::get('/kategori', [AdminKategoriController::class, 'index']);
    Route::post('/kategori', [AdminKategoriController::class, 'store']);
    Route::get('/kategori/{id}', [AdminKategoriController::class, 'show']);
    Route::match(['put', 'patch'], '/kategori/{id}', [AdminKategoriController::class, 'update']);
    Route::delete('/kategori/{id}', [AdminKategoriController::class, 'destroy']);

    // Kurasi & Moderasi Karya Masuk
    Route::get('/karya', [AdminKaryaController::class, 'index']);
    Route::get('/karya/{id}', [AdminKaryaController::class, 'show']);
    Route::patch('/karya/{id}/status', [AdminKaryaController::class, 'updateStatus']);
    Route::delete('/karya/{id}', [AdminKaryaController::class, 'destroy']);
});
