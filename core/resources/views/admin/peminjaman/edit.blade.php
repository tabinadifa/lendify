@extends('layouts.layout')

@section('title', 'Edit Peminjaman - Lendify')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Menunggu Persetujuan',
        'approve' => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Peminjaman</h2>
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

        <form action="{{ route('peminjaman.update', $peminjaman->id) }}"
              method="POST"
              class="row g-3">
            @csrf
            @method('PUT')

            {{-- Alat --}}
            <div class="col-md-6">
                <label for="alat_id" class="form-label">Alat</label>
                <select name="alat_id" id="alat_id" class="form-select" required>
                    <option value="">-- Pilih Alat --</option>
                    @foreach ($alats as $alat)
                        <option value="{{ $alat->id }}"
                            {{ old('alat_id', $peminjaman->alat_id) == $alat->id ? 'selected' : '' }}>
                            {{ $alat->nama_alat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Peminjam (read only) --}}
            <div class="col-md-6">
                <label class="form-label">Peminjam</label>
                <input type="text"
                       class="form-control"
                       value="{{ $peminjaman->peminjam->name }}"
                       disabled>
                <input type="hidden" name="peminjam_id" value="{{ $peminjaman->peminjam_id }}">
            </div>

            {{-- Tanggal Pinjam --}}
            <div class="col-md-3">
                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                <input
                    type="date"
                    id="tanggal_pinjam"
                    name="tanggal_pinjam"
                    class="form-control"
                    value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam) }}"
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
                    value="{{ old('tanggal_kembali', $peminjaman->tanggal_kembali) }}"
                    required
                >
            </div>

            {{-- Total Alat --}}
            <div class="col-md-3">
                <label for="total_alat" class="form-label">Total Alat</label>
                <input
                    type="number"
                    id="total_alat"
                    name="total_alat"
                    class="form-control"
                    value="{{ old('total_alat', $peminjaman->total_alat) }}"
                    min="1"
                    required
                >
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    @foreach ($allowedStatuses as $status)
                        <option value="{{ $status }}"
                            {{ old('status', $peminjaman->status) === $status ? 'selected' : '' }}>
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Alasan Ditolak --}}
            <div class="col-md-12">
                <label for="alasan_ditolak" class="form-label">Alasan Ditolak</label>
                <textarea
                    name="alasan_ditolak"
                    id="alasan_ditolak"
                    rows="3"
                    class="form-control"
                    placeholder="Wajib diisi jika status ditolak"
                >{{ old('alasan_ditolak', $peminjaman->alasan_ditolak) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('peminjaman.list') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Perbarui
                </button>
            </div>

        </form>
    </div>
</div>
@endsection