<?php

namespace App\Http\Controllers\Admin\Kategori;

use App\Http\Controllers\Controller;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KategoriController extends Controller
{
    public function listCategories(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        
        $query = KategoriAlat::select('id', 'nama_kategori', 'created_at', 'updated_at');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $kategoriAlats = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.kategori.list', compact('kategoriAlats'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s]+$/', // hanya huruf & spasi
                    'unique:kategori_alat,nama_kategori',
                ],
            ], [
                'nama_kategori.regex' => 'Nama kategori hanya boleh berisi huruf dan spasi.',
            ]);

            $validated['nama_kategori'] = ucwords(strtolower($validated['nama_kategori']));

            KategoriAlat::create($validated);

            return redirect()
                ->route('kategori.list')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan kategori.');
        }
    }

    public function edit(KategoriAlat $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriAlat $kategori)
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s]+$/',
                    Rule::unique('kategori_alat', 'nama_kategori')->ignore($kategori->id),
                ],
            ], [
                'nama_kategori.regex' => 'Nama kategori hanya boleh berisi huruf dan spasi.',
            ]);

            $validated['nama_kategori'] = ucwords(strtolower($validated['nama_kategori']));

            $kategori->update($validated);

            return redirect()
                ->route('kategori.list')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui kategori.');
        }
    }

    public function destroy(KategoriAlat $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('kategori.list')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
