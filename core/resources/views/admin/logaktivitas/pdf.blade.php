<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Log Aktivitas | Lendify</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            font-size: 10.5px;
            color: #1f2d3d;
            background: #eef2f0;
            padding: 24px 20px;
        }

        /* Container utama seperti kartu premium */
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            padding: 24px 28px 28px 28px;
            transition: all 0.2s;
        }

        /* ========= HEADER AREA ========= */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e4d35 0%, #2b6b4a 100%);
            border-radius: 16px;
            width: 44px;
            height: 44px;
            box-shadow: 0 6px 12px rgba(30, 77, 53, 0.2);
        }

        .logo-icon svg {
            width: 28px;
            height: 28px;
            filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.1));
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.3px;
            background: linear-gradient(120deg, #1e4d35, #3c8b64);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .print-meta {
            background: #f8faf8;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 9px;
            color: #2c5a44;
            font-weight: 500;
            border: 1px solid #dee9e3;
            backdrop-filter: blur(2px);
        }

        .print-meta i {
            font-style: normal;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .title-section {
            border-bottom: 2px solid #e2ede7;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .title-section h1 {
            font-size: 22px;
            font-weight: 700;
            color: #152c22;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title-section h1:before {
            content: "📋";
            font-size: 24px;
            font-weight: normal;
        }

        .subhead {
            font-size: 10.5px;
            color: #5b7c6b;
            margin-top: 6px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* summary premium card */
        .summary-card {
            background: linear-gradient(105deg, #f4faf7 0%, #ecf4f0 100%);
            border-radius: 20px;
            padding: 14px 22px;
            margin-bottom: 28px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #d8e9e0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .period-box {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .period-box span:first-child {
            background: white;
            padding: 5px 12px;
            border-radius: 40px;
            color: #1e4d35;
            font-size: 9.5px;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px #cde2d8;
        }

        .total-badge {
            background: #1e4d35;
            color: white;
            padding: 6px 16px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .total-badge strong {
            font-size: 16px;
            margin-right: 4px;
        }

        /* ========= TABLE MODERN ========= */
        .log-table-wrapper {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e6f0ec;
            background: white;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.8px;
        }

        thead th {
            background: #1e4d35;
            color: white;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #2b6b4a;
        }

        thead th:first-child {
            border-top-left-radius: 16px;
        }

        thead th:last-child {
            border-top-right-radius: 16px;
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #f8fefb !important;
        }

        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #ecf3ef;
            vertical-align: middle;
            color: #1f2f28;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* badge modern */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: #f0f2f0;
            color: #2d3e35;
            box-shadow: inset 0 0 0 0.5px rgba(0, 0, 0, 0.05), 0 1px 1px rgba(0, 0, 0, 0.02);
        }

        .badge-create {
            background: #e0f2e9;
            color: #0a523a;
            border-left: 2px solid #1e9f6e;
        }

        .badge-update {
            background: #fff0db;
            color: #aa6f20;
            border-left: 2px solid #f3b33d;
        }

        .badge-delete {
            background: #ffe7e5;
            color: #b13b2d;
            border-left: 2px solid #e05a4f;
        }

        .badge-login {
            background: #e3f0fc;
            color: #146b9e;
            border-left: 2px solid #4b9fd1;
        }

        .badge-default {
            background: #eef2f5;
            color: #4f6b7c;
            border-left: 2px solid #9aaeb9;
        }

        .user-info {
            font-weight: 600;
            color: #1a4e3a;
        }

        .user-role {
            font-size: 8px;
            color: #6e8b7c;
            margin-top: 3px;
            letter-spacing: 0.2px;
        }

        .text-mono {
            font-family: 'Courier New', 'SF Mono', monospace;
            font-size: 8.5px;
            background: #f5f9f7;
            padding: 3px 6px;
            border-radius: 12px;
            display: inline-block;
            color: #2d5a47;
        }

        .time-cell {
            font-weight: 500;
        }

        .time-date {
            font-size: 9px;
            font-weight: 500;
        }

        .time-hour {
            font-size: 8px;
            color: #7f9b8c;
        }

        .footer-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e2ede7;
            font-size: 8.5px;
            color: #7c9a8a;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            background: #fbfefc;
            border-radius: 24px;
            color: #88aa9a;
            font-weight: 500;
        }

        @media (max-width: 700px) {
            .report-container {
                padding: 18px;
            }

            .summary-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            thead th,
            tbody td {
                padding: 8px 6px;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- TOP HEADER: Logo kiri (dari storage public) + cetak kanan -->
        <div class="top-header">
            <div class="brand-logo">
                <div class="logo-icon">
                    {{-- Ambil logo dari storage public (pastikan file sudah di-link: php artisan storage:link) --}}
                    <img src="{{ public_path('storage/uploads/logo/Lendify.png') }}"
                        alt="Logo Lendify"
                        width="44"
                        height="44"
                        style="object-fit: contain; border-radius: 12px;">
                </div>
                <div class="brand-name">Lendify</div>
            </div>
            <div class="print-meta">
                <i>🖨️ Dicetak: {{ now()->format('d M Y, H:i:s') }}</i>
            </div>
        </div>

        <!-- Judul laporan -->
        <div class="title-section">
            <h1>Laporan Aktivitas Pengguna</h1>
            <div class="subhead">
                <span>📌 Riwayat lengkap perubahan & akses sistem</span>
                <span>🔒 Audit trail terintegrasi</span>
            </div>
        </div>

        <!-- Ringkasan periode + total (premium) -->
        <div class="summary-card">
            <div class="period-box">
                <span>📅 Periode</span>
                <span>
                    {{ $startDate ?? 'Semua tanggal' }}
                    @if($startDate || $endDate)
                    → {{ $endDate ?? 'Sekarang' }}
                    @endif
                </span>
            </div>
            <div class="total-badge">
                <strong>{{ $logs->count() }}</strong> total aktivitas
            </div>
        </div>

        @if($logs->isEmpty())
        <div class="empty-state">
            ✨ Tidak ada aktivitas tercatat pada periode ini ✨
        </div>
        @else
        <div class="log-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:15%">Pengguna</th>
                        <th style="width:10%">Aksi</th>
                        <th style="width:32%">Deskripsi / Kegiatan</th>
                        <th style="width:18%">Target Model</th>
                        <th style="width:20%">Waktu Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $i => $log)
                    <tr>
                        <td style="font-weight: 500; color:#3c6b58;">{{ $i + 1 }}</td>
                        <td>
                            <div class="user-info">{{ $log->user->name ?? 'System / Tamu' }}</div>
                            @if($log->user && isset($log->user->role))
                            <div class="user-role">🎭 {{ ucfirst($log->user->role) }}</div>
                            @elseif(!$log->user)
                            <div class="user-role">⚙️ Otomatis / Cron</div>
                            @endif
                        </td>
                        <td>
                            @php
                            $badgeClass = match ($log->action) {
                            'create' => 'badge-create',
                            'update' => 'badge-update',
                            'delete' => 'badge-delete',
                            'login' => 'badge-login',
                            default => 'badge-default',
                            };
                            $actionIcon = match ($log->action) {
                            'create' => '➕',
                            'update' => '✏️',
                            'delete' => '🗑️',
                            'login' => '🔐',
                            default => '📌',
                            };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $actionIcon }} {{ $log->action }}</span>
                        </td>
                        <td style="line-height: 1.35;">{{ $log->description }}</td>
                        <td>
                            <span class="text-mono">
                                {{ class_basename($log->subject_type) }} <span style="color:#acbba9;">#{{ $log->subject_id }}</span>
                            </span>
                        </td>
                        <td class="time-cell">
                            <div class="time-date">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="time-hour">{{ $log->created_at->format('H:i:s') }} WIB</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer-note">
            <span>📄 Lendify — Sistem Manajemen & Log Aktivitas Terintegrasi</span>
            <span>🔒 Laporan bersifat bukti audit | {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</body>

</html>