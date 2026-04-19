@extends('layouts.layout')

@section('title', 'Detail Peminjaman - Lendify')

@section('content')
@php
    $statusLabels = [
        'pending'  => 'Menunggu Persetujuan',
        'approve'  => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];

    $metodePembayaranLabels = [
        'cash'      => 'Tunai',
        'transfer'  => 'Transfer Bank',
        'qris'      => 'QRIS',
        'ewallet'   => 'E-Wallet',
    ];
@endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Detail Peminjaman</h2>
            <p class="text-muted mb-0 small">ID #{{ $peminjaman->id }}</p>
        </div>
        @php
            $badge = match ($peminjaman->status) {
                'approve'  => 'success',
                'rejected' => 'danger',
                'pending'  => 'warning',
                'returned' => 'primary',
                default    => 'secondary',
            };
        @endphp
        <span class="badge bg-{{ $badge }} fs-6 px-3 py-2">
            {{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
        </span>
    </div>

    <div class="row g-4">

        {{-- ===================== KOLOM KIRI ===================== --}}
        <div class="col-lg-7">

            {{-- Informasi Peminjaman --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard-data text-primary"></i> Informasi Peminjaman
                    </h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="38%" class="text-muted fw-normal">Status</th>
                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Tanggal Pinjam</th>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Tanggal Kembali (Rencana)</th>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}</td>
                        </tr>
                        @if ($peminjaman->status === 'returned' && $peminjaman->tanggal_dikembalikan)
                            <tr>
                                <th class="text-muted fw-normal">Tanggal Dikembalikan</th>
                                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_dikembalikan)->translatedFormat('d F Y') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted fw-normal">Total Alat</th>
                            <td>{{ $peminjaman->total_alat }} unit</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Alasan Ditolak --}}
            @if ($peminjaman->status === 'rejected')
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-danger border-3">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold text-danger mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-x-circle"></i> Alasan Penolakan
                        </h5>
                        <p class="mb-0">{{ $peminjaman->alasan_ditolak ?? '-' }}</p>
                    </div>
                </div>
            @endif

            {{-- ===================== INFORMASI DENDA (jika sudah dikembalikan) ===================== --}}
            @if ($peminjaman->status === 'returned')
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-receipt text-warning"></i> Informasi Pengembalian & Denda
                        </h5>

                        @php
                            $hasDenda = !empty($peminjaman->denda) && $peminjaman->denda > 0;
                        @endphp

                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="38%" class="text-muted fw-normal">Status Denda</th>
                                <td>
                                    @if ($hasDenda)
                                        <span class="badge bg-danger">Ada Denda</span>
                                    @else
                                        <span class="badge bg-success">Tidak Ada Denda</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($hasDenda)
                                <tr>
                                    <th class="text-muted fw-normal">Jumlah Denda</th>
                                    <td class="fw-semibold text-danger fs-5">
                                        Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Alasan Denda</th>
                                    <td>{{ $peminjaman->alasan_denda ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Metode Pembayaran</th>
                                    <td>
                                        @if ($peminjaman->metode_pembayaran)
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-credit-card me-1"></i>
                                                {{ $metodePembayaranLabels[$peminjaman->metode_pembayaran] ?? ucfirst($peminjaman->metode_pembayaran) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Status Pembayaran</th>
                                    <td>
                                        @php
                                            $paid = $peminjaman->status_pembayaran === 'paid' || $peminjaman->is_paid;
                                        @endphp
                                        @if ($paid)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Lunas
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i> Belum Dibayar
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>

                        @if (!$hasDenda)
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-0 mt-3 py-2">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Alat dikembalikan tepat waktu dan dalam kondisi baik. Tidak ada denda.</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- ===================== KOLOM KANAN ===================== --}}
        <div class="col-lg-5">

            {{-- Peminjam --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle text-primary"></i> Peminjam
                    </h5>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;flex-shrink:0">
                            <i class="bi bi-person-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold">{{ $peminjaman->peminjam->name }}</p>
                            <p class="mb-0 text-muted small">@ {{ $peminjaman->peminjam->username }}</p>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-envelope text-muted mt-1"></i>
                            <span>{{ $peminjaman->peminjam->email }}</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-telephone text-muted mt-1"></i>
                            <span>{{ $peminjaman->peminjam->phone ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt text-muted mt-1" style="flex-shrink:0"></i>
                            <span>{{ $peminjaman->peminjam->address ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Alat Dipinjam --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-primary"></i> Alat Dipinjam
                    </h5>

                    <p class="fw-semibold mb-1">{{ $peminjaman->alat->nama_alat }}</p>

                    @if ($peminjaman->alat->kategori ?? false)
                        <span class="badge bg-secondary mb-2">
                            {{ $peminjaman->alat->kategori->nama_kategori }}
                        </span>
                    @endif

                    @if ($peminjaman->alat->deskripsi ?? false)
                        <p class="text-muted small mb-0 mt-2">{{ $peminjaman->alat->deskripsi }}</p>
                    @endif

                    <hr class="my-3">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Jumlah dipinjam</span>
                        <strong class="text-dark">{{ $peminjaman->total_alat }} unit</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Action --}}
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('peminjaman.list') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection