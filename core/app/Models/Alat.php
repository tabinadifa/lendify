<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use \App\Traits\LogsActivity;

    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'deskripsi',
        'jumlah_stok',
        'gambar_alat_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'alat_id');
    }

    public function gambarAlat()
    {
        return $this->belongsTo(FileManager::class, 'gambar_alat_id');
    }
}
