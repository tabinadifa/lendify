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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')
                    ->constrained('alat')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('peminjam_id')
                    ->constrained('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->integer('total_alat');
            $table->enum('status', ['rejected', 'pending', 'approve', 'returned'])->default('pending');
            $table->text('alasan_ditolak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
