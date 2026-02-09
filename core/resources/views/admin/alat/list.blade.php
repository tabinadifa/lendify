@extends('layouts.layout')

@section('title', 'Daftar Alat - Lendify')

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
        <h2 class="fw-bold mb-0">Daftar Alat</h2>
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
                    <a href="{{ route('alat.create') }}" class="btn btn-success w-100">
                        Tambah Alat
                    </a>
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
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Cari nama alat, kategori..."
                           onkeydown="if(event.key==='Enter'){this.form.submit()}">
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alats as $alat)
                            <tr>
                                <td>{{ $alats->firstItem() + $loop->index }}</td>
                                <td>{{ $alat->nama_alat }}</td>
                                <td>
                                    <span class="badge badge-kategori">
                                        {{ $alat->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $alat->jumlah_stok }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('alat.edit', $alat->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('alat.destroy', $alat->id) }}"
                                              method="POST"
                                              class="form-hapus"
                                              data-title="Yakin ingin menghapus?"
                                              data-text="Data alat ini akan dihapus secara permanen.">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Data alat tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $alats->firstItem() }} –
                    {{ $alats->lastItem() }} dari
                    {{ $alats->total() }} data
                </small>

                {{ $alats->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
@endsection