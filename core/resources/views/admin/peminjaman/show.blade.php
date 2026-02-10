@extends('layouts.layout')

@section('title', 'Detail Peminjaman - Lendify')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Menunggu Persetujuan',
        'approve' => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];
@endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Detail Peminjaman</h2>
    </div>

    <div class="row">
        {{-- Informasi Peminjaman --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Informasi Peminjaman</h5>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="35%" class="text-muted">Status</th>
                            <td>
                                @php
                                    $badge = match ($peminjaman->status) {
                                        'approve' => 'success',
                                        'rejected' => 'danger',
                                        'pending' => 'warning',
                                        'returned' => 'primary',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Pinjam</th>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Kembali</th>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Total Alat</th>
                            <td>{{ $peminjaman->total_alat }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Alasan Ditolak --}}
            @if ($peminjaman->status === 'rejected')
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="fw-semibold text-danger mb-2">Alasan Penolakan</h5>
                        <p class="mb-0">
                            {{ $peminjaman->alasan_ditolak ?? '-' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Informasi Peminjam & Alat --}}
        <div class="col-md-5">
            {{-- Peminjam --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Peminjam</h5>

                    <p class="mb-1">
                        <strong>{{ $peminjaman->peminjam->name }}</strong>
                    </p>
                    <p class="mb-1 text-muted">
                        Username: {{ $peminjaman->peminjam->username }}
                    </p>
                    <p class="mb-0 text-muted">
                        Email: {{ $peminjaman->peminjam->email }}
                    </p>
                </div>
            </div>

            {{-- Alat --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Alat Dipinjam</h5>

                    <p class="mb-1">
                        <strong>{{ $peminjaman->alat->nama_alat }}</strong>
                    </p>

                    @if ($peminjaman->alat->kategori ?? false)
                        <span class="badge bg-secondary">
                            {{ $peminjaman->alat->kategori->nama_kategori }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action --}}
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('peminjaman.edit', $peminjaman->id) }}" class="btn btn-outline-primary">
            Edit
        </a>

        <a href="{{ route('peminjaman.list') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
@endsection