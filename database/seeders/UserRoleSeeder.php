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
        // Create roles
        $adminRoleId = DB::table('roles')->insertGetId([
            'role_name' => 'admin',
        ]);

        $userRoleId = DB::table('roles')->insertGetId([
            'role_name' => 'user',
        ]);

        // Create admin user (login menggunakan username)
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

        // Create regular user (login menggunakan username)
        DB::table('users')->insert([
            'username' => 'user',
            'password' => Hash::make('user123'),
            'nama' => 'Regular User',
            'nip' => '0002',
            'email' => null,
            'telp' => null,
            'role_id' => $userRoleId,
            'biro_id' => null,
            'nip_atasan' => null,
            'isdel' => 0,
        ]);
    }
}
