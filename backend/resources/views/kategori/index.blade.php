@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Kategori Komponen</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        Tambah Kategori
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="alert-message">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="alert-message">{{ session('error') }}</div>
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
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Komponen</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoris as $k)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $k->nama }}</td>
                            <td>{{ $k->deskripsi ?? '-' }}</td>
                            <td class="text-center">{{ $k->komponens_count }}</td>
                            <td class="text-end">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary btn-edit"
                                        data-id="{{ $k->id }}"
                                        data-nama="{{ $k->nama }}"
                                        data-deskripsi="{{ $k->deskripsi }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalUbah">
                                    Ubah
                                </button>

                                <form action="{{ route('kategori.destroy', $k) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada kategori.</td>
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
        <form action="{{ route('kategori.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
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
                <h5 class="modal-title">Ubah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" id="ubahNama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="ubahDeskripsi" class="form-control" rows="3"></textarea>
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
            document.getElementById('formUbah').action = "{{ url('kategori') }}/" + this.dataset.id;
            document.getElementById('ubahNama').value = this.dataset.nama;
            document.getElementById('ubahDeskripsi').value = this.dataset.deskripsi || '';
        });
    });
</script>
@endsection