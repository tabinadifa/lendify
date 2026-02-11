<?php

namespace App\Http\Controllers\Peminjam\Riwayat;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\AlatStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    private array $allowedStatuses = ['pending', 'approve', 'rejected', 'returned'];

    public function listPeminjamanUser(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Peminjaman::with([
            'alat:id,nama_alat',
            'pengembalian:id,peminjaman_id,tanggal_pengembalian'
        ])->select(
            'id',
            'alat_id',
            'total_alat',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status',
            'alasan_ditolak',
            'created_at'
        )->where('peminjam_id', Auth::id());

        if ($request->filled('status') && in_array($request->status, $this->allowedStatuses, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->whereHas('alat', function ($sub) use ($keyword) {
                $sub->where('nama_alat', 'like', "%{$keyword}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $riwayats = $query
            ->latest('tanggal_pinjam')
            ->paginate($perPage)
            ->withQueryString();

        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approve' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
        ];

        $statusBadges = [
            'pending' => 'bg-warning text-dark',
            'approve' => 'bg-success',
            'rejected' => 'bg-danger',
            'returned' => 'bg-secondary',
        ];

        return view('peminjam.riwayat.list', [
            'riwayats' => $riwayats,
            'statusLabels' => $statusLabels,
            'statusBadges' => $statusBadges,
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }


        DB::transaction(function () use ($peminjaman) {
            if ($peminjaman->status === 'approve') {
                AlatStockService::restore($peminjaman->alat_id, $peminjaman->total_alat);
            }

            $peminjaman->delete();
        });

        return redirect()
            ->route('peminjam.riwayat.list')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
