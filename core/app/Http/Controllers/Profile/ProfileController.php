<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /* =======================
     * FORM
     * ======================= */
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $activeSince = $user->created_at
            ? $user->created_at->translatedFormat('M Y')
            : '-';

        return view('profile', compact('user', 'activeSince'));
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

        $user = User::select('id', 'name', 'username', 'email', 'role', 'created_at', 'last_active_at')
            ->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }

        $activeSince = $user->created_at ? $user->created_at->translatedFormat('M Y') : null;
        $lastActive = $user->last_active_at ? Carbon::parse($user->last_active_at)->diffForHumans() : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'active_since' => $activeSince,
                'last_active_at' => $lastActive,
            ]
        ]);
    }

    // public function updateProfile(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
    //     }

    //     $validated = $request->validate([
    //         'name' => ['required', 'string', 'max:255' . $user->id],
    //         'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
    //     ]);

    // }
}