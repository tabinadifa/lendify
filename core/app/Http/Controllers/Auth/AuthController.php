<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /* =======================
     * FORM
     * ======================= */
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    /* =======================
     * LOGIN
     * ======================= */
    public function loginProcess(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable'
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (!Auth::attempt([
            $loginField => $request->login,
            'password'  => $request->password,
        ], $request->remember)) {
            return back()->withErrors([
                'login' => 'Username / Email atau password salah'
            ])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user instanceof User) {
            $user->last_active_at = Carbon::now();
            $user->save();
        }

        return redirect()->route('dashboard')->with('success', 'Login berhasil');
    }

    /* =======================
     * REGISTER
     * ======================= */
    public function registerProcess(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'username' => 'required|string|max:100|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'username'       => $request->username,
            'password'       => Hash::make($request->password),
            'role'           => 'peminjam',
            'is_active'      => true,
            'last_active_at' => Carbon::now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil, silakan login');
    }

    /* =======================
     * LOGOUT
     * ======================= */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'Logout berhasil');
    }
}
