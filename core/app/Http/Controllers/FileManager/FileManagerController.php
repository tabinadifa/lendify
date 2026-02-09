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

        return view('admin.filemanager.list', [
            'files' => $files,
            'defaultFolder' => 'bukti-pengembalian',
        ]);
    }

    /* =======================
     * UPLOAD IMAGE (WEB & AJAX)
     * ======================= */
    public function uploadImage(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }
            
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validator = Validator::make($request->all(), [
            'file'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'folder' => 'required|string'
        ], [
            'file.required' => 'File harus dipilih',
            'file.image' => 'File harus berupa gambar',
            'file.mimes' => 'Format file harus JPG, JPEG, PNG, atau WEBP',
            'file.max' => 'Ukuran file maksimal 2MB',
            'folder.required' => 'Folder harus ditentukan',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
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
            $fileModel = FileManager::create([
                'file_name'   => $fileName,
                'file_path'   => $filePath,
                'mime_type'   => $file->getClientMimeType(),
                'size'        => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            // Response untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gambar berhasil diupload',
                    'file' => [
                        'id' => $fileModel->id,
                        'file_name' => $fileModel->file_name,
                        'nama_file' => $fileModel->file_name,
                        'file_path' => asset($fileModel->file_path),
                        'path' => asset($fileModel->file_path),
                        'mime_type' => $fileModel->mime_type,
                        'size' => $fileModel->size,
                        'created_at' => $fileModel->created_at->toISOString(),
                    ]
                ], 200);
            }

            // Response untuk form biasa
            return redirect()
                ->back()
                ->with('success', 'Gambar berhasil diupload');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /* =======================
     * DELETE IMAGE (WEB & AJAX)
     * ======================= */
    public function deleteImage(Request $request, $id)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $file = FileManager::find($id);

        if (!$file) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gambar berhasil dihapus'
                ], 200);
            }

            return redirect()
                ->back()
                ->with('success', 'Gambar berhasil dihapus');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus gambar: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus gambar: ' . $e->getMessage());
        }
    }

    /* =======================
     * GET FILES (API/AJAX)
     * ======================= */
    public function getFiles(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $folder = $request->get('folder');
        
        $query = FileManager::with('uploader:id,name')
            ->where('mime_type', 'like', 'image/%')
            ->select('id', 'file_name', 'file_path', 'mime_type', 'size', 'uploaded_by', 'created_at')
            ->latest();

        if ($folder) {
            $query->where('file_path', 'like', "%/uploads/$folder/%");
        }

        $files = $query->get()->map(function($file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name,
                'nama_file' => $file->file_name,
                'file_path' => asset($file->file_path),
                'path' => asset($file->file_path),
                'mime_type' => $file->mime_type,
                'size' => $file->size,
                'created_at' => $file->created_at->toISOString(),
                'uploader' => $file->uploader ? $file->uploader->name : null,
            ];
        });

        return response()->json([
            'success' => true,
            'files' => $files
        ], 200);
    }
}