<?php

namespace App\Http\Controllers\Admin\Alat;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\FileManager;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlatController extends Controller
{
    public function listAlat(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Alat::with('kategori', 'gambarAlat')
            ->select('id', 'kategori_id', 'nama_alat', 'deskripsi', 'jumlah_stok', 'baik', 'rusak_ringan', 'diperbaiki', 'created_at', 'gambar_alat_id');

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

        return view('admin.alat.list', compact('alats', 'kategoriAlats'));
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $kategoriAlats = KategoriAlat::orderBy('nama_kategori')->get();

        return view('admin.alat.create', [
            'kategoriAlats' => $kategoriAlats,
            'files' => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->where('file_path', 'like', '%/uploads/gambar-alat/%')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kategori_id'    => ['required', 'exists:kategori_alat,id'],
                'nama_alat'      => ['required', 'string', 'max:255'], // regex dihapus
                'deskripsi'      => ['nullable', 'string'],
                'gambar_alat_id' => ['nullable', 'exists:file_managers,id'],
                'baik'           => ['required', 'integer', 'min:0'],
                'rusak_ringan'   => ['required', 'integer', 'min:0'],
                'diperbaiki'     => ['required', 'integer', 'min:0'],
            ]);

            $validated['nama_alat'] = ucwords(strtolower($validated['nama_alat']));
            $validated['jumlah_stok'] = $validated['baik'] + $validated['rusak_ringan'] + $validated['diperbaiki'];

            Alat::create($validated);

            return redirect()
                ->route('alat.list')
                ->with('success', 'Alat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan alat.');
        }
    }

    public function edit(Alat $alat)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $kategoriAlats = KategoriAlat::orderBy('nama_kategori')->get();

        return view('admin.alat.edit', [
            'alat'          => $alat,
            'kategoriAlats' => $kategoriAlats,
            'files'         => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->where('file_path', 'like', '%/uploads/gambar-alat/%')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function update(Request $request, Alat $alat)
    {
        try {
            $validated = $request->validate([
                'kategori_id'    => ['required', 'exists:kategori_alat,id'],
                'nama_alat'      => ['required', 'string', 'max:255'], // regex dihapus
                'deskripsi'      => ['nullable', 'string'],
                'gambar_alat_id' => ['nullable', 'exists:file_managers,id'],
                'baik'           => ['required', 'integer', 'min:0'],
                'rusak_ringan'   => ['required', 'integer', 'min:0'],
                'diperbaiki'     => ['required', 'integer', 'min:0'],
            ]);

            $validated['nama_alat'] = ucwords(strtolower($validated['nama_alat']));
            $validated['jumlah_stok'] = $validated['baik'] + $validated['rusak_ringan'] + $validated['diperbaiki'];

            $alat->update($validated);

            return redirect()
                ->route('alat.list')
                ->with('success', 'Alat berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui alat.');
        }
    }

    public function destroy(Alat $alat)
    {
        try {
            $alat->delete();

            return redirect()
                ->route('alat.list')
                ->with('success', 'Alat berhasil dihapus.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus alat.');
        }
    }
}