<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    // Untuk mencegah duplikasi event dalam waktu singkat
    private static $lastLogged = [];

    // Field yang perubahannya TIDAK perlu dicatat
    protected static $ignoredUpdateFields = [
        'updated_at',
        'last_active_at',
        'remember_token',
        'last_login_at',
        'login_count',
    ];

    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            static::logIfNeeded('create', $model);
        });

        static::updated(function ($model) {
            static::logIfNeeded('update', $model);
        });

        static::deleted(function ($model) {
            static::logIfNeeded('delete', $model);
        });
    }

    /**
     * Log hanya jika ada perubahan signifikan
     */
    protected static function logIfNeeded(string $action, $model)
    {
        // Cegah duplikasi dalam 1 detik untuk model yang sama
        $key = get_class($model) . ':' . ($model->id ?? 'new') . ':' . $action;
        if (isset(self::$lastLogged[$key]) && (microtime(true) - self::$lastLogged[$key]) < 1) {
            return;
        }
        self::$lastLogged[$key] = microtime(true);

        if ($action === 'update') {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            // Hanya ambil field yang tidak diabaikan
            $significant = array_diff_key($dirty, array_flip(self::$ignoredUpdateFields));
            if (empty($significant)) {
                return; // Tidak ada perubahan penting, abaikan
            }
        }

        $description = static::generateDescription($action, $model);
        if (empty($description)) {
            return;
        }

        static::logActivity($action, $description, $model);
    }

    protected static function generateDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $id = $model->id;

        switch ($modelName) {
            case 'User':
                if ($action === 'create') {
                    return "Menambahkan user baru: {$model->name} ({$model->role})";
                }
                if ($action === 'update') {
                    $changes = [];
                    $dirty = $model->getDirty();
                    // Hanya tampilkan perubahan field penting
                    $importantFields = ['name', 'email', 'role', 'phone', 'address', 'password'];
                    foreach ($importantFields as $field) {
                        if (array_key_exists($field, $dirty)) {
                            $changes[] = $field;
                        }
                    }
                    if (empty($changes)) {
                        return ''; // Tidak ada perubahan penting
                    }
                    return "Memperbarui data user {$model->name}: " . implode(', ', $changes);
                }
                if ($action === 'delete') {
                    return "Menghapus user: {$model->name}";
                }
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
                if ($action === 'create') {
                    return "Mengajukan peminjaman #{$id}: {$alat} ({$total} unit) oleh {$peminjam}";
                }
                if ($action === 'update') {
                    $oldStatus = $model->getOriginal('status');
                    $newStatus = $model->status;
                    if ($oldStatus != $newStatus) {
                        return "Mengubah status peminjaman #{$id} dari {$oldStatus} menjadi {$newStatus}";
                    }
                    return "Memperbarui data peminjaman #{$id}";
                }
                if ($action === 'delete') {
                    return "Menghapus peminjaman #{$id} (alat: {$alat})";
                }
                break;

            case 'Pengembalian':
                $peminjamanId = $model->peminjaman_id;
                if ($action === 'create') {
                    return "Melakukan pengembalian untuk peminjaman #{$peminjamanId} dengan kondisi {$model->kondisi_alat}";
                }
                if ($action === 'update') {
                    return "Memperbarui data pengembalian #{$id}";
                }
                if ($action === 'delete') {
                    return "Menghapus data pengembalian #{$id}";
                }
                break;

            case 'FileManager':
                if ($action === 'create') return "Mengunggah file: {$model->file_name}";
                if ($action === 'delete') return "Menghapus file: {$model->file_name}";
                break;
        }

        return ucfirst($action) . " data {$modelName} #{$id}";
    }

    protected static function logActivity($action, $description, $model)
    {
        $user = Auth::user();

        $properties = [];
        if ($action === 'update') {
            $changes = [];
            foreach ($model->getDirty() as $field => $newValue) {
                if (in_array($field, self::$ignoredUpdateFields)) {
                    continue;
                }
                $oldValue = $model->getOriginal($field);
                if ($oldValue != $newValue) {
                    $changes[$field] = ['old' => $oldValue, 'new' => $newValue];
                }
            }
            if (!empty($changes)) {
                $properties = ['changes' => $changes];
            } else {
                return;
            }
        } elseif ($action === 'create') {
            $attributes = $model->getAttributes();
            unset($attributes['password']);
            $properties = ['attributes' => $attributes];
        } elseif ($action === 'delete') {
            $old = $model->getOriginal();
            unset($old['password']);
            $properties = ['old' => $old];
        }

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}