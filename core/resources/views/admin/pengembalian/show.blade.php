@extends('layouts.layout')

@section('title', 'Detail Pengembalian - Lendify')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approve' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            'dikembalikan' => 'Dikembalikan',
        ];

        $peminjaman = $pengembalian->peminjaman;
        $borrower = $peminjaman?->peminjam;
        $alat = $peminjaman?->alat;
        $file = $pengembalian->fileBuktiPengembalian;
        $filePreview = $file ? asset($file->path ?? $file->file_path) : null;
        $dendaValue = (float) ($pengembalian->denda ?? 0);
        $badge = match ($peminjaman?->status) {
            'approve' => 'success',
            'returned', 'dikembalikan' => 'primary',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Pengembalian</h2>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-auto">
            <a href="{{ route('pengembalian.list') }}" class="btn btn-outline-secondary">
                Kembali
            </a>
            <a href="{{ route('pengembalian.edit', $pengembalian->id) }}" class="btn btn-primary">
                Edit
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="35%" class="text-muted">Tanggal Pengembalian</th>
                            <td>{{ $pengembalian->tanggal_pengembalian ? \Illuminate\Support\Carbon::parse($pengembalian->tanggal_pengembalian)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kondisi Alat</th>
                            <td>{{ $pengembalian->kondisi_alat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Denda</th>
                            <td>
                                @if ($dendaValue > 0)
                                    <span class="badge text-bg-danger">Rp
                                        {{ number_format($dendaValue, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge text-bg-success">Tidak ada denda</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted align-top">Catatan</th>
                            <td>{!! $pengembalian->catatan ? nl2br(e($pengembalian->catatan)) : '-' !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Bukti Pengembalian</h5>
                    @if ($file && $filePreview)
                        @php
                            $fileName = $file->nama_file ?? ($file->file_name ?? 'Bukti pengembalian');
                        @endphp
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden mb-3 position-relative cursor-pointer"
                            style="cursor: zoom-in;" data-return-preview data-return-preview-url="{{ $filePreview }}"
                            data-return-preview-name="{{ $fileName }}">
                            <img src="{{ $filePreview }}" alt="{{ $fileName }}" class="w-100 h-100"
                                style="object-fit: cover;">
                            <span
                                class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-50 text-white px-3 py-1 rounded-pill small">
                                Klik untuk lihat detail
                            </span>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap gap-2 small text-muted">
                            <a href="{{ $filePreview }}" class="text-decoration-none" target="_blank" rel="noopener">Buka
                                di tab baru</a>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-image fs-2 d-block mb-2"></i>
                            <p class="mb-0">Belum ada bukti pengembalian yang diunggah.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Ringkasan Peminjaman</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%" class="text-muted">Status</th>
                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ $statusLabels[$peminjaman?->status] ?? ucfirst($peminjaman?->status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Pinjam</th>
                            <td>{{ $peminjaman?->tanggal_pinjam ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Batas Kembali</th>
                            <td>{{ $peminjaman?->tanggal_kembali ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Total Alat</th>
                            <td>{{ $peminjaman?->total_alat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Peminjam & Alat</h5>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Peminjam</p>
                        <p class="fw-semibold mb-0">{{ $borrower->name ?? '-' }}</p>
                        <small class="text-muted d-block">{{ $borrower->email ?? 'Email tidak tersedia' }}</small>
                        <small class="text-muted">Username: {{ $borrower->username ?? '-' }}</small>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Alat</p>
                        <div class="modal fade" id="returnImagePreviewModal" tabindex="-1"
                            aria-labelledby="returnImagePreviewLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title fw-semibold" id="returnImagePreviewLabel">Pratinjau Gambar
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="returnPreviewImage" src="" alt="Pratinjau gambar"
                                            class="img-fluid rounded-4 shadow-sm">
                                    </div>
                                    <div class="modal-footer border-0">
                                        <p class="text-muted mb-0 me-auto small" id="returnPreviewName"></p>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="fw-semibold mb-0">{{ $alat->nama_alat ?? '-' }}</p>

                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const modalElement = document.getElementById('returnImagePreviewModal');
                                    if (!modalElement) {
                                        return;
                                    }

                                    const previewImg = document.getElementById('returnPreviewImage');
                                    const previewName = document.getElementById('returnPreviewName');
                                    const modalInstance = window.bootstrap ? new bootstrap.Modal(modalElement) : null;

                                    function openPreview(url, name) {
                                        if (!url) {
                                            return;
                                        }

                                        if (previewImg) {
                                            previewImg.src = url;
                                            previewImg.alt = name || 'Pratinjau gambar';
                                        }

                                        if (previewName) {
                                            previewName.textContent = name || 'Pratinjau gambar';
                                        }

                                        if (modalInstance) {
                                            modalInstance.show();
                                        } else {
                                            window.open(url, '_blank');
                                        }
                                    }

                                    document.querySelectorAll('[data-return-preview]').forEach((trigger) => {
                                        trigger.addEventListener('click', (event) => {
                                            event.preventDefault();
                                            const url = trigger.getAttribute('data-return-preview-url');
                                            const name = trigger.getAttribute('data-return-preview-name');
                                            openPreview(url, name);
                                        });
                                    });
                                });
                            </script>
                        @endpush
                        @if ($alat?->kategori)
                            <span class="badge text-bg-secondary">{{ $alat->kategori->nama_kategori }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
