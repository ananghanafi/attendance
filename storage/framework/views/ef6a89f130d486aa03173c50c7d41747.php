[ABSEN ONLINE]

<?php
$statusLabel = match($status) {
    'sakit_izin' => 'Sakit/Izin',
    'izin' => 'Sakit/Izin',
    'dinas' => 'Dinas',
    'wfa' => 'WFA',
    default => ucfirst($status)
};
?>
Terdapat pengajuan Izin *<?php echo e(strtoupper($statusLabel)); ?>* dari Pegawai atas nama *<?php echo e(strtoupper($nama_pengaju)); ?>* untuk tanggal *<?php echo e($from); ?>* sd *<?php echo e($to); ?>* dengan alasan sebagai berikut.

*<?php echo e($alasan); ?>*

Harap melakukan approval melalui tautan berikut sebelum pukul *23.59* tanggal *<?php echo e($expired_date); ?>*. Jika melebihi batas approval maka pegawai tersebut akan *DITOLAK OTOMATIS* oleh sistem

<?php echo e($approval_link); ?>


Untuk informasi lebih lanjut bisa menghubungi Information Technology Division atau Human Capital Division melalui kontak di bawah ini.
1. Sdra. *AVIV* (0881-0824-34878)
2. Sdra. *DWIKA* (0821-2139-3139)

Best Regards,
*Human Capital Division*<?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/templates/wa/approval-izin.blade.php ENDPATH**/ ?>