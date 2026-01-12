<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderKerjaV2 extends Model
{
    protected $table = 'kalender_kerja_v2';

    public $timestamps = false;

    protected $fillable = [
        'kalender',
        'tgl_awal',
        'tgl_akhir',
        'nomor_surat',
        'nomor_surat_gubernur',
        'periode',
        'tanggal',
        'persentase',
        'active',
        'persentase_decimal',
        'poin_1',
        'poin_2',
        'poin_3',
        'poin_4',
        'poin_5',
        'judul',
        'persentase_wfa',
    ];

    protected $casts = [
        'tgl_awal' => 'date',
        'tgl_akhir' => 'date',
        'active' => 'boolean',
        'persentase' => 'float',
        'persentase_decimal' => 'float',
        'persentase_wfa' => 'float',
    ];
}
