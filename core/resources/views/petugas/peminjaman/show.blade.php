@extends('layouts.layout')

@section('title', 'Detail Peminjaman - Petugas')

@section('content')
	@php
		$statusLabels = [
			'pending' => 'Menunggu Persetujuan',
			'approve' => 'Disetujui',
			'rejected' => 'Ditolak',
			'returned' => 'Dikembalikan',
		];

		$badge = match ($peminjaman->status) {
			'approve' => 'success',
			'rejected' => 'danger',
			'pending' => 'warning',
			'returned' => 'secondary',
			default => 'secondary',
		};

		$formatDate = static function ($date, $withTime = false) {
			if (!$date) {
				return '-';
			}

			$instance = \Illuminate\Support\Carbon::parse($date);
			return $withTime
				? $instance->translatedFormat('d M Y H:i')
				: $instance->translatedFormat('d M Y');
		};
	@endphp

	<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
		<div>
			<h2 class="fw-bold mb-1">Detail Peminjaman</h2>
			<p class="text-muted mb-0">Pantau status, jadwal, dan informasi peminjam dari satu tempat.</p>
		</div>
		<div class="d-flex flex-wrap gap-2">
			<a href="{{ route('petugas.peminjaman.list') }}" class="btn btn-outline-secondary">
				Kembali ke Daftar
			</a>
			@if ($peminjaman->status !== 'returned')
				<button type="button" class="btn btn-success" data-bs-toggle="modal"
					data-bs-target="#statusModal-{{ $peminjaman->id }}">
					Ubah Status
				</button>
			@endif
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-7">
			<div class="card border-0 shadow-sm rounded-4 mb-4">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Informasi Peminjaman</h5>
					<table class="table table-borderless mb-0">
						<tr>
							<th width="35%" class="text-muted">Status</th>
							<td>
								<span class="badge bg-{{ $badge }}">
									{{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
								</span>
							</td>
						</tr>
						<tr>
							<th class="text-muted">Tanggal Pinjam</th>
							<td>{{ $formatDate($peminjaman->tanggal_pinjam, true) }}</td>
						</tr>
						<tr>
							<th class="text-muted">Tanggal Kembali</th>
							<td>{{ $formatDate($peminjaman->tanggal_kembali, true) }}</td>
						</tr>
						<tr>
							<th class="text-muted">Total Alat</th>
							<td>{{ number_format($peminjaman->total_alat) }} Unit</td>
						</tr>
					</table>
				</div>
			</div>

			@if ($peminjaman->status === 'rejected')
				<div class="card border-0 shadow-sm rounded-4">
					<div class="card-body">
						<h5 class="fw-semibold text-danger mb-2">Alasan Penolakan</h5>
						<p class="mb-0">{{ $peminjaman->alasan_ditolak ?? 'Tidak ada alasan tercatat.' }}</p>
					</div>
				</div>
			@endif
		</div>

		<div class="col-lg-5">
			<div class="card border-0 shadow-sm rounded-4 mb-4">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Data Peminjam</h5>
					<p class="mb-1 fw-semibold">{{ $peminjaman->peminjam->name ?? '-' }}</p>
					<p class="text-muted mb-1">Username: {{ $peminjaman->peminjam->username ?? '-' }}</p>
					<p class="text-muted mb-0">Email: {{ $peminjaman->peminjam->email ?? '-' }}</p>
				</div>
			</div>

			<div class="card border-0 shadow-sm rounded-4">
				<div class="card-body">
					<h5 class="fw-semibold mb-3">Detail Alat</h5>
					<p class="mb-1 fw-semibold">{{ $peminjaman->alat->nama_alat ?? '-' }}</p>
					@if ($peminjaman->alat?->kategori)
						<span class="badge text-bg-secondary">{{ $peminjaman->alat->kategori->nama_kategori }}</span>
					@else
						<span class="text-muted small">Kategori belum diatur</span>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="statusModal-{{ $peminjaman->id }}" tabindex="-1"
		aria-labelledby="statusModalLabel-{{ $peminjaman->id }}" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<form method="POST" action="{{ route('petugas.peminjaman.update-status', $peminjaman) }}">
					@csrf
					@method('PATCH')
					<input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

					<div class="modal-header border-0">
						<h5 class="modal-title" id="statusModalLabel-{{ $peminjaman->id }}">
							Ubah Status Peminjaman
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>

					<div class="modal-body">
						<div class="mb-3">
							<label for="statusSelect-{{ $peminjaman->id }}" class="form-label">Status Peminjaman</label>
							<select id="statusSelect-{{ $peminjaman->id }}" name="status" class="form-select"
								data-reason-toggle="reasonField-{{ $peminjaman->id }}">
								@foreach ($allowedStatuses as $status)
									<option value="{{ $status }}" @selected($peminjaman->status === $status)>
										{{ $statusLabels[$status] ?? ucfirst($status) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="mb-3 d-none" id="reasonField-{{ $peminjaman->id }}">
							<label for="reasonTextarea-{{ $peminjaman->id }}" class="form-label">Alasan Penolakan</label>
							<textarea class="form-control" name="alasan_ditolak" id="reasonTextarea-{{ $peminjaman->id }}" rows="3"
								maxlength="255" placeholder="Tuliskan alasan penolakan secara singkat">{{ $peminjaman->alasan_ditolak }}</textarea>
							<div class="form-text">Wajib diisi ketika memilih status ditolak (maks. 255 karakter).</div>
						</div>

						<div class="alert alert-info small mb-0">
							<i class="bi bi-info-circle-fill me-2"></i>
							Menyetujui peminjaman akan langsung mengurangi stok alat. Menolak peminjaman akan mengembalikan stok
							yang telah dialokasikan.
						</div>
					</div>

					<div class="modal-footer border-0">
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-success">Simpan Perubahan</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const select = document.querySelector('[data-reason-toggle="reasonField-{{ $peminjaman->id }}"]');
			const wrapper = document.getElementById('reasonField-{{ $peminjaman->id }}');
			const textarea = document.getElementById('reasonTextarea-{{ $peminjaman->id }}');

			function toggleReasonField() {
				const shouldShow = select && select.value === 'rejected';
				if (wrapper) {
					wrapper.classList.toggle('d-none', !shouldShow);
				}
				if (textarea) {
					textarea.required = !!shouldShow;
				}
			}

			if (select) {
				select.addEventListener('change', toggleReasonField);
				toggleReasonField();
			}
		});
	</script>
@endpush
