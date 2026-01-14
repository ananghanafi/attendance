[ABSEN ONLINE]

@php
$statusLabel = match($status) {
    'sakit_izin' => 'Sakit/Izin',
    'izin' => 'Sakit/Izin',
    'dinas' => 'Dinas',
    'wfa' => 'WFA',
    default => ucfirst($status)
};
@endphp
Terdapat pengajuan Izin *{{ strtoupper($statusLabel) }}* dari Pegawai atas nama *{{ strtoupper($nama_pengaju) }}* untuk tanggal *{{ $from }}* sd *{{ $to }}* dengan alasan sebagai berikut.

*{{ $alasan }}*

Harap melakukan approval melalui tautan berikut sebelum pukul *23.59* tanggal *{{ $expired_date }}*. Jika melebihi batas approval maka pegawai tersebut akan *DITOLAK OTOMATIS* oleh sistem

{{ $approval_link }}

Untuk informasi lebih lanjut bisa menghubungi Information Technology Division atau Human Capital Division melalui kontak di bawah ini.
1. Sdra. *AVIV* (0881-0824-34878)
2. Sdra. *DWIKA* (0821-2139-3139)

Best Regards,
*Human Capital Division*