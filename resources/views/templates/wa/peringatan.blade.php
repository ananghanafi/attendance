Kepada Yth:
Bpk/Ibu *{{ strtoupper($nama_atasan) }}*
*{{ $jabatan_atasan }}* of *{{ $biro_atasan }}*
PT Wika Gedung Tbk.

Sehubungan dengan upaya peningkatan disiplin kerja dan juga penerapan nilai-nilai AKHLAK BUMN di Lingkungan PT WIKA Gedung Tbk, dengan pemberlakuan *Aturan Jam Kerja dan Disiplin Pegawai PT WIKA Gedung Tbk efektif berlaku mulai tanggal 1 November 2024*, berikut kami lampirkan Rekapitulasi Absensi Personil *{{ strtoupper($jabatan_pegawai) }}* of *{{ strtoupper($biro_pegawai) }}* atas nama *{{ strtoupper($nama_pegawai) }}* yang hadir lebih dari pukul 08.00 dan/atau Tidak Absen sebanyak {{ $violation_count }} ( {{ $violation_words }} ) kali pada periode tanggal *{{ $from }}* s/d *{{ $to }}*.

Dengan Rincian Sebagai Berikut:
@foreach($violations as $index => $v)
{{ $index + 1 }}. {{ $v['date'] }} : {{ $v['reason'] }}
@endforeach

Sesuai dengan aturan tersebut jika personil terlambat 5 kali untuk dapat diberikan Surat Peringatan oleh atasan langsung.
*Surat Aturan Jam Kerja dan Disiplin PT Wika Gedung TBK.*
https://store.wikagedung.co.id/upload/DOC/Aturan_Jam_Kerja_dan_Disiplin_PT_Wika_Gedung_TBK.pdf

Berikut kami lampirkan Draft Contoh Surat Peringatan Absensi.
*Draft Surat Peringatan Absensi*
https://store.wikagedung.co.id/upload/DOC/Surat_Peringatan_Absensi_WG.docx

Demikian yang dapat kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.

*Human Capital Division*
