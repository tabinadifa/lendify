<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\User\UserController;

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

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'profile')->name('profile');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'listUsers')->name('user.list');
        Route::get('users/data', 'getAllUsers')->name('user.data');
    });

});