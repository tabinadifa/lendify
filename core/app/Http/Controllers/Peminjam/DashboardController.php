<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Total peminjaman milik user ini
        $totalPeminjaman = Peminjaman::where('peminjam_id', $user->id)->count();

        // Peminjaman aktif: status 'approve' (disetujui, sedang dipinjam)
        $peminjamanAktif = Peminjaman::where('peminjam_id', $user->id)
            ->where('status', 'approve')
            ->count();

        // Alat yang belum dikembalikan (approve & belum ada pengembalian)
        $belumDikembalikan = Peminjaman::where('peminjam_id', $user->id)
            ->where('status', 'approve')
            ->whereDoesntHave('pengembalian')
            ->count();

        // Peminjaman terlambat (melewati tanggal_kembali & belum dikembalikan)
        $terlambat = Peminjaman::where('peminjam_id', $user->id)
            ->where('status', 'approve')
            ->where('tanggal_kembali', '<', now())
            ->whereDoesntHave('pengembalian')
            ->count();

        // Total sudah dikembalikan
        $sudahDikembalikan = Peminjaman::where('peminjam_id', $user->id)
            ->where('status', 'returned')
            ->count();

        // Daftar alat yang sedang dipinjam (approve & belum ada pengembalian)
        $alatDipinjam = Peminjaman::with('alat')
            ->where('peminjam_id', $user->id)
            ->where('status', 'approve')
            ->orderBy('tanggal_kembali', 'asc')
            ->get();

        // Peminjaman menunggu persetujuan
        $menungguPersetujuan = Peminjaman::where('peminjam_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Riwayat peminjaman terbaru (5 terakhir)
        $riwayatTerbaru = Peminjaman::with('alat')
            ->where('peminjam_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik mingguan (7 hari terakhir)
        $weeklyStats = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->translatedFormat('D'),
                'count' => Peminjaman::where('peminjam_id', $user->id)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        });

        // Persentase pengembalian
        $returnCompletionPercentage = $totalPeminjaman > 0
            ? round(($sudahDikembalikan / $totalPeminjaman) * 100)
            : 0;

        return view('peminjam.dashboard', compact(
            'totalPeminjaman',
            'peminjamanAktif',
            'belumDikembalikan',
            'terlambat',
            'sudahDikembalikan',
            'menungguPersetujuan',
            'alatDipinjam',
            'riwayatTerbaru',
            'weeklyStats',
            'returnCompletionPercentage',
        ));
    }
}