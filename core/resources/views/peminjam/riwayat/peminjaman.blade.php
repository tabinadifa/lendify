@extends('layouts.layout')

@section('title', 'Riwayat Peminjaman - Peminjam')

@push('styles')
    <style>
        .page-title {
            color: #1e4d35;
        }

        .filter-card,
        .table-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 14px rgba(23, 56, 35, 0.08);
        }

        .status-badge {
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    @php
        $formatDate = static function ($date) {
            return $date
                ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d M Y')
                : '—';
        };
        $formatDateTime = static function ($date) {
            return $date
                ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d M Y H:i')
                : null;
        };
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted mb-1 small">Riwayat Saya</p>
            <h1 class="page-title fw-bold mb-0">Riwayat Peminjaman</h1>
        </div>
        <div class="text-end">
            <a href="{{ route('peminjam.peminjaman.list') }}" class="btn btn-success">Pinjam Alat Lagi</a>
        </div>
    </div>

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} border-0 shadow-sm rounded-4 mb-4">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="per_page" class="form-label text-uppercase text-muted small">Per Halaman</label>
                    <select id="per_page" name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach ([5, 10, 25, 50] as $option)
                            <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label text-uppercase text-muted small">Status</label>
                    <select id="status" name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        @foreach ($allowedStatuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ $statusLabels[$status] ?? ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="search" class="form-label text-uppercase text-muted small">Cari Alat</label>
                    <input type="text" id="search" name="search" class="form-control"
                        placeholder="Cari berdasarkan nama alat"
                        value="{{ request('search') }}"
                        onkeydown="if(event.key==='Enter'){this.form.submit()}">
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Alat</th>
                            <th>Jadwal Peminjaman</th>
                            <th>Status</th>
                            <th>Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayats as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->alat->nama_alat ?? '-' }}</div>
                                    <div class="text-muted small">{{ number_format($item->total_alat) }} Unit</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $formatDate($item->tanggal_pinjam) }}</div>
                                    <div class="text-muted small">sampai {{ $formatDate($item->tanggal_kembali) }}</div>
                                </td>
                                <td>
                                    @php
                                        $status = $item->status ?? 'pending';
                                        $badgeClass = $statusBadges[$status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge status-badge {{ $badgeClass }}">
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </span>
                                    @if ($status === 'rejected' && $item->alasan_ditolak)
                                        <p class="text-danger small mb-0 mt-2">Alasan: {{ $item->alasan_ditolak }}</p>
                                    @elseif ($status === 'pending')
                                        <p class="text-muted small mb-0 mt-2">Menunggu persetujuan petugas.</p>
                                    @elseif ($status === 'approve')
                                        <p class="text-muted small mb-0 mt-2">Disetujui, silakan ambil sesuai jadwal.</p>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $returnInfo = $formatDateTime(optional($item->pengembalian)->tanggal_pengembalian);
                                    @endphp
                                    @if ($returnInfo)
                                        <div class="fw-semibold">{{ $returnInfo }}</div>
                                        <div class="text-muted small">Sudah dikembalikan</div>
                                    @elseif ($status === 'returned')
                                        <span class="badge bg-secondary">Sudah dikembalikan</span>
                                    @else
                                        <span class="text-muted">Belum ada data</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox me-2"></i> Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="small text-muted mb-2 mb-md-0">
                @if ($riwayats->total())
                    Menampilkan {{ $riwayats->firstItem() }} - {{ $riwayats->lastItem() }} dari {{ $riwayats->total() }} data
                @else
                    Tidak ada data untuk ditampilkan
                @endif
            </div>
            {{ $riwayats->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
