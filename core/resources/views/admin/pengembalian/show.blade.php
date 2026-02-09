@extends('layouts.layout')

@section('title', 'Detail Pengembalian - Lendify')

@section('content')
    @php
        $peminjaman = $pengembalian->peminjaman;
        $borrower = $peminjaman?->peminjam;
        $alat = $peminjaman?->alat;
        $file = $pengembalian->fileBuktiPengembalian;
        $filePreview = $file ? asset($file->path ?? $file->file_path) : null;
        $dendaValue = (float) ($pengembalian->denda ?? 0);
        $badge = match ($peminjaman?->status) {
            'approve' => 'success',
            'dikembalikan' => 'primary',
            'rejected' => 'danger',
            default => 'warning',
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Pengembalian</h2>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-auto">
            <a href="{{ route('pengembalian.list') }}" class="btn btn-outline-secondary">
                Kembali
            </a>
            <a href="{{ route('pengembalian.edit', $pengembalian->id) }}" class="btn btn-primary">
                Edit
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="35%" class="text-muted">Tanggal Pengembalian</th>
                            <td>{{ $pengembalian->tanggal_pengembalian ? \Illuminate\Support\Carbon::parse($pengembalian->tanggal_pengembalian)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kondisi Alat</th>
                            <td>{{ $pengembalian->kondisi_alat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Denda</th>
                            <td>
                                @if ($dendaValue > 0)
                                    <span class="badge text-bg-danger">Rp
                                        {{ number_format($dendaValue, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge text-bg-success">Tidak ada denda</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted align-top">Catatan</th>
                            <td>{!! $pengembalian->catatan ? nl2br(e($pengembalian->catatan)) : '-' !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Bukti Pengembalian</h5>
                    @if ($file && $filePreview)
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden mb-3">
                            <img src="{{ $filePreview }}" alt="Bukti Pengembalian" class="w-100 h-100"
                                style="object-fit: cover;">
                        </div>
                        <div class="d-flex justify-content-between flex-wrap gap-2 small text-muted">
                            <a href="{{ $filePreview }}" class="text-decoration-none" target="_blank" rel="noopener">Lihat
                                ukuran penuh</a>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-image fs-2 d-block mb-2"></i>
                            <p class="mb-0">Belum ada bukti pengembalian yang diunggah.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Ringkasan Peminjaman</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%" class="text-muted">Status</th>
                            <td><span
                                    class="badge bg-{{ $badge }}">{{ ucfirst($peminjaman?->status ?? 'pending') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Pinjam</th>
                            <td>{{ $peminjaman?->tanggal_pinjam ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Batas Kembali</th>
                            <td>{{ $peminjaman?->tanggal_kembali ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Total Alat</th>
                            <td>{{ $peminjaman?->total_alat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Peminjam & Alat</h5>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Peminjam</p>
                        <p class="fw-semibold mb-0">{{ $borrower->name ?? '-' }}</p>
                        <small class="text-muted d-block">{{ $borrower->email ?? 'Email tidak tersedia' }}</small>
                        <small class="text-muted">Username: {{ $borrower->username ?? '-' }}</small>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Alat</p>
                        <p class="fw-semibold mb-0">{{ $alat->nama_alat ?? '-' }}</p>
                        @if ($alat?->kategori)
                            <span class="badge text-bg-secondary">{{ $alat->kategori->nama_kategori }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
