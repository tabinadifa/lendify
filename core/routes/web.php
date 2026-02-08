<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Admin\User\UserController as AdminUserController;
use App\Http\Controllers\Admin\Kategori\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\Alat\AlatController as AdminAlatController;
use App\Http\Controllers\Admin\Peminjaman\PeminjamanController as AdminPeminjamanController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::prefix(env('ROUTE_PREFIX_LOGIN'))->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'login')->name('auth.login');
        Route::post('login', 'loginProcess')->name('auth.login.process');
        Route::get('register', 'register')->name('auth.register');
        Route::post('register', 'registerProcess')->name('auth.register.process');
        Route::post('logout', 'logout')->name('auth.logout');
    });
});

Route::get('/login', function () {
    return redirect()->route('auth.login');
})->name('login');

Route::prefix('lendify')->middleware('auth')->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'profile')->name('profile');
    });

    Route::controller(AdminUserController::class)->prefix('users')->group(function () {
        Route::get('/', 'listUsers')->name('user.list');
        Route::get('create', 'create')->name('user.create');
        Route::post('/', 'store')->name('user.store');
        Route::get('{user}/edit', 'edit')->name('user.edit');
        Route::put('{user}', 'update')->name('user.update');
        Route::delete('{user}', 'destroy')->name('user.destroy');
        Route::get('data', 'getAllUsers')->name('user.data');
    });

    Route::controller(AdminKategoriController::class)->prefix('kategori')->group(function () {
        Route::get('/', 'listCategories')->name('kategori.list');
        Route::get('create', 'create')->name('kategori.create');
        Route::post('/', 'store')->name('kategori.store');
        Route::get('{kategori}/edit', 'edit')->name('kategori.edit');
        Route::put('{kategori}', 'update')->name('kategori.update');
        Route::delete('{kategori}', 'destroy')->name('kategori.destroy');
    });

    Route::controller(AdminAlatController::class)->prefix('alat')->group(function () {
        Route::get('/', 'listAlat')->name('alat.list');
        Route::get('create', 'create')->name('alat.create');
        Route::post('/', 'store')->name('alat.store');
        Route::get('{alat}/edit', 'edit')->name('alat.edit');
        Route::put('{alat}', 'update')->name('alat.update');
        Route::delete('{alat}', 'destroy')->name('alat.destroy');
    });

    Route::controller(AdminPeminjamanController::class)->prefix('peminjaman')->group(function () {
        Route::get('/', 'listPeminjaman')->name('peminjaman.list');
        Route::get('create', 'create')->name('peminjaman.create');
        Route::post('/', 'store')->name('peminjaman.store');
        Route::get('{peminjaman}', 'show')->name('peminjaman.show');
        Route::get('{peminjaman}/edit', 'edit')->name('peminjaman.edit');
        Route::put('{peminjaman}', 'update')->name('peminjaman.update');
        Route::delete('{peminjaman}', 'destroy')->name('peminjaman.destroy');
    });
});
