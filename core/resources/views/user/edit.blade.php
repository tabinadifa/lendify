@extends('layouts.layout')

@section('title', 'Edit Pengguna - Lendify')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Pengguna</h2>
    <a href="{{ route('user.list') }}" class="btn btn-outline-secondary">Kembali</a>
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

            <div class="col-md-6">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <input type="text" id="role" name="role" class="form-control" value="{{ old('role', $user->role) }}" required>
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah.</small>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('user.list') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
