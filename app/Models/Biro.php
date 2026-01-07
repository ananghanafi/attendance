<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biro extends Model
{
    protected $table = 'biro';

    public $timestamps = false;

    protected $fillable = [
        'biro_name',
        'is_proyek',
        'divisi',
    ];

    protected $casts = [
        'is_proyek' => 'boolean',
    ];
}
