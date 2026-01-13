@extends('layouts.app')

@section('title', 'Absen')

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

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
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

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .info-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
    }

    .info-value.wfo {
        color: #059669;
    }

    .info-value.wfa {
        color: #d97706;
    }

    .info-value.kantor {
        color: #059669;
    }

    .info-value.luar {
        color: #dc2626;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-badge.wfo {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.wfa {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.libur {
        background: #fee2e2;
        color: #991b1b;
    }

    .absen-section {
        text-align: center;
        padding: 32px 0;
    }

    .absen-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 20px 48px;
        font-size: 18px;
        font-weight: 600;
        border: none;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .absen-button.masuk {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
        box-shadow: 0 8px 24px rgba(5, 150, 105, 0.3);
    }

    .absen-button.masuk:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(5, 150, 105, 0.4);
    }

    .absen-button.pulang {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        box-shadow: 0 8px 24px rgba(220, 38, 38, 0.3);
    }

    .absen-button.pulang:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(220, 38, 38, 0.4);
    }

    .absen-button:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .absen-time {
        font-size: 48px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }

    .absen-date {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .absen-record {
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 12px;
        padding: 16px;
        margin-top: 24px;
    }

    .absen-record h4 {
        font-size: 14px;
        color: #065f46;
        margin-bottom: 12px;
    }

    .record-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .record-item {
        text-align: center;
    }

    .record-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .record-value {
        font-size: 20px;
        font-weight: 600;
        color: #065f46;
    }

    .record-value.empty {
        color: #94a3b8;
    }

    .no-jadwal-message {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
    }

    .no-jadwal-message .icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .no-jadwal-message h3 {
        color: #92400e;
        margin-bottom: 8px;
    }

    .no-jadwal-message p {
        color: #a16207;
        font-size: 14px;
    }

    /* Testing Info Box */
    .test-info {
        background: #fef3c7;
        border: 2px dashed #f59e0b;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .test-info h4 {
        color: #92400e;
        font-size: 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .test-info p {
        color: #a16207;
        font-size: 13px;
        margin: 4px 0;
    }

    .test-info code {
        background: #fde68a;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
    }

    @media (max-width: 768px) {
        .absen-time {
            font-size: 36px;
        }

        .absen-button {
            padding: 16px 32px;
            font-size: 16px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<h1 class="page-title">ABSENSI</h1>

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

<!-- Testing Info Box (hapus di production) -->
<div class="test-info">
    <h4>🧪 Mode Testing (By Case)</h4>
    <p>Jam: <code>{{ $info['jam'] }}</code></p>
    <p>Posisi: <code>{{ $info['posisi'] }}</code> ({{ $info['posisi_label'] }})</p>
    <p>Button Type: <code>{{ $info['button_type'] }}</code></p>
    <p>Ada Jadwal: <code>{{ $jadwal['ada_jadwal'] ? 'Ya' : 'Tidak' }}</code></p>
    <p>Status Pengajuan: <code>{{ $jadwal['status_pengajuan'] ?? 'N/A' }}</code></p>
    <p>Pesan: <code>{{ $jadwal['pesan'] ?? '-' }}</code></p>
    <p><small>Ubah nilai di <code>config/absen_test.php</code> untuk testing</small></p>
</div>

<!-- Info Cards -->
<div class="card">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Hari Ini</div>
            <div class="info-value">{{ $jadwal['hari_indo'] ?? now()->format('l') }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Tanggal</div>
            <div class="info-value">{{ now()->format('d M Y') }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Jadwal</div>
            <div class="info-value">
                @if(!$jadwal['ada_jadwal'])
                <span class="status-badge libur">❓ Belum Ada</span>
                @elseif($jadwal['is_libur'] ?? false)
                <span class="status-badge libur">🏖️ Libur</span>
                @elseif($jadwal['is_wfo'])
                <span class="status-badge wfo">🏢 WFO</span>
                @else
                <span class="status-badge wfa">🏠 WFA</span>
                @endif
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Posisi Saat Ini</div>
            <div class="info-value {{ $info['posisi'] === 'kantor' ? 'kantor' : 'luar' }}">
                @if($info['posisi'] === 'kantor')
                📍 Di Kantor
                @else
                📍 Di Luar Kantor
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Absen Section -->
<div class="card">
    <div class="absen-section">
        <div class="absen-time" id="currentTime">{{ $info['jam'] }}</div>
        <div class="absen-date">{{ now()->format('l, d F Y') }}</div>

        @if(!$jadwal['ada_jadwal'])
        <!-- Belum ada jadwal atau status belum close -->
        <div class="no-jadwal-message">
            @if(isset($jadwal['status_pengajuan']))
            <h3>Jadwal Masih Dalam Proses</h3>
            <p>Status pengajuan: <strong>{{ strtoupper($jadwal['status_pengajuan']) }}</strong></p>
            <p>{{ $jadwal['pesan'] }}</p>
            @else
            <h3>Jadwal Belum Ditentukan</h3>
            <p>{{ $jadwal['pesan'] }}</p>
            <p>Silakan hubungi ketua untuk menentukan jadwal WFO/WFA minggu ini.</p>
            @endif
        </div>
        @elseif($jadwal['is_libur'] ?? false)
        <!-- Hari libur -->
        <div class="no-jadwal-message">
            <h3>Hari Libur</h3>
            <p>Hari ini adalah hari libur. Tidak perlu absen.</p>
        </div>
        @elseif($info['button_type'] === 'none')
        <!-- Di luar jam absen -->
        <div class="alert alert-info" style="display: inline-flex;">
            ℹ️ Di luar jam absen. Absen masuk: 04:00-16:59, Absen pulang: 17:00-23:59
        </div>
        @elseif($info['button_type'] === 'masuk')
        <!-- Waktu absen masuk -->
        @if($sudahAbsenMasuk)
        <div class="alert alert-success" style="display: inline-flex;">
            ✅ Anda sudah absen masuk hari ini pada jam {{ $absenHariIni->scan_masuk }}
        </div>
        @else
        <a href="{{ route('absen.formulir', ['tipe' => 'masuk']) }}" class="absen-button masuk">
            🕐 Absen Masuk
        </a>
        @endif
        @elseif($info['button_type'] === 'pulang')
        <!-- Waktu absen pulang -->
        @if(!$sudahAbsenMasuk)
        <div class="alert alert-warning" style="display: inline-flex;">
            ⚠️ Anda belum absen masuk hari ini. Tidak bisa absen pulang.
        </div>
        @elseif($pendingApproval)
        <div class="alert alert-warning" style="display: inline-flex; flex-direction: column; align-items: flex-start;">
            <span>⏳ Absen pulang Anda pada jam {{ $absenHariIni->jam_temp }} sedang menunggu approval atasan.</span>
            <small style="margin-top: 4px;">Alasan: {{ $absenHariIni->alasan_pulang }}</small>
        </div>
        @elseif($sudahAbsenPulang)
        <div class="alert alert-success" style="display: inline-flex;">
            ✅ Anda sudah absen pulang hari ini pada jam {{ $absenHariIni->scan_pulang }}
        </div>
        @else
        <a href="{{ route('absen.formulir', ['tipe' => 'pulang']) }}" class="absen-button pulang">
            🏃 Absen Pulang
        </a>
        @endif
        @endif

        <!-- Record Absen Hari Ini -->
        @if($absenHariIni)
        <div class="absen-record">
            <h4>📋 Record Absen Hari Ini</h4>
            <div class="record-grid">
                <div class="record-item">
                    <div class="record-label">Scan Masuk</div>
                    <div class="record-value {{ !$absenHariIni->scan_masuk ? 'empty' : '' }}">
                        {{ $absenHariIni->scan_masuk ?? '-' }}
                    </div>
                </div>
                <div class="record-item">
                    <div class="record-label">Scan Pulang</div>
                    <div class="record-value {{ !$absenHariIni->scan_pulang ? 'empty' : '' }}">
                        {{ $absenHariIni->scan_pulang ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // Update jam realtime (jika tidak pakai jam test)
    @if(config('absen_test.jam') === null)

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').textContent = hours + ':' + minutes + ':' + seconds;
    }
    setInterval(updateClock, 1000);
    updateClock();
    @endif
</script>
@endsection