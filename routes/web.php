<?php

use App\Http\Controllers\Admin\ReportVerificationController;
use App\Http\Controllers\FieldWorkOrderController;
use App\Http\Controllers\Admin\WorkOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    // CRUD Pengguna & Role RBAC
    Route::resource('users', UserController::class);

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

    Route::view('/categories', 'layouts.placeholder', [
        'title' => 'Master Data Kategori Kerusakan Jalan',
        'subtitle' => 'Pengelolaan klasifikasi jenis kerusakan infrastruktur jalan dan bobot tingkat keparahan (Severity Level)',
        'description' => 'Modul manajemen daftar kategori kerusakan jalan beserta indeks tingkat keparahan (skala 1 hingga 5).',
    ])->name('categories.index');

    Route::view('/districts', 'layouts.placeholder', [
        'title' => 'Master Data Wilayah & Zonasi Kecamatan',
        'subtitle' => 'Pengelolaan daftar wilayah administratif kecamatan dan kode kota operasional Dinas PU',
        'description' => 'Modul manajemen data master wilayah kecamatan untuk pengelompokan lokasi laporan dan penugasan zona petugas.',
    ])->name('districts.index');

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
    Route::patch('/work-orders/{workOrder}/status', [DashboardController::class, 'updateWorkOrderStatus'])->name('work-orders.update-status');
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

    Route::view('/reports/create', 'layouts.placeholder', [
        'title' => 'Buat Laporan Kerusakan Jalan Baru',
        'subtitle' => 'Unggah foto kerusakan jalan, penguncian koordinat GPS otomatis, dan validasi citra berbasis AI',
        'description' => 'Modul formulir pengajuan laporan kerusakan jalan oleh warga lengkap dengan pengambilan titik koordinat lokasi dan pemeriksaan AI.',
    ])->name('reports.create');

    Route::view('/ratings', 'layouts.placeholder', [
        'title' => 'Penilaian & Ulasan Hasil Perbaikan Jalan',
        'subtitle' => 'Berikan penilaian bintang 1–5 dan ulasan atas tiket laporan yang telah selesai diperbaiki (Resolved)',
        'description' => 'Modul evaluasi kepuasan warga terhadap kualitas dan kecepatan tindak lanjut perbaikan jalan oleh Dinas PU.',
    ])->name('ratings.index');
});
