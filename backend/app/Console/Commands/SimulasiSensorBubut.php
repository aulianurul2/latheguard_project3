<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\SensorController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SimulasiSensorBubut extends Command
{
    protected $signature = 'iot:simulasi {mesin_id=1} {--mode=random : off, idle, ringan, sedang, berat, random}';
    protected $description = 'Simulasi kirim data sensor ke SensorController';

    public function handle(SensorController $controller)
    {
        $mesinId = (int) $this->argument('mesin_id');
        $mode = $this->option('mode');

        $this->info("Simulasi sensor berjalan untuk Mesin #{$mesinId} (Mode: {$mode}). Tekan Ctrl+C untuk stop.");

        while (true) {
            [$getaran, $suhu, $suara] = $this->generate($mode);

            // Simulasi kirim per 1 menit operasi agar progres jam cepat terlihat
            $request = new Request([
                'mesin_id'     => $mesinId,
                'getaran_rms'  => $getaran,
                'suhu'         => $suhu,
                'suara'        => $suara,
                'durasi_menit' => 1.0,
                'waktu'        => now()->toDateTimeString(),
            ]);

            $response = $controller->store($request);
            $resData = $response->getData(true)['data'];

            $this->line(sprintf(
                "[%s] Status: %-6s | Getaran: %.3f g | Suhu: %.1f°C | Beban: %-6s (x%.1f) | +%.4f jam",
                now()->format('H:i:s'),
                $resData['status'],
                $getaran,
                $suhu,
                $resData['beban'] ?? '-',
                $resData['bobot'],
                $resData['jam_berbobot']
            ));

            sleep(3);
        }
    }

    private function generate(string $mode): array
    {
        if ($mode === 'random') {
            $modes = ['idle', 'ringan', 'sedang', 'berat'];
            $mode = $modes[array_rand($modes)];
        }

        return match ($mode) {
            'off'    => [rand(1, 4) / 100, rand(260, 290) / 10, rand(30, 45)],
            'idle'   => [rand(6, 18) / 100, rand(320, 360) / 10, rand(50, 65)],
            'ringan' => [rand(21, 38) / 100, rand(380, 480) / 10, rand(68, 78)],
            'sedang' => [rand(41, 68) / 100, rand(510, 620) / 10, rand(79, 88)],
            'berat'  => [rand(72, 95) / 100, rand(660, 780) / 10, rand(89, 105)],
            default  => [0.30, 42.5, 72.0],
        };
    }
}