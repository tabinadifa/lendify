<?php

namespace App\Http\Controllers\Kategori;

use App\Http\Controllers\Controller;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * Show paginated category list.
     */
    public function index(Request $request)
    {
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

        return view('kategori.list', compact('kategoriAlats'));
    }

    /**
     * Backward compatibility with existing route signature.
     */
    public function category(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Persist new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori_alat,nama_kategori'],
        ]);

        KategoriAlat::create($validated);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show edit form.
     */
    public function edit(KategoriAlat $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update existing category.
     */
    public function update(Request $request, KategoriAlat $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori_alat', 'nama_kategori')->ignore($kategori->id),
            ],
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove category.
     */
    public function destroy(KategoriAlat $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}