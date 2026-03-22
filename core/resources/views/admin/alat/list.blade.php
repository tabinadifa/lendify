@extends('layouts.layout')

@section('title', 'Daftar Alat - Lendify')

@push('styles')
<style>
    .badge-kategori {
        background-color: #E3F2FD;
        color: #0D47A1;
        font-weight: 600;
    }
    .alat-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        cursor: zoom-in;
    }
    .alat-thumb-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        border: 1px dashed #d1d5db;
        background: #f9fafb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2 class="fw-bold mb-0">Daftar Alat</h2>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" class="row g-2 mb-3 align-items-center mt-2">
            <div class="col-md-3">
                <a href="{{ route('alat.create') }}" class="btn btn-success w-100">Tambah Alat</a>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach ([5, 10, 25, 50] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control" placeholder="Cari nama alat..."
                    onkeydown="if(event.key==='Enter'){this.form.submit()}">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alats as $alat)
                        @php
                            $gambar       = $alat->gambarAlat;
                            $previewUrl   = $gambar ? asset($gambar->file_path) : null;
                            $previewName  = $gambar ? ($gambar->file_name ?? $alat->nama_alat) : null;
                        @endphp
                        <tr>
                            <td>{{ $alats->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($previewUrl)
                                    <img src="{{ $previewUrl }}"
                                         alt="{{ $previewName }}"
                                         class="alat-thumb"
                                         data-bs-toggle="tooltip"
                                         data-bs-placement="right"
                                         title="{{ $previewName }}"
                                         data-preview-trigger
                                         data-file-url="{{ $previewUrl }}"
                                         data-file-name="{{ $previewName }}">
                                @else
                                    <span class="alat-thumb-placeholder">
                                        <i class="bi bi-image"></i>
                                    </span>
                                @endif
                            </td>
                            <td>{{ $alat->nama_alat }}</td>
                            <td>
                                <span class="badge badge-kategori">
                                    {{ $alat->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $alat->jumlah_stok }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('alat.edit', $alat->id) }}"
                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('alat.destroy', $alat->id) }}"
                                          method="POST"
                                          class="form-hapus"
                                          data-title="Yakin ingin menghapus?"
                                          data-text="Data alat ini akan dihapus secara permanen.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Data alat tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $alats->firstItem() }} –
                {{ $alats->lastItem() }} dari
                {{ $alats->total() }} data
            </small>
            {{ $alats->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modal preview gambar (reuse dari pengembalian) --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreviewModalImage" src="" alt="" class="img-fluid rounded-4 shadow-sm">
            </div>
            <div class="modal-footer border-0">
                <p class="text-muted mb-0 me-auto small" id="imagePreviewModalName"></p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Preview modal ────────────────────────────────────────
    const previewModalEl = document.getElementById('imagePreviewModal');
    const previewImg     = document.getElementById('imagePreviewModalImage');
    const previewName    = document.getElementById('imagePreviewModalName');
    const previewModal   = previewModalEl && window.bootstrap
        ? new bootstrap.Modal(previewModalEl) : null;

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-preview-trigger]');
        if (!trigger) return;
        e.preventDefault();
        const url  = trigger.dataset.fileUrl;
        const name = trigger.dataset.fileName ?? '';
        if (!url) return;
        if (previewImg)  previewImg.src = url, previewImg.alt = name;
        if (previewName) previewName.textContent = name;
        previewModal ? previewModal.show() : window.open(url, '_blank');
    });

    // ── Tooltip Bootstrap ────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ── Konfirmasi hapus ─────────────────────────────────────
    document.querySelectorAll('.form-hapus').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const title = form.dataset.title ?? 'Yakin?';
            const text  = form.dataset.text  ?? '';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title,
                    text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                }).then(result => { if (result.isConfirmed) form.submit(); });
            } else if (window.confirm(`${title}\n${text}`)) {
                form.submit();
            }
        });
    });
});
</script>
@endpush