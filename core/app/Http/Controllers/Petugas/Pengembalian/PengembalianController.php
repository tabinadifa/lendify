<?php

namespace App\Http\Controllers\Petugas\Pengembalian;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\FileManager;
use App\Services\AlatStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PengembalianController extends Controller
{
    /* =======================
     * LIST PENGEMBALIAN
     * ======================= */
    public function listPengembalian(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = Pengembalian::with([
            'peminjaman:id,alat_id,peminjam_id,tanggal_pinjam,tanggal_kembali',
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username,email,phone',
            'fileBuktiPengembalian:id,file_name,file_path',
        ])->select(
            'id',
            'peminjaman_id',
            'tanggal_pengembalian',
            'kondisi_alat',
            'status',
            'denda',
            'metode_pembayaran',
            'file_bukti_pengembalian_id',
            'created_at'
        );

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->whereHas('peminjaman.peminjam', function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })->orWhereHas('peminjaman.alat', function ($sub) use ($keyword) {
                $sub->where('nama_alat', 'like', "%{$keyword}%");
            });
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 10);
        $allowedSizes = [5, 10, 25, 50];
        if (!in_array($perPage, $allowedSizes, true)) {
            $perPage = 10;
        }

        $pengembalians = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('petugas.pengembalian.list', [
            'pengembalians' => $pengembalians,
        ]);
    }

    /* =======================
     * FORM CREATE
     * ======================= */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjamans = Peminjaman::with([
            'alat:id,nama_alat',
            'peminjam:id,name,username',
        ])->select(
            'id',
            'alat_id',
            'peminjam_id',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status'
        )->where('status', 'approve')
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('petugas.pengembalian.create', [
            'peminjamans' => $peminjamans,
            'files' => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->where('file_path', 'like', '%bukti-pengembalian%')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /* =======================
     * STORE
     * ======================= */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjaman,id'],
            'tanggal_pengembalian' => ['required', 'date'],
            'kondisi_alat' => ['required', 'in:baik,rusak_ringan,rusak_berat,hilang'],
            'denda_kondisi' => ['nullable', 'numeric', 'min:0'], // denda dari kondisi alat
            'status' => ['required', 'in:pending,lunas,belum_lunas'], // sesuaikan opsi sesuai kebutuhan
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            'file_bukti_pengembalian_id' => ['nullable', 'exists:file_managers,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $peminjaman = Peminjaman::select('id', 'alat_id', 'total_alat', 'status', 'tanggal_kembali')
                ->whereKey($validated['peminjaman_id'])
                ->lockForUpdate()
                ->first();

            if (!$peminjaman) {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Data peminjaman tidak ditemukan.',
                ]);
            }

            if ($peminjaman->status === 'returned') {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Peminjaman ini sudah dikembalikan.',
                ]);
            }

            if ($peminjaman->status !== 'approve') {
                throw ValidationException::withMessages([
                    'peminjaman_id' => 'Hanya peminjaman berstatus approve yang dapat dikembalikan.',
                ]);
            }

            // Hitung total denda (telat otomatis + kondisi)
            $dendaTelat = $this->hitungDendaTelat(
                $validated['tanggal_pengembalian'],
                $peminjaman->tanggal_kembali
            );
            $dendaKondisi = (float) ($validated['denda_kondisi'] ?? 0);
            $totalDenda = $dendaTelat + $dendaKondisi;

            Pengembalian::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
                'kondisi_alat' => $validated['kondisi_alat'],
                'status' => $validated['status'],
                'denda' => $totalDenda,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'file_bukti_pengembalian_id' => $validated['file_bukti_pengembalian_id'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            AlatStockService::restore($peminjaman->alat_id, $peminjaman->total_alat);

            $peminjaman->update([
                'status' => 'returned',
            ]);
        });

        return redirect()
            ->route('petugas.pengembalian.list')
            ->with('success', 'Data pengembalian berhasil ditambahkan.');
    }

    /* =======================
     * SHOW
     * ======================= */
    public function show(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->load(
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username,email',
            'fileBuktiPengembalian'
        );

        return view('petugas.pengembalian.show', [
            'pengembalian' => $pengembalian,
        ]);
    }

    /* =======================
     * FORM EDIT
     * ======================= */
    public function edit(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->loadMissing(
            'peminjaman.alat:id,nama_alat',
            'peminjaman.peminjam:id,name,username'
        );

        $peminjamans = Peminjaman::with([
            'alat:id,nama_alat',
            'peminjam:id,name,username',
        ])->select(
            'id',
            'alat_id',
            'peminjam_id',
            'tanggal_pinjam',
            'tanggal_kembali',
            'status'
        )->orderByDesc('tanggal_pinjam')
            ->get();

        return view('petugas.pengembalian.edit', [
            'pengembalian' => $pengembalian,
            'peminjamans' => $peminjamans,
            'files' => FileManager::select('id', 'file_name', 'file_path', 'created_at')
                ->where('file_path', 'like', '%bukti-pengembalian%')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update(Request $request, Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjaman,id'],
            'tanggal_pengembalian' => ['required', 'date'],
            'kondisi_alat' => ['required', 'in:baik,rusak_ringan,rusak_berat,hilang'],
            'denda_kondisi' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,lunas,belum_lunas'],
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            'file_bukti_pengembalian_id' => ['nullable', 'exists:file_managers,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        $peminjaman = Peminjaman::select('id', 'tanggal_kembali')
            ->find($validated['peminjaman_id']);

        if (!$peminjaman) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Data peminjaman tidak ditemukan.',
            ]);
        }

        // Hitung ulang denda telat berdasarkan peminjaman yang dipilih
        $dendaTelat = $this->hitungDendaTelat(
            $validated['tanggal_pengembalian'],
            $peminjaman->tanggal_kembali
        );
        $dendaKondisi = (float) ($validated['denda_kondisi'] ?? 0);
        $totalDenda = $dendaTelat + $dendaKondisi;

        $pengembalian->update([
            'peminjaman_id' => $validated['peminjaman_id'],
            'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
            'kondisi_alat' => $validated['kondisi_alat'],
            'status' => $validated['status'],
            'denda' => $totalDenda,
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
            'file_bukti_pengembalian_id' => $validated['file_bukti_pengembalian_id'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()
            ->route('petugas.pengembalian.list')
            ->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    public function sendWhatsApp(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $peminjam = $pengembalian->peminjaman->peminjam;

        if (empty($peminjam->phone)) {
            return redirect()->back()
                ->with('error', 'Nomor WhatsApp peminjam belum diisi.');
        }

        // Bersihkan nomor HP (hapus spasi, tanda kurung, dll)
        $phone = preg_replace('/[^0-9]/', '', $peminjam->phone);
        // Pastikan diawali kode negara (contoh: 62 untuk Indonesia)
        if (!Str::startsWith($phone, '62') && !Str::startsWith($phone, '0')) {
            $phone = '62' . $phone;
        }
        if (Str::startsWith($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = $this->getWhatsAppMessageTemplate($pengembalian);
        $encodedMessage = rawurlencode($message);
        $whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";

        return redirect()->away($whatsappUrl);
    }

    /* =======================
     * DELETE
     * ======================= */
    public function destroy(Pengembalian $pengembalian)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pengembalian->delete();

        return redirect()
            ->route('petugas.pengembalian.list')
            ->with('success', 'Data pengembalian berhasil dihapus.');
    }

    /**
     * Hitung denda keterlambatan.
     * Denda per hari = Rp2.000.
     *
     * @param string $tanggalPengembalian
     * @param string|null $tanggalKembali
     * @return float
     */
    private function hitungDendaTelat(string $tanggalPengembalian, ?string $tanggalKembali): float
    {
        if (!$tanggalKembali) {
            return 0.0;
        }

        $tglKembali = Carbon::parse($tanggalKembali)->startOfDay();
        $tglPengembalian = Carbon::parse($tanggalPengembalian)->startOfDay();

        if ($tglPengembalian->lte($tglKembali)) {
            return 0.0;
        }

        $hariTelat = $tglKembali->diffInDays($tglPengembalian);
        return $hariTelat * 2000;
    }

    private function getWhatsAppMessageTemplate(Pengembalian $pengembalian): string
    {
        $peminjam = $pengembalian->peminjaman->peminjam;
        $alat = $pengembalian->peminjaman->alat;
        $tanggalPengembalian = Carbon::parse($pengembalian->tanggal_pengembalian)->format('d/m/Y');
        $kondisiMap = [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];
        $kondisi = $kondisiMap[$pengembalian->kondisi_alat] ?? ucfirst($pengembalian->kondisi_alat);
        $denda = $pengembalian->denda > 0 ? 'Rp ' . number_format($pengembalian->denda, 0, ',', '.') : 'Tidak ada denda';
        $statusPembayaran = ucfirst($pengembalian->status ?? 'Belum diproses');

        return "📢 *Informasi Pengembalian Alat*
        
Halo *{$peminjam->name}*,

Berikut adalah rincian pengembalian alat Anda:
• *Nama Alat*: {$alat->nama_alat}
• *Tanggal Pengembalian*: {$tanggalPengembalian}
• *Kondisi Alat*: {$kondisi}
• *Denda*: {$denda}
• *Status Pembayaran*: {$statusPembayaran}

Terima kasih telah menggunakan layanan kami.

- *Petugas*";
    }
}
