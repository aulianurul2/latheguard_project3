<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    protected $fillable = [
        'nama',
        'tipe',
        'lokasi',
        'tanggal_pasang',
        'total_jam_berbobot',
        'status_operasional',
        'last_seen_at',
    ];

    protected $casts = [
        'total_jam_berbobot' => 'float',
        'last_seen_at'       => 'datetime',
    ];

    public function komponens()
    {
        return $this->hasMany(Komponen::class);
    }

    public function dataSensors()
    {
        return $this->hasMany(DataSensor::class);
    }
}