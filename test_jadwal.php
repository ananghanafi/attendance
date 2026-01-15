<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$nip = '2304XT212';
$tanggal = Carbon::createFromFormat('d-m-Y', '15-01-2026');

echo "NIP: $nip\n";
echo "Tanggal: " . $tanggal->format('Y-m-d') . " (" . $tanggal->format('l') . ")\n";

$user = DB::table('users')->where('nip', $nip)->first();
if (!$user) {
    echo "User tidak ditemukan!\n";
    exit;
}

echo "Biro ID: " . ($user->biro_id ?? 'null') . "\n";

$pengajuan = DB::table('pengajuan_wao as pw')
    ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
    ->where('pw.biro_id', $user->biro_id)
    ->whereDate('kk.tgl_awal', '<=', $tanggal->format('Y-m-d'))
    ->whereDate('kk.tgl_akhir', '>=', $tanggal->format('Y-m-d'))
    ->where('pw.status', 'final')
    ->select('pw.*', 'kk.tgl_awal', 'kk.tgl_akhir')
    ->first();

if (!$pengajuan) {
    echo "Tidak ada pengajuan final untuk periode ini!\n";
    echo "Jadwal default: WFO (tidak ada jadwal)\n";
    exit;
}

echo "Pengajuan ID: " . $pengajuan->id . "\n";

$detail = DB::table('pengajuan_wao_detail')
    ->where('pengajuan_id', $pengajuan->id)
    ->where('nip', $nip)
    ->first();

if (!$detail) {
    echo "Detail tidak ditemukan untuk NIP ini!\n";
    exit;
}

echo "\nJadwal seminggu untuk $nip:\n";
echo "- Senin: " . ($detail->senin ? 'WFO' : 'WFA') . "\n";
echo "- Selasa: " . ($detail->selasa ? 'WFO' : 'WFA') . "\n";
echo "- Rabu: " . ($detail->rabu ? 'WFO' : 'WFA') . "\n";
echo "- Kamis: " . ($detail->kamis ? 'WFO' : 'WFA') . "\n";
echo "- Jumat: " . ($detail->jumat ? 'WFO' : 'WFA') . "\n";
