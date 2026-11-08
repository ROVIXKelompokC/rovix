<?php

use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\ReportVerificationController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\FieldWorkOrderController;
use App\Http\Controllers\Admin\WorkOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Autentikasi & Registrasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Redirector Dasbor sesuai Role RBAC
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// =========================================================================
// 1. ROLE: ADMIN DINAS PU (Management Dashboard & Master Data RBAC)
// =========================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // CRUD Pengguna & Role RBAC (SCRUM-106 & SCRUM-110)
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show']);

    // GUNTUR - Master Data Wilayah & Zonasi Kecamatan (SCRUM-30 & SCRUM-34)
    Route::resource('districts', DistrictController::class)->only(['index', 'create', 'store', 'show']);

    // SCRUM-8 & SCRUM-9: Modul Verifikasi Laporan, Peta GIS Leaflet.js, & Audit Log (Hafiz)
    Route::get('/verification', [ReportVerificationController::class, 'index'])->name('verification.index');
    Route::get('/verification/{id}', [ReportVerificationController::class, 'show'])->name('verification.show');
    Route::post('/verification/{id}/approve', [ReportVerificationController::class, 'approve'])->name('verification.approve');
    Route::post('/verification/{id}/reject', [ReportVerificationController::class, 'reject'])->name('verification.reject');

    // Alias rute jamak (/admin/verifications) untuk kompatibilitas navigasi sidebar
    Route::get('/verifications', [ReportVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{id}', [ReportVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{id}/approve', [ReportVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{id}/reject', [ReportVerificationController::class, 'reject'])->name('verifications.reject');

    // SCRUM-38 & SCRUM-42: Modul Disposisi SPK Digital (Ersa)
    Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');

    // TIPA - Modul Master Kategori Kerusakan (SCRUM-22 & SCRUM-26)
    Route::get('/categories', [ReportCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [ReportCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [ReportCategoryController::class, 'store'])->name('categories.store');

    Route::view('/ratings', 'layouts.placeholder', [
        'title' => 'Rating & Ulasan Warga',
        'subtitle' => 'Rekapitulasi penilaian rating bintang 1–5 dan ulasan warga terhadap hasil perbaikan jalan',
        'description' => 'Modul pemantauan dan evaluasi hasil pengerjaan petugas lapangan berdasarkan ulasan warga pelapor.',
    ])->name('ratings.index');
});

// =========================================================================
// 2. ROLE: PETUGAS LAPANGAN (Responsive Field View)
// =========================================================================
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');
    Route::get('/history', [DashboardController::class, 'petugasHistory'])->name('history');
    Route::get('/work-orders/{workOrder}', [FieldWorkOrderController::class, 'show'])->name('work-orders.show');

    Route::view('/work-orders', 'layouts.placeholder', [
        'title' => 'Update Progres & Bukti Fisik Perbaikan',
        'subtitle' => 'Pembaruan status pengerjaan SPK di lapangan dan pengunggahan dokumentasi foto selesai diperbaiki',
        'description' => 'Modul lapangan bagi petugas teknis untuk memperbarui status SPK, mencatat pemakaian material, dan mengunggah foto hasil perbaikan.',
    ])->name('work-orders.index');
});

// =========================================================================
// 3. ROLE: WARGA PELAPOR (Public Citizen Portal)
// =========================================================================
Route::middleware(['auth', 'role:warga,admin'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wargaDashboard'])->name('dashboard');

    // SCRUM-6 & SCRUM-7: Pengajuan Laporan & Riwayat Laporan Aduan Warga
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');

    Route::view('/ratings', 'layouts.placeholder', [
        'title' => 'Penilaian & Ulasan Hasil Perbaikan Jalan',
        'subtitle' => 'Berikan penilaian bintang 1–5 dan ulasan atas tiket laporan yang telah selesai diperbaiki (Resolved)',
        'description' => 'Modul evaluasi kepuasan warga terhadap kualitas dan kecepatan tindak lanjut perbaikan jalan oleh Dinas PU.',
    ])->name('ratings.index');
});
// ==================================================
// Modul Rating & Evaluasi Warga (Naswa)
// ==================================================
Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
Route::get('/reports/{report}/rate', [RatingController::class, 'create'])->name('ratings.create');
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');