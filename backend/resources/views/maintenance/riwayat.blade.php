@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Riwayat Maintenance</h4>
            <small class="text-muted">Catatan servis, perawatan, dan perbaikan yang telah dilakukan</small>
        </div>
        <a href="{{ route('jadwal') }}" class="btn btn-outline-primary btn-sm">
            Ke Jadwal Maintenance
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Mesin</th>
                            <th>Komponen</th>
                            <th>Jenis</th>
                            <th>Jam Saat Servis</th>
                            <th>Dicatat Oleh</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayats as $r)
                            <tr>
                                <td>{{ $r->tanggal_servis?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>{{ $r->mesin->nama ?? '-' }}</td>
                                <td class="fw-semibold">{{ $r->komponen->nama ?? '-' }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $r->jenis }}</span></td>
                                <td>{{ number_format($r->jam_saat_servis, 1, ',', '.') }} jam</td>
                                <td>{{ $r->user->name ?? '-' }}</td>
                                <td>{{ $r->catatan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada riwayat maintenance tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayats->links() }}
            </div>
        </div>
    </div>
</div>
@endsection