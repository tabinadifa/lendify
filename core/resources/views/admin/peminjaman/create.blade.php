@extends('layouts.layout')

@section('title', 'Tambah Peminjaman - Lendify')

@section('content')
@php
    $today = now()->format('Y-m-d');
    $statusLabels = [
        'pending' => 'Menunggu Persetujuan',
        'approve' => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Peminjaman</h2>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        {{-- Error --}}
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

        <form action="{{ route('peminjaman.store') }}" method="POST" class="row g-3">
            @csrf

            {{-- Alat --}}
            <div class="col-md-6">
                <label for="alat_id" class="form-label">Alat</label>
                <select name="alat_id" id="alat_id" class="form-select" required>
                    <option value="" disabled selected>Pilih alat</option>
                    @foreach ($alats as $alat)
                        <option value="{{ $alat->id }}"
                            {{ old('alat_id') == $alat->id ? 'selected' : '' }}>
                            {{ $alat->nama_alat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Peminjam --}}
            <div class="col-md-6">
                <label for="peminjam_id" class="form-label">Nama Peminjam</label>
                <select name="peminjam_id" id="peminjam_id" class="form-select" required>
                    <option value="" disabled selected>Pilih peminjam</option>
                    @foreach ($peminjams as $peminjam)
                        <option value="{{ $peminjam->id }}"
                            {{ old('peminjam_id') == $peminjam->id ? 'selected' : '' }}>
                            {{ $peminjam->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Pinjam --}}
            <div class="col-md-3">
                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                <input
                    type="date"
                    id="tanggal_pinjam"
                    name="tanggal_pinjam"
                    class="form-control"
                    value="{{ old('tanggal_pinjam') }}"
                    min="{{ $today }}"
                    required
                >
            </div>

            {{-- Tanggal Kembali --}}
            <div class="col-md-3">
                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                <input
                    type="date"
                    id="tanggal_kembali"
                    name="tanggal_kembali"
                    class="form-control"
                    value="{{ old('tanggal_kembali') }}"
                    min="{{ $today }}"
                    required
                >
            </div>

            {{-- Status (default pending) --}}
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    @foreach ($allowedStatuses as $status)
                        <option value="{{ $status }}"
                            {{ old('status', 'pending') == $status ? 'selected' : '' }}>
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Total Alat --}}
            <div class="col-md-3">
                <label for="total_alat" class="form-label">Total Alat</label>
                <input
                    type="number"
                    id="total_alat"
                    name="total_alat"
                    class="form-control"
                    value="{{ old('total_alat', 1) }}"
                    min="1"
                    required
                >
            </div>

            {{-- Alasan Ditolak --}}
            <div class="col-md-12">
                <label for="alasan_ditolak" class="form-label">Alasan Ditolak</label>
                <textarea
                    name="alasan_ditolak"
                    id="alasan_ditolak"
                    rows="3"
                    class="form-control"
                    placeholder="Isi jika status ditolak"
                >{{ old('alasan_ditolak') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('peminjaman.list') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-success">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const today = '{{ $today }}';
    const pinjamInput = document.getElementById('tanggal_pinjam');
    const kembaliInput = document.getElementById('tanggal_kembali');

    if (pinjamInput) {
        pinjamInput.min = today;
        if (pinjamInput.value && pinjamInput.value < today) {
            pinjamInput.value = today;
        }
        pinjamInput.addEventListener('change', () => {
            const minDate = pinjamInput.value || today;
            if (kembaliInput) {
                kembaliInput.min = minDate;
                if (kembaliInput.value && kembaliInput.value < minDate) {
                    kembaliInput.value = minDate;
                }
            }
        });
    }

    if (kembaliInput) {
        const minDate = pinjamInput?.value || today;
        kembaliInput.min = minDate;
        if (kembaliInput.value && kembaliInput.value < minDate) {
            kembaliInput.value = minDate;
        }
    }
});
</script>
@endpush