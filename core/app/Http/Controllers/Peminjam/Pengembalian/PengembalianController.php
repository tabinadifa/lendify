<?php

namespace App\Http\Controllers\Peminjam\Pengembalian;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends Controller
{
    public function listPengembalian(Request $request)
    {
        $userId = $request->user()?->id;
        if (!$userId) {
            abort(403, 'Unauthorized');
        }

        $query = Pengembalian::with([
            'peminjaman:id,alat_id,total_alat,tanggal_pinjam,tanggal_kembali,status',
            'peminjaman.alat:id,nama_alat'
        ])->select(
            'id',
            'peminjaman_id',
            'tanggal_pengembalian',
            'kondisi_alat',
            'denda',
            'created_at'
        );

        $query->whereHas('peminjaman', function ($sub) use ($userId) {
            $sub->where('peminjam_id', $userId);
        });

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->whereHas('peminjaman.alat', function ($sub) use ($keyword) {
                $sub->where('nama_alat', 'like', "%{$keyword}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $pengembalians = $query
            ->latest('tanggal_pengembalian')
            ->paginate($perPage)
            ->withQueryString();

        return view('peminjam.pengembalian.list', compact('pengembalians'));
    }

    public function show(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->load(
            'peminjaman:id,alat_id,peminjam_id,total_alat,tanggal_pinjam,tanggal_kembali,status,alasan_ditolak',
            'peminjaman.alat:id,nama_alat,kategori_id',
            'peminjaman.alat.kategori:id,nama_kategori',
            'peminjaman.peminjam:id,name,username,email',
            'fileBuktiPengembalian'
        );

        if ($pengembalian->peminjaman?->peminjam_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('peminjam.pengembalian.show', [
            'pengembalian' => $pengembalian,
        ]);
    }
}