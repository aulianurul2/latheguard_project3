<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataSensor;
use App\Models\Komponen;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'mesin_id'     => 'required|exists:mesins,id',
            'getaran_rms'  => 'required|numeric|min:0',
            'suhu'         => 'nullable|numeric|between:-20,300',
            'suara'        => 'nullable|numeric|min:0',
            'durasi_menit' => 'required|numeric|min:0.1|max:1440',
            'waktu'        => 'nullable|date',
        ]);

        // Status dan bobot ditentukan dari getaran
        // (angka ambang hanya awal; nanti dipindah ke tabel pengaturan)
        $getaran = (float) $data['getaran_rms'];

        if ($getaran < 0.05) {
            [$status, $beban, $bobot] = ['off', null, 0];
        } elseif ($getaran < 0.20) {
            [$status, $beban, $bobot] = ['idle', null, 0];
        } elseif ($getaran < 0.40) {
            [$status, $beban, $bobot] = ['active', 'ringan', 1.0];
        } elseif ($getaran < 0.70) {
            [$status, $beban, $bobot] = ['active', 'sedang', 1.5];
        } else {
            [$status, $beban, $bobot] = ['active', 'berat', 2.0];
        }

        $durasiJam   = $data['durasi_menit'] / 60;
        $jamBerbobot = round($durasiJam * $bobot, 4);

        $mesin = Mesin::findOrFail($data['mesin_id']);

        $sensor = DB::transaction(function () use ($data, $mesin, $status, $beban, $bobot, $jamBerbobot) {
            $baris = DataSensor::create([
                'mesin_id'     => $mesin->id,
                'getaran_rms'  => $data['getaran_rms'],
                'suhu'         => $data['suhu'] ?? null,
                'suara'        => $data['suara'] ?? null,
                'status'       => $status,
                'beban'        => $beban,
                'bobot'        => $bobot,
                'durasi_menit' => $data['durasi_menit'],
                'jam_berbobot' => $jamBerbobot,
                'waktu'        => $data['waktu'] ?? now(),
            ]);

            if ($jamBerbobot > 0) {
                $mesin->increment('total_jam_berbobot', $jamBerbobot);
                Komponen::where('mesin_id', $mesin->id)->increment('jam_terpakai', $jamBerbobot);
            }

            return $baris;
        });

        return response()->json([
            'message'   => 'Data sensor berhasil disimpan dan diproses',
            'data'      => $sensor,
            'komponens' => $mesin->komponens()->get(),
        ], 201);
    }

    public function index(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mesins,id',
            'dari'     => 'nullable|date',
            'sampai'   => 'nullable|date',
            'limit'    => 'nullable|integer|min:1|max:500',
        ]);

        $query = DataSensor::where('mesin_id', $request->mesin_id)->orderByDesc('waktu');

        if ($request->filled('dari')) {
            $query->where('waktu', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('waktu', '<=', $request->sampai);
        }

        return response()->json([
            'message' => 'Riwayat data sensor',
            'data'    => $query->limit($request->input('limit', 100))->get(),
        ]);
    }

    public function terkini(Mesin $mesin)
    {
        return response()->json([
            'message' => 'Data sensor terkini',
            'mesin'   => $mesin->only('id', 'nama', 'total_jam_berbobot'),
            'data'    => $mesin->dataSensors()->orderByDesc('waktu')->first(),
        ]);
    }
}