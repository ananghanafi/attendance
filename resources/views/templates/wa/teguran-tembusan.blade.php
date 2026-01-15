*[Tembusan_Teguran]*

Kepada Yth:
Bpk/Ibu *{{ strtoupper($nama_pegawai) }}*
Di tempat

*Perihal: Teguran untuk Peningkatan Disiplin Waktu Kehadiran*

Dengan hormat,

Berdasarkan rekap absensi, tercatat Anda hadir lebih dari pukul 08.00 dan/atau Tidak Absen sebanyak {{ $violation_count }} ( {{ $violation_words }} ) kali dalam periode Tanggal *{{ $from }}* s/d *{{ $to }}*. Kami mengingatkan pentingnya kedisiplinan waktu kehadiran sebagai bagian dari komitmen dan profesionalisme di PT Wijaya Karya Bangunan Gedung Tbk.

Dengan Rincian Sebagai Berikut:
@foreach($violations as $index => $v)
{{ $index + 1 }}. {{ $v['date'] }} : {{ $v['reason'] }}
@endforeach

*Surat Aturan Jam Kerja dan Disiplin PT Wika Gedung TBK.*
https://store.wikagedung.co.id/upload/DOC/Aturan_Jam_Kerja_dan_Disiplin_PT_Wika_Gedung_TBK.pdf

Terima kasih atas perhatian dan kerjasamanya.

*Human Capital Division*
