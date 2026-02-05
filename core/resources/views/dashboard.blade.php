@extends('layouts.layout')

@section('title', 'Dashboard - Lendify')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="fw-bold mb-1">Dashboard Peminjaman Alat</h2>
		<p class="text-muted mb-0">Kelola peminjaman alat praktikum dengan mudah dan efisien.</p>
	</div>
	<div class="d-flex gap-2">
		<button class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Tambah Peminjaman</button>
		<button class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Unduh Laporan</button>
	</div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
	<div class="col-md-3">
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
	<div class="col-md-3">
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
	<div class="col-md-3">
		<div class="metric-card bg-white">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label text-muted">Peminjaman Aktif</div>
					<div class="metric-value text-dark">27</div>
					<small class="text-muted"><i class="bi bi-clock me-1"></i>Sedang berlangsung</small>
				</div>
				<i class="bi bi-hourglass-split"></i>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="metric-card bg-white">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label text-muted">Pengembalian Pending</div>
					<div class="metric-value text-dark">8</div>
					<small class="text-muted">Harus dikembalikan segera</small>
				</div>
				<i class="bi bi-exclamation-triangle"></i>
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

	<!-- Alat Populer -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h6 class="fw-bold mb-0">Alat Paling Sering Dipinjam</h6>
					<a href="#" class="text-decoration-none" style="font-size: 0.875rem;">Lihat Semua</a>
				</div>
				<div class="list-group list-group-flush">
					<div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
						<div><i class="bi bi-eyedropper text-primary me-2"></i>Mikroskop Digital</div>
						<span class="badge bg-primary">28x</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
						<div><i class="bi bi-mortarboard text-success me-2"></i>Tabung Reaksi Set</div>
						<span class="badge bg-success">24x</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
						<div><i class="bi bi-thermometer text-info me-2"></i>Termometer Digital</div>
						<span class="badge bg-info">19x</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
						<div><i class="bi bi-droplet text-warning me-2"></i>Pipet Tetes</div>
						<span class="badge bg-warning">15x</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
						<div><i class="bi bi-calculator text-secondary me-2"></i>pH Meter</div>
						<span class="badge bg-secondary">12x</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Peminjaman Terbaru -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h6 class="fw-bold mb-0">Peminjaman Terbaru</h6>
					<a href="#" class="text-decoration-none" style="font-size: 0.875rem;">Lihat Semua</a>
				</div>
				<div class="list-group list-group-flush">
					<div class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
						<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">AM</div>
						<div class="flex-grow-1">
							<div style="font-size: 0.875rem;">Ahmad Maulana</div>
							<small class="text-muted">Mikroskop #7 • 2 jam lalu</small>
						</div>
						<span class="badge bg-success">Disetujui</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
						<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">SN</div>
						<div class="flex-grow-1">
							<div style="font-size: 0.875rem;">Siti Nurhaliza</div>
							<small class="text-muted">Tabung Reaksi • 3 jam lalu</small>
						</div>
						<span class="badge bg-warning">Menunggu</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
						<div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">BW</div>
						<div class="flex-grow-1">
							<div style="font-size: 0.875rem;">Budi Wijaya</div>
							<small class="text-muted">pH Meter • 5 jam lalu</small>
						</div>
						<span class="badge bg-success">Disetujui</span>
					</div>
					<div class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
						<div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">DP</div>
						<div class="flex-grow-1">
							<div style="font-size: 0.875rem;">Dewi Purnama</div>
							<small class="text-muted">Termometer • Kemarin</small>
						</div>
						<span class="badge bg-secondary">Selesai</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Status Ketersediaan Alat -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body d-flex flex-column justify-content-center align-items-center">
				<h6 class="fw-bold mb-4">Ketersediaan Alat</h6>
				<div class="progress-circle mb-3" style="background: conic-gradient(var(--primary-green) 74%, #E5E7EB 0);">
					<div class="progress-text">74%</div>
				</div>
				<p class="text-muted mb-3">Alat Tersedia</p>
				<div class="d-flex gap-3 w-100 justify-content-center">
					<div class="text-center">
						<div class="d-flex align-items-center gap-1">
							<div class="rounded-circle" style="width: 10px; height: 10px; background-color: var(--primary-green);"></div>
							<small>Tersedia (107)</small>
						</div>
					</div>
					<div class="text-center">
						<div class="d-flex align-items-center gap-1">
							<div class="rounded-circle" style="width: 10px; height: 10px; background-color: #E5E7EB;"></div>
							<small>Dipinjam (38)</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Aktivitas Hari Ini -->
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<h6 class="fw-bold mb-3">Aktivitas Hari Ini</h6>
				<div class="d-flex justify-content-around text-center mb-3">
					<div>
						<div class="fs-2 fw-bold text-success">15</div>
						<small class="text-muted">Peminjaman Baru</small>
					</div>
					<div class="border-start"></div>
					<div>
						<div class="fs-2 fw-bold text-primary">12</div>
						<small class="text-muted">Pengembalian</small>
					</div>
				</div>
				<hr>
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="text-muted">Pending Approval</span>
					<span class="badge bg-warning">5</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="text-muted">Terlambat Kembali</span>
					<span class="badge bg-danger">3</span>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="text-muted">Maintenance</span>
					<span class="badge bg-secondary">2</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
