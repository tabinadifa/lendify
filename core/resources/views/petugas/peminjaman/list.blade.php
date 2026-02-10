@extends('layouts.layout')

@section('title', 'Daftar Peminjaman - Lendify')

@push('styles')
<style>
	.page-title {
		color: #1E4D35;
	}

	.filter-card,
	.table-card {
		border: none;
		border-radius: 1rem;
		box-shadow: 0 4px 14px rgba(23, 56, 35, 0.08);
	}

	.status-badge {
		font-size: 0.85rem;
		font-weight: 600;
		text-transform: uppercase;
	}

	.table thead th {
		background-color: #F5F7FA;
		border-top: none;
		color: #5A7863;
		font-size: 0.9rem;
		text-transform: uppercase;
		letter-spacing: 0.04em;
	}

	.table > :not(caption) > * > * {
		vertical-align: middle;
	}

	.info-chip {
		font-size: 0.85rem;
		color: #5A7863;
	}
</style>
@endpush

@section('content')
		@php
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

		$perPageOptions = [5, 10, 25, 50];

		$formatDate = static function ($date) {
			return $date
				? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d M Y')
				: '—';
		};
	@endphp

	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<p class="text-uppercase text-muted mb-1 small">Modul Peminjaman</p>
			<h1 class="page-title fw-bold mb-0">Daftar Peminjaman</h1>
		</div>
		<div class="text-end">
			<span class="info-chip">
				Total Data: <strong>{{ number_format($peminjaman->total()) }}</strong>
			</span>
		</div>
	</div>

	{{-- Alert --}}
	@foreach (['error', 'info'] as $msg)
		@if (session($msg))
			<div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show border-0 shadow-sm mb-4">
				<div class="d-flex align-items-center">
					<i class="bi bi-{{ $msg === 'error' ? 'exclamation-triangle' : 'info-circle' }}-fill me-2"></i>
					<div>{{ session($msg) }}</div>
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif
	@endforeach

	<div class="card filter-card mb-4">
		<div class="card-body">
			<form method="GET" class="row g-3 align-items-end">
				<div class="col-md-2">
					<label for="per_page" class="form-label text-uppercase text-muted small">Per Halaman</label>
					<select id="per_page" name="per_page" class="form-select" onchange="this.form.submit()">
						@foreach ($perPageOptions as $option)
							<option value="{{ $option }}" @selected((int) request('per_page', 10) === $option)>
								{{ $option }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-6">
					<label for="search" class="form-label text-uppercase text-muted small">Kata Kunci</label>
					<input type="text" id="search" name="search" class="form-control"
						placeholder="Cari nama peminjam atau nama alat"
						value="{{ request('search') }}"
						onkeydown="if(event.key==='Enter'){this.form.submit()}">
				</div>
			</form>
		</div>
	</div>

	<div class="card table-card">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0">
					<thead>
						<tr>
							<th>Data Peminjam</th>
							<th>Nama Alat</th>
							<th>Tanggal Pinjam</th>
							<th>Tanggal Kembali</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($peminjaman as $item)
							<tr>
								<td>
									<div class="fw-semibold">{{ $item->peminjam->name }}</div>
									<div class="small text-muted">{{ $item->peminjam->email }}</div>
									<a href="{{ route('petugas.peminjaman.show', $item) }}" class="small fw-semibold text-decoration-none">
										Lihat detail
									</a>
								</td>
								<td class="fw-semibold">{{ $item->alat->nama_alat }}</td>
								<td>{{ $formatDate($item->tanggal_pinjam) }}</td>
								<td>{{ $formatDate($item->tanggal_kembali) }}</td>
								<td>
									<span class="badge status-badge {{ $statusBadges[$item->status] ?? 'bg-secondary' }}">
										{{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
									</span>
									@if ($item->status !== 'returned')
										<button type="button" class="btn btn-outline-success btn-sm ms-2"
											data-bs-toggle="modal" data-bs-target="#statusModal-{{ $item->id }}">
											Ubah Status
										</button>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="text-center py-5">
									<div class="text-muted">
										<i class="bi bi-inbox me-2"></i> Data peminjaman tidak ditemukan
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
			<div class="small text-muted mb-2 mb-md-0">
				@if ($peminjaman->total())
					Menampilkan {{ $peminjaman->firstItem() }} - {{ $peminjaman->lastItem() }} dari
					{{ $peminjaman->total() }} data
				@else
					Tidak ada data untuk ditampilkan
				@endif
			</div>
			{{ $peminjaman->onEachSide(1)->links('pagination::bootstrap-5') }}
		</div>
	</div>

		@foreach ($peminjaman as $item)
		@php
			$isModalReopened = old('peminjaman_id') && (int) old('peminjaman_id') === $item->id;
			$selectedStatus = $isModalReopened ? old('status') : $item->status;
			$reasonValue = $isModalReopened ? old('alasan_ditolak') : $item->alasan_ditolak;
				$shouldShowReason = $selectedStatus === 'rejected';
		@endphp

		@continue($item->status === 'returned')

		<div class="modal fade" id="statusModal-{{ $item->id }}" tabindex="-1"
			aria-labelledby="statusModalLabel-{{ $item->id }}" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form method="POST" action="{{ route('petugas.peminjaman.update-status', $item) }}">
						@csrf
						@method('PATCH')
						<input type="hidden" name="peminjaman_id" value="{{ $item->id }}">

						<div class="modal-header border-0">
							<h5 class="modal-title" id="statusModalLabel-{{ $item->id }}">
								Ubah Status Peminjaman
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
							<div class="mb-3">
								<label for="statusSelect-{{ $item->id }}" class="form-label">Status Peminjaman</label>
								<select id="statusSelect-{{ $item->id }}" name="status" class="form-select"
									data-reason-toggle="reasonField-{{ $item->id }}">
									@foreach ($allowedStatuses as $status)
										<option value="{{ $status }}" @selected($selectedStatus === $status)>
											{{ $statusLabels[$status] ?? ucfirst($status) }}
										</option>
									@endforeach
								</select>
							</div>

							<div class="mb-3 {{ $shouldShowReason ? '' : 'd-none' }}" id="reasonField-{{ $item->id }}">
								<label for="reasonTextarea-{{ $item->id }}" class="form-label">Alasan Penolakan</label>
								<textarea class="form-control" name="alasan_ditolak" id="reasonTextarea-{{ $item->id }}" rows="3"
									maxlength="255" placeholder="Tuliskan alasan penolakan secara singkat">{{ $reasonValue }}</textarea>
								<div class="form-text">Wajib diisi ketika memilih status ditolak (maks. 255 karakter).</div>
							</div>

							<div class="alert alert-info small mb-0">
								<i class="bi bi-info-circle-fill me-2"></i>
								Menyetujui peminjaman akan langsung mengurangi stok alat. Menolak peminjaman akan mengembalikan stok yang
								telah dialokasikan.
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
	@endforeach
@endsection

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const toggleReasonField = (select) => {
			const targetId = select.getAttribute('data-reason-toggle');
			if (!targetId) {
				return;
			}

			const wrapper = document.getElementById(targetId);
			if (!wrapper) {
				return;
			}

			const textarea = wrapper.querySelector('textarea');
			const shouldShow = select.value === 'rejected';

			wrapper.classList.toggle('d-none', !shouldShow);
			if (textarea) {
				textarea.required = shouldShow;
			}
		};

		document.querySelectorAll('[data-reason-toggle]').forEach((select) => {
			const modal = select.closest('.modal');

			select.addEventListener('change', () => toggleReasonField(select));

			if (modal) {
				modal.addEventListener('shown.bs.modal', () => toggleReasonField(select));
			}

			toggleReasonField(select);
		});

		const failedModalId = @json(old('peminjaman_id'));
		if (failedModalId) {
			const modalEl = document.getElementById(`statusModal-${failedModalId}`);
			if (modalEl) {
				const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
				modalInstance.show();
			}
		}
	});
</script>
@endpush