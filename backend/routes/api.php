<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KomponenController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\KategoriController;
// Import controller tambahan sesuai SRS LatheGuard-IIoT
use App\Http\Controllers\Api\MesinController;
use App\Http\Controllers\Api\AnomaliController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\JadwalPerawatanController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\KonfigurasiController;
use App\Http\Controllers\Api\LaporanController;

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

    // =========================================================================
    // TAMBAHAN ROUTE SESUAI KEBUTUHAN SRS LATHEGUARD-IIOT
    // =========================================================================

    // 1. Monitoring Mesin Bubut & Mesin Pendukung (Owner & Teknisi - KF-02, KF-03, KF-04, KF-07, KF-09)
    Route::get('/mesins', [MesinController::class, 'index']); // Daftar semua mesin (kartu Beranda)
    Route::get('/mesins/{mesin}', [MesinController::class, 'show']); // Detail mesin, JKB & progres komponen
    Route::get('/mesins/{mesin}/grafik-sensor', [SensorController::class, 'grafik']); // Grafik tren 1 jam, 24 jam, 7 hari (KF-03)
    Route::get('/mesins/{mesin}/anomalis', [AnomaliController::class, 'index']); // Tingkat kondisi, anomali & saran pemeriksaan (KF-09, KF-10)

    // 2. Pencatatan Servis, Perawatan & Kerusakan + Riwayat (Owner & Teknisi - KF-12, KF-13)
    Route::post('/riwayats', [RiwayatController::class, 'store']); // Catat tindakan servis (reset progres) atau kerusakan
    Route::get('/riwayats', [RiwayatController::class, 'index']); // Riwayat kronologis (filter mesin, jenis, periode)

    // 3. Tugas Perawatan Manual Mesin Pendukung (Owner & Teknisi - KF-16, KF-17)
    Route::get('/tugas-perawatan', [JadwalPerawatanController::class, 'index']); // Daftar tugas & penanda terlambat
    Route::post('/tugas-perawatan/{jadwal}/selesai', [JadwalPerawatanController::class, 'catatPelaksanaan']); // Catat pelaksanaan & hitung jatuh tempo berikutnya

    // 4. Notifikasi Peringatan Servis & Anomali (Owner & Teknisi - KF-11)
    Route::get('/notifikasis', [NotifikasiController::class, 'index']);
    Route::put('/notifikasis/{notifikasi}/baca', [NotifikasiController::class, 'tandaiDibaca']);

    // 5. Fitur Khusus Owner (KF-05, KF-08, KF-14, KF-15, KF-18)
    Route::middleware('role:owner')->group(function () {
        // Kelola Data Mesin & Perangkat IoT / API Key (KF-14, UC03, UC04)
        Route::post('/mesins', [MesinController::class, 'store']);
        Route::put('/mesins/{mesin}', [MesinController::class, 'update']);
        Route::delete('/mesins/{mesin}', [MesinController::class, 'destroy']);
        Route::post('/mesins/{mesin}/regenerate-key', [MesinController::class, 'regenerateApiKey']);

        // Atur Ambang Status, Bobot Beban & Pembelajaran Baseline (KF-05, KF-08, UC05)
        Route::get('/mesins/{mesin}/konfigurasi', [KonfigurasiController::class, 'show']);
        Route::put('/mesins/{mesin}/konfigurasi', [KonfigurasiController::class, 'update']);
        Route::post('/mesins/{mesin}/baseline/mulai', [KonfigurasiController::class, 'mulaiBaseline']);
        Route::post('/mesins/{mesin}/baseline/tutup', [KonfigurasiController::class, 'tutupBaseline']);

        // Kelola Jadwal Perawatan Rutin Mesin Pendukung (KF-15, UC21)
        Route::post('/jadwal-perawatan', [JadwalPerawatanController::class, 'store']);
        Route::put('/jadwal-perawatan/{jadwal}', [JadwalPerawatanController::class, 'update']);
        Route::patch('/jadwal-perawatan/{jadwal}/nonaktifkan', [JadwalPerawatanController::class, 'nonaktifkan']);

        // Laporan Ringkas Owner (KF-18, UC15)
        Route::get('/laporan-ringkas', [LaporanController::class, 'index']);
    });
});