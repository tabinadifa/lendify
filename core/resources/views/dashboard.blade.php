@extends('layouts.layout')

@section('title', 'Dashboard - Lendify')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="fw-bold mb-1">Dashboard Peminjaman Alat</h2>
		<p class="text-muted mb-0">Kelola peminjaman alat praktikum dengan mudah dan efisien.</p>
	</div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
	<div class="col-12 col-sm-6 col-md-4">
		<div class="metric-card green">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label">Total Alat</div>
					<div class="metric-value">145</div>
					<small><i class="bi bi-arrow-up me-1"></i>Bertambah 12 bulan ini</small>
				</div>
				<i class="bi bi-box-seam"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-md-4">
		<div class="metric-card bg-white">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label text-muted">Alat Dipinjam</div>
					<div class="metric-value text-dark">38</div>
					<small class="text-muted"><i class="bi bi-graph-up me-1"></i>Dari total 145 alat</small>
				</div>
				<i class="bi bi-clipboard-check"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-md-4">
		<div class="metric-card bg-white">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label text-muted">Total Peminjaman</div>
					<div class="metric-value text-dark">27</div>
					<small class="text-muted"><i class="bi bi-clock me-1"></i>Sedang berlangsung</small>
				</div>
				<i class="bi bi-hourglass-split"></i>
			</div>
		</div>
	</div>
</div>

<!-- Content Grid -->
<div class="row g-3">
	<!-- Statistik Peminjaman -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<h6 class="fw-bold mb-3">Statistik Peminjaman Mingguan</h6>
				<div class="d-flex align-items-end gap-2" style="height: 180px;">
					<div class="chart-bar" style="width: 40px; height: 55%;"></div>
					<div class="chart-bar" style="width: 40px; height: 70%;"></div>
					<div class="chart-bar" style="width: 40px; height: 85%;"></div>
					<div class="chart-bar" style="width: 40px; height: 90%;"></div>
					<div class="chart-bar" style="width: 40px; height: 65%;"></div>
					<div class="chart-bar" style="width: 40px; height: 75%;"></div>
					<div class="chart-bar" style="width: 40px; height: 45%; opacity: 0.5;"></div>
				</div>
				<div class="text-center mt-2">
					<small class="text-muted">Sen - Min (5 Feb Ini)</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Pengingat Pengembalian -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h6 class="fw-bold mb-0">Pengingat Pengembalian</h6>
				</div>
				<div class="p-3 rounded mb-2" style="background-color: #FEF3C7;">
					<div class="d-flex align-items-center">
						<i class="bi bi-exclamation-triangle text-warning me-2"></i>
						<div class="flex-grow-1">
							<h6 class="mb-0" style="font-size: 0.875rem;">Mikroskop #12</h6>
							<small class="text-muted">Jatuh tempo: Hari ini, 16:00</small>
						</div>
					</div>
				</div>
				<div class="p-3 rounded" style="background-color: #FEE2E2;">
					<div class="d-flex align-items-center">
						<i class="bi bi-x-circle text-danger me-2"></i>
						<div class="flex-grow-1">
							<h6 class="mb-0" style="font-size: 0.875rem;">Tabung Reaksi Set</h6>
							<small class="text-muted">Terlambat 2 hari</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection