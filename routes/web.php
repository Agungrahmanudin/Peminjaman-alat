<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'auth']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store']);
Route::get('/auth-google-redirect', [AuthController::class, 'googleRedirect'])->name('auth-google-redirect');
Route::get('/auth-google-callback', [AuthController::class, 'googleCallback'])->name('auth-google-callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard — semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----------------------------------------------------------------
    // ALAT — index: semua role bisa akses
    // ----------------------------------------------------------------
    Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');

    // ----------------------------------------------------------------
    // HANYA ADMIN & PETUGAS
    // ----------------------------------------------------------------
    Route::middleware('role:admin,petugas')->group(function () {

        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomersController::class, 'index'])->name('customers.index');
            Route::get('/export', [CustomersController::class, 'export'])->name('customers.export');
            Route::get('/{customer}/data', [CustomersController::class, 'getCustomerData'])->name('customers.data');
            Route::get('/{customer}', [CustomersController::class, 'show'])->name('customers.show');
            Route::put('/{customer}', [CustomersController::class, 'update'])->name('customers.update');
            Route::delete('/{customer}', [CustomersController::class, 'destroy'])->name('customers.destroy');
        });

        // Alat — Spesifik routes (Harus di atas wildcard /{alat})
        Route::get('/alat/create', [AlatController::class, 'create'])->name('alat.create');
        Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
        Route::delete('/alat/delete-all', [AlatController::class, 'deleteAll'])->name('alat.deleteAll');
        Route::get('/alat/check-kode', [AlatController::class, 'checkKode'])->name('alat.check-kode');
        Route::get('/alat/search', [AlatController::class, 'search'])->name('alat.search');

        // Alat — Wildcard routes (Edit, Update, Delete)
        Route::get('/alat/{alat}/edit', [AlatController::class, 'edit'])->name('alat.edit');
        Route::put('/alat/{alat}', [AlatController::class, 'update'])->name('alat.update');
        Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])->name('alat.destroy');

        // Laporan

        // Di dalam grup reports
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/peminjaman', [ReportController::class, 'peminjaman'])->name('reports.peminjaman');
            Route::get('/alat', [ReportController::class, 'alat'])->name('reports.alat');
            Route::get('/pengembalian', [ReportController::class, 'pengembalian'])->name('reports.pengembalian');
            Route::get('/keuangan', [ReportController::class, 'keuangan'])->name('reports.keuangan');
            Route::get('/export', [ReportController::class, 'exportPDF'])->name('reports.export');
            Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::put('/{user}/status', [UserController::class, 'updateStatus'])->name('status');
        });

        // Pengembalian — proses hanya admin & petugas
        Route::get('/pengembalian/create/{peminjaman_id?}', [PengembalianController::class, 'create'])->name('pengembalian.create');
        Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');
        Route::post('/pengembalian/{pengembalian}/bayar-denda', [PengembalianController::class, 'bayarDenda'])->name('pengembalian.bayar-denda');

        // Kategori
        Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');

    }); // end role:admin,petugas

    // ----------------------------------------------------------------
    // ALAT — show: semua role bisa akses
    // (Diletakkan di SINI agar tidak bentrok dengan /alat/create dll)
    // ----------------------------------------------------------------
    Route::get('/alat/{alat}', [AlatController::class, 'show'])->name('alat.show');

    // ----------------------------------------------------------------
    // SEMUA ROLE — Peminjaman (data difilter di controller)
    // ----------------------------------------------------------------
    Route::prefix('peminjaman')->group(function () {
        Route::get('/', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/search-alat', [PeminjamanController::class, 'searchAlat'])->name('peminjaman.searchAlat');
        Route::get('/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::get('/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
        Route::put('/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::delete('/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
        Route::get('/{peminjaman}/cetak', [PeminjamanController::class, 'cetak'])->name('peminjaman.cetak');
        Route::post('/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/{peminjaman}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
        Route::get('/{peminjaman}/pengembalian', [PeminjamanController::class, 'formPengembalian'])->name('peminjaman.pengembalian');
        Route::post('/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('peminjaman.perpanjang');
        Route::post('/{peminjaman}/perpanjang/approve', [PeminjamanController::class, 'approvePerpanjang'])->name('peminjaman.perpanjang.approve')->middleware('role:admin,petugas');
        Route::post('/{peminjaman}/perpanjang/reject', [PeminjamanController::class, 'rejectPerpanjang'])->name('peminjaman.perpanjang.reject')->middleware('role:admin,petugas');
    });

    // Pengembalian — index & show semua role (data difilter di controller)
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');

    // Pengajuan pengembalian oleh peminjam
    Route::post('/pengembalian/ajukan/{peminjaman}', [PeminjamanController::class, 'ajukanPengembalian'])->name('pengembalian.ajukan');

    // Profile & Settings — semua role
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/cover', [ProfileController::class, 'updateCover'])->name('profile.cover');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::post('/settings/update-password', [ProfileController::class, 'updatePassword'])->name('settings.update-password');
    Route::post('/settings/update-email', [ProfileController::class, 'updateEmail'])->name('settings.update-email');
    Route::post('/settings/delete-account', [ProfileController::class, 'deleteAccount'])->name('settings.delete-account');
    // Settings tambahan
    Route::post('/settings/update-notifications', [ProfileController::class, 'updateNotifications'])->name('settings.update-notifications');
    Route::post('/settings/update-privacy', [ProfileController::class, 'updatePrivacy'])->name('settings.update-privacy');
    Route::post('/settings/update-language', [ProfileController::class, 'updateLanguage'])->name('settings.update-language');
}); // end auth