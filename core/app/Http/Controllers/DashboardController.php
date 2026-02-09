<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $today = Carbon::today();

        $totalAlat = Alat::count();
        $totalStok = (int) Alat::sum('jumlah_stok');
        $alatAddedThisMonth = Alat::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        $totalPeminjaman = Peminjaman::count();
        $borrowedCount = (int) Peminjaman::where('status', 'approve')->sum('total_alat');
        $activeLoans = Peminjaman::whereIn('status', ['pending', 'approve'])->count();
        $totalPengembalian = Pengembalian::count();

        $returnCompletionPercentage = $totalPeminjaman > 0
            ? (int) min(100, max(0, round(($totalPengembalian / $totalPeminjaman) * 100)))
            : 0;

        $borrowedPercentage = $totalStok > 0
            ? round(($borrowedCount / $totalStok) * 100)
            : 0;

        $weeklyStats = $this->getWeeklyLoanStats();
        $reminders = [
            'dueSoon' => $this->getDueSoonLoans($today),
            'overdue' => $this->getOverdueLoans($today),
        ];

        return view('dashboard', [
            'totalAlat' => $totalAlat,
            'alatAddedThisMonth' => $alatAddedThisMonth,
            'borrowedCount' => $borrowedCount,
            'borrowedPercentage' => $borrowedPercentage,
            'activeLoans' => $activeLoans,
            'totalPeminjaman' => $totalPeminjaman,
            'totalPengembalian' => $totalPengembalian,
            'returnCompletionPercentage' => $returnCompletionPercentage,
            'weeklyStats' => $weeklyStats,
            'reminders' => $reminders,
        ]);
    }

    private function getWeeklyLoanStats(): Collection
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays(6);

        $loanCounts = Peminjaman::selectRaw('DATE(tanggal_pinjam) as tanggal, COUNT(*) as total')
            ->whereBetween('tanggal_pinjam', [$start->toDateString(), $end->toDateString()])
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $stats = collect();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $stats->push([
                'label' => $date->translatedFormat('D'),
                'count' => (int) ($loanCounts[$date->toDateString()] ?? 0),
            ]);
        }

        return $stats;
    }

    private function getDueSoonLoans(Carbon $today): Collection
    {
        $rangeEnd = $today->copy()->addDays(2);

        return Peminjaman::with(['alat:id,nama_alat', 'peminjam:id,name'])
            ->whereNotNull('tanggal_kembali')
            ->whereBetween('tanggal_kembali', [$today->toDateString(), $rangeEnd->toDateString()])
            ->where('status', 'approve')
            ->orderBy('tanggal_kembali')
            ->limit(3)
            ->get();
    }

    private function getOverdueLoans(Carbon $today): Collection
    {
        return Peminjaman::with(['alat:id,nama_alat', 'peminjam:id,name'])
            ->whereNotNull('tanggal_kembali')
            ->whereDate('tanggal_kembali', '<', $today->toDateString())
            ->where('status', 'approve')
            ->orderByDesc('tanggal_kembali')
            ->limit(3)
            ->get();
    }
}