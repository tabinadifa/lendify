<?php

namespace App\Http\Controllers\Admin\LogAktivitas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        $this->applyFilters($query, $request);

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.logaktivitas.list', compact('logs'));
    }

    public function exportPdf(Request $request)
    {
        $query = ActivityLog::with('user');

        $this->applyFilters($query, $request);

        $logs = $query->latest()->get();

        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->start_date)->format('d M Y')
            : null;

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->end_date)->format('d M Y')
            : null;

        $pdf = Pdf::loadView('admin.logaktivitas.pdf', compact('logs', 'startDate', 'endDate'))
            ->setPaper('a4', 'landscape');

        $filename = 'log-aktivitas-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    }
}