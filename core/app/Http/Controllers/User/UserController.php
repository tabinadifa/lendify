<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Halaman list pengguna (Blade)
     */
    public function listUsers(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = User::select(
            'id',
            'name',
            'username',
            'email',
            'role',
            'created_at',
            'last_active_at'
        );

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        // 📄 Per page
        $perPage = $request->get('per_page', 10);

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 🔄 Transform data tambahan
        $users->getCollection()->transform(function ($user) {
            $user->active_since = $user->created_at
                ? $user->created_at->translatedFormat('M Y')
                : '-';

            $user->last_active = $user->last_active_at
                ? Carbon::parse($user->last_active_at)->diffForHumans()
                : '-';

            return $user;
        });

        return view('user.list', compact('users'));
    }

    /**
     * API data pengguna (JSON / AJAX)
     */
    public function getAllUsers(Request $request)
    {
        $query = User::select(
            'id',
            'name',
            'username',
            'email',
            'role',
            'created_at',
            'last_active_at'
        );

        // 🔍 Search (opsional)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'active_since' => $user->created_at
                    ? $user->created_at->translatedFormat('M Y')
                    : '-',
                'last_active' => $user->last_active_at
                    ? Carbon::parse($user->last_active_at)->diffForHumans()
                    : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
