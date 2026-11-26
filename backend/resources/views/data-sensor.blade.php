@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Data Sensor</h4>
        <small class="text-muted">Riwayat pembacaan sensor mesin dengan filter mesin dan tanggal</small>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('data-sensor') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mesin</label>
                    <select name="mesin_id" class="form-select">
                        <option value="">-- Semua Mesin --</option>
                        @foreach ($mesins as $m)
                            <option value="{{ $m->id }}" {{ (string) $mesinId === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="{{ $dariTanggal }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="{{ $sampaiTanggal }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('data-sensor') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Mesin</th>
                            <th>Getaran RMS</th>
                            <th>Suhu (&deg;C)</th>
                            <th>Suara</th>
                            <th>Status</th>
                            <th>Beban</th>
                            <th>Bobot</th>
                            <th>Jam Berbobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $waktuLog = $log->waktu ?? $log->created_at;
                            @endphp
                            <tr>
                                <td>{{ $waktuLog ? \Carbon\Carbon::parse($waktuLog)->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ $log->mesin->nama ?? '-' }}</td>
                                <td>{{ $log->getaran_rms !== null ? number_format((float) $log->getaran_rms, 2, ',', '.') : '-' }}</td>
                                <td>{{ $log->suhu !== null ? number_format((float) $log->suhu, 1, ',', '.') . ' °C' : '-' }}</td>
                                <td>{{ $log->suara !== null ? number_format((float) $log->suara, 1, ',', '.') : '-' }}</td>
                                <td>
                                    <span class="badge {{ ($log->status ?? '') === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $log->status ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $log->beban ?? '-' }}</td>
                                <td>{{ $log->bobot !== null ? number_format((float) $log->bobot, 1, ',', '.') : '-' }}</td>
                                <td>{{ $log->jam_berbobot !== null ? number_format((float) $log->jam_berbobot, 4, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data sensor yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection