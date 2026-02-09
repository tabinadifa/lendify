<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileManager extends Model
{
    protected $table = 'file_managers';
    
    protected $fillable = [
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function uploader()
    {   
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getNamaFileAttribute()
    {
        return $this->attributes['file_name'] ?? null;
    }

    public function getPathAttribute()
    {
        return $this->attributes['file_path'] ?? null;
    }
}
