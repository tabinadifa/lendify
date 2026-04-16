<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Log Aktivitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #2d2d2d;
            background: #fff;
        }

        .header {
            border-bottom: 3px solid #1e4d35;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e4d35;
            font-weight: bold;
        }

        .header p {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 9px;
            color: #555;
        }

        .meta-row span {
            background: #f0f5f2;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #d0e5d8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #1e4d35;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        tbody tr:nth-child(even) {
            background-color: #f5faf7;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e8efeb;
            vertical-align: top;
            font-size: 9.5px;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-create  { background: #d4edda; color: #155724; }
        .badge-update  { background: #fff3cd; color: #856404; }
        .badge-delete  { background: #f8d7da; color: #721c24; }
        .badge-login   { background: #d1ecf1; color: #0c5460; }
        .badge-default { background: #e2e3e5; color: #383d41; }

        .text-mono {
            font-family: 'Courier New', monospace;
            font-size: 8.5px;
            color: #555;
        }

        .text-muted { color: #888; font-size: 8.5px; }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #e1e7e4;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            color: #aaa;
        }

        .summary-box {
            margin-bottom: 14px;
            padding: 8px 12px;
            background: #f0f5f2;
            border-left: 4px solid #1e4d35;
            border-radius: 0 6px 6px 0;
            font-size: 9.5px;
            color: #444;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>&#128196; Lendify</h1>
        <p>Laporan aktivitas pengguna &mdash; dicetak pada {{ now()->format('d M Y, H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <strong>Periode:</strong>
        {{ $startDate ?? 'Semua tanggal' }}
        @if($startDate || $endDate)
            &mdash; {{ $endDate ?? 'Semua tanggal' }}
        @endif
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Catatan:</strong> {{ $logs->count() }} aktivitas
    </div>

    @if($logs->isEmpty())
        <p style="text-align:center; color:#999; padding: 30px 0;">Tidak ada data aktivitas pada periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:14%">User</th>
                    <th style="width:9%">Aksi</th>
                    <th style="width:35%">Deskripsi</th>
                    <th style="width:18%">Model / Subject</th>
                    <th style="width:14%">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $i => $log)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $log->user->name ?? 'System/Guest' }}</strong>
                            @if($log->user)
                                <br><span class="text-muted">{{ $log->user->role }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match ($log->action) {
                                    'create' => 'badge-create',
                                    'update' => 'badge-update',
                                    'delete' => 'badge-delete',
                                    'login'  => 'badge-login',
                                    default  => 'badge-default',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="text-mono">
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        </td>
                        <td>
                            {{ $log->created_at->format('d M Y') }}<br>
                            <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <span>Sistem Informasi &mdash; Laporan Log Aktivitas</span>
        <span>Dicetak: {{ now()->format('d M Y H:i') }}</span>
    </div>

</body>
</html>