<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminKalenderKerjaController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminMasterDataController;

// Landing page -> tunjukkan login form langsung
Route::get('/', [AuthController::class, 'showLoginForm']);

// Auth (username-based)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Grouped settings landing pages (standalone, no sidebar)
    Route::view('/settings', 'settings.index')->name('settings.index');
    Route::view('/settings/user', 'settings.user')->name('settings.user');
    Route::view('/settings/biro', 'settings.biro')->name('settings.biro');
    Route::view('/settings/jabatan', 'settings.jabatan')->name('settings.jabatan');
    Route::view('/settings/role', 'settings.role')->name('settings.role');

    // Users (admin-only via controller)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/addusers', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/addusers', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Master data (admin-only via controller)
    // Biro
    Route::get('/biro', [AdminMasterDataController::class, 'biroIndex'])->name('biro.index');
    Route::get('/biro/create', [AdminMasterDataController::class, 'biroCreate'])->name('biro.create');
    Route::post('/biro', [AdminMasterDataController::class, 'biroStore'])->name('biro.store');
    Route::get('/biro/{id}/edit', [AdminMasterDataController::class, 'biroEdit'])->name('biro.edit');
    Route::put('/biro/{id}', [AdminMasterDataController::class, 'biroUpdate'])->name('biro.update');
    Route::delete('/biro/{id}', [AdminMasterDataController::class, 'biroDestroy'])->name('biro.destroy');

    // Jabatan
    Route::get('/jabatan', [AdminMasterDataController::class, 'jabatanIndex'])->name('jabatan.index');
    Route::get('/jabatan/create', [AdminMasterDataController::class, 'jabatanCreate'])->name('jabatan.create');
    Route::post('/jabatan', [AdminMasterDataController::class, 'jabatanStore'])->name('jabatan.store');
    Route::get('/jabatan/{id}/edit', [AdminMasterDataController::class, 'jabatanEdit'])->name('jabatan.edit');
    Route::put('/jabatan/{id}', [AdminMasterDataController::class, 'jabatanUpdate'])->name('jabatan.update');
    Route::delete('/jabatan/{id}', [AdminMasterDataController::class, 'jabatanDestroy'])->name('jabatan.destroy');

    // Role
    Route::get('/role', [AdminMasterDataController::class, 'roleIndex'])->name('role.index');
    Route::get('/role/create', [AdminMasterDataController::class, 'roleCreate'])->name('role.create');
    Route::post('/role', [AdminMasterDataController::class, 'roleStore'])->name('role.store');
    Route::get('/role/{id}/edit', [AdminMasterDataController::class, 'roleEdit'])->name('role.edit');
    Route::put('/role/{id}', [AdminMasterDataController::class, 'roleUpdate'])->name('role.update');
    Route::delete('/role/{id}', [AdminMasterDataController::class, 'roleDestroy'])->name('role.destroy');

    // Kalender Kerja (URL netral, akses tetap dibatasi di controller)
    Route::get('/kalender-kerja', [AdminKalenderKerjaController::class, 'index'])->name('admin.kalender');
    Route::post('/kalender-kerja', [AdminKalenderKerjaController::class, 'store'])->name('admin.kalender.store');

    // Edit Kalender Kerja
    Route::get('/kalender-kerja/{id}/edit', [AdminKalenderKerjaController::class, 'edit'])->name('kalender.edit');
    Route::put('/kalender-kerja/{id}', [AdminKalenderKerjaController::class, 'update'])->name('kalender.update');

    // Hapus Kalender Kerja
    Route::delete('/kalender-kerja/{id}', [AdminKalenderKerjaController::class, 'destroy'])->name('kalender.destroy');
});
