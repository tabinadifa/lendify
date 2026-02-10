@extends('layouts.layout')

@section('title', 'Detail Pengembalian - Peminjam')

@push('styles')
	<style>
		.page-title {
			color: #1e4d35;
		}

		.detail-card {
			border: none;
			border-radius: 1rem;
			box-shadow: 0 4px 14px rgba(23, 56, 35, 0.08);
		}

		.info-label {
			width: 40%;
			color: #6b7b73;
		}
	</style>
@endpush

@section('content')
	@php
		$formatDate = static function ($date, $withTime = false) {
			if (!$date) {
				return '-';
			}

			$instance = \Illuminate\Support\Carbon::parse($date);
			return $withTime
				? $instance->translatedFormat('d M Y')
				: $instance->translatedFormat('d M Y');
		};

		$statusLabels = [
			'pending' => 'Menunggu Persetujuan',
			'approve' => 'Disetujui',
			'rejected' => 'Ditolak',
			'returned' => 'Dikembalikan',
		];

		$statusBadges = [
			'pending' => 'bg-warning text-dark',
			'approve' => 'bg-success',
			'rejected' => 'bg-danger',
			'returned' => 'bg-secondary',
		];

		$peminjaman = $pengembalian->peminjaman;
		$alat = $peminjaman?->alat;
		$file = $pengembalian->fileBuktiPengembalian;
		$fileUrl = $file ? asset($file->path ?? $file->file_path) : null;
		$isImageProof = $file && \Illuminate\Support\Str::startsWith($file->mime_type ?? '', 'image/');
		$dendaValue = (float) ($pengembalian->denda ?? 0);
		$status = $peminjaman?->status;
		$badgeClass = $statusBadges[$status] ?? 'bg-secondary';
	@endphp

	<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
		<div>
			<p class="text-uppercase text-muted mb-1 small">Pengembalian Alat</p>
			<h1 class="page-title fw-bold mb-1">Detail Pengembalian</h1>
			<p class="text-muted mb-0">Lihat informasi lengkap terkait pengembalian alat yang Anda lakukan.</p>
		</div>
		<a href="{{ route('peminjam.pengembalian.list') }}" class="btn btn-outline-secondary">
			Kembali ke Riwayat
		</a>
	</div>

	<div class="row g-4">
		<div class="col-lg-7">
			<div class="card detail-card mb-4">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Informasi Pengembalian</h5>
					<table class="table table-borderless align-middle mb-0">
						<tr>
							<th class="info-label">Tanggal Pengembalian</th>
							<td>{{ $formatDate($pengembalian->tanggal_pengembalian, true) }}</td>
						</tr>
						<tr>
							<th class="info-label">Kondisi Alat</th>
							<td>{{ $pengembalian->kondisi_alat ?? '-' }}</td>
						</tr>
						<tr>
							<th class="info-label">Denda</th>
							<td>
								@if ($dendaValue > 0)
									<span class="badge bg-danger">Rp {{ number_format($dendaValue, 0, ',', '.') }}</span>
								@else
									<span class="badge bg-success">Tidak ada denda</span>
								@endif
							</td>
						</tr>
						<tr>
							<th class="info-label align-top">Catatan</th>
							<td>{!! $pengembalian->catatan ? nl2br(e($pengembalian->catatan)) : '-' !!}</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="card detail-card">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Bukti Pengembalian</h5>
					@if ($file && $fileUrl)
						@php
							$fileName = $file->nama_file ?? ($file->file_name ?? 'Bukti pengembalian');
						@endphp
						@if ($isImageProof)
							<div class="ratio ratio-16x9 rounded-4 overflow-hidden mb-3">
								<img src="{{ $fileUrl }}" alt="{{ $fileName }}" class="w-100 h-100" style="object-fit: cover;">
							</div>
						@endif
						<div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
							<span class="text-muted small">{{ $fileName }}</span>
							<a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm">
								Lihat / Unduh
							</a>
						</div>
					@else
						<div class="text-center text-muted py-4">
							<i class="bi bi-file-earmark-text fs-2 d-block mb-2"></i>
							<p class="mb-0">Belum ada bukti pengembalian yang diunggah.</p>
						</div>
					@endif
				</div>
			</div>
		</div>

		<div class="col-lg-5">
			<div class="card detail-card mb-4">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Ringkasan Peminjaman</h5>
					<table class="table table-borderless mb-0">
						<tr>
							<th class="info-label">Status</th>
							<td>
								<span class="badge {{ $badgeClass }}">
									{{ $statusLabels[$status] ?? ($status ? ucfirst($status) : 'Status tidak tersedia') }}
								</span>
							</td>
						</tr>
						<tr>
							<th class="info-label">Tanggal Pinjam</th>
							<td>{{ $formatDate($peminjaman?->tanggal_pinjam, true) }}</td>
						</tr>
						<tr>
							<th class="info-label">Jatuh Tempo</th>
							<td>{{ $formatDate($peminjaman?->tanggal_kembali, true) }}</td>
						</tr>
						<tr>
							<th class="info-label">Total Alat</th>
							<td>
								@if ($peminjaman?->total_alat)
									{{ $peminjaman->total_alat }}
								@else
									-
								@endif
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="card detail-card">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Detail Alat</h5>
					<p class="fw-semibold mb-1">{{ $alat?->nama_alat ?? 'Nama alat tidak tersedia' }}</p>
					@if ($alat?->kategori)
						<span class="badge text-bg-secondary">{{ $alat->kategori->nama_kategori }}</span>
					@else
						<span class="text-muted small">Kategori belum diatur</span>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
