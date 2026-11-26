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
        // 1. Sesuaikan penentuan bobot dengan kombinasi getaran_rms & suhu
        $getaran = (float) $data['getaran_rms'];
        $suhu    = isset($data['suhu']) ? (float) $data['suhu'] : 0;

        if ($getaran < 0.05) {
            [$status, $beban, $bobot] = ['off', null, 0];
        } elseif ($getaran < 0.20) {
            [$status, $beban, $bobot] = ['idle', null, 0];
        } elseif ($getaran >= 0.70 || $suhu >= 65.0) {
            [$status, $beban, $bobot] = ['active', 'berat', 2.0];
        } elseif ($getaran >= 0.40 || $suhu >= 50.0) {
            [$status, $beban, $bobot] = ['active', 'sedang', 1.5];
        } else {
            [$status, $beban, $bobot] = ['active', 'ringan', 1.0];
        }
        $durasiJam   = $data['durasi_menit'] / 60;
        $jamBerbobot = round($durasiJam * $bobot, 4);

        $mesin = Mesin::findOrFail($data['mesin_id']);

        // Cari baris ini di SensorController.php (sekitar baris 47) dan tambahkan $durasiJam:
        $sensor = DB::transaction(function () use ($data, $mesin, $status, $beban, $bobot, $durasiJam, $jamBerbobot) {
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

                // Tambahan: Catat ke rekap harian (untuk laporan Harian & Mingguan)
                $tanggal = \Carbon\Carbon::parse($data['waktu'] ?? now())->toDateString();
                DB::table('rekap_jam_mesins')->updateOrInsert(
                    ['mesin_id' => $mesin->id, 'tanggal' => $tanggal],
                    [
                        'jam_aktual_active' => DB::raw("COALESCE(jam_aktual_active, 0) + {$durasiJam}"),
                        'jam_berbobot'      => DB::raw("COALESCE(jam_berbobot, 0) + {$jamBerbobot}"),
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ]
                );
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
    $hariIni     = Carbon::today()->toDateString();
    $awalMinggu  = Carbon::now()->startOfWeek()->toDateString();
    $akhirMinggu = Carbon::now()->endOfWeek()->toDateString();

    $rekapHariIni = DB::table('rekap_jam_mesins')
        ->where('mesin_id', $mesin->id)
        ->where('tanggal', $hariIni)
        ->first();

    $rekapMingguIni = DB::table('rekap_jam_mesins')
        ->where('mesin_id', $mesin->id)
        ->whereBetween('tanggal', [$awalMinggu, $akhirMinggu])
        ->selectRaw('
            COALESCE(SUM(jam_aktual_active), 0) as total_aktual,
            COALESCE(SUM(jam_berbobot), 0) as total_berbobot
        ')
        ->first();

    $latest = $mesin->dataSensors()->orderByDesc('waktu')->orderByDesc('id')->first();

    return response()->json([
        'message' => 'Data sensor terkini',
        'mesin'   => $mesin->only('id', 'nama', 'total_jam_berbobot'),
        'data'    => $latest,
        'rekap'   => [
            'harian_berbobot'   => round((float) ($rekapHariIni->jam_berbobot ?? 0), 4),
            'harian_aktual'     => round((float) ($rekapHariIni->jam_aktual_active ?? 0), 4),
            'mingguan_berbobot' => round((float) ($rekapMingguIni->total_berbobot ?? 0), 4),
            'mingguan_aktual'   => round((float) ($rekapMingguIni->total_aktual ?? 0), 4),
            'total_berbobot'    => round((float) ($mesin->total_jam_berbobot ?? 0), 4),
        ],
    ]);
}
}