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

		.stock-chip {
			font-size: 0.85rem;
			font-weight: 600;
		}

		.alat-card {
			border: 1px solid rgba(30, 77, 53, 0.12);
			border-radius: 1rem;
			padding: 1.5rem;
			height: 100%;
			transition: border-color 0.2s ease, border-width 0.2s ease, transform 0.2s ease;
			box-shadow: 0 15px 35px rgba(30, 77, 53, 0.16), 0 6px 14px rgba(0, 0, 0, 0.08);
		}

		.alat-card:hover {
			border-color: #1e4d35;
			border-width: 1.5px;
			transform: translateY(-4px);
			box-shadow: 0 24px 45px rgba(30, 77, 53, 0.2), 0 12px 24px rgba(0, 0, 0, 0.1);
		}

		.alat-description {
			max-height: 3.75rem;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	</style>
@endpush

@section('content')
	@php
		$today = now()->format('Y-m-d');
	@endphp

	<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
		<div>
			<p class="text-uppercase text-muted mb-1 small">Peminjaman Alat</p>
			<h1 class="page-title fw-bold mb-0">Pilih Alat Untuk Dipinjam</h1>
		</div>
		<div class="text-end">
			<span class="text-muted small">Tersedia {{ number_format($alats->total()) }} alat aktif</span>
		</div>
	</div>

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
					<p class="mb-0">Belum ada alat yang tersedia saat ini.</p>
				</div>
			@else
				<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
					@foreach ($alats as $alat)
						@php
							$isOutOfStock = (int) $alat->jumlah_stok <= 0;
						@endphp
						<div class="col">
							<div class="alat-card h-100 d-flex flex-column">
								<div class="d-flex justify-content-between align-items-start mb-3">
									<div>
										<h5 class="fw-semibold mb-1">{{ $alat->nama_alat }}</h5>
										<p class="text-muted small mb-0">
											{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}
										</p>
									</div>
									<span class="badge bg-{{ $isOutOfStock ? 'danger' : 'success' }} stock-chip">
										{{ $isOutOfStock ? 'Stok Habis' : 'Jumlah: ' . $alat->jumlah_stok }}
									</span>
								</div>
								<div class="d-flex justify-content-between align-items-center mt-3">
									<a href="{{ $isOutOfStock ? '#' : route('peminjam.peminjaman.create', $alat) }}"
										class="btn btn-success {{ $isOutOfStock ? 'disabled' : '' }}">
										Ajukan Pinjam
									</a>
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
