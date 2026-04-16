<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return view('profile', compact('user'));
    }

    public function getDataUser(Request $request)
    {
        $userId = $request->input('user_id', Auth::id());
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terautentikasi.'
            ], 401);
        }

        $user = User::select('id', 'name', 'username', 'email', 'role', 'created_at', 'last_active_at', 'phone', 'address')
            ->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'active_since' => $user->created_at?->translatedFormat('M Y'),
                'last_active_at' => $user->last_active_at ? Carbon::parse($user->last_active_at)->diffForHumans() : null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['nullable', 'string', 'max:13', 'regex:/^[0-9]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        // ✅ Tidak ada lagi error merah!
        $user->fill($validated);
        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        // ✅ Juga aman
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diubah.');
    }
}