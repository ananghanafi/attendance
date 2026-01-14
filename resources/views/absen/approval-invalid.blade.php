<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Valid - {{ config('app.name') }}</title>
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

        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            text-align: center;
            padding: 50px 40px;
        }

        .error-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .error-icon i {
            font-size: 60px;
            color: white;
        }

        .error-title {
            font-size: 28px;
            font-weight: 700;
            color: #f5576c;
            margin-bottom: 15px;
        }

        .error-message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .reason-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .reason-box h6 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .reason-box p {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        .reason-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }

        .reason-item i {
            color: #f5576c;
            margin-right: 10px;
        }

        .close-note {
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon">
            @if($reason === 'expired')
            <i class="bi bi-clock-history"></i>
            @elseif($reason === 'processed')
            <i class="bi bi-check-circle"></i>
            @else
            <i class="bi bi-exclamation-triangle"></i>
            @endif
        </div>

        @if($reason === 'expired')
        <h2 class="error-title">Link Kadaluarsa</h2>
        <p class="error-message">
            Link approval ini sudah melewati batas waktu yang ditentukan.
            Pengajuan telah ditolak secara otomatis oleh sistem.
        </p>
        @elseif($reason === 'processed')
        <h2 class="error-title">Sudah Diproses</h2>
        <p class="error-message">
            Pengajuan ini sudah diproses sebelumnya.
            Anda tidak dapat memproses ulang approval yang sama.
        </p>
        @else
        <h2 class="error-title">Link Tidak Valid</h2>
        <p class="error-message">
            Link yang Anda akses tidak valid atau tidak ditemukan dalam sistem.
        </p>
        @endif

        <div class="reason-box">
            <h6><i class="bi bi-info-circle me-2"></i>Kemungkinan Penyebab:</h6>
            @if($reason === 'expired')
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Link approval hanya berlaku sampai jam 23:59 di hari yang sama</span>
            </div>
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Pengajuan otomatis ditolak karena melewati batas waktu</span>
            </div>
            @elseif($reason === 'processed')
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Pengajuan telah disetujui atau ditolak sebelumnya</span>
            </div>
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Setiap pengajuan hanya dapat diproses satu kali</span>
            </div>
            @else
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Link mungkin tidak lengkap atau salah disalin</span>
            </div>
            <div class="reason-item">
                <i class="bi bi-dot"></i>
                <span>Data approval mungkin telah dihapus dari sistem</span>
            </div>
            @endif
        </div>

        <p class="close-note">
            <i class="bi bi-info-circle me-1"></i>
            Jika Anda memerlukan bantuan, silakan hubungi Admin atau VP.
        </p>
    </div>
</body>

</html>