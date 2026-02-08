<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Kategori\KategoriController;

Route::get('/', function () {
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

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'listUsers')->name('user.list');
        Route::get('create', 'create')->name('user.create');
        Route::post('/', 'store')->name('user.store');
        Route::get('{user}/edit', 'edit')->name('user.edit');
        Route::put('{user}', 'update')->name('user.update');
        Route::delete('{user}', 'destroy')->name('user.destroy');
        Route::get('data', 'getAllUsers')->name('user.data');
    });

    Route::controller(KategoriController::class)->prefix('kategori')->group(function () {
        Route::get('/', 'listCategories')->name('kategori.list');
        Route::get('create', 'create')->name('kategori.create');
        Route::post('/', 'store')->name('kategori.store');
        Route::get('{kategori}/edit', 'edit')->name('kategori.edit');
        Route::put('{kategori}', 'update')->name('kategori.update');
        Route::delete('{kategori}', 'destroy')->name('kategori.destroy');
    });
});