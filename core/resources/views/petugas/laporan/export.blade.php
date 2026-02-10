<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman & Pengembalian</title>
</head>

<body>
    @php
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approve' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
        ];

        $formatDate = static function ($date) {
            return $date
                ? \Carbon\Carbon::parse($date)->translatedFormat('d-m-Y')
                : '-';
        };
    @endphp

    <h2 style="margin-bottom: 8px;">Ringkasan Laporan</h2>
    <table border="1" cellspacing="0" cellpadding="6">
        <thead style="background:#f3f4f6;">
            <tr>
                <th>Metrik</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Peminjaman</td>
                <td>{{ $summary['total_peminjaman'] }}</td>
            </tr>
            <tr>
                <td>Total Pengembalian</td>
                <td>{{ $summary['total_pengembalian'] }}</td>
            </tr>
            <tr>
                <td>Alat Dipinjam</td>
                <td>{{ $summary['alat_dipinjam'] }}</td>
            </tr>
            <tr>
                <td>Alat Dikembalikan</td>
                <td>{{ $summary['alat_dikembalikan'] }}</td>
            </tr>
        </tbody>
    </table>

    <h2 style="margin:24px 0 8px;">Detail Peminjaman</h2>
    <table border="1" cellspacing="0" cellpadding="6">
        <thead style="background:#f3f4f6;">
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjaman as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($item->peminjam)->name ?? '-' }}</td>
                    <td>{{ optional($item->alat)->nama_alat ?? '-' }}</td>
                    <td>{{ $formatDate($item->tanggal_pinjam) }}</td>
                    <td>{{ $formatDate($item->tanggal_kembali) }}</td>
                    <td>{{ $item->total_alat }}</td>
                    <td>{{ $statusLabels[$item->status] ?? ($item->status ?? '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data peminjaman sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 style="margin:24px 0 8px;">Detail Pengembalian</h2>
    <table border="1" cellspacing="0" cellpadding="6">
        <thead style="background:#f3f4f6;">
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tanggal Pengembalian</th>
                <th>Kondisi</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengembalian as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional(optional($item->peminjaman)->peminjam)->name ?? '-' }}</td>
                    <td>{{ optional(optional($item->peminjaman)->alat)->nama_alat ?? '-' }}</td>
                    <td>{{ $formatDate($item->tanggal_pengembalian) }}</td>
                    <td>{{ ucfirst($item->kondisi_alat ?? '-') }}</td>
                    <td>Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data pengembalian sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
