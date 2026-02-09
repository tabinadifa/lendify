@extends('layouts.layout')

@section('title', 'Dashboard - Lendify')

@section('content')
@php
	$maxWeeklyValue = max(1, $weeklyStats->max('count'));
	$returnCompletionPercentage = $returnCompletionPercentage ?? 0;
@endphp

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
					<div class="metric-value">{{ number_format($totalAlat) }}</div>
					<small><i class="bi bi-arrow-up me-1"></i>Bertambah {{ number_format($alatAddedThisMonth) }} bulan ini</small>
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
					<div class="metric-value text-dark">{{ number_format($borrowedCount) }}</div>
					<small class="text-muted"><i class="bi bi-graph-up me-1"></i>{{ $borrowedPercentage }}% dari total alat</small>
				</div>
				<i class="bi bi-clipboard-check"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-md-4">
		<div class="metric-card bg-white">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="metric-label text-muted">Peminjaman Aktif</div>
					<div class="metric-value text-dark">{{ number_format($activeLoans) }}</div>
					<small class="text-muted"><i class="bi bi-hourglass-split me-1"></i>{{ number_format($totalPeminjaman) }} total peminjaman</small>
				</div>
				<i class="bi bi-hourglass-split"></i>
			</div>
		</div>
	</div>
</div>

<!-- Content Grid -->
<div class="row g-3">
	<!-- Statistik Peminjaman -->
	<div class="col-md-6 col-lg-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<h6 class="fw-bold mb-3">Statistik Peminjaman Mingguan</h6>
				<div class="d-flex align-items-end gap-2" style="height: 180px;">
					@foreach ($weeklyStats as $stat)
						@php
							$height = $maxWeeklyValue > 0 ? ($stat['count'] / $maxWeeklyValue) * 100 : 0;
						@endphp
						<div class="chart-bar text-center" style="width: 40px; height: {{ max(8, $height) }}%;">
							<div class="small text-white" style="font-size: 0.7rem;">{{ $stat['count'] }}</div>
						</div>
					@endforeach
				</div>
				<div class="d-flex justify-content-between text-muted small mt-3">
					@foreach ($weeklyStats as $stat)
						<span>{{ $stat['label'] }}</span>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<!-- Pengingat Pengembalian -->
	<div class="col-md-6 col-lg-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h6 class="fw-bold mb-0">Pengingat Pengembalian</h6>
					<span class="badge text-bg-light">{{ $reminders['dueSoon']->count() + $reminders['overdue']->count() }} item</span>
				</div>

				@if($reminders['dueSoon']->isEmpty() && $reminders['overdue']->isEmpty())
					<p class="text-muted mb-0">Tidak ada pengingat saat ini.</p>
				@else
					@if($reminders['dueSoon']->isNotEmpty())
						<p class="text-muted text-uppercase small mb-2">Jatuh Tempo</p>
						@foreach ($reminders['dueSoon'] as $loan)
							@php
								$dueLabel = $loan->tanggal_kembali
									? \Illuminate\Support\Carbon::parse($loan->tanggal_kembali)->translatedFormat('d M Y H:i')
									: '-';
							@endphp
							<div class="p-3 rounded mb-2" style="background-color: #FEF3C7;">
								<div class="d-flex align-items-center">
									<i class="bi bi-exclamation-triangle text-warning me-2"></i>
									<div class="flex-grow-1">
										<h6 class="mb-0" style="font-size: 0.875rem;">{{ $loan->alat->nama_alat ?? '-' }}</h6>
										<small class="text-muted">{{ $loan->peminjam->name ?? 'Tidak diketahui' }} • {{ $dueLabel }}</small>
									</div>
								</div>
							</div>
						@endforeach
					@endif

					@if($reminders['overdue']->isNotEmpty())
						<p class="text-muted text-uppercase small mt-3 mb-2">Terlambat</p>
						@foreach ($reminders['overdue'] as $loan)
							@php
								$lateDays = $loan->tanggal_kembali
									? \Illuminate\Support\Carbon::parse($loan->tanggal_kembali)->diffInDays(now())
									: 0;
							@endphp
							<div class="p-3 rounded mb-2" style="background-color: #FEE2E2;">
								<div class="d-flex align-items-center">
									<i class="bi bi-x-circle text-danger me-2"></i>
									<div class="flex-grow-1">
										<h6 class="mb-0" style="font-size: 0.875rem;">{{ $loan->alat->nama_alat ?? '-' }}</h6>
										<small class="text-muted">{{ $loan->peminjam->name ?? 'Tidak diketahui' }} • Terlambat {{ $lateDays }} hari</small>
									</div>
								</div>
							</div>
						@endforeach
					@endif
				@endif
			</div>
		</div>
	</div>

	<!-- Ringkasan Pengembalian -->
	<div class="col-md-12 col-lg-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<h6 class="fw-bold mb-3">Ringkasan Pengembalian</h6>
				<div class="progress-circle mx-auto mb-3" style="background: conic-gradient(var(--primary-green) {{ $returnCompletionPercentage }}%, #E5E7EB 0);">
					<div class="progress-text">{{ $returnCompletionPercentage }}%</div>
				</div>
				<p class="text-center text-muted small mb-0">{{ number_format($totalPengembalian) }} pengembalian dari {{ number_format($totalPeminjaman) }} peminjaman</p>
			</div>
		</div>
	</div>
</div>
@endsection