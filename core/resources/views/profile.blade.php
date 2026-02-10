@extends('layouts.layout')

@section('title', 'Profil Pengguna - Lendify')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Profil Pengguna</h2>
            <p class="text-muted mb-0">Kelola informasi akun dan preferensi penggunaan sistem.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 140px; height: 140px; border-radius: 50%; background-color: var(--light-green); color: var(--primary-green); font-size: 3rem;">
                        <i class="bi bi-person"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $user->name ?? '-' }}</h4>
                    <p class="text-muted text-capitalize">{{ $user->role ?? '-' }}</p>
                    <div class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                        Aktif sejak {{ $activeSince ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Informasi Akun</h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $user->name ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Peran</label>
                            <div class="border rounded-3 px-3 py-2 bg-light d-flex align-items-center justify-content-between">
                                <span class="fw-semibold text-dark text-capitalize">{{ $user->role ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Username</label>
                            <input type="text" class="form-control" value="{{ $user->username ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
