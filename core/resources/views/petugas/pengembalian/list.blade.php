@extends('layouts.layout')

@section('title', 'Daftar Pengembalian - Petugas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-5">
    <h2 class="fw-bold mb-0">Daftar Pengembalian</h2>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        @foreach (['error', 'info'] as $msg)
        @if (session($msg))
        <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show">
            {{ session($msg) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @endforeach

        <form method="GET" class="row g-2 mb-3 align-items-center mt-2">
            <div class="col-md-3">
                <a href="{{ route('petugas.pengembalian.create') }}" class="btn btn-success w-100">
                    Tambah Pengembalian
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
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Cari peminjam / alat..."
                    onkeydown="if(event.key==='Enter'){this.form.submit()}">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pengembalian</th>
                        <th>Kondisi</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengembalians as $item)
                    <tr>
                        <td>{{ $pengembalians->firstItem() + $loop->index }}</td>

                        <td>
                            <strong>{{ $item->peminjaman->peminjam->name }}</strong><br>
                            <small class="text-muted">
                                {{ $item->peminjaman->peminjam->email }}
                            </small>
                        </td>

                        <td>{{ $item->peminjaman->alat->nama_alat }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d M Y') }}
                        </td>

                        <td>
                            @php
                            $kondisi = $item->kondisi_alat;
                            // Ubah underscore menjadi spasi (jika ada) lalu kapital setiap kata
                            $kondisiDisplay = str_replace('_', ' ', $kondisi);
                            $kondisiDisplay = ucwords($kondisiDisplay);

                            $badgeColor = match ($kondisi) {
                            'baik' => 'success',
                            'rusak_ringan' => 'warning',
                            'rusak_berat' => 'danger',
                            'hilang' => 'dark',
                            default => 'secondary',
                            };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">
                                {{ $kondisiDisplay }}
                            </span>
                        </td>

                        <td>
                            @if ($item->denda > 0)
                            <span class="text-danger fw-semibold">
                                Rp {{ number_format($item->denda, 0, ',', '.') }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if (is_null($item->status))
                            Tidak Ada Denda
                            @else
                            {{ ucfirst($item->status) }}
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('petugas.pengembalian.edit', $item->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                                <a href="{{ route('petugas.pengembalian.show', $item->id) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    Lihat
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Data pengembalian tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $pengembalians->firstItem() }} –
                {{ $pengembalians->lastItem() }} dari
                {{ $pengembalians->total() }} data
            </small>

            {{ $pengembalians->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>
@endsection