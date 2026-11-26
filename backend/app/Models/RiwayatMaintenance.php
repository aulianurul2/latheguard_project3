<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatMaintenance extends Model
{
    protected $fillable = [
        'komponen_id',
        'mesin_id',
        'user_id',
        'jenis',
        'jam_saat_servis',
        'catatan',
        'tanggal_servis',
    ];

    protected $casts = [
        'jam_saat_servis' => 'float',
        'tanggal_servis'  => 'datetime',
    ];

    public function komponen(): BelongsTo
    {
        return $this->belongsTo(Komponen::class);
    }

    public function mesin(): BelongsTo
    {
        return $this->belongsTo(Mesin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}