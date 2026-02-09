<?php

namespace App\Http\Controllers\Admin\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
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

        return view('admin.peminjaman.list', [
            'peminjaman' => $peminjaman,
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin.peminjaman.create', [
            'peminjams' => User::select('id', 'name')->get(),
            'alats' => Alat::select('id', 'nama_alat')->get(),
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'alat_id' => ['required', 'exists:alat,id'],
            'peminjam_id' => ['required', 'exists:users,id'],
            'total_alat' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['nullable', Rule::in($this->allowedStatuses)],
            'alasan_ditolak' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $validated['status'] ?? 'pending';
        $payload = [
            'alat_id' => $validated['alat_id'],
            'peminjam_id' => $validated['peminjam_id'],
            'total_alat' => $validated['total_alat'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'status' => $status,
            'alasan_ditolak' => $validated['alasan_ditolak'] ?? null,
        ];

        DB::transaction(function () use ($payload, $status) {
            if ($status === 'approve') {
                AlatStockService::deduct($payload['alat_id'], $payload['total_alat']);
            }

            Peminjaman::create($payload);
        });

        return redirect()
            ->route('peminjaman.list')
            ->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    public function show(Peminjaman $peminjaman)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjaman->loadMissing('alat:id,nama_alat', 'peminjam:id,name,username,email');

        return view('admin.peminjaman.show', [
            'peminjaman' => $peminjaman->load('alat', 'peminjam'),
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function edit(Peminjaman $peminjaman)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjaman->loadMissing('alat:id,nama_alat', 'peminjam:id,name');

        return view('admin.peminjaman.edit', [
            'peminjaman' => $peminjaman->load('peminjam'),
            'alats' => Alat::select('id', 'nama_alat')->orderBy('nama_alat')->get(),
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'alat_id' => ['required', 'exists:alat,id'],
            'peminjam_id' => ['required', 'exists:users,id'],
            'total_alat' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in($this->allowedStatuses)],
            'alasan_ditolak' => ['nullable', 'string', 'max:255'],
        ]);

        // Jika status bukan rejected, alasan ditolak dikosongkan
        if ($validated['status'] !== 'rejected') {
            $validated['alasan_ditolak'] = null;
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
            if ($previousStatus === 'approve') {
                AlatStockService::restore($previousAlatId, $previousTotal);
            }

            $peminjaman->update([
                'alat_id' => $validated['alat_id'],
                'peminjam_id' => $validated['peminjam_id'],
                'total_alat' => $validated['total_alat'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status' => $validated['status'],
                'alasan_ditolak' => $validated['alasan_ditolak'],
            ]);

            if ($validated['status'] === 'approve') {
                AlatStockService::deduct($validated['alat_id'], $validated['total_alat']);
            }
        });

        return redirect()
            ->route('peminjaman.list')
            ->with('success', 'Data peminjaman berhasil diperbarui.');
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
            ->route('peminjaman.list')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
