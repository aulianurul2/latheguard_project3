<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    protected $fillable = ['nama', 'tipe', 'lokasi', 'tanggal_pasang', 'total_jam_berbobot'];

    public function komponens()
    {
        return $this->hasMany(Komponen::class);
    }
    public function dataSensors()
    {
    return $this->hasMany(DataSensor::class);
    }
}