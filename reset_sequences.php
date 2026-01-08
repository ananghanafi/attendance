<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Reset sequence untuk pengajuan_wao
    DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao', 'id'), COALESCE((SELECT MAX(id) FROM pengajuan_wao), 0) + 1, false)");
    echo "✅ Sequence pengajuan_wao reset\n";
    
    // Reset sequence untuk pengajuan_wao_detail
    DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao_detail', 'id'), COALESCE((SELECT MAX(id) FROM pengajuan_wao_detail), 0) + 1, false)");
    echo "✅ Sequence pengajuan_wao_detail reset\n";
    
    // Reset sequence untuk pengajuan_wao_detail_tanggal
    DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao_detail_tanggal', 'id'), COALESCE((SELECT MAX(id) FROM pengajuan_wao_detail_tanggal), 0) + 1, false)");
    echo "✅ Sequence pengajuan_wao_detail_tanggal reset\n";
    
    // Reset sequence untuk kalender_kerja_v2
    DB::statement("SELECT setval(pg_get_serial_sequence('kalender_kerja_v2', 'id'), COALESCE((SELECT MAX(id) FROM kalender_kerja_v2), 0) + 1, false)");
    echo "✅ Sequence kalender_kerja_v2 reset\n";
    
    echo "\n🎉 Semua sequence berhasil di-reset!\n";
    echo "ID berikutnya akan dimulai dari 1 (jika tabel kosong)\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
