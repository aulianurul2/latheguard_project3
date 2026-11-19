<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    protected $fillable = ['mesin_id', 'kategori_id', 'nama', 'interval_jam', 'jam_terpakai'];

    protected $appends = ['sisa_jam'];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function getSisaJamAttribute()
    {
        return max(0, $this->interval_jam - $this->jam_terpakai);
    }
}