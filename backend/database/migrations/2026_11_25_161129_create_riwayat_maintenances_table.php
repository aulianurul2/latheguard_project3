<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komponen_id')->constrained('komponens')->cascadeOnDelete();
            $table->foreignId('mesin_id')->constrained('mesins')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis', 30)->default('servis'); // servis | perawatan | kerusakan
            $table->decimal('jam_saat_servis', 10, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_servis')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_maintenances');
    }
};