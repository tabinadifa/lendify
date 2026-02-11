@extends('layouts.layout')

@section('title', 'Daftar Peminjaman - Lendify')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Menunggu Persetujuan',
        'approve' => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];
@endphp

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h2 class="fw-bold mb-0">Daftar Peminjaman</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            {{-- Alert --}}
            @foreach (['error', 'info'] as $msg)
                @if (session($msg))
                    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show">
                        {{ session($msg) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            {{-- Controls --}}
            <form method="GET" class="row g-2 mb-3 align-items-center mt-2">
                <div class="col-md-3">
                    <a href="{{ route('peminjaman.create') }}" class="btn btn-success w-100">
                        Tambah Peminjaman
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
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama peminjam / alat..." onkeydown="if(event.key==='Enter'){this.form.submit()}">
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Alat</th>
                            <th>Total</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjaman as $item)
                            <tr>
                                <td>{{ $peminjaman->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $item->peminjam->name }}</strong><br>
                                    <small class="text-muted">{{ $item->peminjam->email }}</small>
                                </td>
                                <td>{{ $item->alat->nama_alat }}</td>
                                <td>{{ $item->total_alat }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $badge = match ($item->status) {
                                            'approve' => 'success',
                                            'rejected' => 'danger',
                                            'pending' => 'warning',
                                            'returned' => 'primary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">
                                        {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('peminjaman.show', $item->id) }}"
                                            class="btn btn-sm btn-outline-info">
                                            Detail
                                        </a>

                                        <a href="{{ route('peminjaman.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST"
                                            class="form-hapus" data-title="Yakin ingin menghapus?"
                                            data-text="Data peminjaman ini akan dihapus secara permanen.">
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
                                <td colspan="8" class="text-center text-muted py-4">
                                    Data peminjaman tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $peminjaman->firstItem() }} –
                    {{ $peminjaman->lastItem() }} dari
                    {{ $peminjaman->total() }} data
                </small>

                {{ $peminjaman->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
@endsection