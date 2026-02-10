@extends('layouts.layout')

@section('title', 'Riwayat Pengembalian - Peminjam')

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

		.table thead th {
			border-bottom: 2px solid #e1e7e4;
			text-transform: uppercase;
			font-size: 0.8rem;
			letter-spacing: 0.05em;
			color: #789082;
		}

		.badge-status {
			font-size: 0.75rem;
			padding: 0.4rem 0.75rem;
		}
	</style>
@endpush

@section('content')
	@php
		$formatDate = fn($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('d M Y') : '-';
	@endphp

	<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
		<div>
			<p class="text-uppercase text-muted mb-1 small">Pengembalian Alat</p>
			<h1 class="page-title fw-bold mb-0">Riwayat Pengembalian Saya</h1>
		</div>
		<div class="text-end">
			<span class="text-muted small">Total {{ number_format($pengembalians->total()) }} pengembalian</span>
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
							<option value="{{ $option }}" @selected((int) request('per_page', 10) === $option)>
								{{ $option }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-6">
					<label for="search" class="form-label text-uppercase text-muted small">Cari Alat</label>
					<input type="text" id="search" name="search" class="form-control"
						placeholder="Masukkan nama alat"
						value="{{ request('search') }}"
						onkeydown="if(event.key==='Enter'){this.form.submit()}">
				</div>
			</form>
		</div>
	</div>

	<div class="card table-card">
		<div class="card-body p-0">
			@if ($pengembalians->isEmpty())
				<div class="text-center py-5 text-muted">
					<i class="bi bi-clipboard-x mb-2" style="font-size: 2rem;"></i>
					<p class="mb-0">Belum ada data pengembalian.</p>
				</div>
			@else
				<div class="table-responsive">
					<table class="table align-middle mb-0">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Alat</th>
								<th>Jumlah</th>
								<th>Tgl Pinjam</th>
								<th>Jatuh Tempo</th>
								<th>Tgl Pengembalian</th>
								<th>Kondisi</th>
								<th>Denda</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($pengembalians as $pengembalian)
								@php
									$peminjaman = $pengembalian->peminjaman;
								@endphp
								<tr>
									<td>{{ $loop->iteration + ($pengembalians->firstItem() - 1) }}</td>
									<td class="fw-semibold">{{ $peminjaman?->alat?->nama_alat ?? 'Tidak diketahui' }}</td>
									<td>{{ $peminjaman?->total_alat ?? '-' }}</td>
									<td>{{ $formatDate($peminjaman?->tanggal_pinjam) }}</td>
									<td>{{ $formatDate($peminjaman?->tanggal_kembali) }}</td>
									<td>{{ $formatDate($pengembalian->tanggal_pengembalian) }}</td>
									<td>{{ $pengembalian->kondisi_alat ?? '-' }}</td>
									<td>Rp {{ number_format((float) $pengembalian->denda, 0, ',', '.') }}</td>
									<td>
										<a href="{{ route('peminjam.pengembalian.show', $pengembalian) }}" class="btn btn-outline-success btn-sm">
											Lihat Detail
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>

		@if ($pengembalians->hasPages())
			<div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
				<small class="text-muted mb-2 mb-md-0">
					Menampilkan {{ $pengembalians->firstItem() }} - {{ $pengembalians->lastItem() }} dari {{ $pengembalians->total() }} pengembalian
				</small>
				{{ $pengembalians->onEachSide(1)->links('pagination::bootstrap-5') }}
			</div>
		@endif
	</div>
@endsection
