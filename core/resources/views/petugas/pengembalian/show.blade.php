@extends('layouts.layout')

@section('title', 'Detail Pengembalian - Petugas')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approve' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            'dikembalikan' => 'Dikembalikan',
        ];

        $pengembalianStatusLabels = [
            'lunas' => 'Lunas',
            'belum_lunas' => 'Belum Lunas',
            'pending' => 'Pending',
        ];

        $peminjaman = $pengembalian->peminjaman;
        $borrower = $peminjaman?->peminjam;
        $alat = $peminjaman?->alat;
        $file = $pengembalian->fileBuktiPengembalian;
        $filePreview = $file ? asset($file->path ?? $file->file_path) : null;
        $dendaValue = (float) ($pengembalian->denda ?? 0);
        $dendaTelat = 0;
        $dendaKondisi = 0;

        // Hitung denda telat jika tanggal kembali tersedia
        if ($peminjaman && $peminjaman->tanggal_kembali && $pengembalian->tanggal_pengembalian) {
            $tglKembali = \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $tglPengembalian = \Illuminate\Support\Carbon::parse($pengembalian->tanggal_pengembalian)->startOfDay();
            if ($tglPengembalian->gt($tglKembali)) {
                $hariTelat = $tglKembali->diffInDays($tglPengembalian);
                $dendaTelat = $hariTelat * 2000;
            }
        }

        // Asumsikan denda kondisi diambil dari selisih total denda - denda telat (jika tidak tersimpan terpisah)
        $dendaKondisi = max(0, $dendaValue - $dendaTelat);

        $badgePeminjaman = match ($peminjaman?->status) {
            'approve' => 'success',
            'returned', 'dikembalikan' => 'primary',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };

        $badgeStatusPengembalian = match ($pengembalian->status) {
            'lunas' => 'success',
            'belum_lunas' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Pengembalian</h2>
            <p class="text-muted mb-0">Informasi lengkap transaksi pengembalian alat</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('petugas.pengembalian.list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('petugas.pengembalian.edit', $pengembalian->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: Detail Pengembalian & Bukti --}}
        <div class="col-lg-7">
            {{-- Kartu Detail Pengembalian --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Informasi Pengembalian</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Tanggal Pengembalian</p>
                            <p class="fw-semibold mb-0">
                                {{ $pengembalian->tanggal_pengembalian ? \Illuminate\Support\Carbon::parse($pengembalian->tanggal_pengembalian)->translatedFormat('d M Y H:i') : '-' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Kondisi Alat</p>
                            <p class="fw-semibold mb-0">
                                @php
                                    $kondisiBadge = match (strtolower($pengembalian->kondisi_alat ?? '')) {
                                        'baik' => 'success',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger',
                                        'hilang' => 'dark',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $kondisiBadge }}">{{ ucfirst($pengembalian->kondisi_alat ?? '-') }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Status Pembayaran Denda</p>
                            <p>
                                <span class="badge bg-{{ $badgeStatusPengembalian }}">
                                    {{ $pengembalianStatusLabels[$pengembalian->status] ?? ucfirst($pengembalian->status ?? 'pending') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Metode Pembayaran</p>
                            <p class="fw-semibold mb-0">{{ $pengembalian->metode_pembayaran ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-muted mb-1">Rincian Denda</p>
                            <div class="bg-light p-3 rounded-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Denda keterlambatan (Rp2.000/hari)</span>
                                    <span class="fw-semibold">Rp {{ number_format($dendaTelat, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Denda kondisi alat</span>
                                    <span class="fw-semibold">Rp {{ number_format($dendaKondisi, 0, ',', '.') }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total Denda</span>
                                    <span class="fw-bold text-danger">Rp {{ number_format($dendaValue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($pengembalian->catatan)
                            <div class="col-12">
                                <p class="text-muted mb-1">Catatan</p>
                                <div class="bg-light p-3 rounded-4">
                                    {!! nl2br(e($pengembalian->catatan)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kartu Bukti Pengembalian --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Bukti Pengembalian</h5>
                </div>
                <div class="card-body">
                    @if ($file && $filePreview)
                        @php
                            $fileName = $file->nama_file ?? ($file->file_name ?? 'Bukti pengembalian');
                            $fileSize = $file->file_size ?? null;
                            $uploadedAt = $file->created_at ?? null;
                        @endphp
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden mb-3 position-relative cursor-pointer"
                            style="cursor: zoom-in;" data-return-preview data-return-preview-url="{{ $filePreview }}"
                            data-return-preview-name="{{ $fileName }}">
                            <img src="{{ $filePreview }}" alt="{{ $fileName }}" class="w-100 h-100"
                                style="object-fit: cover;">
                            <span
                                class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-50 text-white px-3 py-1 rounded-pill small">
                                Klik untuk perbesar
                            </span>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap gap-2 small text-muted">
                            <div>
                                <i class="bi bi-file-earmark-image me-1"></i> {{ $fileName }}
                                @if ($fileSize)
                                    <span class="mx-1">•</span> {{ number_format($fileSize / 1024, 1) }} KB
                                @endif
                                @if ($uploadedAt)
                                    <span class="mx-1">•</span> Diupload: {{ \Illuminate\Support\Carbon::parse($uploadedAt)->translatedFormat('d M Y H:i') }}
                                @endif
                            </div>
                            <a href="{{ $filePreview }}" class="text-decoration-none" target="_blank" rel="noopener">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka di tab baru
                            </a>
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

        {{-- KOLOM KANAN: Ringkasan Peminjaman, Peminjam & Alat --}}
        <div class="col-lg-5">
            {{-- Ringkasan Peminjaman --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Ringkasan Peminjaman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%" class="text-muted">Status Peminjaman</th>
                            <td>
                                <span class="badge bg-{{ $badgePeminjaman }}">
                                    {{ $statusLabels[$peminjaman?->status] ?? ucfirst($peminjaman?->status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Pinjam</th>
                            <td>{{ $peminjaman?->tanggal_pinjam ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Batas Kembali</th>
                            <td>{{ $peminjaman?->tanggal_kembali ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Jumlah Alat</th>
                            <td>{{ $peminjaman?->total_alat ?? '-' }} unit</td>
                        </tr>
                        @if ($pengembalian->created_at)
                            <tr>
                                <th class="text-muted">Dicatat pada</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($pengembalian->created_at)->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @endif
                        @if ($pengembalian->updated_at && $pengembalian->updated_at != $pengembalian->created_at)
                            <tr>
                                <th class="text-muted">Terakhir diupdate</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($pengembalian->updated_at)->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Informasi Peminjam --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Data Peminjam</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-person fs-4 text-primary"></i>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0">{{ $borrower->name ?? '-' }}</p>
                            <small class="text-muted">{{ $borrower->email ?? 'Email tidak tersedia' }}</small>
                        </div>
                    </div>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="35%" class="text-muted">Username</th>
                            <td>{{ $borrower->username ?? '-' }}</td>
                        </tr>
                        @if (!empty($borrower->phone))
                            <tr>
                                <th class="text-muted">No. Telepon</th>
                                <td>{{ $borrower->phone }}</td>
                            </tr>
                        @endif
                        @if (!empty($borrower->address))
                            <tr>
                                <th class="text-muted">Alamat</th>
                                <td>{{ $borrower->address }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Informasi Alat --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Alat</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-tools fs-4 text-info"></i>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0">{{ $alat->nama_alat ?? '-' }}</p>
                            @if ($alat && $alat->kode_alat)
                                <small class="text-muted">Kode: {{ $alat->kode_alat }}</small>
                            @endif
                        </div>
                    </div>
                    <table class="table table-borderless table-sm mb-0">
                        @if ($alat && $alat->kategori)
                            <tr>
                                <th width="35%" class="text-muted">Kategori</th>
                                <td>
                                    <span class="badge text-bg-secondary">{{ $alat->kategori->nama_kategori }}</span>
                                </td>
                            </tr>
                        @endif
                        @if ($alat && isset($alat->stok))
                            <tr>
                                <th class="text-muted">Stok Tersedia</th>
                                <td>{{ $alat->stok }} unit</td>
                            </tr>
                        @endif
                        @if ($alat && $alat->deskripsi)
                            <tr>
                                <th class="text-muted align-top">Deskripsi</th>
                                <td>{{ $alat->deskripsi }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal preview gambar --}}
    <div class="modal fade" id="returnImagePreviewModal" tabindex="-1" aria-labelledby="returnImagePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="returnImagePreviewLabel">Pratinjau Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="returnPreviewImage" src="" alt="Pratinjau gambar" class="img-fluid rounded-4 shadow-sm">
                </div>
                <div class="modal-footer border-0">
                    <p class="text-muted mb-0 me-auto small" id="returnPreviewName"></p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalElement = document.getElementById('returnImagePreviewModal');
        if (!modalElement) return;

        const previewImg = document.getElementById('returnPreviewImage');
        const previewName = document.getElementById('returnPreviewName');
        let modalInstance = null;
        if (window.bootstrap) {
            modalInstance = new bootstrap.Modal(modalElement);
        }

        function openPreview(url, name) {
            if (!url) return;
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

        document.querySelectorAll('[data-return-preview]').forEach(trigger => {
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