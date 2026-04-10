@extends('layouts.layout')

@section('title', 'Tambah Pengembalian - Petugas')

@section('content')
@php
$statusClasses = [
'pending' => 'text-bg-warning',
'approve' => 'text-bg-primary',
'approved' => 'text-bg-primary',
'borrowed' => 'text-bg-info',
'rejected' => 'text-bg-danger',
'returned' => 'text-bg-success',
];

$statusLabels = [
'pending' => 'Menunggu Persetujuan',
'approve' => 'Disetujui',
'approved' => 'Disetujui',
'borrowed' => 'Sedang Dipinjam',
'rejected' => 'Ditolak',
'returned' => 'Dikembalikan',
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
                    $statusKey = $peminjaman->status ?? 'pending';
                    $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                    $statusClass = $statusClasses[$statusKey] ?? 'text-bg-secondary';
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
                                {{ $peminjaman->peminjam->name ?? 'Peminjam tidak tersedia' }}
                            </div>
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

<form action="{{ route('petugas.pengembalian.store') }}" method="POST" class="row g-4">
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
        $statusKey = $peminjaman->status ?? 'pending';
        $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
        $statusClass = $statusClasses[$statusKey] ?? 'text-bg-secondary';
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
                            value="{{ date('Y-m-d') }}" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="kondisi_alat" class="form-label">Kondisi Alat</label>
                        <select name="kondisi_alat" id="kondisi_alat" class="form-select" required>
                            <option value="">Pilih kondisi</option>
                            <option value="baik" {{ old('kondisi_alat') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ old('kondisi_alat') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ old('kondisi_alat') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="hilang" {{ old('kondisi_alat') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>
                    <!-- Denda Kondisi Alat (dari petugas) -->
                    <div class="col-md-6">
                        <label for="denda_kondisi" class="form-label">Denda Kondisi Alat</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="denda_kondisi" name="denda_kondisi" class="form-control"
                                value="{{ old('denda_kondisi', 0) }}" min="0" step="1000">
                        </div>
                        <small class="text-muted">Denda karena kerusakan/kehilangan (isi sesuai kondisi).</small>
                    </div>
                    <!-- Status Pembayaran Denda -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status Pembayaran Denda</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">Pilih metode</option>
                            <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <!-- Metode Pembayaran -->
                    <div class="col-md-6">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                            <option value="">Pilih metode</option>
                            <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="QRIS" {{ old('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>

                    <!-- Tampilkan gambar QRIS jika metode = QRIS -->
                    <div class="col-12" id="qrisContainer" style="display: none;">
                        <div class="alert alert-info text-center p-3">
                            <img src="{{ asset('storage/uploads/qris.jpg') }}" alt="QRIS Code" style="max-width: 200px;" class="img-fluid rounded">
                            <p class="mt-2 mb-0">Scan QRIS untuk melakukan pembayaran</p>
                        </div>
                    </div>

                    <!-- Estimasi Total Denda (readonly) -->
                    <div class="col-md-6">
                        <label class="form-label">Estimasi Total Denda</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="estimasi_total_denda" class="form-control" readonly disabled>
                        </div>
                        <small class="text-muted" id="info_denda_telat">Denda telat otomatis @ Rp2.000/hari.</small>
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
                    <a href="{{ route('petugas.pengembalian.list') }}" class="btn btn-outline-secondary">
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

@include('petugas.pengembalian.partials.file-picker-modal', ['files' => $files])
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
        const dendaKondisiInput = document.getElementById('denda_kondisi');
        const estimasiTotalDendaSpan = document.getElementById('estimasi_total_denda');
        const infoDendaTelatSpan = document.getElementById('info_denda_telat');
        const metodeBayarSelect = document.getElementById('metode_pembayaran');
        const qrisContainer = document.getElementById('qrisContainer');
        const searchInput = document.getElementById('searchPeminjaman');
        const fileSelect = document.getElementById('file_bukti_pengembalian_id');
        const fileNameTarget = document.querySelector('[data-file-name]');
        const filePreviewTarget = document.querySelector('[data-file-preview]');
        const fileModalElement = document.getElementById('filePickerModal');
        const todayValue = '{{ $today }}';
        let fileModal = null;

        // Fungsi untuk toggle QRIS image
        function toggleQRIS() {
            if (metodeBayarSelect && qrisContainer) {
                if (metodeBayarSelect.value === 'QRIS') {
                    qrisContainer.style.display = 'block';
                } else {
                    qrisContainer.style.display = 'none';
                }
            }
        }

        // Panggil saat load dan saat select berubah
        toggleQRIS();
        if (metodeBayarSelect) {
            metodeBayarSelect.addEventListener('change', toggleQRIS);
        }

        // AJAX Upload Elements (sama seperti sebelumnya, pertahankan semua)
        const uploadForm = document.getElementById('modalUploadForm');
        const uploadButton = document.getElementById('uploadButton');
        const uploadSuccess = document.getElementById('uploadSuccess');
        const uploadError = document.getElementById('uploadError');
        const filesContainer = document.getElementById('filesContainer');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? document.querySelector('input[name="_token"]')?.value ?? '';
        const deleteRouteTemplate = @json(route('filemanager.delete', ['id' => '__ID__']));

        if (fileModalElement && window.bootstrap) {
            fileModal = new bootstrap.Modal(fileModalElement);
        }

        function hideFileAlerts() {
            if (uploadSuccess) uploadSuccess.classList.add('d-none');
            if (uploadError) uploadError.classList.add('d-none');
        }

        function showSuccessMessage(message) {
            if (!uploadSuccess) return;
            uploadSuccess.textContent = message;
            uploadSuccess.classList.remove('d-none');
            setTimeout(() => uploadSuccess && uploadSuccess.classList.add('d-none'), 3000);
        }

        function showErrorMessage(message) {
            if (!uploadError) return;
            uploadError.textContent = message;
            uploadError.classList.remove('d-none');
        }

        function renderEmptyFilesState() {
            if (!filesContainer) return;
            filesContainer.innerHTML = `
                    <div class="text-center py-4" id="emptyState">
                        <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Belum ada file yang dapat dipilih.</p>
                    </div>
                `;
        }

        function removeFileOption(fileId) {
            if (!fileSelect) return;
            const option = Array.from(fileSelect.options).find((opt) => opt.value === String(fileId));
            if (option) option.remove();
        }

        function attachFileActions(row) {
            if (!row) return;
            const pickButton = row.querySelector('[data-file-pick]');
            if (pickButton) {
                pickButton.addEventListener('click', () => {
                    const id = pickButton.dataset.fileId;
                    if (!id) return;
                    selectFileOption(id);
                    if (fileModal) fileModal.hide();
                });
            }
            const deleteButton = row.querySelector('[data-file-delete]');
            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    const fileId = deleteButton.dataset.fileId;
                    if (!fileId) return;
                    handleFileDelete(fileId, deleteButton);
                });
            }
        }

        async function handleFileDelete(fileId, triggerButton) {
            if (!csrfToken) {
                showErrorMessage('Token CSRF tidak ditemukan. Muat ulang halaman.');
                return;
            }
            const fileName = triggerButton?.dataset?.fileName ?? 'file ini';
            let confirmed = true;
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Hapus gambar?',
                    text: `Anda akan menghapus ${fileName}. Tindakan ini tidak bisa dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                });
                confirmed = result.isConfirmed;
            } else if (!window.confirm(`Hapus ${fileName}?`)) {
                confirmed = false;
            }
            if (!confirmed) return;
            const originalHtml = triggerButton.innerHTML;
            triggerButton.disabled = true;
            triggerButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            try {
                const response = await fetch(deleteRouteTemplate.replace('__ID__', fileId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                let data = {};
                try {
                    data = await response.json();
                } catch (e) {}
                if (!response.ok || !data.success) throw new Error(data.message || 'Gagal menghapus file.');
                const row = triggerButton.closest('[data-file-row]');
                if (row) row.remove();
                removeFileOption(fileId);
                if (fileSelect && fileSelect.value === String(fileId)) {
                    fileSelect.value = '';
                    fileSelect.dispatchEvent(new Event('change'));
                }
                const tbody = document.getElementById('filesTableBody');
                if (!tbody || !tbody.children.length) renderEmptyFilesState();
                showSuccessMessage('Gambar berhasil dihapus.');
                if (typeof Swal !== 'undefined') {
                    await Swal.fire({
                        title: 'Terhapus',
                        text: 'Gambar berhasil dihapus.',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                showErrorMessage(error.message || 'Gagal menghapus file.');
                if (typeof Swal !== 'undefined') Swal.fire({
                    title: 'Gagal',
                    text: error.message,
                    icon: 'error'
                });
            } finally {
                triggerButton.disabled = false;
                triggerButton.innerHTML = originalHtml;
            }
        }

        function formatDisplayDate(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (isNaN(date.getTime())) return '-';
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function highlightSelectedRow() {
            const selectedId = peminjamanSelect?.value ?? '';
            peminjamanRows.forEach(row => {
                if (row.dataset.peminjamanRow === selectedId) row.classList.add('table-active');
                else row.classList.remove('table-active');
            });
        }

        function updateSummary() {
            const option = peminjamanSelect?.selectedOptions?.[0];
            if (!option || !option.value) {
                Object.values(summaryFields).forEach(node => node && (node.textContent = '-'));
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
            summaryFields.pinjam && (summaryFields.pinjam.textContent = formatDisplayDate(option.dataset.pinjamDate));
            const overdueNow = option.dataset.overdue === 'true';
            let kembaliText = formatDisplayDate(option.dataset.dueDate);
            if (overdueNow && kembaliText !== '-') kembaliText += ' (Telat)';
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

        function hitungDendaTelat() {
            const option = peminjamanSelect?.selectedOptions?.[0];
            const dueDateStr = option?.dataset?.dueDate ?? '';
            const actualDateStr = tanggalPengembalianInput?.value ?? '';
            if (!dueDateStr || !actualDateStr) return 0;
            const dueDate = new Date(dueDateStr);
            const actualDate = new Date(actualDateStr);
            if (isNaN(dueDate) || isNaN(actualDate)) return 0;
            if (actualDate <= dueDate) return 0;
            const diffDays = Math.ceil((actualDate - dueDate) / (1000 * 60 * 60 * 24));
            return diffDays > 0 ? diffDays * 2000 : 0;
        }

        function updateEstimasiTotalDenda() {
            const dendaTelat = hitungDendaTelat();
            const dendaKondisi = parseFloat(dendaKondisiInput?.value) || 0;
            const total = dendaTelat + dendaKondisi;
            if (estimasiTotalDendaSpan) estimasiTotalDendaSpan.value = total.toLocaleString('id-ID');
            if (infoDendaTelatSpan) {
                if (dendaTelat > 0) {
                    const hariTelat = dendaTelat / 2000;
                    infoDendaTelatSpan.innerHTML = `⚠️ Denda telat: Rp ${dendaTelat.toLocaleString('id-ID')} (${hariTelat} hari × Rp2.000)`;
                    infoDendaTelatSpan.classList.add('text-danger');
                } else {
                    infoDendaTelatSpan.innerHTML = `Denda telat dihitung otomatis @ Rp2.000/hari.`;
                    infoDendaTelatSpan.classList.remove('text-danger');
                }
            }
        }

        function updateFilePreview() {
            const option = fileSelect?.selectedOptions?.[0];
            if (!option || !option.value) {
                if (filePreviewTarget) filePreviewTarget.innerHTML = '<span class="text-muted">Belum ada gambar dipilih</span>';
                if (fileNameTarget) fileNameTarget.textContent = 'Belum ada gambar dipilih';
                return;
            }
            if (filePreviewTarget) {
                const img = document.createElement('img');
                img.src = option.dataset.preview ?? '';
                img.alt = option.dataset.name ?? 'Preview file';
                filePreviewTarget.innerHTML = '';
                filePreviewTarget.appendChild(img);
            }
            if (fileNameTarget) fileNameTarget.textContent = option.dataset.name ?? 'File terpilih';
        }

        function selectPeminjaman(id) {
            if (!peminjamanSelect) return;
            peminjamanSelect.value = id;
            peminjamanSelect.dispatchEvent(new Event('change'));
        }

        function selectFileOption(id) {
            if (!fileSelect) return;
            fileSelect.value = id;
            fileSelect.dispatchEvent(new Event('change'));
        }

        function addFileToTable(file) {
            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.remove();
            let tbody = document.getElementById('filesTableBody');
            if (!tbody) {
                const container = document.getElementById('filesContainer');
                if (container) {
                    container.innerHTML = `
                            <div class="table-responsive">
                                <table class="table align-middle" id="filesTable">
                                    <thead class="table-light">
                                        <tr><th style="width:80px">Preview</th><th>Nama File</th><th class="text-end">Aksi</th></tr>
                                    </thead>
                                    <tbody id="filesTableBody"></tbody>
                                </table>
                            </div>
                        `;
                    tbody = document.getElementById('filesTableBody');
                }
            }
            if (!tbody) return;
            const row = document.createElement('tr');
            row.dataset.fileRow = String(file.id);
            const previewPath = file.path || file.file_path;
            const fileName = file.nama_file || file.file_name || 'Tanpa nama';
            row.innerHTML = `
                    <td><div class="rounded overflow-hidden border" data-preview-box style="width:64px;height:64px;cursor:zoom-in;"><img src="${previewPath}" alt="${fileName}" class="w-100 h-100 object-fit-cover"></div></td>
                    <td><div class="fw-semibold">${fileName}</div><div class="text-muted small">ID: ${file.id}</div></td>
                    <td class="text-end"><div class="d-flex justify-content-end gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" data-preview-trigger>Lihat</button><button type="button" class="btn btn-sm btn-outline-primary" data-file-pick data-file-id="${file.id}">Gunakan</button><button type="button" class="btn btn-sm btn-outline-danger" data-file-delete data-file-id="${file.id}">Hapus</button></div></td>
                `;
            tbody.insertBefore(row, tbody.firstChild);
            const previewBox = row.querySelector('[data-preview-box]');
            if (previewBox) {
                previewBox.setAttribute('data-file-preview', '');
                previewBox.setAttribute('data-file-url', previewPath);
                previewBox.setAttribute('data-file-name', fileName);
            }
            const previewTrigger = row.querySelector('[data-preview-trigger]');
            if (previewTrigger) {
                previewTrigger.setAttribute('data-file-preview', '');
                previewTrigger.setAttribute('data-file-url', previewPath);
                previewTrigger.setAttribute('data-file-name', fileName);
            }
            const deleteButton = row.querySelector('[data-file-delete]');
            if (deleteButton) deleteButton.dataset.fileName = fileName;
            attachFileActions(row);
        }

        function addFileToSelect(file) {
            if (!fileSelect) return;
            const option = document.createElement('option');
            const previewPath = file.path || file.file_path;
            const fileName = file.nama_file || file.file_name || 'Tanpa nama';
            option.value = file.id;
            option.dataset.preview = previewPath;
            option.dataset.name = fileName;
            option.textContent = fileName;
            if (fileSelect.options.length > 1) fileSelect.insertBefore(option, fileSelect.options[1]);
            else fileSelect.appendChild(option);
        }

        if (uploadForm) {
            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(uploadForm);
                uploadButton.disabled = true;
                uploadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
                hideFileAlerts();
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
                        showSuccessMessage('Gambar berhasil diupload!');
                        uploadForm.reset();
                        if (data.file) {
                            addFileToTable(data.file);
                            addFileToSelect(data.file);
                            selectFileOption(data.file.id);
                        }
                    } else throw new Error(data.message || 'Upload gagal');
                } catch (error) {
                    showErrorMessage(error.message || 'Gagal mengupload file.');
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
                updateEstimasiTotalDenda();
            });
        }
        peminjamanRows.forEach(row => {
            row.addEventListener('click', (event) => {
                if (event.target.closest('button')) return;
                selectPeminjaman(row.dataset.peminjamanRow);
            });
        });
        document.querySelectorAll('[data-peminjaman-trigger]').forEach(button => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                selectPeminjaman(button.dataset.peminjamanTrigger);
            });
        });
        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                const keyword = event.target.value.toLowerCase();
                peminjamanRows.forEach(row => {
                    const text = row.textContent?.toLowerCase() ?? '';
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
        if (tanggalPengembalianInput) {
            tanggalPengembalianInput.min = todayValue;
            if (tanggalPengembalianInput.value && tanggalPengembalianInput.value < todayValue) tanggalPengembalianInput.value = todayValue;
            tanggalPengembalianInput.addEventListener('change', () => {
                if (tanggalPengembalianInput.value && tanggalPengembalianInput.value < todayValue) tanggalPengembalianInput.value = todayValue;
                updateEstimasiTotalDenda();
            });
        }
        if (dendaKondisiInput) {
            dendaKondisiInput.addEventListener('input', updateEstimasiTotalDenda);
        }
        if (fileSelect) fileSelect.addEventListener('change', updateFilePreview);
        document.querySelectorAll('[data-open-file-modal]').forEach(button => {
            button.addEventListener('click', () => {
                if (fileModal) fileModal.show();
            });
        });
        document.querySelectorAll('[data-file-row]').forEach(row => attachFileActions(row));
        highlightSelectedRow();
        updateSummary();
        updateEstimasiTotalDenda();
        updateFilePreview();
    });
</script>
@endpush