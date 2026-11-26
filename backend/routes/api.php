<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KomponenController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\KategoriController;

Route::post('/login', [AuthController::class, 'login']);

// Endpoint khusus perangkat IoT (ESP32)
Route::post('/sensor-data', [SensorController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Akses baca untuk Owner dan Teknisi
    Route::get('/komponens', [KomponenController::class, 'index']);
    Route::get('/komponens/{komponen}', [KomponenController::class, 'show']);
    Route::get('/kategoris', [KategoriController::class, 'index']);
    Route::get('/kategoris/{kategori}', [KategoriController::class, 'show']);
    Route::get('/sensor-data', [SensorController::class, 'index']);
    Route::get('/mesins/{mesin}/sensor-terkini', [SensorController::class, 'terkini']);

    // Akses kelola khusus Owner
    Route::middleware('role:owner')->group(function () {
        Route::apiResource('komponens', KomponenController::class)->except(['index', 'show']);
        Route::apiResource('kategoris', KategoriController::class)->except(['index', 'show']);
        Route::apiResource('users', UserController::class)->except(['show']);
    });
});