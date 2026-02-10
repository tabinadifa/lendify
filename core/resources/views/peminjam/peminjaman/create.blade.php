@extends('layouts.layout')

@section('title', 'Ajukan Peminjaman - Peminjam')

@section('content')
	@php
		$today = now()->format('Y-m-d');
		$tomorrow = now()->addDay()->format('Y-m-d');
	@endphp

	<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
		<div>
			<h2 class="fw-bold mb-1">Ajukan Peminjaman</h2>
			<p class="text-muted mb-0">Lengkapi formulir di bawah untuk meminjam alat pilihan Anda.</p>
		</div>
		<a href="{{ route('peminjam.peminjaman.list') }}" class="btn btn-outline-secondary">Kembali ke Daftar Alat</a>
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
					<table class="table table-borderless mb-0">
						<tr>
							<th class="text-muted" width="40%">Nama Alat</th>
							<td class="fw-semibold">{{ $alat->nama_alat }}</td>
						</tr>
						<tr>
							<th class="text-muted">Stok Tersedia</th>
							<td>
								<span class="badge text-bg-{{ $alat->jumlah_stok > 0 ? 'success' : 'danger' }}">
									{{ number_format($alat->jumlah_stok) }} 
								</span>
							</td>
						</tr>
					</table>
					<p class="text-muted small mt-3 mb-0">
						Pastikan tanggal pinjam dan kembali disesuaikan dengan kebutuhan pemakaian.
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
								min="1" max="{{ $alat->jumlah_stok }}" value="{{ old('total_alat', 1) }}" required>
							<small class="text-muted">Maksimal {{ number_format($alat->jumlah_stok) }} unit.</small>
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
								Setelah diajukan, petugas akan meninjau permintaan Anda. Status peminjaman dapat dipantau pada halaman
								riwayat peminjaman.
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
