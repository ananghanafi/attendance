@extends('layouts.app')

@section('title', 'Approval Absen Pulang')

@section('styles')
<style>
    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 24px;
        color: var(--text);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e7eaf3;
        box-shadow: 0 4px 20px rgba(35, 45, 120, .08);
        padding: 24px;
        margin-bottom: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e7eaf3;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #64748b;
    }

    .empty-state .icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 18px;
        color: var(--text);
        margin-bottom: 8px;
    }

    .approval-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .approval-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s ease;
    }

    .approval-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .approval-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .approval-employee {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .approval-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
    }

    .approval-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .approval-info p {
        font-size: 13px;
        color: #64748b;
    }

    .approval-date {
        font-size: 13px;
        color: #64748b;
        text-align: right;
    }

    .approval-date strong {
        display: block;
        font-size: 14px;
        color: var(--text);
    }

    .approval-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
    }

    .approval-reason {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 16px;
    }

    .approval-reason h5 {
        font-size: 12px;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .approval-reason p {
        font-size: 14px;
        color: #78350f;
    }

    .approval-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-reject:hover {
        background: #fecaca;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-count {
        background: #dc2626;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }

    @media (max-width: 768px) {
        .approval-header {
            flex-direction: column;
            gap: 12px;
        }

        .approval-date {
            text-align: left;
        }

        .approval-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<h1 class="page-title">
    📋 APPROVAL ABSEN PULANG
    @if($pendingApprovals->count() > 0)
    <span class="badge-count">{{ $pendingApprovals->count() }}</span>
    @endif
</h1>

@if(session('success'))
<div class="alert alert-success">
    ✅ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    ❌ {{ session('error') }}
</div>
@endif

<div class="card">
    <h3 class="card-title">Daftar Absen Menunggu Persetujuan</h3>

    @if($pendingApprovals->count() === 0)
    <div class="empty-state">
        <div class="icon">✅</div>
        <h3>Tidak Ada Pengajuan</h3>
        <p>Semua absen pulang bawahan Anda sudah disetujui atau tidak ada yang pending.</p>
    </div>
    @else
    <div class="approval-list">
        @foreach($pendingApprovals as $item)
        <div class="approval-item">
            <div class="approval-header">
                <div class="approval-employee">
                    <div class="approval-avatar">
                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                    </div>
                    <div class="approval-info">
                        <h4>{{ $item->nama }}</h4>
                        <p>NIP: {{ $item->user_nip }} | {{ $item->biro_name ?? '-' }}</p>
                    </div>
                </div>
                <div class="approval-date">
                    <strong>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong>
                    <span class="badge badge-pending">⏳ Pending</span>
                </div>
            </div>

            <div class="approval-details">
                <div class="detail-item">
                    <div class="detail-label">Scan Masuk</div>
                    <div class="detail-value">{{ $item->scan_masuk ?? '-' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Jam Pengajuan Pulang</div>
                    <div class="detail-value" style="color: #f59e0b;">{{ $item->jam_temp ?? '-' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status Izin</div>
                    <div class="detail-value">{{ ucfirst(str_replace('_', '/', $item->status_izin ?? '-')) }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">IP Address</div>
                    <div class="detail-value" style="font-size: 12px;">{{ $item->ip ?? '-' }}</div>
                </div>
            </div>

            @if($item->alasan_pulang)
            <div class="approval-reason">
                <h5>📝 Alasan Absen Pulang di Luar Kantor</h5>
                <p>{{ $item->alasan_pulang }}</p>
            </div>
            @endif

            <div class="approval-actions">
                <form action="{{ route('absen.reject', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menolak absen pulang ini?')">
                    @csrf
                    <button type="submit" class="btn btn-reject">
                        ✕ Tolak
                    </button>
                </form>
                <form action="{{ route('absen.approve', $item->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-approve">
                        ✓ Setujui
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection