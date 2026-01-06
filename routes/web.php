<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminKalenderKerjaController;

// Landing page -> tunjukkan login form langsung
Route::get('/', [AuthController::class, 'showLoginForm']);

// Auth (username-based)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Kalender Kerja (URL netral, akses tetap dibatasi di controller)
    Route::get('/kalender-kerja', [AdminKalenderKerjaController::class, 'index'])->name('admin.kalender');
    Route::post('/kalender-kerja', [AdminKalenderKerjaController::class, 'store'])->name('admin.kalender.store');

    // Edit Kalender Kerja
    Route::get('/kalender-kerja/{id}/edit', [AdminKalenderKerjaController::class, 'edit'])->name('kalender.edit');
    Route::put('/kalender-kerja/{id}', [AdminKalenderKerjaController::class, 'update'])->name('kalender.update');

    // Hapus Kalender Kerja
    Route::delete('/kalender-kerja/{id}', [AdminKalenderKerjaController::class, 'destroy'])->name('kalender.destroy');
});
