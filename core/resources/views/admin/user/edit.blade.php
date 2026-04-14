@extends('layouts.layout')

@section('title', 'Edit Pengguna - Lendify')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Pengguna</h2>
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

        <form action="{{ route('user.update', $user->id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            {{-- Nama Lengkap --}}
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Username (alpha_dash) --}}
            <div class="col-md-6">
                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', $user->username) }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <small class="text-muted">Hanya huruf, angka, garis bawah (_), dan strip (-).</small>
                @enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role --}}
            <div class="col-md-6">
                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password (opsional) dengan toggle --}}
            <div class="col-md-6">
                <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @else
                    <small class="text-muted">Minimal 8 karakter. Isi hanya jika ingin mengganti password.</small>
                @enderror
            </div>

            {{-- Tombol aksi --}}
            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('user.list') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

{{-- Script toggle password --}}
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>
@endsection