<div class="modal fade" id="filePickerModal" tabindex="-1" aria-labelledby="filePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-semibold" id="filePickerModalLabel">Pilih Gambar Bukti</h5>
                    <p class="text-muted mb-0">Klik tombol "Gunakan" untuk mengaitkan gambar ke pengembalian.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modalUploadForm" class="border rounded-4 p-3 mb-4">
                    @csrf
                    <input type="hidden" name="folder" value="bukti-pengembalian">
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
                    <div class="mt-2">
                        <div class="alert alert-success d-none" id="uploadSuccess">File berhasil diupload!</div>
                        <div class="alert alert-danger d-none" id="uploadError">Gagal mengupload file.</div>
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
                                        <th>Tanggal</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="filesTableBody">
                                    @foreach ($files as $file)
                                        @php
                                            $previewPath = asset($file->path ?? $file->file_path);
                                            $fileName = $file->nama_file ?? $file->file_name ?? 'Tanpa nama';
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="rounded overflow-hidden border" style="width: 64px; height: 64px;">
                                                    <img src="{{ $previewPath }}" alt="{{ $fileName }}" class="w-100 h-100 object-fit-cover">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $fileName }}</div>
                                                <div class="text-muted small">ID: {{ $file->id }}</div>
                                            </td>
                                            <td>{{ $file->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-file-pick data-file-id="{{ $file->id }}">
                                                    Gunakan
                                                </button>
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
                <a href="{{ route('filemanager.list') }}" target="_blank" rel="noopener" class="btn btn-outline-success">Kelola File</a>
            </div>
        </div>
    </div>
</div>

