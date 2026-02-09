@extends('layouts.layout')

@section('title', 'Tambah Pengembalian - Lendify')

@section('content')
    @php
        $statusClasses = [
            'pending' => 'text-bg-warning',
            'approved' => 'text-bg-primary',
            'borrowed' => 'text-bg-info',
            'rejected' => 'text-bg-danger',
            'returned' => 'text-bg-success',
        ];
        $today = now()->format('Y-m-d');
    @endphp

    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tambah Pengembalian</h2>
            <p class="text-muted mb-0">Pilih peminjaman yang ingin diselesaikan lalu lengkapi detail pengembalian.</p>
        </div>
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

    @if ($peminjamans->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
            Belum ada peminjaman yang tersedia untuk diproses. Tambahkan peminjaman terlebih dahulu.
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div>
                        <h5 class="fw-semibold mb-1">Pilih Peminjaman</h5>
                        <p class="text-muted small mb-0" data-selected-alert> Klik baris peminjaman untuk memilihnya.</p>
                    </div>
                    <div class="ms-auto" style="min-width: 240px;">
                        <input type="search" class="form-control" id="searchPeminjaman"
                            placeholder="Cari peminjam atau alat">
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-peminjaman-table>
                            @foreach ($peminjamans as $peminjaman)
                                @php
                                    $pinjamDate = $peminjaman->tanggal_pinjam
                                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)
                                        : null;
                                    $dueDate = $peminjaman->tanggal_kembali
                                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)
                                        : null;
                                    $isOverdueNow = $dueDate ? $dueDate->isPast() : false;
                                    $statusLabel = ucfirst($peminjaman->status ?? 'pending');
                                    $statusClass = $statusClasses[$peminjaman->status] ?? 'text-bg-secondary';
                                @endphp
                                <tr class="peminjaman-row" data-peminjaman-row="{{ $peminjaman->id }}"
                                    data-peminjaman-nama="{{ $peminjaman->peminjam->name ?? '-' }}"
                                    data-peminjaman-username="{{ $peminjaman->peminjam->username ?? '-' }}"
                                    data-alat="{{ $peminjaman->alat->nama_alat ?? '-' }}"
                                    data-pinjam-date="{{ $pinjamDate?->format('Y-m-d') }}"
                                    data-due-date="{{ $dueDate?->format('Y-m-d') }}" data-status-label="{{ $statusLabel }}"
                                    data-status-class="{{ $statusClass }}"
                                    data-overdue="{{ $isOverdueNow ? 'true' : 'false' }}">
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $peminjaman->peminjam->name ?? 'Peminjam tidak tersedia' }}</div>
                                        <div class="text-muted small">{{ $peminjaman->peminjam->username ?? '-' }}</div>
                                    </td>
                                    <td>{{ $peminjaman->alat->nama_alat ?? 'Alat tidak ditemukan' }}</td>
                                    <td>{{ $pinjamDate?->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        @if ($dueDate)
                                            {{ $dueDate->format('d M Y') }}
                                            @if ($isOverdueNow)
                                                <span class="badge text-bg-danger ms-2">Telat</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-peminjaman-trigger="{{ $peminjaman->id }}">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="selected-summary border rounded-4 p-3 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-1">Detail Peminjaman Terpilih</h6>
                        </div>
                        <span class="badge text-bg-secondary" data-summary-status>Belum dipilih</span>
                    </div>
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Peminjam</p>
                            <p class="fw-semibold mb-0" data-summary="peminjam">-</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Alat</p>
                            <p class="fw-semibold mb-0" data-summary="alat">-</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Tanggal Pinjam</p>
                            <p class="fw-semibold mb-0" data-summary="pinjam">-</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Batas Kembali</p>
                            <p class="fw-semibold mb-0" data-summary="kembali">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('pengembalian.store') }}" method="POST" class="row g-4">
        @csrf

        <select name="peminjaman_id" id="peminjaman_id" class="form-select d-none" aria-hidden="true" tabindex="-1">
            <option value="" disabled {{ old('peminjaman_id') ? '' : 'selected' }}>Pilih peminjaman</option>
            @foreach ($peminjamans as $peminjaman)
                @php
                    $pinjamDate = $peminjaman->tanggal_pinjam
                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)
                        : null;
                    $dueDate = $peminjaman->tanggal_kembali
                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)
                        : null;
                    $statusLabel = ucfirst($peminjaman->status ?? 'pending');
                    $statusClass = $statusClasses[$peminjaman->status] ?? 'text-bg-secondary';
                    $isOverdueNow = $dueDate ? $dueDate->isPast() : false;
                @endphp
                <option value="{{ $peminjaman->id }}" data-peminjam="{{ $peminjaman->peminjam->name ?? '-' }}"
                    data-username="{{ $peminjaman->peminjam->username ?? '-' }}"
                    data-alat="{{ $peminjaman->alat->nama_alat ?? '-' }}"
                    data-pinjam-date="{{ $pinjamDate?->format('Y-m-d') }}"
                    data-due-date="{{ $dueDate?->format('Y-m-d') }}" data-status-label="{{ $statusLabel }}"
                    data-status-class="{{ $statusClass }}" data-overdue="{{ $isOverdueNow ? 'true' : 'false' }}"
                    {{ old('peminjaman_id') == $peminjaman->id ? 'selected' : '' }}>
                    {{ $peminjaman->peminjam->name ?? 'Peminjam tidak tersedia' }} -
                    {{ $peminjaman->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                </option>
            @endforeach
        </select>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Informasi Pengembalian</h5>
                    <div class="alert alert-info mb-4" role="alert">
                        <i class="bi bi-info-circle me-2"></i>Silakan pilih peminjaman melalui tabel di atas sebelum
                        menyimpan data.
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" id="tanggal_pengembalian" name="tanggal_pengembalian" class="form-control"
                                value="{{ old('tanggal_pengembalian') }}" min="{{ $today }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="kondisi_alat" class="form-label">Kondisi Alat</label>
                            <input type="text" id="kondisi_alat" name="kondisi_alat" class="form-control"
                                placeholder="Contoh: Baik, lengkap" value="{{ old('kondisi_alat') }}" required>
                        </div>
                        <div class="col-12">
                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" id="catatan" rows="4" class="form-control"
                                placeholder="Catatan tambahan mengenai pengembalian">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column gap-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="denda" class="form-label mb-0">Denda</label>
                            <span class="badge text-bg-secondary" data-denda-badge>Opsional</span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="denda" name="denda" class="form-control"
                                value="{{ old('denda', 0) }}" min="0" step="1000">
                        </div>
                        <small class="text-muted" data-denda-message>Isi jika ada kerusakan, kehilangan, atau
                            keterlambatan.</small>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" for="file_bukti_pengembalian_id">Gambar Bukti</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-open-file-modal>Buka
                                    Direktori</button>
                            </div>
                        </div>
                        <select name="file_bukti_pengembalian_id" id="file_bukti_pengembalian_id"
                            class="form-select d-none" aria-hidden="true">
                            <option value="" {{ old('file_bukti_pengembalian_id') ? '' : 'selected' }}>Pilih file
                            </option>
                            @foreach ($files as $file)
                                @php
                                    $previewPath = asset($file->path ?? $file->file_path);
                                    $fileName = $file->nama_file ?? ($file->file_name ?? 'Tanpa nama');
                                @endphp
                                <option value="{{ $file->id }}" data-preview="{{ $previewPath }}"
                                    data-name="{{ $fileName }}"
                                    {{ (string) old('file_bukti_pengembalian_id') === (string) $file->id ? 'selected' : '' }}>
                                    {{ $fileName }}
                                </option>
                            @endforeach
                        </select>
                        <div class="selected-preview" data-file-preview>
                            <span class="text-muted">Belum ada gambar dipilih</span>
                        </div>
                        <p class="small text-muted mt-2" data-file-name>Belum ada gambar dipilih</p>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-auto">
                        <a href="{{ route('pengembalian.list') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('pengembalian.partials.file-picker-modal', ['files' => $files])
@endsection

@push('styles')
    <style>
        .peminjaman-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .peminjaman-row.table-active {
            background-color: #f0f7f4;
        }

        .selected-summary {
            background: #f9fafb;
        }

        .selected-preview {
            width: 100%;
            height: 180px;
            border: 1px dashed #d1d5db;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .selected-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const peminjamanSelect = document.getElementById('peminjaman_id');
            const peminjamanRows = document.querySelectorAll('[data-peminjaman-row]');
            const summaryFields = {
                peminjam: document.querySelector('[data-summary="peminjam"]'),
                alat: document.querySelector('[data-summary="alat"]'),
                pinjam: document.querySelector('[data-summary="pinjam"]'),
                kembali: document.querySelector('[data-summary="kembali"]'),
            };
            const summaryStatus = document.querySelector('[data-summary-status]');
            const selectedAlert = document.querySelector('[data-selected-alert]');
            const tanggalPengembalianInput = document.getElementById('tanggal_pengembalian');
            const dendaInput = document.getElementById('denda');
            const dendaMessage = document.querySelector('[data-denda-message]');
            const dendaBadge = document.querySelector('[data-denda-badge]');
            const searchInput = document.getElementById('searchPeminjaman');
            const fileSelect = document.getElementById('file_bukti_pengembalian_id');
            const fileNameTarget = document.querySelector('[data-file-name]');
            const filePreviewTarget = document.querySelector('[data-file-preview]');
            const fileModalElement = document.getElementById('filePickerModal');
            const todayValue = '{{ $today }}';
            let fileModal = null;

            // AJAX Upload Elements
            const uploadForm = document.getElementById('modalUploadForm');
            const uploadButton = document.getElementById('uploadButton');
            const uploadSuccess = document.getElementById('uploadSuccess');
            const uploadError = document.getElementById('uploadError');

            if (fileModalElement && window.bootstrap) {
                fileModal = new bootstrap.Modal(fileModalElement);
            }

            function formatDisplayDate(value) {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) {
                    return '-';
                }
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function highlightSelectedRow() {
                const selectedId = peminjamanSelect?.value ?? '';
                peminjamanRows.forEach((row) => {
                    if (row.dataset.peminjamanRow === selectedId) {
                        row.classList.add('table-active');
                    } else {
                        row.classList.remove('table-active');
                    }
                });
            }

            function updateSummary() {
                const option = peminjamanSelect?.selectedOptions?.[0];
                if (!option || !option.value) {
                    Object.values(summaryFields).forEach((node) => node && (node.textContent = '-'));
                    if (summaryStatus) {
                        summaryStatus.textContent = 'Belum dipilih';
                        summaryStatus.className = 'badge text-bg-secondary';
                    }
                    if (selectedAlert) {
                        selectedAlert.classList.remove('text-danger');
                        selectedAlert.textContent = 'Klik baris peminjaman untuk memilihnya.';
                    }
                    return;
                }

                summaryFields.peminjam && (summaryFields.peminjam.textContent = option.dataset.peminjam ?? '-');
                summaryFields.alat && (summaryFields.alat.textContent = option.dataset.alat ?? '-');
                summaryFields.pinjam && (summaryFields.pinjam.textContent = formatDisplayDate(option.dataset
                    .pinjamDate));
                const overdueNow = option.dataset.overdue === 'true';
                let kembaliText = formatDisplayDate(option.dataset.dueDate);
                if (overdueNow && kembaliText !== '-') {
                    kembaliText += ' (Telat)';
                }
                summaryFields.kembali && (summaryFields.kembali.textContent = kembaliText);

                if (summaryStatus) {
                    summaryStatus.textContent = option.dataset.statusLabel ?? 'Peminjaman';
                    summaryStatus.className = 'badge ' + (option.dataset.statusClass ?? 'text-bg-secondary');
                }

                if (selectedAlert) {
                    selectedAlert.classList.remove('text-danger');
                    selectedAlert.textContent = 'Peminjaman sudah dipilih. Lengkapi data pengembalian.';
                }
            }

            function updatePenaltyState() {
                const option = peminjamanSelect?.selectedOptions?.[0];
                const dueValue = option?.dataset?.dueDate ?? '';
                const actualValue = tanggalPengembalianInput?.value ?? '';
                if (!dueValue || !actualValue) {
                    if (dendaInput) {
                        dendaInput.required = false;
                    }
                    if (dendaMessage) {
                        dendaMessage.textContent = 'Isi jika ada kerusakan, kehilangan, atau keterlambatan.';
                        dendaMessage.classList.remove('text-danger');
                        dendaMessage.classList.add('text-muted');
                    }
                    if (dendaBadge) {
                        dendaBadge.textContent = 'Opsional';
                        dendaBadge.classList.remove('text-bg-danger');
                        dendaBadge.classList.add('text-bg-secondary');
                    }
                    return;
                }

                const dueDate = new Date(dueValue);
                const actualDate = new Date(actualValue);
                const isLate = !Number.isNaN(dueDate.getTime()) && !Number.isNaN(actualDate.getTime()) && actualDate
                    .getTime() > dueDate.getTime();

                if (isLate) {
                    if (dendaInput) {
                        dendaInput.required = true;
                    }
                    if (dendaMessage) {
                        dendaMessage.textContent = 'Tanggal pengembalian melewati batas, wajib isi nominal denda.';
                        dendaMessage.classList.remove('text-muted');
                        dendaMessage.classList.add('text-danger');
                    }
                    if (dendaBadge) {
                        dendaBadge.textContent = 'Wajib karena telat';
                        dendaBadge.classList.remove('text-bg-secondary');
                        dendaBadge.classList.add('text-bg-danger');
                    }
                } else {
                    if (dendaInput) {
                        dendaInput.required = false;
                    }
                    if (dendaMessage) {
                        dendaMessage.textContent = 'Isi jika ada kerusakan, kehilangan, atau keterlambatan.';
                        dendaMessage.classList.remove('text-danger');
                        dendaMessage.classList.add('text-muted');
                    }
                    if (dendaBadge) {
                        dendaBadge.textContent = 'Opsional';
                        dendaBadge.classList.remove('text-bg-danger');
                        dendaBadge.classList.add('text-bg-secondary');
                    }
                }
            }

            function updateFilePreview() {
                const option = fileSelect?.selectedOptions?.[0];
                if (!option || !option.value) {
                    if (filePreviewTarget) {
                        filePreviewTarget.innerHTML = '<span class="text-muted">Belum ada gambar dipilih</span>';
                    }
                    if (fileNameTarget) {
                        fileNameTarget.textContent = 'Belum ada gambar dipilih';
                    }
                    return;
                }

                if (filePreviewTarget) {
                    const img = document.createElement('img');
                    img.src = option.dataset.preview ?? '';
                    img.alt = option.dataset.name ?? 'Preview file';
                    filePreviewTarget.innerHTML = '';
                    filePreviewTarget.appendChild(img);
                }

                if (fileNameTarget) {
                    fileNameTarget.textContent = option.dataset.name ?? 'File terpilih';
                }
            }

            function selectPeminjaman(id) {
                if (!peminjamanSelect) {
                    return;
                }
                peminjamanSelect.value = id;
                peminjamanSelect.dispatchEvent(new Event('change'));
            }

            function selectFileOption(id) {
                if (!fileSelect) {
                    console.error('fileSelect element not found');
                    return;
                }
                fileSelect.value = id;
                fileSelect.dispatchEvent(new Event('change'));
            }

            function addFileToTable(file) {
                // Remove empty state if exists
                const emptyState = document.getElementById('emptyState');
                if (emptyState) {
                    emptyState.remove();
                }

                // Get or create table body
                let tbody = document.getElementById('filesTableBody');

                // Create table if doesn't exist
                if (!tbody) {
                    const container = document.getElementById('filesContainer');
                    if (container) {
                        container.innerHTML = `
					<div class="table-responsive">
						<table class="table align-middle" id="filesTable">
							<thead class="table-light">
								<tr>
									<th style="width: 80px;">Preview</th>
									<th>Nama File</th>
									<th>Tanggal</th>
									<th class="text-end">Aksi</th>
								</tr>
							</thead>
							<tbody id="filesTableBody"></tbody>
						</table>
					</div>
				`;
                        tbody = document.getElementById('filesTableBody');
                    }
                }

                if (!tbody) {
                    console.error('Could not create or find table body');
                    return;
                }

                const row = document.createElement('tr');
                const previewPath = file.path || file.file_path;
                const fileName = file.nama_file || file.file_name || 'Tanpa nama';
                const date = new Date(file.created_at || Date.now());
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                row.innerHTML = `
			<td>
				<div class="rounded overflow-hidden border" style="width: 64px; height: 64px;">
					<img src="${previewPath}" alt="${fileName}" class="w-100 h-100 object-fit-cover">
				</div>
			</td>
			<td>
				<div class="fw-semibold">${fileName}</div>
				<div class="text-muted small">ID: ${file.id}</div>
			</td>
			<td>${formattedDate}</td>
			<td class="text-end">
				<button type="button" class="btn btn-sm btn-outline-primary" data-file-pick data-file-id="${file.id}">
					Gunakan
				</button>
			</td>
		`;

                tbody.insertBefore(row, tbody.firstChild);

                // Attach event listener to new button
                const pickButton = row.querySelector('[data-file-pick]');
                if (pickButton) {
                    pickButton.addEventListener('click', () => {
                        selectFileOption(file.id);
                        if (fileModal) {
                            fileModal.hide();
                        }
                    });
                }
            }

            function addFileToSelect(file) {
                if (!fileSelect) {
                    console.error('fileSelect element not found');
                    return;
                }

                const option = document.createElement('option');
                const previewPath = file.path || file.file_path;
                const fileName = file.nama_file || file.file_name || 'Tanpa nama';

                option.value = file.id;
                option.dataset.preview = previewPath;
                option.dataset.name = fileName;
                option.textContent = fileName;

                // Insert after the first option (placeholder)
                if (fileSelect.options.length > 1) {
                    fileSelect.insertBefore(option, fileSelect.options[1]);
                } else {
                    fileSelect.appendChild(option);
                }
            }

            // AJAX Upload Handler
            if (uploadForm) {
                uploadForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const formData = new FormData(uploadForm);
                    uploadButton.disabled = true;
                    uploadButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

                    // Hide previous messages
                    if (uploadSuccess) uploadSuccess.classList.add('d-none');
                    if (uploadError) uploadError.classList.add('d-none');

                    try {
                        const response = await fetch('{{ route('filemanager.upload') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Show success message
                            if (uploadSuccess) {
                                uploadSuccess.classList.remove('d-none');
                            }
                            uploadForm.reset();

                            // Add new file to table and select
                            if (data.file) {
                                addFileToTable(data.file);
                                addFileToSelect(data.file);

                                // Automatically select the newly uploaded file
                                selectFileOption(data.file.id);
                            }

                            // Hide success message after 3 seconds
                            setTimeout(() => {
                                if (uploadSuccess) {
                                    uploadSuccess.classList.add('d-none');
                                }
                            }, 3000);
                        } else {
                            throw new Error(data.message || 'Upload gagal');
                        }
                    } catch (error) {
                        if (uploadError) {
                            uploadError.textContent = error.message || 'Gagal mengupload file.';
                            uploadError.classList.remove('d-none');
                        }
                        console.error('Upload error:', error);
                    } finally {
                        uploadButton.disabled = false;
                        uploadButton.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload';
                    }
                });
            }

            if (peminjamanSelect) {
                peminjamanSelect.addEventListener('change', () => {
                    highlightSelectedRow();
                    updateSummary();
                    updatePenaltyState();
                });
            }

            peminjamanRows.forEach((row) => {
                row.addEventListener('click', (event) => {
                    if (event.target.closest('button')) {
                        return;
                    }
                    selectPeminjaman(row.dataset.peminjamanRow);
                });
            });

            document.querySelectorAll('[data-peminjaman-trigger]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    selectPeminjaman(button.dataset.peminjamanTrigger);
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', (event) => {
                    const keyword = event.target.value.toLowerCase();
                    peminjamanRows.forEach((row) => {
                        const text = row.textContent?.toLowerCase() ?? '';
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });
            }

            if (tanggalPengembalianInput) {
                tanggalPengembalianInput.min = todayValue;
                if (tanggalPengembalianInput.value && tanggalPengembalianInput.value < todayValue) {
                    tanggalPengembalianInput.value = todayValue;
                }
                tanggalPengembalianInput.addEventListener('change', () => {
                    if (tanggalPengembalianInput.value && tanggalPengembalianInput.value < todayValue) {
                        tanggalPengembalianInput.value = todayValue;
                    }
                    updatePenaltyState();
                });
            }

            if (fileSelect) {
                fileSelect.addEventListener('change', updateFilePreview);
            }

            document.querySelectorAll('[data-open-file-modal]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (fileModal) {
                        fileModal.show();
                    }
                });
            });

            document.querySelectorAll('[data-file-pick]').forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.dataset.fileId;
                    if (!id) {
                        return;
                    }
                    selectFileOption(id);
                    if (fileModal) {
                        fileModal.hide();
                    }
                });
            });

            // Initialize state on load
            highlightSelectedRow();
            updateSummary();
            updatePenaltyState();
            updateFilePreview();
        });
    </script>
@endpush
