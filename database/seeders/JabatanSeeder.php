<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // repeatable seeding
        DB::table('jabatan')->delete();

        // Reset sequence (PostgreSQL)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('jabatan', 'id'), 1, false);");
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $names = [
            'STAF',
            'MANAJER DIVISI',
            'MANAJER BAGIAN',
            'OB',
            'DRIVER',
            'AHLI MUDA',
            'MANAJER BIDANG',
            'KEPALA PEMERIKSA',
            'MANAJER BIDANG QS',
            'KOORDINATOR',
            'MANAJER UNIT',
        ];

        $rows = [];
        foreach ($names as $name) {
            $rows[] = [
                'jabatan' => $name,
                'is_proyek' => false,
            ];
        }

        DB::table('jabatan')->insert($rows);
    }
}
