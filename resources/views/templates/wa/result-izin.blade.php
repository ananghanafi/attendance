[ABSEN ONLINE]

Kepada Yth.
*{{ strtoupper($nama_user) }}*

Pengajuan Izin *{{ strtoupper($status) }}* untuk tanggal *{{ $from }}* sd *{{ $to }}* dengan alasan *{{ $alasan }}* telah *{{ $result === 'approved' ? 'DISETUJUI' : 'DITOLAK' }}* oleh *{{ strtoupper($processed_by) }}*.

@if($result === 'approved')
Silakan melanjutkan aktivitas sesuai dengan izin yang telah diajukan.
@else
Silakan menghubungi atasan Anda untuk informasi lebih lanjut.
@endif

Untuk informasi lebih lanjut bisa menghubungi Information Technology Division atau Human Capital Division melalui kontak di bawah ini.
1. Sdra. *AVIV* (0881-0824-34878)
2. Sdra. *DWIKA* (0821-2139-3139)

Best Regards,
*Human Capital Division*