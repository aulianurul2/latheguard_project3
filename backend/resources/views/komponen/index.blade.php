@extends('layouts.app')

@section('title', 'Komponen')


@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Komponen Mesin</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        Tambah Komponen
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="alert-message">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="alert-message">
            @foreach ($errors->all() as $pesan)
                <div>{{ $pesan }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Komponen</th>
                        <th>Mesin</th>
                        <th>Kategori</th>
                        <th class="text-end">Interval (jam)</th>
                        <th style="min-width: 180px">Pemakaian</th>
                        <th class="text-end">Sisa (jam)</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($komponens as $k)
                        @php
                            $persen = $k->interval_jam > 0
                                ? min(100, round($k->jam_terpakai / $k->interval_jam * 100))
                                : 0;

                            if ($persen >= 100) {
                                $status = 'Terlambat'; $warna = 'danger';
                            } elseif ($persen >= 80) {
                                $status = 'Segera'; $warna = 'warning';
                            } else {
                                $status = 'Normal'; $warna = 'success';
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $k->nama }}</td>
                            <td>{{ $k->mesin->nama ?? '-' }}</td>
                            <td>{{ $k->kategori->nama ?? '-' }}</td>
                            <td class="text-end">{{ number_format($k->interval_jam, 0, ',', '.') }}</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $warna }}" style="width: {{ $persen }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format($k->jam_terpakai, 1, ',', '.') }} jam ({{ $persen }}%)
                                </small>
                            </td>
                            <td class="text-end">{{ number_format($k->sisa_jam, 1, ',', '.') }}</td>
                            <td><span class="badge bg-{{ $warna }}">{{ $status }}</span></td>
                            <td class="text-end">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary btn-edit"
                                        data-id="{{ $k->id }}"
                                        data-nama="{{ $k->nama }}"
                                        data-mesin="{{ $k->mesin_id }}"
                                        data-kategori="{{ $k->kategori_id }}"
                                        data-interval="{{ $k->interval_jam }}"
                                        data-terpakai="{{ $k->jam_terpakai }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalUbah">
                                    Ubah
                                </button>

                                <form action="{{ route('komponen.destroy', $k) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus komponen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada komponen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('komponen.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Komponen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama komponen</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mesin</label>
                    <select name="mesin_id" class="form-select" required>
                        @foreach ($mesins as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">- Tanpa kategori -</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interval servis (jam)</label>
                    <input type="number" name="interval_jam" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jam terpakai (opsional)</label>
                    <input type="number" name="jam_terpakai" class="form-control" min="0" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal ubah --}}
<div class="modal fade" id="modalUbah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formUbah" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Ubah Komponen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama komponen</label>
                    <input type="text" name="nama" id="ubahNama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mesin</label>
                    <select name="mesin_id" id="ubahMesin" class="form-select" required>
                        @foreach ($mesins as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" id="ubahKategori" class="form-select">
                        <option value="">- Tanpa kategori -</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interval servis (jam)</label>
                    <input type="number" name="interval_jam" id="ubahInterval" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jam terpakai</label>
                    <input type="number" name="jam_terpakai" id="ubahTerpakai" class="form-control" min="0" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('formUbah').action = "{{ url('komponen') }}/" + d.id;
            document.getElementById('ubahNama').value = d.nama;
            document.getElementById('ubahMesin').value = d.mesin;
            document.getElementById('ubahKategori').value = d.kategori || '';
            document.getElementById('ubahInterval').value = d.interval;
            document.getElementById('ubahTerpakai').value = d.terpakai;
        });
    });
</script>
@endsection