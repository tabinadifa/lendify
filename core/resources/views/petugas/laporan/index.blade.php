@extends('layouts.layout')

@section('title', 'Laporan Petugas')

@push('styles')
	<style>
		.report-hero {
			border-radius: 1.25rem;
			background: linear-gradient(120deg, #1E4D35 0%, #2D6F4E 100%);
			color: #fff;
			padding: 2rem;
			box-shadow: 0 15px 40px rgba(30, 77, 53, 0.25);
		}

		.report-section-title {
			font-size: 1rem;
			font-weight: 600;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: #5A7863;
		}

		.report-card {
			border: none;
			border-radius: 1.25rem;
			box-shadow: 0 12px 30px rgba(22, 48, 32, 0.08);
			height: 100%;
		}

		.week-nav button,
		.week-nav a {
			white-space: nowrap;
		}

		.report-table thead th {
			font-size: 0.75rem;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			border-bottom: 1px solid #E5E7EB;
			color: #6b7280;
		}

		.report-table tbody td {
			vertical-align: middle;
			font-size: 0.95rem;
		}
	</style>
@endpush

@section('content')
	@php
		$startFilter = $filters['start_date'] ?? null;
		$endFilter = $filters['end_date'] ?? null;
		$getDateLabel = static fn($date) => $date
			? \Carbon\Carbon::parse($date)->translatedFormat('d M Y')
			: null;
		if ($startFilter && $endFilter) {
			$periodLabel = sprintf('%s – %s', $getDateLabel($startFilter), $getDateLabel($endFilter));
		} elseif ($startFilter) {
			$periodLabel = 'Mulai ' . $getDateLabel($startFilter);
		} elseif ($endFilter) {
			$periodLabel = 'Hingga ' . $getDateLabel($endFilter);
		} else {
			$periodLabel = 'Semua Periode';
		}

		$statusStyles = [
			'pending' => 'bg-warning text-dark',
			'approve' => 'bg-success',
			'rejected' => 'bg-danger',
			'returned' => 'bg-primary',
		];

		$statusLabels = [
			'pending' => 'Menunggu Persetujuan',
			'approve' => 'Disetujui',
			'rejected' => 'Ditolak',
			'returned' => 'Dikembalikan',
		];

		$formatDate = fn($date) => $date
			? \Carbon\Carbon::parse($date)->translatedFormat('d M Y')
			: '-';

		$resolveRowNumber = static function ($dataset, $loop) {
			if (is_object($dataset) && method_exists($dataset, 'firstItem')) {
				$first = $dataset->firstItem();
				return is_null($first) ? $loop->iteration : $first + $loop->index;
			}
			return $loop->iteration;
		};
	@endphp

	<div class="report-hero mb-4">
		<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
			<div>
				<p class="mb-2 text-uppercase text-white-50 small">Laporan Petugas</p>
				<h1 class="fw-bold mb-2">Peminjaman & Pengembalian</h1>
				<p class="mb-0 text-white-50">Ikhtisar aktivitas berdasarkan rentang tanggal yang dipilih.</p>
			</div>
			<div class="text-md-end">
				<span class="badge bg-light text-dark px-3 py-2">{{ $periodLabel }}</span>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body">
			<form method="GET" class="row g-3 align-items-end">
				<div class="col-md-4">
					<label for="start_date" class="form-label text-muted small mb-1">Tanggal Mulai</label>
					<input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startFilter }}">
				</div>
				<div class="col-md-4">
					<label for="end_date" class="form-label text-muted small mb-1">Tanggal Selesai</label>
					<input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endFilter }}">
				</div>
				<div class="col-md-4 text-md-end">
					<div class="d-grid d-md-flex gap-2">
						<button type="submit" class="btn btn-success px-4">Terapkan Filter</button>
						
						<a href="{{ $exportUrl }}" class="btn btn-outline-primary px-4">
							<i class="bi bi-file-earmark-spreadsheet"></i>
							<span class="ms-1">Unduh Excel</span>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="row g-4 mb-4">
		<div class="col-sm-6 col-lg-3">
			<div class="metric-card green">
				<p class="metric-label text-uppercase mb-2">Total Peminjaman</p>
				<p class="metric-value">{{ number_format($summary['total_peminjaman']) }}</p>
				<small class="text-white-50">Transaksi pekan ini</small>
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div class="metric-card">
				<p class="metric-label text-uppercase text-muted mb-2">Alat Dipinjam</p>
				<p class="metric-value text-dark">{{ number_format($summary['alat_dipinjam']) }}</p>
				<small class="text-muted">Total unit keluar</small>
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div class="metric-card">
				<p class="metric-label text-uppercase text-muted mb-2">Alat Dikembalikan</p>
				<p class="metric-value text-dark">{{ number_format($summary['alat_dikembalikan']) }}</p>
				<small class="text-muted">Unit kembali ke gudang</small>
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div class="metric-card green">
				<p class="metric-label text-uppercase mb-2">Total Pengembalian</p>
				<p class="metric-value">{{ number_format($summary['total_pengembalian']) }}</p>
				<small class="text-white-50">Transaksi selesai</small>
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-6">
			<div class="report-card p-4">
				<div class="d-flex justify-content-between mb-3">
					<div>
						<p class="report-section-title mb-1">Peminjaman</p>
						<h4 class="mb-0 fw-semibold">Daftar Transaksi</h4>
					</div>
					<span class="badge bg-light text-dark">{{ number_format($peminjaman->total()) }} data</span>
				</div>
				<div class="table-responsive">
					<table class="table report-table mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Peminjam</th>
								<th>Alat</th>
								<th>Tanggal</th>
								<th class="text-end">Jumlah</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($peminjaman as $item)
								@php($rowNumber = $resolveRowNumber($peminjaman, $loop))
								<tr>
									<td>{{ $rowNumber }}</td>
									<td>
										<div class="fw-semibold">{{ optional($item->peminjam)->name ?? '-' }}</div>
										<small class="text-muted">{{ optional($item->peminjam)->username ?? '' }}</small>
									</td>
									<td>
										<div class="fw-semibold">{{ optional($item->alat)->nama_alat ?? '-' }}</div>
									</td>
									<td>
										<div>{{ $formatDate($item->tanggal_pinjam) }}</div>
										<small class="text-muted">Kembali {{ $formatDate($item->tanggal_kembali) }}</small>
									</td>
									<td class="text-end fw-semibold">{{ $item->total_alat }}</td>
									<td>
										<span class="badge rounded-pill {{ $statusStyles[$item->status] ?? 'bg-secondary' }}">
											{{ $statusLabels[$item->status] ?? ($item->status ? ucfirst($item->status) : 'Tidak diketahui') }}
										</span>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center text-muted py-4">Belum ada data peminjaman untuk periode ini.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($peminjaman->total())
					<div class="pt-3 mt-3 border-top">
						<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
							<small class="text-muted mb-0">
								Menampilkan {{ $peminjaman->firstItem() }} - {{ $peminjaman->lastItem() }} dari {{ $peminjaman->total() }} data
							</small>
							@if ($peminjaman->hasPages())
								{{ $peminjaman->onEachSide(1)->links('pagination::bootstrap-5') }}
							@endif
						</div>
					</div>
				@endif
			</div>
		</div>

		<div class="col-lg-6">
			<div class="report-card p-4">
				<div class="d-flex justify-content-between mb-3">
					<div>
						<p class="report-section-title mb-1">Pengembalian</p>
						<h4 class="mb-0 fw-semibold">Riwayat Lengkap</h4>
					</div>
					<span class="badge bg-light text-dark">{{ number_format($pengembalian->total()) }} data</span>
				</div>
				<div class="table-responsive">
					<table class="table report-table mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Peminjam</th>
								<th>Alat</th>
								<th>Tgl Kembali</th>
								<th>Kondisi</th>
								<th class="text-end">Denda</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($pengembalian as $item)
								@php($returnRow = $resolveRowNumber($pengembalian, $loop))
								<tr>
									<td>{{ $returnRow }}</td>
									<td>
										<div class="fw-semibold">{{ optional(optional($item->peminjaman)->peminjam)->name ?? '-' }}</div>
										<small class="text-muted">{{ optional(optional($item->peminjaman)->peminjam)->username ?? '' }}</small>
									</td>
									<td>
										<div class="fw-semibold">{{ optional(optional($item->peminjaman)->alat)->nama_alat ?? '-' }}</div>
										<small class="text-muted">Jumlah {{ optional($item->peminjaman)->total_alat ?? '-' }}</small>
									</td>
									<td>{{ $formatDate($item->tanggal_pengembalian) }}</td>
									<td>{{ ucfirst($item->kondisi_alat ?? '-') }}</td>
									<td class="text-end fw-semibold">Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center text-muted py-4">Belum ada data pengembalian untuk periode ini.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($pengembalian->total())
					<div class="pt-3 mt-3 border-top">
						<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
							<small class="text-muted mb-0">
								Menampilkan {{ $pengembalian->firstItem() }} - {{ $pengembalian->lastItem() }} dari {{ $pengembalian->total() }} data
							</small>
							@if ($pengembalian->hasPages())
								{{ $pengembalian->onEachSide(1)->links('pagination::bootstrap-5') }}
							@endif
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
