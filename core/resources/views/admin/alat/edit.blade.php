@extends('layouts.layout')

@section('title', 'Edit Alat - Lendify')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Alat</h2>
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

        <form action="{{ route('alat.update', $alat->id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori Alat</label>
                <select name="kategori_id" id="kategori_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategoriAlats as $kategori)
                        <option value="{{ $kategori->id }}"
                            {{ old('kategori_id', $alat->kategori_id) == $kategori->id ? 'selected' : '' }}>
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
                    value="{{ old('nama_alat', $alat->nama_alat) }}"
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
                    value="{{ old('jumlah_stok', $alat->jumlah_stok) }}"
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
                >{{ old('deskripsi', $alat->deskripsi) }}</textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('alat.list') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
