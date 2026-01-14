<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Approval Absen - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .approval-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .approval-header {
            padding: 30px;
            text-align: center;
            color: white;
        }

        .approval-header.izin {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .approval-header.pulang {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .approval-header i {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .approval-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .approval-body {
            padding: 30px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-weight: 500;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-dinas {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-sakit {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-wfa {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pulang {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-approve {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(17, 153, 142, 0.3);
            color: white;
        }

        .btn-reject {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(235, 51, 73, 0.3);
            color: white;
        }

        .expired-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .expired-warning i {
            color: #ffc107;
            font-size: 20px;
            margin-right: 8px;
        }

        .alasan-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .alasan-box p {
            margin: 0;
            color: #4a5568;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="approval-card">
        <div class="approval-header {{ $approval->type }}">
            @if($approval->type === 'izin')
            <i class="bi bi-file-earmark-text"></i>
            <h4>Approval Pengajuan Izin</h4>
            @else
            <i class="bi bi-clock-history"></i>
            <h4>Approval Absen Pulang</h4>
            @endif
        </div>

        <div class="approval-body">
            @if($approval->type === 'izin')
            {{-- Approval Izin --}}
            <div class="info-item">
                <span class="info-label">Nama Pengaju</span>
                <span class="info-value">{{ $data['nama_pengaju'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">NIP</span>
                <span class="info-value">{{ $data['nip'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jenis Izin</span>
                <span class="info-value">
                    @php
                    $statusClass = [
                    'dinas' => 'status-dinas',
                    'sakit' => 'status-sakit',
                    'sakit_izin' => 'status-sakit',
                    'izin' => 'status-sakit',
                    'wfa' => 'status-wfa',
                    ][$data['status']] ?? 'status-dinas';
                    $statusLabel = [
                    'dinas' => 'Dinas',
                    'sakit' => 'Sakit',
                    'sakit_izin' => 'Sakit/Izin',
                    'izin' => 'Sakit/Izin',
                    'wfa' => 'WFA',
                    ][$data['status']] ?? ucfirst($data['status']);
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal</span>
                <span class="info-value">
                    @if($data['from'] === $data['to'])
                    {{ \Carbon\Carbon::parse($data['from'])->translatedFormat('d F Y') }}
                    @else
                    {{ \Carbon\Carbon::parse($data['from'])->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($data['to'])->translatedFormat('d M Y') }}
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Alasan</span>
            </div>
            <div class="alasan-box">
                <p>{{ $data['alasan'] ?? '-' }}</p>
            </div>
            @else
            {{-- Approval Absen Pulang --}}
            <div class="info-item">
                <span class="info-label">Nama Pengaju</span>
                <span class="info-value">{{ $data['nama_pengaju'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">NIP</span>
                <span class="info-value">{{ $data['nip'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jenis</span>
                <span class="info-value">
                    <span class="status-badge status-pulang">Absen Pulang</span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('d F Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jam Pulang</span>
                <span class="info-value">{{ $data['jam_pulang'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Alasan</span>
            </div>
            <div class="alasan-box">
                <p>{{ $data['alasan_pulang'] ?? '-' }}</p>
            </div>
            @endif

            <div class="expired-warning mt-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Link akan kadaluarsa pada <strong>{{ \Carbon\Carbon::parse($approval->expired_at)->format('H:i') }}</strong> hari ini</span>
            </div>

            <form method="POST" action="{{ route('absen.approval.process', $approval->token) }}">
                @csrf
                <button type="submit" name="action" value="approve" class="btn btn-approve">
                    <i class="bi bi-check-circle me-2"></i>Setujui
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-reject">
                    <i class="bi bi-x-circle me-2"></i>Tolak
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>