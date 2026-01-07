<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Biar bisa di-seed berulang kali tanpa dobel data
        DB::table('users')->delete();
        DB::table('roles')->delete();

        // Reset sequence supaya id mulai dari 1 lagi (PostgreSQL)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('roles', 'id'), 1, false);");
                DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), 1, false);");
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $roleNames = [
            'STAF',
            'MANAGER',
            'ADMIN',
            'VP',
            'ASS DIREKTUR OPERASI I',
            'ASS DIREKTUR TEKNIK DAN PENGEMBANGAN',
            'DIREKTUR TEKNIK DAN PENGEMBANGAN',
            'DIREKTUR OPERASI II',
            'DIREKTUR OPERASI I',
            'DIREKTUR KEUANGAN, HC DAN MANRISK',
            'DIREKTUR UTAMA',
        ];

        $adminRoleId = null;
        foreach ($roleNames as $name) {
            $id = DB::table('roles')->insertGetId([
                'role_name' => $name,
            ]);
            if ($name === 'ADMIN') {
                $adminRoleId = $id;
            }
        }

        // Default admin user (login pakai username)
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama' => 'Administrator',
            'nip' => '0001',
            'email' => null,
            'telp' => null,
            'role_id' => $adminRoleId,
            'biro_id' => null,
            'nip_atasan' => null,
            'isdel' => 0,
        ]);
    }
}
