@extends('layouts.layout')

@section('title', 'Ajukan Peminjaman - Peminjam')

@push('styles')
<style>
    .alat-img-box {
        width: 100%;
        height: 220px;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #f0f4f2;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
    }
    .alat-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .alat-img-placeholder {
        font-size: 3.5rem;
        color: #9ca3af;
    }
    .condition-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
    }
</style>
@endpush

@section('content')
@php
    $today    = now()->format('Y-m-d');
    $tomorrow = now()->addDay()->format('Y-m-d');
    $gambar   = $alat->gambarAlat;
    $gambarUrl = $gambar ? asset($gambar->file_path) : null;
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Ajukan Peminjaman</h2>
        <p class="text-muted mb-0">Lengkapi formulir di bawah untuk meminjam alat pilihan Anda.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Informasi Alat</h5>

                <div class="alat-img-box">
                    @if ($gambarUrl)
                        <img src="{{ $gambarUrl }}" alt="{{ $alat->nama_alat }}">
                    @else
                        <div class="alat-img-placeholder">
                            <i class="bi bi-tools"></i>
                        </div>
                    @endif
                </div>

                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" width="40%">Nama Alat</th>
                        <td class="fw-semibold">{{ $alat->nama_alat }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Kategori</th>
                        <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Kondisi Alat</th>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="condition-badge bg-success text-white">Baik: {{ $alat->baik }}</span>
                                <span class="condition-badge bg-warning text-dark">Rusak Ringan: {{ $alat->rusak_ringan }}</span>
                                <span class="condition-badge bg-info text-dark">Diperbaiki: {{ $alat->diperbaiki }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Stok Baik Tersedia</th>
                        <td>
                            <span class="badge text-bg-{{ $alat->baik > 0 ? 'success' : 'danger' }}">
                                {{ number_format($alat->baik) }}
                            </span>
                        </td>
                    </tr>
                    @if ($alat->deskripsi)
                        <tr>
                            <th class="text-muted">Deskripsi</th>
                            <td class="text-muted small">{{ $alat->deskripsi }}</td>
                        </tr>
                    @endif
                </table>

                <p class="text-muted small mt-3 mb-0">
                    Hanya alat dengan kondisi <strong>baik</strong> yang dapat dipinjam. Stok rusak ringan dan diperbaiki tidak tersedia untuk peminjaman.
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <form action="{{ route('peminjam.peminjaman.store') }}" method="POST" class="row g-4">
                    @csrf
                    <input type="hidden" name="alat_id" value="{{ $alat->id }}">

                    <div class="col-md-6">
                        <label for="total_alat" class="form-label">Jumlah Alat</label>
                        <input type="number" id="total_alat" name="total_alat" class="form-control"
                            min="1" max="{{ $alat->baik }}"
                            value="{{ old('total_alat', 1) }}" required>
                        <small class="text-muted">Maksimal {{ number_format($alat->baik) }} unit (stok baik).</small>
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="form-control"
                            min="{{ $today }}" value="{{ old('tanggal_pinjam', $today) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                        <input type="date" id="tanggal_kembali" name="tanggal_kembali" class="form-control"
                            min="{{ $tomorrow }}" value="{{ old('tanggal_kembali', $tomorrow) }}" required>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info border-0 rounded-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Setelah diajukan, petugas akan meninjau permintaan Anda. Status peminjaman
                            dapat dipantau pada halaman riwayat peminjaman.
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('peminjam.peminjaman.list') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-success">Ajukan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection