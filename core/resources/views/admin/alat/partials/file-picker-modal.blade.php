<div class="modal fade" id="filePickerModal" tabindex="-1" aria-labelledby="filePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-semibold" id="filePickerModalLabel">Pilih Gambar Alat</h5>
                    <p class="text-muted mb-0">Klik tombol "Gunakan" untuk mengaitkan gambar ke pengembalian.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modalUploadForm" class="border rounded-4 p-3 mb-4" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="folder" value="gambar-alat">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="modal_upload_file" class="form-label">Upload Gambar Baru</label>
                            <input type="file" name="file" id="modal_upload_file" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format JPG, JPEG, PNG, atau WEBP (maks 2 MB).</small>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100" id="uploadButton">
                                <i class="bi bi-cloud-upload me-2"></i>Upload
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="filesContainer">
                    @if ($files->isEmpty())
                        <div class="text-center py-4" id="emptyState">
                            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada file yang dapat dipilih.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle" id="filesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Preview</th>
                                        <th>Nama File</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="filesTableBody">
                                    @foreach ($files as $file)
                                        @php
                                            $previewPath = asset($file->file_path ?? $file->path);
                                            $fileName = $file->nama_file ?? $file->file_name ?? 'Tanpa nama';
                                        @endphp
                                        <tr data-file-row="{{ $file->id }}">
                                            <td>
                                                <div class="rounded overflow-hidden border" style="width: 64px; height: 64px; cursor: zoom-in;"
                                                    data-file-preview
                                                    data-file-url="{{ $previewPath }}"
                                                    data-file-name="{{ $fileName }}">
                                                    <img src="{{ $previewPath }}" alt="{{ $fileName }}" class="w-100 h-100 object-fit-cover">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $fileName }}</div>
                                                <div class="text-muted small">ID: {{ $file->id }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-file-preview data-file-url="{{ $previewPath }}" data-file-name="{{ $fileName }}">
                                                        Lihat
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-file-pick data-file-id="{{ $file->id }}">
                                                        Gunakan
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-file-delete data-file-id="{{ $file->id }}" data-file-name="{{ $fileName }}">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreviewModalImage" src="" alt="Pratinjau gambar" class="img-fluid rounded-4 shadow-sm">
            </div>
            <div class="modal-footer border-0">
                <p class="text-muted mb-0 me-auto small" id="imagePreviewModalName"></p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalElement = document.getElementById('imagePreviewModal');
                if (!modalElement) {
                    return;
                }

                const previewImage = document.getElementById('imagePreviewModalImage');
                const previewName = document.getElementById('imagePreviewModalName');
                const bootstrapModal = window.bootstrap ? new bootstrap.Modal(modalElement) : null;

                function openPreview(url, name) {
                    if (!url) {
                        return;
                    }

                    if (previewImage) {
                        previewImage.src = url;
                        previewImage.alt = name || 'Pratinjau gambar';
                    }

                    if (previewName) {
                        previewName.textContent = name || 'Pratinjau gambar';
                    }

                    if (bootstrapModal) {
                        bootstrapModal.show();
                    } else {
                        window.open(url, '_blank');
                    }
                }

                document.addEventListener('click', (event) => {
                    const trigger = event.target.closest('[data-file-preview]');
                    if (!trigger) {
                        return;
                    }

                    event.preventDefault();
                    const url = trigger.getAttribute('data-file-url');
                    const name = trigger.getAttribute('data-file-name') || 'Pratinjau gambar';
                    openPreview(url, name);
                });
            });
        </script>
    @endpush
@endonce

