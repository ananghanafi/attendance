<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * Kolom yang boleh diisi via create()/update() (mass assignment)
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'nama',
        'nip',
        'email',
        'telp',
        'role_id',
        'biro_id',
        'nip_atasan',
        'isdel',
        'tgl_lahir',
        'transportasi',
        'id_kel',
        'id_lokasi_car_pooling',
        'is_covid_ranger',
        'is_tim_covid',
        'is_satgas_covid',
        'is_hc',
        'is_umum',
        'jabatan',
        'is_kirim',
        'is_crot',
        'is_pulang',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'isdel' => 'boolean',
            'is_satgas_covid' => 'boolean',
            'is_hc' => 'boolean',
            'is_kirim' => 'boolean',
            'is_crot' => 'boolean',
            'tgl_lahir' => 'date',
        ];
    }
}
