<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('data_sensors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mesin_id')->constrained('mesins')->cascadeOnDelete();
        $table->decimal('getaran_rms', 8, 4);
        $table->decimal('suhu', 6, 2)->nullable();
        $table->decimal('suara', 8, 2)->nullable();
        $table->string('status');                       // off, idle, active
        $table->string('beban')->nullable();            // ringan, sedang, berat
        $table->decimal('bobot', 4, 2)->default(0);
        $table->decimal('durasi_menit', 8, 2);
        $table->decimal('jam_berbobot', 10, 4)->default(0);
        $table->timestamp('waktu')->useCurrent();
        $table->timestamps();

        $table->index(['mesin_id', 'waktu']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_sensors');
    }
};
