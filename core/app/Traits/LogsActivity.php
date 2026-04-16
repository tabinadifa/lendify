<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $description = static::generateDescription('create', $model);
            static::logActivity('create', $description, $model);
        });

        static::updated(function ($model) {
            if (empty($model->getDirty())) {
                return;
            }
            $description = static::generateDescription('update', $model);
            static::logActivity('update', $description, $model);
        });

        static::deleted(function ($model) {
            $description = static::generateDescription('delete', $model);
            static::logActivity('delete', $description, $model);
        });
    }

    /**
     * Generate deskripsi log secara dinamis berdasarkan model dan aksi.
     */
    protected static function generateDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $modelId = $model->id;

        switch ($modelName) {
            case 'User':
                if ($action === 'create') return "Menambahkan user baru: {$model->name} ({$model->role})";
                if ($action === 'update') return "Memperbarui data user: {$model->name}";
                if ($action === 'delete') return "Menghapus user: {$model->name}";
                break;

            case 'Alat':
                if ($action === 'create') return "Menambahkan alat baru: {$model->nama_alat}";
                if ($action === 'update') return "Memperbarui data alat: {$model->nama_alat}";
                if ($action === 'delete') return "Menghapus alat: {$model->nama_alat}";
                break;

            case 'KategoriAlat':
                if ($action === 'create') return "Menambahkan kategori alat: {$model->nama_kategori}";
                if ($action === 'update') return "Memperbarui kategori alat: {$model->nama_kategori}";
                if ($action === 'delete') return "Menghapus kategori alat: {$model->nama_kategori}";
                break;

            case 'Peminjaman':
                $peminjam = $model->peminjam->name ?? 'unknown';
                $alat = $model->alat->nama_alat ?? 'unknown';
                $total = $model->total_alat;
                $statusLama = $model->getOriginal('status');
                $statusBaru = $model->status;

                if ($action === 'create') {
                    return "Mengajukan peminjaman #{$modelId}: {$alat} ({$total} unit) oleh {$peminjam}";
                }
                if ($action === 'update' && $statusLama !== $statusBaru) {
                    return "Mengubah status peminjaman #{$modelId} dari {$statusLama} menjadi {$statusBaru}";
                }
                if ($action === 'update') {
                    return "Memperbarui data peminjaman #{$modelId}";
                }
                if ($action === 'delete') {
                    return "Menghapus peminjaman #{$modelId} (alat: {$alat})";
                }
                break;

            case 'Pengembalian':
                $peminjamanId = $model->peminjaman_id;
                $kondisi = $model->kondisi_alat;
                if ($action === 'create') {
                    return "Melakukan pengembalian untuk peminjaman #{$peminjamanId} dengan kondisi {$kondisi}";
                }
                if ($action === 'update') {
                    return "Memperbarui data pengembalian #{$modelId} (peminjaman #{$peminjamanId})";
                }
                if ($action === 'delete') {
                    return "Menghapus data pengembalian #{$modelId}";
                }
                break;

            case 'FileManager':
                $fileName = $model->file_name;
                if ($action === 'create') return "Mengunggah file: {$fileName}";
                if ($action === 'delete') return "Menghapus file: {$fileName}";
                break;
        }

        // Default deskripsi jika tidak ada yang spesifik
        return ucfirst($action) . " data " . $modelName . " #" . $modelId;
    }

    protected static function logActivity($action, $description, $model)
    {
        $user = Auth::user();

        $properties = [];
        if ($action === 'update') {
            $properties = [
                'old' => $model->getOriginal(),
                'attributes' => $model->getAttributes(),
            ];
        } elseif ($action === 'create') {
            $properties = [
                'attributes' => $model->getAttributes(),
            ];
        } elseif ($action === 'delete') {
            $properties = [
                'old' => $model->getOriginal(),
            ];
        }

        // Hapus field sensitif
        if (isset($properties['attributes']['password'])) unset($properties['attributes']['password']);
        if (isset($properties['old']['password'])) unset($properties['old']['password']);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}