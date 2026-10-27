<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Karyawan;
use App\Http\Controllers\Pemohon;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/jadwal-kunjungan', [HomeController::class, 'jadwalKunjungan'])
    ->name('jadwal-kunjungan');

Route::get('/struktur-karyawan', [HomeController::class, 'strukturKaryawan'])
    ->name('struktur-karyawan');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/daftar', [AuthController::class, 'showRegister'])
        ->name('daftar');

    Route::post('/daftar', [AuthController::class, 'register'])
        ->name('daftar.store');
});


Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:pemohon'])
    ->prefix('pemohon')
    ->name('pemohon.')
    ->group(function () {
        Route::get('/dashboard', [Pemohon\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('kunjungan', Pemohon\KunjunganController::class)
            ->parameters(['kunjungan' => 'kunjungan'])
            ->except(['show']);

        Route::get('/histori', [Pemohon\HistoriController::class, 'index'])
            ->name('histori.index');
    });


Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('karyawan', Admin\KaryawanController::class)
            ->parameters(['karyawan' => 'karyawan'])
            ->except(['show']);

        Route::resource('kunjungan', Admin\KunjunganController::class)
            ->parameters(['kunjungan' => 'kunjungan'])
            ->only(['index', 'edit', 'update']);

        Route::resource('setting-kunjungan', Admin\SettingKunjunganController::class)
            ->parameters(['setting-kunjungan' => 'setting_kunjungan'])
            ->except(['show']);

        Route::resource('kegiatan', Admin\KegiatanController::class)
            ->parameters(['kegiatan' => 'kegiatan'])
            ->except(['show']);
    });


Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', [Karyawan\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('kunjungan', Karyawan\KunjunganController::class)
            ->parameters(['kunjungan' => 'kunjungan'])
            ->only(['index', 'edit', 'update']);

        Route::get('/setting', [Karyawan\SettingController::class, 'index'])
            ->name('setting.index');

        Route::resource('kegiatan', Karyawan\KegiatanController::class)
            ->parameters(['kegiatan' => 'kegiatan'])
            ->except(['show']);
    });
