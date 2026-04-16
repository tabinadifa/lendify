<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class KategoriAlat extends Model
{
    use LogsActivity;

    protected $table = 'kategori_alat';

    protected $fillable = [
        'nama_kategori'
    ];

    public function alat()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}
