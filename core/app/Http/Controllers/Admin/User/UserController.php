<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        return view('admin.user.list', compact('users'));
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

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'last_active_at' => ['nullable', 'date'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('user.list')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('user.list')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('user.list')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('user.list')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
