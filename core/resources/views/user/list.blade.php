@extends('layouts.layout')

@section('title', 'Daftar Pengguna - Lendify')

@push('styles')
    <style>
        .badge-role {
            background-color: #E8F5E9;
            color: #2D6F4E;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <h2 class="fw-bold mb-0">Daftar Pengguna</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Controls -->
            <form method="GET" class="row g-2 mb-3 align-items-center mt-2">
                <div class="col-md-3">
                    <a href="{{ route('user.create') }}" class="btn btn-success w-100">Tambah Pengguna</a>
                </div>

                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach ([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 ms-auto">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari nama, username, email, role..."
                        onkeydown="if(event.key==='Enter'){this.form.submit()}">
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aktif Sejak</th>
                            <th>Terakhir Aktif</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge badge-role text-capitalize">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td>{{ $user->active_since }}</td>
                                <td>{{ $user->last_active }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('user.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                            class="form-hapus" data-title="Yakin ingin menghapus?"
                                            data-text="Data user ini akan dihapus secara permanen.">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $users->firstItem() }} –
                    {{ $users->lastItem() }} dari
                    {{ $users->total() }} data
                </small>

                {{ $users->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
@endsection
