<?php

namespace App\Http\Controllers\Petugas\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    private array $allowedStatuses = ['rejected', 'pending', 'approve'];

    public function listPeminjaman(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Peminjaman::with([
            'alat:id,nama_alat',
            'peminjam:id,name,username,email',
        ])->select(
            'id',
            'alat_id',
            'peminjam_id',
            'total_alat',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status',
            'created_at'
        );

        if ($request->filled('status') && in_array($request->status, $this->allowedStatuses, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($builder) use ($keyword) {
                $builder->whereHas('peminjam', function ($sub) use ($keyword) {
                    $sub->where('name', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('alat', function ($sub) use ($keyword) {
                    $sub->where('nama_alat', 'like', "%{$keyword}%");
                });
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $peminjaman = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('peminjaman.list', [
            'peminjaman' => $peminjaman,
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjaman = Peminjaman::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $this->allowedStatuses)],
        ]);

        $peminjaman->status = $validated['status'];
        $peminjaman->save();

        return redirect()->route('petugas.peminjaman.list')
            ->with('success', 'Status peminjaman berhasil diperbarui.');
    }
}
