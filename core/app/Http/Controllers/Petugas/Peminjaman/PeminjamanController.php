<?php

namespace App\Http\Controllers\Petugas\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\AlatStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    public function updateStatus(Request $request, Peminjaman $peminjaman)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in($this->allowedStatuses)],
        ]);

        if ($peminjaman->status === $validated['status']) {
            return back()->with('info', 'Status peminjaman sudah sesuai.');
        }

        $previousStatus = $peminjaman->status;
        $previousAlatId = $peminjaman->alat_id;
        $previousTotal = $peminjaman->total_alat;

        DB::transaction(function () use (
            $peminjaman,
            $validated,
            $previousStatus,
            $previousAlatId,
            $previousTotal
        ) {
            if ($previousStatus === 'approve' && $validated['status'] !== 'approve') {
                AlatStockService::restore($previousAlatId, $previousTotal);
            }

            $peminjaman->update([
                'status' => $validated['status'],
            ]);

            if ($validated['status'] === 'approve' && $previousStatus !== 'approve') {
                AlatStockService::deduct($peminjaman->alat_id, $peminjaman->total_alat);
            }
        });

        return back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }
}
