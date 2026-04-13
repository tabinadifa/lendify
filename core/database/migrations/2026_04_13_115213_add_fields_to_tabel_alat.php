<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->unsignedInteger('rusak_ringan')->default(0)->after('jumlah_stok');
            $table->unsignedInteger('diperbaiki')->default(0)->after('rusak_ringan');
            $table->unsignedInteger('baik')->default(0)->after('diperbaiki');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            //
        });
    }
};
