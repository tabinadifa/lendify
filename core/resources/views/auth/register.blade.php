@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card auth-card shadow-lg">
            <div class="card-body">
                <h4 class="text-center mb-2 auth-title">Buat Akun Baru</h4>

                <form method="POST" action="{{ route('auth.register.process') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" maxlength="13" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-auth-primary w-100">
                        Daftar
                    </button>

                    <div class="text-center mt-3">
                        <small>Sudah punya akun?
                            <a class="auth-link" href="{{ route('login') }}">Login</a>
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('auth.register.process') }}"]');
        if (!form) return;

        form.addEventListener('submit', function(event) {
            const password = form.querySelector('input[name="password"]');
            const confirmation = form.querySelector('input[name="password_confirmation"]');
            if (password && confirmation && password.value !== confirmation.value) {
                event.preventDefault();
                Swal.fire({
                    title: 'Password tidak sama',
                    text: 'Pastikan password dan konfirmasi password sudah sesuai.',
                    icon: 'error',
                    confirmButtonColor: '#90AB8B'
                });
            }
        });

        // ✅ Fixed line below
        const emailError = @json($errors->first('email'));
        if (emailError) {
            Swal.fire({
                title: 'Email sudah terdaftar',
                text: emailError,
                icon: 'error',
                confirmButtonColor: '#90AB8B'
            });
        }

        const phoneInput = form.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 13);
            });
        }
    });
</script>
@endpush