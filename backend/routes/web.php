<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\KategoriWebController;
use App\Http\Controllers\Web\KomponenWebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Web\MaintenanceWebController;

Route::get('/', fn () => redirect('/dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login']);
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    Route::resource('kategori', KategoriWebController::class)
        ->except(['create', 'show', 'edit']);
    
    Route::resource('komponen', KomponenWebController::class)
    ->parameters(['komponen' => 'komponen'])
    ->except(['create', 'show', 'edit']);

    Route::get('/mesins/{mesin}/sensor-terkini', [SensorController::class, 'terkini'])->name('mesin.sensor.terkini');

    Route::get('/jadwal', [MaintenanceWebController::class, 'jadwal'])->name('jadwal');
    Route::get('/jadwal-maintenance', [MaintenanceWebController::class, 'jadwal'])->name('maintenance.jadwal');
    Route::post('/maintenance', [MaintenanceWebController::class, 'store'])->name('maintenance.store');
    Route::get('/riwayat', [MaintenanceWebController::class, 'riwayat'])->name('maintenance.riwayat');
    Route::get('/sensor', [MaintenanceWebController::class, 'dataSensor'])->name('data-sensor');
});