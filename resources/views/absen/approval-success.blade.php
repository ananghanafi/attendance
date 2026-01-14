<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Berhasil - {{ config('app.name') }}</title>
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

        .result-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            text-align: center;
            padding: 50px 40px;
        }

        .result-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .result-icon.approved {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .result-icon.rejected {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .result-icon i {
            font-size: 60px;
            color: white;
        }

        .result-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .result-title.approved {
            color: #11998e;
        }

        .result-title.rejected {
            color: #eb3349;
        }

        .result-message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: #6c757d;
        }

        .info-row .value {
            font-weight: 600;
            color: #2d3748;
        }

        .close-note {
            margin-top: 30px;
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="result-card">
        @if($status === 'approved')
        <div class="result-icon approved">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 class="result-title approved">Disetujui!</h2>
        <p class="result-message">
            Anda telah menyetujui pengajuan dari <strong>{{ $data['nama_pengaju'] ?? 'Karyawan' }}</strong>.
            @if($type === 'izin')
            Izin telah dicatat dalam sistem.
            @else
            Absen pulang telah dicatat dalam sistem.
            @endif
        </p>
        @else
        <div class="result-icon rejected">
            <i class="bi bi-x-lg"></i>
        </div>
        <h2 class="result-title rejected">Ditolak</h2>
        <p class="result-message">
            Anda telah menolak pengajuan dari <strong>{{ $data['nama_pengaju'] ?? 'Karyawan' }}</strong>.
            @if($type === 'izin')
            Izin tidak akan dicatat dalam sistem.
            @else
            Absen pulang tidak akan dicatat dalam sistem.
            @endif
        </p>
        @endif

        <div class="info-box">
            @if($type === 'izin')
            <div class="info-row">
                <span class="label">Jenis</span>
                <span class="value">{{ ucfirst($data['status'] ?? 'Izin') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal</span>
                <span class="value">
                    @if(isset($data['from']) && isset($data['to']) && $data['from'] === $data['to'])
                    {{ \Carbon\Carbon::parse($data['from'])->translatedFormat('d M Y') }}
                    @elseif(isset($data['from']) && isset($data['to']))
                    {{ \Carbon\Carbon::parse($data['from'])->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($data['to'])->translatedFormat('d M Y') }}
                    @else
                    -
                    @endif
                </span>
            </div>
            @else
            <div class="info-row">
                <span class="label">Jenis</span>
                <span class="value">Absen Pulang</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ isset($data['tanggal']) ? \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('d M Y') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jam</span>
                <span class="value">{{ $data['jam_pulang'] ?? '-' }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Diproses</span>
                <span class="value">{{ now()->translatedFormat('d M Y H:i') }}</span>
            </div>
        </div>

        <p class="close-note">
            <i class="bi bi-info-circle me-1"></i>
            Notifikasi hasil telah dikirim ke pengaju.<br>
            Anda dapat menutup halaman ini.
        </p>
    </div>
</body>

</html>