<?php

namespace App\Http\Controllers\Peminjam\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function listAlat(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Alat::with('kategori')
            ->select('id', 'kategori_id', 'nama_alat', 'deskripsi', 'jumlah_stok', 'created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_alat', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $alats = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $kategoriAlats = KategoriAlat::orderBy('nama_kategori')->get();
        return view('peminjam.peminjaman.list', compact('alats', 'kategoriAlats'));
    }

    public function create(Request $request, Alat $alat) {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $alat = Alat::select('id', 'nama_alat', 'jumlah_stok')
            ->where('id', $alat->id)
            ->first();

        return view('peminjam.peminjaman.create', compact('alat'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'alat_id' => ['required', 'exists:alat,id'],
            'total_alat' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali' => ['required', 'date', 'after:tanggal_pinjam'],
        ]);

        $alat = Alat::find($validated['alat_id']);
        if ($validated['total_alat'] > $alat->jumlah_stok) {
            return back()->withInput()
                ->withErrors(['total_alat' => "Jumlah alat yang dipinjam tidak boleh melebihi stok tersedia ({$alat->jumlah_stok})."]);
        }

        Peminjaman::create([
            'alat_id' => $validated['alat_id'],
            'peminjam_id' => Auth::id(),
            'total_alat' => $validated['total_alat'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'status' => 'pending',
        ]);

        return redirect()->route('peminjam.peminjaman.list')
            ->with('success', 'Peminjaman berhasil diajukan. Silakan tunggu persetujuan dari petugas.');
    }
}
