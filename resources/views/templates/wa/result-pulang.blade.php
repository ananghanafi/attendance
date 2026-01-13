[ABSEN ONLINE]

Kepada Yth.
*{{ strtoupper($nama_user) }}*

Approval Absen Pulang Di Luar Kantor dengan alasan *{{ $alasan }}* telah *{{ $result === 'approved' ? 'DISETUJUI' : 'DITOLAK' }}* oleh *{{ strtoupper($processed_by) }}*.

@if($result === 'approved')
Absen pulang Anda telah tercatat dalam sistem.
@else
Absen pulang Anda tidak tercatat dalam sistem. Silakan menghubungi atasan Anda untuk informasi lebih lanjut.
@endif

Untuk informasi lebih lanjut bisa menghubungi Information Technology Division atau Human Capital Division melalui kontak di bawah ini.
1. Sdra. *AVIV* (0881-0824-34878)
2. Sdra. *DWIKA* (0821-2139-3139)

Best Regards,
*Human Capital Division*