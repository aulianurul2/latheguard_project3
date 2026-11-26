<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan presisi jam_terpakai di komponens mendukung desimal mikro (4 angka di belakang koma)
        Schema::table('komponens', function (Blueprint $table) {
            $table->decimal('jam_terpakai', 10, 4)->default(0)->change();
        });

        // 2. Tambah status real-time & koneksi terakhir di tabel mesins
        Schema::table('mesins', function (Blueprint $table) {
            if (!Schema::hasColumn('mesins', 'status_operasional')) {
                $table->string('status_operasional')->default('Off');
            }
            if (!Schema::hasColumn('mesins', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable();
            }
        });

        // 3. Tabel Rekap Jam Harian (untuk rekap Harian, Mingguan, Total)
        Schema::create('rekap_jam_mesins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesins')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('jam_aktual_active', 10, 4)->default(0);
            $table->decimal('jam_berbobot', 10, 4)->default(0);
            $table->timestamps();

            $table->unique(['mesin_id', 'tanggal']);
        });

        // 4. Tabel Riwayat Maintenance (Servis, Perawatan, Kerusakan oleh Owner/Teknisi)
        Schema::create('riwayat_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesins')->onDelete('cascade');
            $table->foreignId('komponen_id')->nullable()->constrained('komponens')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis', ['Servis Preventif', 'Perawatan Rutin', 'Perbaikan Kerusakan']);
            $table->decimal('jam_saat_servis', 10, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->boolean('reset_jam_komponen')->default(true);
            $table->date('tanggal_maintenance');
            $table->timestamps();
        });

        // 5. Tabel Hasil Analisis AI & Notifikasi (Anomali & Peringatan Servis)
        Schema::create('log_anomali_notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesins')->onDelete('cascade');
            $table->foreignId('komponen_id')->nullable()->constrained('komponens')->onDelete('cascade');
            $table->enum('tipe', ['Anomali Sensor', 'Peringatan Servis']);
            $table->enum('tingkat_kondisi', ['Normal', 'Waspada', 'Kritis'])->default('Waspada');
            $table->float('anomaly_score')->nullable();
            $table->string('judul');
            $table->text('rekomendasi');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_anomali_notifikasis');
        Schema::dropIfExists('riwayat_maintenances');
        Schema::dropIfExists('rekap_jam_mesins');
    }
};