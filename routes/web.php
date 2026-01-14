<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminKalenderKerjaController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminMasterDataController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PengajuanWfoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\ApprovalController;

// Landing page -> tunjukkan login form langsung
Route::get('/', [AuthController::class, 'showLoginForm']);

// Example API route
Route::get('/example-api', [IndexController::class, 'example_api']);

// Magic Link Auto-Login untuk Pengajuan WFO (tidak perlu auth middleware)
Route::get('/pengajuan-wfo/{token}', [MagicLinkController::class, 'login'])->name('magic.login');

// WhatsApp Approval Magic Link (tidak perlu auth middleware)
Route::get('/absen/approval/{token}', [ApprovalController::class, 'show'])->name('absen.approval.show');
Route::post('/absen/approval/{token}', [ApprovalController::class, 'process'])->name('absen.approval.process');

// Absen untuk orang lain (tanpa auth) - format tanggal: dd-mm-yyyy
// Route dengan parameter catch-all - redirect ke tanggal hari ini jika format salah
Route::get('/absensi/mywgabsen/{nip}/{tanggal?}', [AbsenController::class, 'absenOtomatis'])
    ->where('tanggal', '.*')
    ->name('absen.otomatis');
Route::get('/absensi/form-harian/{nip}/{tanggal?}', [AbsenController::class, 'absenFormStandalone'])
    ->where('tanggal', '.*')
    ->name('absen.form.standalone');
Route::post('/absensi/form-harian/{nip}/{tanggal}', [AbsenController::class, 'storeAbsenStandalone'])->name('absen.store.standalone');

// Auth (username-based)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Settings (with tabs for user, biro, jabatan, role)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::view('/settings/user', 'settings.user')->name('settings.user');
    Route::view('/settings/biro', 'settings.biro')->name('settings.biro');
    Route::view('/settings/jabatan', 'settings.jabatan')->name('settings.jabatan');
    Route::view('/settings/role', 'settings.role')->name('settings.role');

    // Users (admin-only via controller)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/addusers', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/addusers', [AdminUserController::class, 'store'])->name('users.store');
    Route::post('/users/set-edit', [AdminUserController::class, 'setEdit'])->name('users.setEdit');
    Route::get('/users/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/update', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/delete', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Master data (admin-only via controller)
    // Biro
    Route::get('/biro', [AdminMasterDataController::class, 'biroIndex'])->name('biro.index');
    Route::get('/biro/create', [AdminMasterDataController::class, 'biroCreate'])->name('biro.create');
    Route::post('/biro', [AdminMasterDataController::class, 'biroStore'])->name('biro.store');
    Route::post('/biro/set-edit', [AdminMasterDataController::class, 'biroSetEdit'])->name('biro.setEdit');
    Route::get('/biro/edit', [AdminMasterDataController::class, 'biroEdit'])->name('biro.edit');
    Route::put('/biro/update', [AdminMasterDataController::class, 'biroUpdate'])->name('biro.update');
    Route::delete('/biro/delete', [AdminMasterDataController::class, 'biroDestroy'])->name('biro.destroy');

    // Jabatan
    Route::get('/jabatan', [AdminMasterDataController::class, 'jabatanIndex'])->name('jabatan.index');
    Route::get('/jabatan/create', [AdminMasterDataController::class, 'jabatanCreate'])->name('jabatan.create');
    Route::post('/jabatan', [AdminMasterDataController::class, 'jabatanStore'])->name('jabatan.store');
    Route::post('/jabatan/set-edit', [AdminMasterDataController::class, 'jabatanSetEdit'])->name('jabatan.setEdit');
    Route::get('/jabatan/edit', [AdminMasterDataController::class, 'jabatanEdit'])->name('jabatan.edit');
    Route::put('/jabatan/update', [AdminMasterDataController::class, 'jabatanUpdate'])->name('jabatan.update');
    Route::delete('/jabatan/delete', [AdminMasterDataController::class, 'jabatanDestroy'])->name('jabatan.destroy');

    // Role
    Route::get('/role', [AdminMasterDataController::class, 'roleIndex'])->name('role.index');
    Route::get('/role/create', [AdminMasterDataController::class, 'roleCreate'])->name('role.create');
    Route::post('/role', [AdminMasterDataController::class, 'roleStore'])->name('role.store');
    Route::post('/role/set-edit', [AdminMasterDataController::class, 'roleSetEdit'])->name('role.setEdit');
    Route::get('/role/edit', [AdminMasterDataController::class, 'roleEdit'])->name('role.edit');
    Route::put('/role/update', [AdminMasterDataController::class, 'roleUpdate'])->name('role.update');
    Route::delete('/role/delete', [AdminMasterDataController::class, 'roleDestroy'])->name('role.destroy');

    // Kalender Kerja (URL netral, akses tetap dibatasi di controller)
    Route::get('/kalender-kerja', [AdminKalenderKerjaController::class, 'index'])->name('admin.kalender');
    Route::post('/kalender-kerja', [AdminKalenderKerjaController::class, 'store'])->name('admin.kalender.store');
    Route::match(['get', 'post'], '/kalender-kerja/page', [AdminKalenderKerjaController::class, 'index'])->name('admin.kalender.page');

    // Edit Kalender Kerja (ID disimpan di session)
    Route::post('/kalender-kerja/set-edit', [AdminKalenderKerjaController::class, 'setEdit'])->name('kalender.setEdit');
    Route::get('/kalender-kerja/edit', [AdminKalenderKerjaController::class, 'edit'])->name('kalender.edit');
    Route::put('/kalender-kerja/update', [AdminKalenderKerjaController::class, 'update'])->name('kalender.update');

    // Hapus Kalender Kerja (ID disimpan di session, langsung dihapus)
    Route::post('/kalender-kerja/delete', [AdminKalenderKerjaController::class, 'setDelete'])->name('kalender.setDelete');

    // Broadcast ulang ke semua biro untuk kalender tertentu
    Route::post('/kalender-kerja/broadcast', [AdminKalenderKerjaController::class, 'broadcast'])->name('kalender.broadcast');

    // Kalender Libur
    Route::get('/kalender-kerja/libur', [AdminKalenderKerjaController::class, 'liburIndex'])->name('kalender.libur.index');
    Route::post('/kalender-kerja/libur', [AdminKalenderKerjaController::class, 'liburStore'])->name('kalender.libur.store');
    Route::delete('/kalender-kerja/libur/{id}', [AdminKalenderKerjaController::class, 'liburDestroy'])->name('kalender.libur.destroy');

    // Pengajuan WFO
    Route::get('/pengajuan', [PengajuanWfoController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan/set-view', [PengajuanWfoController::class, 'setView'])->name('pengajuan.setView');
    Route::get('/pengajuan/view', [PengajuanWfoController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/set-edit', [PengajuanWfoController::class, 'setEdit'])->name('pengajuan.setEdit');
    Route::get('/pengajuan/edit', [PengajuanWfoController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/pengajuan/update', [PengajuanWfoController::class, 'update'])->name('pengajuan.update');
    Route::post('/pengajuan/save-draft', [PengajuanWfoController::class, 'saveDraft'])->name('pengajuan.saveDraft');
    
    // Broadcast ulang ke biro tertentu
    Route::post('/pengajuan/broadcast', [PengajuanWfoController::class, 'broadcast'])->name('pengajuan.broadcast');
    
    // Broadcast ulang ke multiple biro
    Route::post('/pengajuan/broadcast-multiple', [PengajuanWfoController::class, 'broadcastMultiple'])->name('pengajuan.broadcastMultiple');

    // Absen
    Route::get('/absen', [AbsenController::class, 'index'])->name('absen.index');
    Route::get('/absen/formulir', [AbsenController::class, 'showFormulir'])->name('absen.formulir');
    Route::post('/absen/masuk', [AbsenController::class, 'storeAbsenMasuk'])->name('absen.storeMasuk');
    Route::post('/absen/pulang', [AbsenController::class, 'storeAbsenPulang'])->name('absen.storePulang');
});
