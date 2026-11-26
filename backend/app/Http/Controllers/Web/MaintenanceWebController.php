<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Komponen;
use App\Models\RiwayatMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSensor;
use App\Models\Mesin;

class MaintenanceWebController extends Controller
{
    public function jadwal(Request $request)
    {
        $filterStatus = $request->get('status', 'perlu_perhatian'); // perlu_perhatian | semua | terlambat | mendekati

        $komponens = Komponen::with(['mesin', 'kategori'])
            ->get()
            ->map(function ($k) {
                $interval = (float) ($k->interval_jam ?? 0);
                $terpakai = (float) ($k->jam_terpakai ?? 0);
                $persen   = $interval > 0 ? round(($terpakai / $interval) * 100, 1) : 0;
                $sisaJam  = round(max(0, $interval - $terpakai), 1);

                if ($persen >= 100) {
                    $statusJadwal = 'terlambat';
                } elseif ($persen >= 80) {
                    $statusJadwal = 'mendekati';
                } else {
                    $statusJadwal = 'normal';
                }

                $k->persen_jadwal = $persen;
                $k->sisa_jam_jadwal = $sisaJam;
                $k->status_jadwal = $statusJadwal;

                return $k;
            });

        $ringkasan = [
            'total'     => $komponens->count(),
            'normal'    => $komponens->where('status_jadwal', 'normal')->count(),
            'mendekati' => $komponens->where('status_jadwal', 'mendekati')->count(),
            'terlambat' => $komponens->where('status_jadwal', 'terlambat')->count(),
        ];

        $daftarTampil = match ($filterStatus) {
            'terlambat' => $komponens->where('status_jadwal', 'terlambat'),
            'mendekati' => $komponens->where('status_jadwal', 'mendekati'),
            'semua'     => $komponens,
            default     => $komponens->filter(fn ($k) => $k->persen_jadwal >= 80),
        };

        return view('maintenance.jadwal', [
            'komponens'     => $daftarTampil->sortByDesc('persen_jadwal')->values(),
            'semuaKomponen' => $komponens->sortByDesc('persen_jadwal')->values(),
            'ringkasan'     => $ringkasan,
            'filterStatus'  => $filterStatus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'komponen_id'    => 'required|exists:komponens,id',
            'jenis'          => 'required|in:servis,perawatan,kerusakan',
            'tanggal_servis' => 'nullable|date',
            'catatan'        => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $komponen = Komponen::lockForUpdate()->findOrFail($validated['komponen_id']);

            RiwayatMaintenance::create([
                'komponen_id'     => $komponen->id,
                'mesin_id'        => $komponen->mesin_id,
                'user_id'         => auth()->id(),
                'jenis'           => $validated['jenis'],
                'jam_saat_servis' => (float) ($komponen->jam_terpakai ?? 0),
                'catatan'         => $validated['catatan'] ?? null,
                'tanggal_servis'  => $validated['tanggal_servis'] ?? now(),
            ]);

            $komponen->update([
                'jam_terpakai' => 0,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Servis berhasil dicatat dan jam terpakai komponen telah di-reset menjadi 0.');
    }

    public function riwayat()
    {
        $riwayats = RiwayatMaintenance::with(['komponen', 'mesin', 'user'])
            ->orderByDesc('tanggal_servis')
            ->orderByDesc('id')
            ->paginate(15);

        return view('maintenance.riwayat', compact('riwayats'));
    }

    public function dataSensor(Request $request)
    {
        $mesins        = Mesin::orderBy('nama')->get();
        $mesinId       = $request->get('mesin_id');
        $dariTanggal   = $request->get('dari');
        $sampaiTanggal = $request->get('sampai');

        $query = DataSensor::with('mesin')->orderByDesc('waktu')->orderByDesc('id');

        if ($mesinId) {
            $query->where('mesin_id', $mesinId);
        }

        if ($dariTanggal) {
            $query->whereDate('waktu', '>=', $dariTanggal);
        }

        if ($sampaiTanggal) {
            $query->whereDate('waktu', '<=', $sampaiTanggal);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('data-sensor', [
            'mesins'        => $mesins,
            'logs'          => $logs,
            'mesinId'       => $mesinId,
            'dariTanggal'   => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
        ]);
    }
}