<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KomponenController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\KategoriController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Owner dan teknisi boleh melihat
    Route::get('/komponens', [KomponenController::class, 'index']);
    Route::get('/komponens/{komponen}', [KomponenController::class, 'show']);

    // Khusus owner
    Route::middleware('role:owner')->group(function () {
        Route::post('/komponens', [KomponenController::class, 'store']);
        Route::put('/komponens/{komponen}', [KomponenController::class, 'update']);
        Route::delete('/komponens/{komponen}', [KomponenController::class, 'destroy']);
        Route::post('/sensor-data', [SensorController::class, 'store']);


        Route::middleware('role:owner')->group(function () {
            Route::post('/kategoris', [KategoriController::class, 'store']);
            Route::put('/kategoris/{kategori}', [KategoriController::class, 'update']);
            Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy']);
        });
        // Baca data sensor (owner dan teknisi)
        Route::get('/sensor-data', [SensorController::class, 'index']);
        Route::get('/mesins/{mesin}/sensor-terkini', [SensorController::class, 'terkini']);
    });

    // Kelola kategori
        Route::get('/kategoris', [KategoriController::class, 'index']);
        Route::get('/kategoris/{kategori}', [KategoriController::class, 'show']);

    // Kelola pengguna (khusus owner)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});