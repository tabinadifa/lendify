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

        // Tampilkan alat yang memiliki stok baik > 0 ATAU stok rusak_ringan > 0
        $query = Alat::with('kategori', 'gambarAlat')
            ->select('id', 'kategori_id', 'nama_alat', 'deskripsi', 'jumlah_stok', 'baik', 'rusak_ringan', 'diperbaiki', 'created_at', 'gambar_alat_id')
            ->where(function($q) {
                $q->where('baik', '>', 0)->orWhere('rusak_ringan', '>', 0);
            });

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

    public function create(Request $request, Alat $alat)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek ketersediaan: baik > 0 atau rusak_ringan > 0
        if ($alat->baik <= 0 && $alat->rusak_ringan <= 0) {
            return redirect()->route('peminjam.peminjaman.list')
                ->with('error', 'Alat ini tidak tersedia untuk dipinjam (stok baik dan rusak ringan habis).');
        }

        $alat = Alat::with('gambarAlat')
            ->select('id', 'nama_alat', 'jumlah_stok', 'baik', 'rusak_ringan', 'diperbaiki', 'gambar_alat_id', 'deskripsi')
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
            'alat_id'      => ['required', 'exists:alat,id'],
            'total_alat'   => ['required', 'integer', 'min:1'],
            'tanggal_pinjam'  => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali' => ['required', 'date', 'after:tanggal_pinjam'],
        ]);

        $alat = Alat::find($validated['alat_id']);

        // Tentukan kondisi yang bisa dipinjam dan batas maksimal
        if ($alat->baik > 0) {
            // Prioritas: pakai stok baik
            $maxBorrow = $alat->baik;
            $kondisi = 'baik';
        } elseif ($alat->rusak_ringan > 0) {
            // Stok baik habis, gunakan rusak ringan sebagai pilihan terakhir
            $maxBorrow = $alat->rusak_ringan;
            $kondisi = 'rusak_ringan';
        } else {
            return back()->withInput()
                ->withErrors(['total_alat' => 'Maaf, alat ini sedang tidak tersedia untuk dipinjam.']);
        }

        if ($validated['total_alat'] > $maxBorrow) {
            return back()->withInput()
                ->withErrors(['total_alat' => "Jumlah alat yang dipinjam tidak boleh melebihi stok {$kondisi} yang tersedia ({$maxBorrow})."]);
        }

        Peminjaman::create([
            'alat_id'          => $validated['alat_id'],
            'peminjam_id'      => Auth::id(),
            'total_alat'       => $validated['total_alat'],
            'kondisi_dipinjam' => $kondisi, // simpan kondisi yang dipinjam
            'tanggal_pinjam'   => $validated['tanggal_pinjam'],
            'tanggal_kembali'  => $validated['tanggal_kembali'],
            'status'           => 'pending',
        ]);

        return redirect()->route('peminjam.peminjaman.list')
            ->with('success', 'Peminjaman berhasil diajukan. Silakan tunggu persetujuan dari petugas.');
    }
}