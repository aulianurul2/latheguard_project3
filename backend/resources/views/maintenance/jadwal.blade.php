@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Jadwal Maintenance</h4>
            <small class="text-muted">Komponen dengan progres &ge; 80% dari interval jam kerja berbobot</small>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('jadwal', ['status' => 'perlu_perhatian']) }}"
               class="btn {{ $filterStatus === 'perlu_perhatian' ? 'btn-primary' : 'btn-outline-primary' }}">
                Perlu Perhatian (&ge; 80%)
            </a>
            <a href="{{ route('jadwal', ['status' => 'mendekati']) }}"
               class="btn {{ $filterStatus === 'mendekati' ? 'btn-warning' : 'btn-outline-warning' }}">
                Mendekati ({{ $ringkasan['mendekati'] }})
            </a>
            <a href="{{ route('jadwal', ['status' => 'terlambat']) }}"
               class="btn {{ $filterStatus === 'terlambat' ? 'btn-danger' : 'btn-outline-danger' }}">
                Terlambat ({{ $ringkasan['terlambat'] }})
            </a>
            <a href="{{ route('jadwal', ['status' => 'semua']) }}"
               class="btn {{ $filterStatus === 'semua' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Semua ({{ $ringkasan['total'] }})
            </a>
        </div>
    </div>



    <div class="row g-4">
        {{-- Tabel Jadwal Komponen --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th>Mesin</th>
                                    <th>Interval</th>
                                    <th>Pemakaian</th>
                                    <th>Sisa Jam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($komponens as $k)
                                    @php
                                        $badgeClass = match($k->status_jadwal) {
                                            'terlambat' => 'bg-danger',
                                            'mendekati' => 'bg-warning text-dark',
                                            default     => 'bg-success',
                                        };
                                        $labelStatus = match($k->status_jadwal) {
                                            'terlambat' => 'Terlambat',
                                            'mendekati' => 'Mendekati',
                                            default     => 'Normal',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $k->nama }}</td>
                                        <td>{{ $k->mesin->nama ?? '-' }}</td>
                                        <td>{{ number_format($k->interval_jam, 0, ',', '.') }} jam</td>
                                        <td style="min-width: 160px;">
                                            <div class="progress mb-1" style="height: 6px;">
                                                <div class="progress-bar {{ $badgeClass }}"
                                                     style="width: {{ min(100, $k->persen_jadwal) }}%;"></div>
                                            </div>
                                            <small class="text-muted">
                                                {{ number_format($k->jam_terpakai, 1, ',', '.') }} jam ({{ $k->persen_jadwal }}%)
                                            </small>
                                        </td>
                                        <td>{{ number_format($k->sisa_jam_jadwal, 1, ',', '.') }} jam</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $labelStatus }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada komponen pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pencatatan Servis & Reset Jam --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-1">Catat Servis / Pemeliharaan</h5>
                    <small class="text-muted d-block mb-3">
                        Menyimpan servis akan otomatis me-reset <code>jam_terpakai</code> komponen menjadi <strong>0</strong>.
                    </small>

                    <form action="{{ route('maintenance.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Komponen</label>
                            <select name="komponen_id" class="form-select" required>
                                <option value="">-- Pilih Komponen --</option>
                                @foreach ($semuaKomponen as $item)
                                    <option value="{{ $item->id }}" {{ old('komponen_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }} ({{ $item->mesin->nama ?? 'Mesin' }}) — {{ number_format($item->jam_terpakai, 1, ',', '.') }}/{{ $item->interval_jam }} jam
                                    </option>
                                @endforeach
                            </select>
                            @error('komponen_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Tindakan</label>
                            <select name="jenis" class="form-select" required>
                                <option value="servis" {{ old('jenis') === 'servis' ? 'selected' : '' }}>Servis / Penggantian</option>
                                <option value="perawatan" {{ old('jenis') === 'perawatan' ? 'selected' : '' }}>Perawatan Rutin</option>
                                <option value="kerusakan" {{ old('jenis') === 'kerusakan' ? 'selected' : '' }}>Perbaikan Kerusakan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Servis</label>
                            <input type="datetime-local" name="tanggal_servis" class="form-control"
                                   value="{{ old('tanggal_servis', now()->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" rows="3" class="form-control"
                                      placeholder="Contoh: Ganti oli pelumas & cek kekencangan belt">{{ old('catatan') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            Simpan & Reset Jam Komponen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection