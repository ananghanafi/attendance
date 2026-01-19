<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterUangMakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_uang_makan')->truncate();
        
        DB::table('master_uang_makan')->insert([
            'id_jenis' => 1,
            'uang' => 35000,
        ]);
    }
}
