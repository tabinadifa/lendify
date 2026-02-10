<?php

namespace App\Http\Controllers\Petugas\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private const PER_PAGE = 5;

    public function index(Request $request)
    {
        $filters = $this->validateFilters($request);

        $data = $this->prepareReportData($filters, paginate: true);

        return view('petugas.laporan.index', $data);
    }

    public function export(Request $request)
    {
        $filters = $this->validateFilters($request);

        $data = $this->prepareReportData($filters, paginate: false);

        $filename = 'laporan-peminjaman-pengembalian-' . now()->format('Ymd_His') . '.xls';

        $content = view('petugas.laporan.export', $data)->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function validateFilters(Request $request): array
    {
        return $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }

    protected function prepareReportData(array $filters, bool $paginate = true): array
    {
        $start = isset($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : null;
        $end = isset($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : null;

        $peminjamanQuery = Peminjaman::with(['alat', 'peminjam'])
            ->orderByDesc('tanggal_pinjam');
        $pengembalianQuery = Pengembalian::with(['peminjaman.alat', 'peminjaman.peminjam'])
            ->orderByDesc('tanggal_pengembalian');

        $this->applyDateFilter($peminjamanQuery, 'tanggal_pinjam', $start, $end);
        $this->applyDateFilter($pengembalianQuery, 'tanggal_pengembalian', $start, $end);

        $peminjamanForCount = clone $peminjamanQuery;
        $peminjamanForSum = clone $peminjamanQuery;
        $pengembalianForSummary = clone $pengembalianQuery;

        $peminjaman = $paginate
            ? (clone $peminjamanQuery)->paginate(self::PER_PAGE)->withQueryString()
            : (clone $peminjamanQuery)->get();

        $pengembalian = $paginate
            ? (clone $pengembalianQuery)->paginate(self::PER_PAGE)->withQueryString()
            : (clone $pengembalianQuery)->get();

        $pengembalianSummary = $pengembalianForSummary->get();

        $summary = [
            'total_peminjaman' => $peminjamanForCount->count(),
            'total_pengembalian' => $pengembalianSummary->count(),
            'alat_dipinjam' => (int) $peminjamanForSum->sum('total_alat'),
            'alat_dikembalikan' => (int) $pengembalianSummary->sum(fn ($item) => optional($item->peminjaman)->total_alat ?? 0),
        ];

        return [
            'peminjaman' => $peminjaman,
            'pengembalian' => $pengembalian,
            'summary' => $summary,
            'filters' => $filters,
            'exportUrl' => route('petugas.laporan.export', array_filter($filters)),
        ];
    }

    /**
     * Tambahan kecil agar filter tanggal bisa digunakan ulang untuk dua query.
     */
    protected function applyDateFilter(Builder $query, string $column, ?Carbon $start, ?Carbon $end): void
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
            return;
        }

        if ($start) {
            $query->whereDate($column, '>=', $start);
        }

        if ($end) {
            $query->whereDate($column, '<=', $end);
        }
    }
}