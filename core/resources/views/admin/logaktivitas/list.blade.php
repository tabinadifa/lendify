@extends('layouts.layout')

@section('title', 'Log Aktivitas - Administrator')

@push('styles')
    <style>
        .page-title {
            color: #1e4d35;
        }

        .filter-card,
        .table-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 14px rgba(23, 56, 35, 0.08);
        }

        .table thead th {
            border-bottom: 2px solid #e1e7e4;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            color: #789082;
            background-color: #f8f9fa;
        }

        .badge-action {
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted mb-1 small">Sistem</p>
            <h1 class="page-title fw-bold mb-0">Log Aktivitas</h1>
        </div>
        <div class="text-end">
            <span class="text-muted small">Total {{ number_format($logs->total()) }} catatan</span>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            @if ($logs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clock-history mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">Belum ada aktivitas tercatat.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">User</th>
                                <th style="width: 10%;">Aksi</th>
                                <th style="width: 30%;">Deskripsi</th>
                                <th style="width: 15%;">Model / Subject</th>
                                <th style="width: 15%;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration + ($logs->firstItem() - 1) }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $log->user->name ?? 'System/Guest' }}</div>
                                        @if($log->user)
                                        <div class="small text-muted">{{ $log->user->role }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($log->action) {
                                                'create' => 'bg-success',
                                                'update' => 'bg-warning text-dark',
                                                'delete' => 'bg-danger',
                                                'login' => 'bg-info text-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-action {{ $badgeClass }}">{{ $log->action }}</span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td class="small font-monospace">
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    </td>
                                    <td>
                                        <div>{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
                <small class="text-muted mb-2 mb-md-0">
                    Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas
                </small>
                {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
