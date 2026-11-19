<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    protected $fillable = [
        'mesin_id', 'getaran_rms', 'suhu', 'suara', 'status', 'beban',
        'bobot', 'durasi_menit', 'jam_berbobot', 'waktu',
    ];

    protected $casts = [
        'getaran_rms'  => 'float',
        'suhu'         => 'float',
        'suara'        => 'float',
        'bobot'        => 'float',
        'durasi_menit' => 'float',
        'jam_berbobot' => 'float',
        'waktu'        => 'datetime',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}