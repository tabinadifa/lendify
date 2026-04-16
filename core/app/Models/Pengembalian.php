<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use LogsActivity;

    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'tanggal_pengembalian',
        'kondisi_alat',
        'status',
        'denda',
        'metode_pembayaran',
        'file_bukti_pengembalian_id',
        'catatan',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function fileBuktiPengembalian()
    {
        return $this->belongsTo(FileManager::class, 'file_bukti_pengembalian_id');
    }
}
