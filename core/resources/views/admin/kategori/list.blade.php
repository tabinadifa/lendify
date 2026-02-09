@extends('layouts.layout')

@section('title', 'Kategori Alat - Lendify')

@push('styles')
    <style>
        .badge-kategori {
            background-color: #E3F2FD;
            color: #0D47A1;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <h2 class="fw-bold mb-0">Kategori Alat</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            {{-- Alert --}}

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Controls -->
            <form method="GET" class="row g-2 mb-3 align-items-center mt-2">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                        data-bs-target="#modalTambahKategori">
                        Tambah Kategori
                    </button>

                </div>

                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach ([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 ms-auto">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama kategori..." onkeydown="if(event.key==='Enter'){this.form.submit()}">
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoriAlats as $kategori)
                            <tr>
                                <td>{{ $kategoriAlats->firstItem() + $loop->index }}</td>
                                <td> {{ $kategori->nama_kategori }} </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit"
                                            data-bs-toggle="modal" data-bs-target="#modalEditKategori"
                                            data-id="{{ $kategori->id }}" data-nama="{{ $kategori->nama_kategori }}">
                                            Edit
                                        </button>

                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                                            class="form-hapus" data-title="Yakin ingin menghapus?"
                                            data-text="Data kategori ini akan dihapus secara permanen.">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Data kategori tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $kategoriAlats->firstItem() }} –
                    {{ $kategoriAlats->lastItem() }} dari
                    {{ $kategoriAlats->total() }} data
                </small>

                {{ $kategoriAlats->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalTambahKategori" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form method="POST" action="{{ route('kategori.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" required
                                placeholder="Contoh: Elektronik">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditKategori" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form method="POST" id="formEditKategori">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="editNamaKategori" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditKategori');
            const formEdit = document.getElementById('formEditKategori');
            const inputNama = document.getElementById('editNamaKategori');

            const updateUrlTemplate = "{{ route('kategori.update', ':id') }}";

            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');

                inputNama.value = nama;

                formEdit.action = updateUrlTemplate.replace(':id', id);
            });
        });
    </script>

@endsection
