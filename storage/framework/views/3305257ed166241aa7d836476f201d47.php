[ABSEN ONLINE]

Kepada Yth.
*<?php echo e(strtoupper($nama_user)); ?>*

<?php
$statusLabel = match($status) {
    'sakit_izin' => 'Sakit/Izin',
    'izin' => 'Sakit/Izin',
    'dinas' => 'Dinas',
    'wfa' => 'WFA',
    default => ucfirst($status)
};
?>
Pengajuan Izin *<?php echo e(strtoupper($statusLabel)); ?>* untuk tanggal *<?php echo e($from); ?>* sd *<?php echo e($to); ?>* dengan alasan *<?php echo e($alasan); ?>* telah *<?php echo e($result === 'approved' ? 'DISETUJUI' : 'DITOLAK'); ?>* oleh *<?php echo e(strtoupper($processed_by)); ?>*.

<?php if($result === 'approved'): ?>
Silakan melanjutkan aktivitas sesuai dengan izin yang telah diajukan.
<?php else: ?>
Silakan menghubungi atasan Anda untuk informasi lebih lanjut.
<?php endif; ?>

Untuk informasi lebih lanjut bisa menghubungi Information Technology Division atau Human Capital Division melalui kontak di bawah ini.
1. Sdra. *AVIV* (0881-0824-34878)
2. Sdra. *DWIKA* (0821-2139-3139)

Best Regards,
*Human Capital Division*<?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/templates/wa/result-izin.blade.php ENDPATH**/ ?>