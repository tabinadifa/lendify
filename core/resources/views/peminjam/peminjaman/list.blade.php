@extends('layouts.layout')

@section('title', 'Daftar Alat - Peminjam')

@push('styles')
<style>
    .page-title {
        color: #1e4d35;
    }

    .filter-card,
    .catalog-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 14px rgba(23, 56, 35, 0.08);
    }

    .alat-card {
        border: 1px solid rgba(30, 77, 53, 0.12);
        border-radius: 1rem;
        overflow: hidden;
        height: 100%;
        transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 15px 35px rgba(30, 77, 53, 0.16), 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .alat-card:hover {
        border-color: #1e4d35;
        transform: translateY(-4px);
        box-shadow: 0 24px 45px rgba(30, 77, 53, 0.2), 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .alat-card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .alat-card-img-placeholder {
        width: 100%;
        height: 180px;
        background: #f0f4f2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 2.5rem;
    }

    .alat-card-body {
        padding: 1.25rem;
    }

    .condition-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
    }

    .badge-tersedia {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        background-color: #198754;
        color: white;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted mb-1 small">Peminjaman Alat</p>
        <h1 class="page-title fw-bold mb-0">Pilih Alat Untuk Dipinjam</h1>
    </div>
    <div class="text-end">
        <span class="text-muted small">Menampilkan {{ number_format($alats->total()) }} alat tersedia</span>
    </div>
</div>

<div class="card filter-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="per_page" class="form-label text-uppercase text-muted small">Per Halaman</label>
                <select id="per_page" name="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach ([5, 10, 25, 50] as $option)
                    <option value="{{ $option }}" @selected((int) request('per_page', 10)===$option)>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label text-uppercase text-muted small">Cari Alat</label>
                <input type="text" id="search" name="search" class="form-control"
                    placeholder="Masukkan nama alat yang ingin dipinjam"
                    value="{{ request('search') }}"
                    onkeydown="if(event.key==='Enter'){this.form.submit()}">
            </div>
        </form>
    </div>
</div>

<div class="card catalog-card">
    <div class="card-body">
        @if ($alats->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox mb-2" style="font-size: 2rem;"></i>
            <p class="mb-0">Tidak ada alat yang tersedia untuk dipinjam saat ini.</p>
        </div>
        @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @foreach ($alats as $alat)
            @php
                $totalTersedia = $alat->baik + $alat->rusak_ringan;
                $canBorrow = $totalTersedia > 0;
                $gambar = $alat->gambarAlat;
                $gambarUrl = $gambar ? asset($gambar->file_path) : null;
            @endphp
            <div class="col">
                <div class="alat-card d-flex flex-column">
                    @if ($gambarUrl)
                        <img src="{{ $gambarUrl }}" alt="{{ $alat->nama_alat }}" class="alat-card-img">
                    @else
                        <div class="alat-card-img-placeholder">
                            <i class="bi bi-tools"></i>
                        </div>
                    @endif
                    <div class="alat-card-body d-flex flex-column flex-grow-1">
                        <div class="mb-2">
                            <h5 class="fw-semibold mb-1">{{ $alat->nama_alat }}</h5>
                            <p class="text-muted small mb-0">
                                {{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}
                            </p>
                        </div>

                        {{-- Badge TERSEDIA --}}
                        <div class="mb-2">
                            <span class="badge-tersedia">
                                <i class="bi bi-check-circle-fill me-1"></i> Tersedia {{ $totalTersedia }} unit
                            </span>
                        </div>

                        {{-- Rincian kondisi --}}
                        <div class="d-flex gap-2 mb-2 flex-wrap">
                            @if ($alat->baik > 0)
                                <span class="condition-badge bg-success text-white">Baik: {{ $alat->baik }}</span>
                            @endif
                            @if ($alat->rusak_ringan > 0)
                                <span class="condition-badge bg-warning text-dark">Rusak Ringan: {{ $alat->rusak_ringan }}</span>
                            @endif
                            @if ($alat->diperbaiki > 0)
                                <span class="condition-badge bg-info text-dark">Diperbaiki: {{ $alat->diperbaiki }}</span>
                            @endif
                        </div>

                        @if ($alat->deskripsi)
                            <p class="text-muted small mb-3" style="max-height:3.75rem;overflow:hidden;">
                                {{ $alat->deskripsi }}
                            </p>
                        @endif

                        <div class="mt-auto">
                            @if ($canBorrow)
                                <a href="{{ route('peminjam.peminjaman.create', $alat) }}" class="btn btn-success w-100">
                                    Ajukan Pinjam ({{ $totalTersedia }} tersedia)
                                </a>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @if ($alats->hasPages())
    <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
        <small class="text-muted mb-2 mb-md-0">
            Menampilkan {{ $alats->firstItem() }} - {{ $alats->lastItem() }} dari {{ $alats->total() }} alat
        </small>
        {{ $alats->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection