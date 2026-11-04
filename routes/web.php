<?php

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

    // CRUD Pengguna & Role RBAC (Angel - SCRUM-106 & SCRUM-110)
    Route::resource('users', UserController::class);
});

// =========================================================================
// 2. ROLE: PETUGAS LAPANGAN (Responsive Field View)
// =========================================================================
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');
});

// =========================================================================
// 3. ROLE: WARGA PELAPOR (Public Citizen Portal)
// =========================================================================
Route::middleware(['auth', 'role:warga,admin'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wargaDashboard'])->name('dashboard');
});
