@extends('layouts.layout')

@section('title', 'Edit Alat - Lendify')

@section('content')
@php
    $selectedFileId      = old('gambar_alat_id', $alat->gambar_alat_id);
    $selectedFile        = $selectedFileId ? $files->firstWhere('id', $selectedFileId) : null;
    $selectedFilePreview = $selectedFile ? asset($selectedFile->file_path) : null;
    $selectedFileName    = $selectedFile ? ($selectedFile->file_name ?? 'Tanpa nama') : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Alat</h2>
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

<form action="{{ route('alat.update', $alat->id) }}" method="POST" class="row g-4">
    @csrf
    @method('PUT')

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Informasi Alat</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="kategori_id" class="form-label">Kategori Alat</label>
                        <select name="kategori_id" id="kategori_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoriAlats as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old('kategori_id', $alat->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_alat" class="form-label">Nama Alat</label>
                        <input type="text" id="nama_alat" name="nama_alat" class="form-control"
                            value="{{ old('nama_alat', $alat->nama_alat) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                        <input type="number" id="jumlah_stok" name="jumlah_stok" class="form-control"
                            min="0" value="{{ old('jumlah_stok', $alat->jumlah_stok) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                            class="form-control">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
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
                        <label class="form-label mb-0">Foto Alat</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-open-alat-file-modal>
                            <i class="bi bi-folder2-open me-1"></i>Buka Direktori
                        </button>
                    </div>

                    {{-- Hidden select --}}
                    <select name="gambar_alat_id" id="gambar_alat_id" class="form-select d-none" aria-hidden="true">
                        <option value="" {{ $selectedFileId ? '' : 'selected' }}>Pilih file</option>
                        @foreach ($files as $file)
                            @php
                                $fp  = asset($file->file_path);
                                $fn  = $file->file_name ?? 'Tanpa nama';
                            @endphp
                            <option value="{{ $file->id }}"
                                data-preview="{{ $fp }}"
                                data-name="{{ $fn }}"
                                {{ (string) $selectedFileId === (string) $file->id ? 'selected' : '' }}>
                                {{ $fn }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Preview box --}}
                    <div class="alat-file-preview" id="alatFilePreview">
                        @if ($selectedFilePreview)
                            <img src="{{ $selectedFilePreview }}" alt="{{ $selectedFileName }}">
                        @else
                            <span class="text-muted small">Belum ada gambar dipilih</span>
                        @endif
                    </div>

                    <p class="small text-muted mt-2 mb-0" id="alatFileName">
                        {{ $selectedFileName ?? 'Belum ada gambar dipilih' }}
                    </p>

                    <button type="button" class="btn btn-outline-danger btn-sm mt-2 {{ $selectedFileId ? '' : 'd-none' }}" id="alatFileClear">
                        <i class="bi bi-x-circle me-1"></i>Hapus Pilihan
                    </button>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-auto">
                    <a href="{{ route('alat.list') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </div>
        </div>
    </div>
</form>

@include('admin.pengembalian.partials.file-picker-modal', ['files' => $files])
@endsection

@push('styles')
<style>
    .alat-file-preview {
        width: 100%;
        height: 180px;
        border: 2px dashed #d1d5db;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f9fafb;
        margin-top: 0.5rem;
    }
    .alat-file-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileSelect       = document.getElementById('gambar_alat_id');
    const filePreview      = document.getElementById('alatFilePreview');
    const fileNameLabel    = document.getElementById('alatFileName');
    const fileClearBtn     = document.getElementById('alatFileClear');
    const fileModalElement = document.getElementById('filePickerModal');
    const uploadForm       = document.getElementById('modalUploadForm');
    const uploadButton     = document.getElementById('uploadButton');
    const filesContainer   = document.getElementById('filesContainer');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                   ?? document.querySelector('input[name="_token"]')?.value ?? '';
    const deleteRouteTemplate = @json(route('filemanager.delete', ['id' => '__ID__']));

    let fileModal = null;
    if (fileModalElement && window.bootstrap) {
        fileModal = new bootstrap.Modal(fileModalElement);
    }

    // ── Preview ──────────────────────────────────────────────
    function updateFilePreview() {
        const opt = fileSelect?.selectedOptions?.[0];
        if (!opt || !opt.value) {
            filePreview.innerHTML = '<span class="text-muted small">Belum ada gambar dipilih</span>';
            fileNameLabel.textContent = 'Belum ada gambar dipilih';
            fileClearBtn?.classList.add('d-none');
            return;
        }
        const img = document.createElement('img');
        img.src = opt.dataset.preview ?? '';
        img.alt = opt.dataset.name ?? 'Preview';
        filePreview.innerHTML = '';
        filePreview.appendChild(img);
        fileNameLabel.textContent = opt.dataset.name ?? 'File terpilih';
        fileClearBtn?.classList.remove('d-none');
    }

    function selectFileOption(id) {
        if (!fileSelect) return;
        fileSelect.value = id;
        fileSelect.dispatchEvent(new Event('change'));
    }

    // ── Tombol buka modal ────────────────────────────────────
    document.querySelectorAll('[data-open-alat-file-modal]').forEach(btn => {
        btn.addEventListener('click', () => fileModal?.show());
    });

    // ── Tombol hapus pilihan ─────────────────────────────────
    fileClearBtn?.addEventListener('click', () => {
        fileSelect.value = '';
        fileSelect.dispatchEvent(new Event('change'));
    });

    // ── Aksi per baris modal ─────────────────────────────────
    function attachPickAction(row) {
        row.querySelector('[data-file-pick]')?.addEventListener('click', () => {
            selectFileOption(row.querySelector('[data-file-pick]').dataset.fileId);
            fileModal?.hide();
        });
        row.querySelector('[data-file-delete]')?.addEventListener('click', () => {
            handleFileDelete(
                row.querySelector('[data-file-delete]').dataset.fileId,
                row.querySelector('[data-file-delete]')
            );
        });
    }

    // ── Delete ───────────────────────────────────────────────
    async function handleFileDelete(fileId, triggerButton) {
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
                cancelButtonColor: '#6c757d',
            });
            confirmed = result.isConfirmed;
        } else {
            confirmed = window.confirm(`Hapus ${fileName}?`);
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
                    'Accept': 'application/json',
                },
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'Gagal menghapus file.');

            triggerButton.closest('[data-file-row]')?.remove();
            Array.from(fileSelect?.options ?? []).find(o => o.value === String(fileId))?.remove();

            if (fileSelect?.value === String(fileId)) {
                fileSelect.value = '';
                fileSelect.dispatchEvent(new Event('change'));
            }

            const tbody = document.getElementById('filesTableBody');
            if (!tbody?.children.length) {
                filesContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-images text-muted" style="font-size:3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Belum ada file yang dapat dipilih.</p>
                    </div>`;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Terhapus', icon: 'success', timer: 1500, showConfirmButton: false });
            }
        } catch (err) {
            console.error(err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Gagal', text: err.message, icon: 'error' });
            }
            triggerButton.disabled = false;
            triggerButton.innerHTML = originalHtml;
        }
    }

    // ── Upload AJAX ──────────────────────────────────────────
    function addFileToTable(file) {
        document.getElementById('emptyState')?.remove();

        let tbody = document.getElementById('filesTableBody');
        if (!tbody) {
            filesContainer.innerHTML = `
                <div class="table-responsive">
                    <table class="table align-middle" id="filesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px;">Preview</th>
                                <th>Nama File</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="filesTableBody"></tbody>
                    </table>
                </div>`;
            tbody = document.getElementById('filesTableBody');
        }

        const previewPath = file.file_path || file.path;
        const fileName    = file.file_name || file.nama_file || 'Tanpa nama';
        const row         = document.createElement('tr');
        row.dataset.fileRow = String(file.id);
        row.innerHTML = `
            <td>
                <div class="rounded overflow-hidden border" style="width:64px;height:64px;cursor:zoom-in;"
                    data-file-preview data-file-url="${previewPath}" data-file-name="${fileName}">
                    <img src="${previewPath}" alt="${fileName}" class="w-100 h-100 object-fit-cover">
                </div>
            </td>
            <td>
                <div class="fw-semibold">${fileName}</div>
                <div class="text-muted small">ID: ${file.id}</div>
            </td>
            <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-file-preview data-file-url="${previewPath}" data-file-name="${fileName}">Lihat</button>
                    <button type="button" class="btn btn-sm btn-outline-primary"
                        data-file-pick data-file-id="${file.id}">Gunakan</button>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                        data-file-delete data-file-id="${file.id}" data-file-name="${fileName}">Hapus</button>
                </div>
            </td>`;
        tbody.insertBefore(row, tbody.firstChild);
        attachPickAction(row);
    }

    function addFileToSelect(file) {
        if (!fileSelect) return;
        const opt = document.createElement('option');
        opt.value           = file.id;
        opt.dataset.preview = file.file_path || file.path;
        opt.dataset.name    = file.file_name || file.nama_file || 'Tanpa nama';
        opt.textContent     = opt.dataset.name;
        fileSelect.options.length > 1
            ? fileSelect.insertBefore(opt, fileSelect.options[1])
            : fileSelect.appendChild(opt);
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
            try {
                const response = await fetch('{{ route('filemanager.upload') }}', {
                    method: 'POST',
                    body: new FormData(uploadForm),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Upload gagal');
                uploadForm.reset();
                if (data.file) {
                    addFileToTable(data.file);
                    addFileToSelect(data.file);
                    selectFileOption(data.file.id);
                }
            } catch (err) {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Gagal', text: err.message, icon: 'error' });
                }
            } finally {
                uploadButton.disabled = false;
                uploadButton.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload';
            }
        });
    }

    // ── Init ─────────────────────────────────────────────────
    document.querySelectorAll('[data-file-row]').forEach(row => attachPickAction(row));
    fileSelect?.addEventListener('change', updateFilePreview);
    updateFilePreview();
});
</script>
@endpush