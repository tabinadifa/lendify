<?php

namespace App\Services;

use App\Models\Alat;
use Illuminate\Support\Facades\DB;
use Exception;

class AlatStockService
{
    /**
     * Mengurangi stok alat (prioritas baik, lalu rusak ringan).
     * Mengembalikan array ['baik' => int, 'rusak' => int] yang menunjukkan jumlah yang diambil dari masing-masing.
     *
     * @param int $alatId
     * @param int $jumlah
     * @return array
     * @throws Exception
     */
    public static function deduct(int $alatId, int $jumlah): array
    {
        return DB::transaction(function () use ($alatId, $jumlah) {
            $alat = Alat::where('id', $alatId)->lockForUpdate()->first();

            if (!$alat) {
                throw new Exception("Alat tidak ditemukan.");
            }

            $totalTersedia = $alat->baik + $alat->rusak_ringan;
            if ($totalTersedia < $jumlah) {
                throw new Exception("Stok tidak mencukupi. Tersedia total (baik + rusak ringan): {$totalTersedia}, Dibutuhkan: {$jumlah}.");
            }

            $ambilDariBaik = min($alat->baik, $jumlah);
            $ambilDariRusak = $jumlah - $ambilDariBaik;

            // Kurangi stok
            $alat->baik -= $ambilDariBaik;
            $alat->rusak_ringan -= $ambilDariRusak;
            $alat->jumlah_stok -= $jumlah; // total stok juga berkurang
            $alat->save();

            return [
                'baik' => $ambilDariBaik,
                'rusak' => $ambilDariRusak,
            ];
        });
    }

    /**
     * Mengembalikan stok alat berdasarkan jumlah yang sebelumnya diambil dari baik dan rusak.
     *
     * @param int $alatId
     * @param int $jumlahBaik
     * @param int $jumlahRusak
     * @throws Exception
     */
    public static function restore(int $alatId, int $jumlahBaik, int $jumlahRusak): void
    {
        DB::transaction(function () use ($alatId, $jumlahBaik, $jumlahRusak) {
            $alat = Alat::where('id', $alatId)->lockForUpdate()->first();

            if (!$alat) {
                throw new Exception("Alat tidak ditemukan.");
            }

            $alat->baik += $jumlahBaik;
            $alat->rusak_ringan += $jumlahRusak;
            $alat->jumlah_stok += ($jumlahBaik + $jumlahRusak);
            $alat->save();
        });
    }
}