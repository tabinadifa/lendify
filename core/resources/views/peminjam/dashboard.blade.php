@extends('layouts.layout')

@section('title', 'Dashboard - Lendify')

@section('content')
@php
    $maxWeeklyValue = max(1, collect($weeklyStats)->max('count'));
    $user = Auth::user();
@endphp

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 mt-3">Halo, {{ $user->name }} 👋</h2>
        <p class="text-muted mb-0">Berikut ringkasan aktivitas peminjaman Anda.</p>
    </div>
    <a href="{{ route('peminjam.peminjaman.list') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Pinjam Alat
    </a>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card green h-100">
            <div class="metric-label">Total Peminjaman</div>
            <div class="metric-value">{{ number_format($totalPeminjaman) }}</div>
            <small><i class="bi bi-clipboard-data me-1"></i>Semua waktu</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card bg-white h-100">
            <div class="metric-label text-muted">Sedang Dipinjam</div>
            <div class="metric-value text-dark">{{ number_format($peminjamanAktif) }}</div>
            <small class="text-muted"><i class="bi bi-box-seam me-1"></i>Aktif</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card bg-white h-100">
            <div class="metric-label text-muted">Belum Dikembalikan</div>
            <div class="metric-value text-dark">{{ number_format($belumDikembalikan) }}</div>
            <small class="text-muted"><i class="bi bi-arrow-return-left me-1"></i>Perlu dikembalikan</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card bg-white h-100">
            <div class="metric-label text-muted">Terlambat</div>
            <div class="metric-value {{ $terlambat > 0 ? 'text-danger' : 'text-dark' }}">{{ number_format($terlambat) }}</div>
            <small class="text-muted"><i class="bi bi-exclamation-circle me-1"></i>Lewat jatuh tempo</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card bg-white h-100">
            <div class="metric-label text-muted">Sudah Dikembalikan</div>
            <div class="metric-value text-dark">{{ number_format($sudahDikembalikan) }}</div>
            <small class="text-muted"><i class="bi bi-check-circle me-1"></i>Selesai</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="metric-card bg-white h-100">
            <div class="metric-label text-muted">Menunggu Persetujuan</div>
            <div class="metric-value {{ $menungguPersetujuan > 0 ? 'text-warning' : 'text-dark' }}">{{ number_format($menungguPersetujuan) }}</div>
            <small class="text-muted"><i class="bi bi-hourglass-split me-1"></i>Pending</small>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="row g-3">

    <!-- Alat yang Sedang Dipinjam -->
    <div class="col-md-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Alat yang Sedang Dipinjam</h6>
                    <a href="{{ route('peminjam.riwayat.list') }}" class="text-muted small text-decoration-none">
                        Lihat semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if($alatDipinjam->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Anda tidak sedang meminjam alat apapun.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Nama Alat</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alatDipinjam as $loan)
                                @php
                                    $isLate = $loan->tanggal_kembali && \Illuminate\Support\Carbon::parse($loan->tanggal_kembali)->isPast();
                                    $dueDays = $loan->tanggal_kembali
                                        ? \Illuminate\Support\Carbon::parse($loan->tanggal_kembali)->diffInDays(now(), false)
                                        : null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 0.875rem;">{{ $loan->alat->nama_alat ?? '-' }}</div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Illuminate\Support\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="small {{ $isLate ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $loan->tanggal_kembali ? \Illuminate\Support\Carbon::parse($loan->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                        @if($isLate)
                                            <span class="badge bg-danger ms-1">+{{ abs($dueDays) }}h</span>
                                        @elseif($dueDays !== null && $dueDays >= -3)
                                            <span class="badge bg-warning text-dark ms-1">Segera</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isLate)
                                            <span class="badge" style="background-color:#FEE2E2; color:#DC2626;">Terlambat</span>
                                        @elseif($loan->status === 'approve')
                                            <span class="badge" style="background-color:#DCFCE7; color:#16A34A;">Disetujui</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ringkasan Pengembalian -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Ringkasan Pengembalian</h6>
                <div class="progress-circle mx-auto mb-3" style="background: conic-gradient(var(--primary-green) {{ $returnCompletionPercentage }}%, #E5E7EB 0);">
                    <div class="progress-text">{{ $returnCompletionPercentage }}%</div>
                </div>
                <p class="text-center text-muted small mb-0">
                    {{ number_format($sudahDikembalikan) }} dikembalikan dari {{ number_format($totalPeminjaman) }} peminjaman
                </p>
            </div>
        </div>
    </div>

    <!-- Statistik Mingguan -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Aktivitas 7 Hari Terakhir</h6>
                <div class="d-flex align-items-end gap-2" style="height: 150px;">
                    @foreach ($weeklyStats as $stat)
                        @php
                            $height = $maxWeeklyValue > 0 ? ($stat['count'] / $maxWeeklyValue) * 100 : 0;
                        @endphp
                        <div class="flex-fill chart-bar text-center" style="height: {{ max(8, $height) }}%;">
                            <div class="small text-white" style="font-size: 0.7rem;">{{ $stat['count'] > 0 ? $stat['count'] : '' }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                    @foreach ($weeklyStats as $stat)
                        <span>{{ $stat['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Terbaru -->
    <div class="col-md-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Riwayat Peminjaman Terbaru</h6>
                    <a href="{{ route('peminjam.riwayat.list') }}" class="text-muted small text-decoration-none">
                        Lihat semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if($riwayatTerbaru->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                        Belum ada riwayat peminjaman.
                    </div>
                @else
                    @foreach ($riwayatTerbaru as $loan)
                    @php
                        $statusColor = match($loan->status) {
                            'returned'  => ['bg' => '#DCFCE7', 'text' => '#16A34A', 'label' => 'Dikembalikan'],
                            'approve'   => ['bg' => '#DBEAFE', 'text' => '#2563EB', 'label' => 'Disetujui'],
                            'pending'   => ['bg' => '#FEF3C7', 'text' => '#D97706', 'label' => 'Menunggu'],
                            'rejected'  => ['bg' => '#FEE2E2', 'text' => '#DC2626', 'label' => 'Ditolak'],
                            default     => ['bg' => '#F3F4F6', 'text' => '#6B7280', 'label' => ucfirst($loan->status)],
                        };
                    @endphp
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;background-color:#F0FDF4;">
                                <i class="bi bi-box-seam text-success" style="font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:0.875rem;">{{ $loan->alat->nama_alat ?? '-' }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($loan->created_at)->translatedFormat('d M Y') }}</small>
                            </div>
                        </div>
                        <span class="badge" style="background-color:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};">
                            {{ $statusColor['label'] }}
                        </span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>
@endsection