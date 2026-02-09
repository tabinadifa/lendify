@extends('layouts.layout')

@section('title', 'File Manager - Lendify')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">File Manager</h2>
        <p class="text-muted mb-0">Upload bukti pengembalian atau gambar lain yang dibutuhkan.</p>
    </div>
    <a href="{{ route('pengembalian.create') }}" class="btn btn-outline-success">
        <i class="bi bi-plus-circle me-2"></i>Tambah Pengembalian
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Upload Gambar</h5>
                <form action="{{ route('filemanager.upload') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label for="folder" class="form-label">Folder Tujuan</label>
                        <input
                            type="text"
                            id="folder"
                            name="folder"
                            class="form-control @error('folder') is-invalid @enderror"
                            value="{{ old('folder', $defaultFolder ?? 'bukti-pengembalian') }}"
                            placeholder="contoh: bukti-pengembalian"
                            required
                        >
                        @error('folder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nama folder otomatis diamankan sebelum penyimpanan.</small>
                    </div>

                    <div>
                        <label for="file" class="form-label">Pilih Gambar</label>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            class="form-control @error('file') is-invalid @enderror"
                            accept="image/*"
                            required
                        >
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP (maks 2 MB).</small>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-cloud-upload me-2"></i>Upload
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Daftar Gambar</h5>
                    <span class="badge text-bg-light">{{ $files->count() }} file</span>
                </div>

                @if ($files->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Belum ada gambar yang diupload.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 90px;">Preview</th>
                                    <th>Nama File</th>
                                    <th>Folder</th>
                                    <th>Ukuran</th>
                                    <th>Diunggah Oleh</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                    @php
                                        $folderName = Str::after($file->path ?? $file->file_path, 'storage/uploads/');
                                        $folderName = $folderName ? explode('/', $folderName)[0] : '-';
                                        $sizeInKb = $file->size ? number_format($file->size / 1024, 2) . ' KB' : '-';
                                    @endphp
                                    <tr>
                                    <td>
                                        <div class="file-preview rounded position-relative overflow-hidden">
                                            <img src="{{ asset($file->path ?? $file->file_path) }}" alt="{{ $file->nama_file ?? 'Preview' }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $file->nama_file ?? $file->file_name ?? 'Tanpa nama' }}
                                        <div>
                                            <a href="{{ asset($file->path ?? $file->file_path) }}" class="small" target="_blank" rel="noopener">Lihat file</a>
                                        </div>
                                    </td>
                                    <td>{{ $folderName }}</td>
                                    <td>{{ $sizeInKb }}</td>
                                    <td>{{ $file->uploader->name ?? 'Tidak diketahui' }}</td>
                                    <td>{{ $file->created_at?->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('filemanager.delete', $file->id) }}" method="POST" class="d-inline form-hapus" data-title="Hapus file?" data-text="File akan dihapus permanen.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .file-preview {
        width: 70px;
        height: 70px;
        background-color: #F8F9FA;
        border: 1px solid #E5E7EB;
    }
</style>
@endpush
