<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    public $timestamps = false;

    protected $fillable = [
        'jabatan',
        'is_proyek',
    ];

    protected $casts = [
        'is_proyek' => 'boolean',
    ];
}
