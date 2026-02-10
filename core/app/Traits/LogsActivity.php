<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logActivity('create', 'Menambahkan data baru', $model);
        });

        static::updated(function ($model) {
            // Check if there are any changes
            if (empty($model->getDirty())) {
                return;
            }
            self::logActivity('update', 'Memperbarui data', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('delete', 'Menghapus data', $model);
        });
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
        } else if ($action === 'create') {
            $properties = [
                'attributes' => $model->getAttributes(),
            ];
        } else if ($action === 'delete') {
            $properties = [
                'old' => $model->getOriginal(), // Or getAttributes() since it's deleted but object persists in memory
            ];
        }

        // Avoid logging hidden attributes (like password)
        if (isset($properties['attributes']) && isset($properties['attributes']['password'])) {
            unset($properties['attributes']['password']);
        }
        if (isset($properties['old']) && isset($properties['old']['password'])) {
            unset($properties['old']['password']);
        }

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
