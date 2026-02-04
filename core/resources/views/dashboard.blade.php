<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

	<style>
		:root {
			--dash-primary: #90AB8B;
			--dash-secondary: #EBF4DD;
			--dash-accent: #5A7863;
		}

		body {
			background-color: #f7f9f5;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.navbar {
			background-color: var(--dash-accent);
		}

		.navbar-brand,
		.nav-link,
		.navbar-toggler {
			color: #ffffff !important;
		}

		.summary-card {
			border: none;
			border-radius: 1rem;
			box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
		}

		.summary-icon {
			width: 2.75rem;
			height: 2.75rem;
			border-radius: 0.75rem;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #ffffff;
			background-color: var(--dash-primary);
		}

		.summary-value {
			font-size: 1.85rem;
			font-weight: 600;
			color: var(--dash-accent);
		}

		.summary-label {
			color: rgba(36, 48, 36, 0.7);
		}

		.activity-item {
			border-left: 4px solid var(--dash-primary);
			padding-left: 1rem;
		}

		.table thead {
			background-color: var(--dash-secondary);
		}
	</style>
</head>
<body>

	<nav class="navbar navbar-expand-lg">
		<div class="container">
			<a class="navbar-brand fw-semibold" href="#">Lendify</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Dashboard</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Transaksi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Profil</a>
					</li>
				</ul>
				<div class="d-flex ms-lg-3">
					<button class="btn btn-outline-light">Keluar</button>
				</div>
			</div>
		</div>
	</nav>

	<main class="py-5">
		<div class="container">
			<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
				<div>
					<h2 class="fw-bold mb-1">Halo, Selamat Datang Kembali</h2>
					<p class="text-muted mb-0">Ringkasan aktivitas terbaru dan status akun Anda tersedia di bawah ini.</p>
				</div>
				<button class="btn btn-success px-4 py-2">Buat Transaksi</button>
			</div>

			<div class="row g-4 mb-4">
				<div class="col-12 col-md-6 col-xl-3">
					<div class="card summary-card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="summary-icon">
								<i class="bi bi-wallet2"></i>
							</div>
							<div>
								<div class="summary-value">Rp12,5jt</div>
								<div class="summary-label">Total Investasi</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-xl-3">
					<div class="card summary-card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="summary-icon">
								<i class="bi bi-graph-up"></i>
							</div>
							<div>
								<div class="summary-value">8,6%</div>
								<div class="summary-label">Imbal Hasil Tahunan</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-xl-3">
					<div class="card summary-card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="summary-icon">
								<i class="bi bi-people"></i>
							</div>
							<div>
								<div class="summary-value">27</div>
								<div class="summary-label">Peminjam Aktif</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-xl-3">
					<div class="card summary-card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="summary-icon">
								<i class="bi bi-calendar-check"></i>
							</div>
							<div>
								<div class="summary-value">3</div>
								<div class="summary-label">Tagihan Minggu Ini</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row g-4">
				<div class="col-xl-8">
					<div class="card h-100 border-0 shadow-sm rounded-4">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h5 class="card-title mb-0">Aktivitas Terbaru</h5>
								<a href="#" class="text-decoration-none">Lihat semua</a>
							</div>

							<div class="list-group list-group-flush">
								<div class="list-group-item px-0 py-3 activity-item">
									<div class="d-flex justify-content-between">
										<div>
											<h6 class="mb-1">Pendanaan baru ke UMKM A</h6>
											<small class="text-muted">2 jam lalu</small>
										</div>
										<span class="badge rounded-pill text-bg-success">Berhasil</span>
									</div>
								</div>
								<div class="list-group-item px-0 py-3 activity-item">
									<div class="d-flex justify-content-between">
										<div>
											<h6 class="mb-1">Pembayaran cicilan dari UMKM B</h6>
											<small class="text-muted">Kemarin</small>
										</div>
										<span class="badge rounded-pill text-bg-primary">Masuk</span>
									</div>
								</div>
								<div class="list-group-item px-0 py-3 activity-item">
									<div class="d-flex justify-content-between">
										<div>
											<h6 class="mb-1">Notifikasi jatuh tempo UMKM C</h6>
											<small class="text-muted">3 hari lalu</small>
										</div>
										<span class="badge rounded-pill text-bg-warning">Pengingat</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-4">
					<div class="card border-0 shadow-sm rounded-4 mb-4">
						<div class="card-body">
							<h5 class="card-title">Nilai Portfolio</h5>
							<div class="d-flex align-items-center mb-2">
								<div class="progress flex-grow-1" style="height: 12px;">
									<div class="progress-bar" role="progressbar" style="width: 68%; background-color: var(--dash-primary);"></div>
								</div>
								<span class="ms-3 fw-semibold">68%</span>
							</div>
							<p class="text-muted mb-0">Portofolio Anda berada di kategori pertumbuhan stabil bulan ini.</p>
						</div>
					</div>

					<div class="card border-0 shadow-sm rounded-4">
						<div class="card-body">
							<h5 class="card-title">Jadwal Pembayaran</h5>
							<ul class="list-unstyled mb-0">
								<li class="d-flex justify-content-between py-2 border-bottom">
									<span>UMKM D</span>
									<span class="text-muted">5 Feb</span>
								</li>
								<li class="d-flex justify-content-between py-2 border-bottom">
									<span>UMKM E</span>
									<span class="text-muted">8 Feb</span>
								</li>
								<li class="d-flex justify-content-between py-2">
									<span>UMKM F</span>
									<span class="text-muted">11 Feb</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="card border-0 shadow-sm rounded-4 mt-4">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h5 class="card-title mb-0">Daftar Transaksi Terakhir</h5>
						<a href="#" class="text-decoration-none">Unduh laporan</a>
					</div>

					<div class="table-responsive">
						<table class="table align-middle mb-0">
							<thead>
								<tr>
									<th scope="col">Tanggal</th>
									<th scope="col">Deskripsi</th>
									<th scope="col">Status</th>
									<th scope="col" class="text-end">Jumlah</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>03 Feb 2026</td>
									<td>Investasi ke UMKM G</td>
									<td><span class="badge text-bg-success">Selesai</span></td>
									<td class="text-end">Rp3.500.000</td>
								</tr>
								<tr>
									<td>01 Feb 2026</td>
									<td>Pembayaran bunga UMKM H</td>
									<td><span class="badge text-bg-primary">Masuk</span></td>
									<td class="text-end">Rp420.000</td>
								</tr>
								<tr>
									<td>28 Jan 2026</td>
									<td>Pencairan saldo</td>
									<td><span class="badge text-bg-warning">Proses</span></td>
									<td class="text-end">Rp2.000.000</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
