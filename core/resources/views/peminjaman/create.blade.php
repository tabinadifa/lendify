@extends('layouts.layout')

@section('title', 'Tambah Peminjaman - Lendify')

@section('content')
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
                    required
                >
            </div>

            {{-- Status (default pending) --}}
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="pending" selected>Pending</option>
                    @foreach ($allowedStatuses as $status)
                        @if ($status !== 'pending')
                            <option value="{{ $status }}"
                                {{ old('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Alasan Ditolak --}}
            <div class="col-md-6">
                <label for="alasan_ditolak" class="form-label">Alasan Ditolak</label>
                <textarea
                    name="alasan_ditolak"
                    id="alasan_ditolak"
                    rows="3"
                    class="form-control"
                    placeholder="Isi jika status rejected"
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
