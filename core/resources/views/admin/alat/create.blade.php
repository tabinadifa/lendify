@extends('layouts.layout')

@section('title', 'Tambah Alat - Lendify')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Alat</h2>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('alat.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori Alat</label>
                <select name="kategori_id" id="kategori_id" class="form-select" required>
                    <option value="" disabled selected>Pilih kategori</option>
                    @foreach ($kategoriAlats as $kategori)
                        <option value="{{ $kategori->id }}"
                            {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="nama_alat" class="form-label">Nama Alat</label>
                <input
                    type="text"
                    id="nama_alat"
                    name="nama_alat"
                    class="form-control"
                    value="{{ old('nama_alat') }}"
                    required
                >
            </div>

            <div class="col-md-6">
                <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                <input
                    type="number"
                    id="jumlah_stok"
                    name="jumlah_stok"
                    class="form-control"
                    min="0"
                    value="{{ old('jumlah_stok') }}"
                    required
                >
            </div>

            <div class="col-md-6">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    rows="3"
                    class="form-control"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- ===== FOTO ALAT ===== --}}
            <div class="col-12">
                <hr class="my-1">
                <label class="form-label fw-semibold">Foto Alat</label>

                {{-- Hidden select untuk menyimpan file_id yang dipilih --}}
                <select name="gambar_alat_id" id="gambar_alat_id" class="form-select d-none" aria-hidden="true">
                    <option value="" {{ old('gambar_alat_id') ? '' : 'selected' }}>Pilih file</option>
                    @foreach ($files as $file)
                        @php
                            $previewPath = asset($file->path ?? $file->file_path);
                            $fileName    = $file->nama_file ?? $file->file_name ?? 'Tanpa nama';
                        @endphp
                        <option
                            value="{{ $file->id }}"
                            data-preview="{{ $previewPath }}"
                            data-name="{{ $fileName }}"
                            {{ (string) old('gambar_alat_id') === (string) $file->id ? 'selected' : '' }}
                        >
                            {{ $fileName }}
                        </option>
                    @endforeach
                </select>

                <div class="d-flex align-items-start gap-4 flex-wrap mt-1">
                    {{-- Thumbnail preview --}}
                    <div class="alat-file-preview" id="alatFilePreview">
                        <span class="text-muted small">Belum ada gambar dipilih</span>
                    </div>

                    <div class="d-flex flex-column gap-2 justify-content-center">
                        <p class="small text-muted mb-1" id="alatFileName">Belum ada gambar dipilih</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-open-alat-file-modal>
                            <i class="bi bi-folder2-open me-1"></i>Buka Direktori
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm d-none" id="alatFileClear">
                            <i class="bi bi-x-circle me-1"></i>Hapus Pilihan
                        </button>
                    </div>
                </div>
            </div>
            {{-- ===== END FOTO ALAT ===== --}}

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('alat.list') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Reuse modal file picker yang sama --}}
@include('admin.pengembalian.partials.file-picker-modal', ['files' => $files])
@endsection

@push('styles')
<style>
    .alat-file-preview {
        width: 160px;
        height: 160px;
        border: 2px dashed #d1d5db;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        background: #f9fafb;
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
    // ── Elemen ──────────────────────────────────────────────
    const fileSelect       = document.getElementById('gambar_alat_id');
    const filePreview      = document.getElementById('alatFilePreview');
    const fileNameLabel    = document.getElementById('alatFileName');
    const fileClearBtn     = document.getElementById('alatFileClear');
    const fileModalElement = document.getElementById('filePickerModal');
    const uploadForm       = document.getElementById('modalUploadForm');
    const uploadButton     = document.getElementById('uploadButton');
    const filesContainer   = document.getElementById('filesContainer');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                   ?? document.querySelector('input[name="_token"]')?.value
                   ?? '';

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

    // ── Pilih file dari modal ────────────────────────────────
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

    // ── Delete file ──────────────────────────────────────────
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

            // Hapus dari option select juga
            Array.from(fileSelect?.options ?? [])
                .find(o => o.value === String(fileId))?.remove();

            if (fileSelect?.value === String(fileId)) {
                fileSelect.value = '';
                fileSelect.dispatchEvent(new Event('change'));
            }

            // Tampilkan empty state jika tabel kosong
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
            } else {
                alert(err.message);
            }
            triggerButton.disabled = false;
            triggerButton.innerHTML = originalHtml;
        }
    }

    // ── Upload AJAX ──────────────────────────────────────────
    function addFileToTable(file) {
        const emptyState = document.getElementById('emptyState');
        emptyState?.remove();

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

        const previewPath = file.path || file.file_path;
        const fileName    = file.nama_file || file.file_name || 'Tanpa nama';

        const row = document.createElement('tr');
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
        opt.dataset.preview = file.path || file.file_path;
        opt.dataset.name    = file.nama_file || file.file_name || 'Tanpa nama';
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
                } else {
                    alert(err.message);
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

    // Inisialisasi old() value jika ada
    @if(old('gambar_alat_id'))
        fileSelect.value = '{{ old('gambar_alat_id') }}';
        updateFilePreview();
    @endif
});
</script>
@endpush