<?php

namespace App\Services;

use App\Models\Alat;
use Illuminate\Validation\ValidationException;

class AlatStockService
{
    public static function deduct(int $alatId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $alat = self::lockAlat($alatId);

        if ($alat->jumlah_stok < $quantity) {
            throw ValidationException::withMessages([
                'total_alat' => 'Stok alat tidak mencukupi untuk jumlah yang diminta.',
            ]);
        }

        $alat->decrement('jumlah_stok', $quantity);
    }

    public static function restore(int $alatId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $alat = self::lockAlat($alatId);

        $alat->increment('jumlah_stok', $quantity);
    }

    private static function lockAlat(int $alatId): Alat
    {
        $alat = Alat::whereKey($alatId)->lockForUpdate()->first();

        if (!$alat) {
            throw ValidationException::withMessages([
                'alat_id' => 'Data alat tidak ditemukan.',
            ]);
        }

        return $alat;
    }
}
