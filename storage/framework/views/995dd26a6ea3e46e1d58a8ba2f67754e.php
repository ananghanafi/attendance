<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen - <?php echo e($success ? 'Berhasil' : (isset($type) && $type === 'pending_approval' ? 'Menunggu' : 'Gagal')); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        /* Animated Checkmark */
        .checkmark-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            position: relative;
            display: inline-block;
            vertical-align: top;
        }

        .checkmark-circle .background {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #38a169;
            position: absolute;
            animation: checkmark-bg 0.3s ease-out forwards;
        }

        .checkmark-circle .checkmark {
            border-radius: 5px;
        }

        .checkmark-circle .checkmark.draw:after {
            animation-delay: 0.2s;
            animation-duration: 0.6s;
            animation-timing-function: ease;
            animation-name: checkmark-draw;
            transform: scaleX(-1) rotate(135deg);
            animation-fill-mode: forwards;
        }

        .checkmark-circle .checkmark:after {
            opacity: 0;
            height: 32px;
            width: 16px;
            transform-origin: left top;
            border-right: 4px solid #fff;
            border-top: 4px solid #fff;
            border-radius: 2.5px !important;
            content: '';
            left: 21px;
            top: 40px;
            position: absolute;
        }

        @keyframes checkmark-bg {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes checkmark-draw {
            0% { height: 0; width: 0; opacity: 1; }
            20% { height: 0; width: 16px; opacity: 1; }
            40% { height: 32px; width: 16px; opacity: 1; }
            100% { height: 32px; width: 16px; opacity: 1; }
        }

        /* Animated Timer/Hourglass */
        .timer-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            position: relative;
        }

        .timer-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #d69e2e;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: timer-pulse 2s ease-in-out infinite;
        }

        .timer-icon {
            font-size: 36px;
            color: #fff;
            animation: timer-flip 2s ease-in-out infinite;
        }

        @keyframes timer-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(214, 158, 46, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(214, 158, 46, 0); }
        }

        @keyframes timer-flip {
            0%, 45% { transform: rotate(0deg); }
            50%, 95% { transform: rotate(180deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 24px;
            color: #1a202c;
            margin-bottom: 12px;
        }

        .message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .sub-message {
            font-size: 14px;
            color: #718096;
            margin-top: 8px;
        }

        .user-info {
            background: #f7fafc;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }

        .user-name {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .user-nip {
            font-size: 14px;
            color: #718096;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 12px;
        }

        .status-badge.success {
            background: #c6f6d5;
            color: #276749;
        }

        .status-badge.error {
            background: #fed7d7;
            color: #c53030;
        }

        .status-badge.warning {
            background: #fefcbf;
            color: #975a16;
        }

        .status-badge.info {
            background: #bee3f8;
            color: #2b6cb0;
        }

        /* Date info box */
        .date-info {
            background: #c6f6d5;
            border-radius: 12px;
            padding: 16px 24px;
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-label {
            font-size: 12px;
            color: #276749;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .date-value {
            font-size: 18px;
            font-weight: 700;
            color: #276749;
        }

        .absen-detail {
            background: #edf2f7;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            text-align: left;
        }

        .absen-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .absen-row:last-child {
            border-bottom: none;
        }

        .absen-label {
            color: #718096;
            font-size: 14px;
        }

        .absen-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        /* Error icon */
        .error-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .error-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e53e3e;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: error-shake 0.5s ease;
        }

        .error-icon {
            font-size: 40px;
            color: #fff;
            font-weight: bold;
        }

        @keyframes error-shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Info icon */
        .info-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .info-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3182ce;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-icon {
            font-size: 40px;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if($success): ?>
            <?php if(isset($type) && $type === 'already_complete'): ?>
                <div class="checkmark-container">
                    <div class="checkmark-circle">
                        <div class="background"></div>
                        <div class="checkmark draw"></div>
                    </div>
                </div>
                <h1>Anda Sudah Absen Hari Ini</h1>
            <?php elseif(isset($type) && $type === 'already_izin'): ?>
                <div class="checkmark-container">
                    <div class="checkmark-circle">
                        <div class="background"></div>
                        <div class="checkmark draw"></div>
                    </div>
                </div>
                <h1>Status Izin Aktif</h1>
            <?php elseif(isset($type) && in_array($type, ['masuk', 'pulang'])): ?>
                <div class="checkmark-container">
                    <div class="checkmark-circle">
                        <div class="background"></div>
                        <div class="checkmark draw"></div>
                    </div>
                </div>
                <h1>Absen <?php echo e($type === 'masuk' ? 'Masuk' : 'Pulang'); ?> Berhasil</h1>
            <?php elseif(isset($type) && $type === 'pending_approval'): ?>
                <div class="timer-container">
                    <div class="timer-circle">
                        <span class="timer-icon">⏳</span>
                    </div>
                </div>
                <h1>Pengajuan Terkirim</h1>
            <?php else: ?>
                <div class="checkmark-container">
                    <div class="checkmark-circle">
                        <div class="background"></div>
                        <div class="checkmark draw"></div>
                    </div>
                </div>
                <h1>Berhasil</h1>
            <?php endif; ?>
        <?php else: ?>
            
            <?php if(isset($type) && $type === 'pending_approval'): ?>
                <div class="timer-container">
                    <div class="timer-circle">
                        <span class="timer-icon">⏳</span>
                    </div>
                </div>
                <h1>Menunggu Approval</h1>
            <?php else: ?>
                <div class="error-container">
                    <div class="error-circle">
                        <span class="error-icon">✕</span>
                    </div>
                </div>
                <h1>Gagal</h1>
            <?php endif; ?>
        <?php endif; ?>

        <p class="message">
            <?php echo e($message); ?>

            <?php if(isset($nama_atasan)): ?>
                <strong><?php echo e($nama_atasan); ?></strong>
            <?php endif; ?>
        </p>

        <?php if(isset($sub_message)): ?>
            <p class="sub-message"><?php echo e($sub_message); ?></p>
        <?php endif; ?>

        <?php if(isset($user)): ?>
            <div class="user-info">
                <div class="user-name"><?php echo e($user->nama); ?></div>
                <div class="user-nip">NIP: <?php echo e($user->nip); ?></div>
            </div>
        <?php endif; ?>

        <?php if(isset($type)): ?>
            <?php if($type === 'masuk'): ?>
                <span class="status-badge success">Tercatat Masuk</span>
            <?php elseif($type === 'pulang'): ?>
                <span class="status-badge success">Tercatat Pulang</span>
            <?php elseif($type === 'already_complete'): ?>
                <?php if(isset($tanggal_formatted)): ?>
                <div class="date-info">
                    <span class="date-label">Tanggal</span>
                    <span class="date-value"><?php echo e($tanggal_formatted); ?></span>
                </div>
                <?php endif; ?>
            <?php elseif($type === 'already_izin'): ?>
                <?php if(isset($tanggal_from) && isset($tanggal_to)): ?>
                <div class="date-info">
                    <span class="date-label">Periode Izin</span>
                    <span class="date-value"><?php echo e($tanggal_from); ?> - <?php echo e($tanggal_to); ?></span>
                </div>
                <?php endif; ?>
            <?php elseif($type === 'pending_approval'): ?>
                <span class="status-badge warning">Menunggu Approval</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(!$success && (!isset($type) || $type !== 'pending_approval')): ?>
            <span class="status-badge error">Gagal</span>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/absen/standalone-result.blade.php ENDPATH**/ ?>