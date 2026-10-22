<?php

use App\Http\Controllers\Api\AdminLayananController;
use App\Http\Controllers\Api\AdminPengajuanController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PengajuanController;
use Illuminate\Support\Facades\Route;

// Public Endpoints
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/layanan/{slug}', [LayananController::class, 'show']);
Route::post('/pengajuan', [PengajuanController::class, 'store']);
Route::get('/pengajuan/{nomor_pelacakan}', [PengajuanController::class, 'show']);
Route::put('/pengajuan/{nomor_pelacakan}', [PengajuanController::class, 'update']);

// Admin Endpoints (Protected by X-Admin-Token middleware)
Route::middleware('admin.token')->prefix('admin')->group(function (): void {
    // Admin CRUD Layanan
    Route::get('/layanan', [AdminLayananController::class, 'index']);
    Route::post('/layanan', [AdminLayananController::class, 'store']);
    Route::get('/layanan/{id}', [AdminLayananController::class, 'show']);
    Route::match(['put', 'patch'], '/layanan/{id}', [AdminLayananController::class, 'update']);
    Route::delete('/layanan/{id}', [AdminLayananController::class, 'destroy']);

    // Admin Pengajuan
    Route::get('/pengajuan', [AdminPengajuanController::class, 'index']);
    Route::get('/pengajuan/{id}', [AdminPengajuanController::class, 'show']);
    Route::patch('/pengajuan/{id}/status', [AdminPengajuanController::class, 'updateStatus']);
    Route::delete('/pengajuan/{id}', [AdminPengajuanController::class, 'destroy']);
});
