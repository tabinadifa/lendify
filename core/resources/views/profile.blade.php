@extends('layouts.layout')

@section('title', 'Profil Saya')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <!-- Card Info Ringkas -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center p-4">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-radius: 50%; font-size: 2.5rem; font-weight: 500;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small">@ {{ $user->username }}</p>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ ucfirst($user->role) }}</span>
                    <hr class="my-3">
                    <div class="text-start small">
                        <div class="mb-2"><i class="bi bi-envelope me-2"></i> {{ $user->email }}</div>
                        <div class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $user->phone ?? '-' }}</div>
                        <div class="mb-2"><i class="bi bi-geo-alt me-2"></i> {{ $user->address ?? '-' }}</div>
                        <div><i class="bi bi-calendar me-2"></i> Bergabung {{ $user->created_at->translatedFormat('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Tab Navigasi -->
            <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informasi Profil</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Ubah Password</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab Informasi Profil -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Edit Informasi Profil</h5>
                            <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" maxlength="13">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab Ubah Password -->
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Ganti Password</h5>
                            <form method="POST" action="{{ route('profile.password') }}" id="passwordForm">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                                    @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary px-4">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .nav-tabs .nav-link {
        color: #4b5563;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        background: transparent;
    }
    .card {
        border-radius: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alert after 3 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => alert.remove(), 3000);
        }

        // Konfirmasi sebelum update password (opsional)
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                const newPass = document.querySelector('input[name="new_password"]').value;
                const confirmPass = document.querySelector('input[name="new_password_confirmation"]').value;
                if (newPass !== confirmPass) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password tidak cocok',
                        text: 'Pastikan password baru dan konfirmasi sama.',
                        confirmButtonColor: '#4f46e5'
                    });
                } else if (newPass.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password terlalu pendek',
                        text: 'Password minimal 8 karakter.',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });
        }
    });
</script>
@endpush