<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriAlat extends Model
{
    protected $table = 'kategori_alat';

    protected $fillable = [
        'nama_kategori'
    ];

    public function alat()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}
