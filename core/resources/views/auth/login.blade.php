@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card auth-card shadow-lg">
                <div class="card-body">
                    <h4 class="text-center mb-2 auth-title">Masuk ke Akun</h4>
                    <p class="text-center mb-4 auth-description">Silakan masuk untuk melanjutkan ke dashboard Anda.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first('login') ?? 'Login gagal, periksa kembali data Anda.' }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auth.login.process') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email atau Username</label>
                            <input type="text"
                                   name="login"
                                   value="{{ old('login') }}"
                                   class="form-control @error('login') is-invalid @enderror"
                                   required>
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-auth-primary w-100">
                            Masuk
                        </button>

                        <div class="text-center mt-3">
                            <small>Belum punya akun?
                                <a class="auth-link" href="{{ route('auth.register') }}">Daftar</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
