<?php

namespace App\Http\Controllers\Petugas\Pengembalian;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\FileManager;
use App\Services\AlatStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengembalianController extends Controller
{
    /* =======================
     * LIST PENGEMBALIAN
     * ======================= */
    public function listPengembalian(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Pengembalian::with([
            'peminjaman:id,alat_id,peminjam_id,tanggal_pinjam,tanggal_kembali',
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username,email',
            'fileBuktiPengembalian:id,file_name,file_path',
        ])->select(
            'id',
            'peminjaman_id',
            'tanggal_pengembalian',
            'kondisi_alat',
            'denda',
            'file_bukti_pengembalian_id',
            'created_at'
        );

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->whereHas('peminjaman.peminjam', function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })->orWhereHas('peminjaman.alat', function ($sub) use ($keyword) {
                $sub->where('nama_alat', 'like', "%{$keyword}%");
            });
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $pengembalians = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('petugas.pengembalian.list', [
            'pengembalians' => $pengembalians,
        ]);
    }

    /* =======================
     * FORM CREATE
     * ======================= */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjamans = Peminjaman::with([
            'alat:id,nama_alat',
            'peminjam:id,name,username',
        ])->select(
            'id',
            'alat_id',
            'peminjam_id',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status'
        )->where('status', 'approve')
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('petugas.pengembalian.create', [
            'peminjamans' => $peminjamans,
            'files' => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /* =======================
     * STORE
     * ======================= */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjaman,id'],
            'tanggal_pengembalian' => ['required', 'date'],
            'kondisi_alat' => ['required', 'string', 'max:255'],
            'denda' => ['nullable', 'numeric', 'min:0'],
            'file_bukti_pengembalian_id' => ['nullable', 'exists:file_managers,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $peminjaman = Peminjaman::select('id', 'alat_id', 'total_alat', 'status', 'tanggal_kembali')
                ->whereKey($validated['peminjaman_id'])
                ->lockForUpdate()
                ->first();

            if (!$peminjaman) {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Data peminjaman tidak ditemukan.',
                ]);
            }

            if ($peminjaman->status === 'returned') {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Peminjaman ini sudah dikembalikan.',
                ]);
            }

            if ($peminjaman->status !== 'approve') {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Hanya peminjaman berstatus approve yang dapat dikembalikan.',
                ]);
            }

            $dendaValue = $this->resolveDendaValue($validated, $peminjaman);

            Pengembalian::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
                'kondisi_alat' => $validated['kondisi_alat'],
                'denda' => $dendaValue,
                'file_bukti_pengembalian_id' => $validated['file_bukti_pengembalian_id'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            AlatStockService::restore($peminjaman->alat_id, $peminjaman->total_alat);

            $peminjaman->update([
                'status' => 'returned',
            ]);
        });

        return redirect()
            ->route('petugas.pengembalian.list')
            ->with('success', 'Data pengembalian berhasil ditambahkan.');
    }

    /* =======================
     * SHOW
     * ======================= */
    public function show(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->load(
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username,email',
            'fileBuktiPengembalian'
        );

        return view('petugas.pengembalian.show', [
            'pengembalian' => $pengembalian,
        ]);
    }

    /* =======================
     * FORM EDIT
     * ======================= */
    public function edit(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->loadMissing(
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username,email'
        );

        $peminjamans = Peminjaman::with([
            'alat:id,nama_alat',
            'peminjam:id,name,username',
        ])->select(
            'id',
            'alat_id',
            'peminjam_id',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status'
        )->where('status', 'approve')
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('petugas.pengembalian.edit', [
            'pengembalian' => $pengembalian,
            'peminjamans' => $peminjamans,
            'files' => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update(Request $request, Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjaman,id'],
            'tanggal_pengembalian' => ['required', 'date'],
            'kondisi_alat' => ['required', 'string', 'max:255'],
            'denda' => ['nullable', 'numeric', 'min:0'],
            'file_bukti_pengembalian_id' => ['nullable', 'exists:file_managers,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        $peminjaman = Peminjaman::select('id', 'tanggal_kembali')
            ->find($validated['peminjaman_id']);

        if (!$peminjaman) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Data peminjaman tidak ditemukan.',
            ]);
        }

        $dendaValue = $this->resolveDendaValue($validated, $peminjaman);

        $pengembalian->update([
            'peminjaman_id' => $validated['peminjaman_id'],
            'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
            'kondisi_alat' => $validated['kondisi_alat'],
            'denda' => $dendaValue,
            'file_bukti_pengembalian_id' => $validated['file_bukti_pengembalian_id'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()
            ->route('pengembalian.list')
            ->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    private function resolveDendaValue(array $validated, Peminjaman $peminjaman): float
    {
        $isLate = $peminjaman->tanggal_kembali
            && Carbon::parse($validated['tanggal_pengembalian'])
            ->gt(Carbon::parse($peminjaman->tanggal_kembali));

        if ($isLate && ((float) ($validated['denda'] ?? 0) <= 0)) {
            throw ValidationException::withMessages([
                'denda' => 'Denda wajib diisi karena pengembalian melewati tanggal kembali.',
            ]);
        }

        return (float) ($validated['denda'] ?? 0);
    }
}
