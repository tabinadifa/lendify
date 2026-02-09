<?php

namespace App\Http\Controllers\FileManager;

use App\Http\Controllers\Controller;
use App\Models\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    public function listImages()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $files = FileManager::with('uploader:id,name')
            ->where('mime_type', 'like', 'image/%')
            ->select('id', 'file_name', 'file_path', 'mime_type', 'size', 'uploaded_by', 'created_at')
            ->latest()
            ->get();

        return view('filemanager.list', [
            'files' => $files,
            'defaultFolder' => 'bukti-pengembalian',
        ]);
    }
    /* =======================
     * UPLOAD IMAGE (WEB)
     * ======================= */
    public function uploadImage(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validator = Validator::make($request->all(), [
            'file'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'folder' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $folder = basename($request->folder);
        $file   = $request->file('file');

        $basePath = storage_path("app/public/uploads/$folder");
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0777, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension    = $file->getClientOriginalExtension();
        $cleanName    = Str::slug($originalName);

        $fileName = time() . '_' . $cleanName . '.' . $extension;

        // Simpan file
        $file->storeAs("uploads/$folder", $fileName, 'public');

        $filePath = "storage/uploads/$folder/$fileName";

        // Simpan ke database
        FileManager::create([
            'file_name'   => $fileName,
            'file_path'   => $filePath,
            'mime_type'   => $file->getClientMimeType(),
            'size'        => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Gambar berhasil diupload');
    }

    /* =======================
     * DELETE IMAGE (WEB)
     * ======================= */
    public function deleteImage($id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $file = FileManager::find($id);

        if (!$file) {
            return redirect()
                ->back()
                ->with('error', 'File tidak ditemukan');
        }

        try {
            $relativePath = ltrim(str_replace('storage/', '', $file->file_path), '/');
            $fullPath = storage_path('app/public/' . $relativePath);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            $file->delete();

            return redirect()
                ->back()
                ->with('success', 'Gambar berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus gambar');
        }
    }
}