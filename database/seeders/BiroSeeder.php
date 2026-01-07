<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Repeatable seeding
        DB::table('biro')->delete();

        // Reset sequence (PostgreSQL)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('biro', 'id'), 1, false);");
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $names = [
            'DIREKSI DAN KOMITE',
            'INTERNAL AUDITOR',
            'CORPORATE SECRETARIAT',
            'OPERATION DIVISION',
            'OPERATION AND MODULAR DIVISION',
            'OPERATION AND CONCESSION DIVISION',
            'FINANCE DIVISION',
            'INFORMATION TECHNOLOGY DIVISION',
            'HUMAN CAPITAL DIVISION',
            'PROJECT MANAGEMENT OFFICE AND RISK MANAGEMENT DIVISION',
            'BIRO MANAJEMEN RISIKO',
            'SCM DIVISION',
            'QSHE DIVISION',
            'MARKETING DIVISION',
            'TRANSFORMATION AND BUSINESS PORTFOLIO DEVELOPMENT DIVISION',
            'LEGAL AND CONTRACT MANAGEMENT DIVISION',
            'ENGINEERING DIVISION',
            'MEP DAN QS DIVISI 1',
            'MEP DAN QS DIVISI 2',
            'MEP DAN QS DIVISI 3',
            'PROYEK GRAHA MANDIRI TABUNGAN PENSIUN',
            'PROYEK GRHA PERTAMINA',
            'PROYEK WORKSHOP ALAT DAN GUDANG ARSIP',
            'PROYEK TAMAN BUDAYA GUNUNG KIDUL',
            'PROYEK THE PARK MALL SEMARANG',
            'PROYEK ARANDRA RESIDENCE',
            'PROYEK WIKASATRIAN',
            'PABRIK MODULAR',
            'PROYEK TAMAN ISMAIL MARZUKI',
            'PROYEK RUSUN PADAT KARYA',
            'PROYEK PODOMORO GOLF VIEW',
            'PROYEK TRANSPARK CIBUBUR',
            'PROYEK JAKARTA INTERNATIONAL STADIUM',
            'PROYEK APARTEMEN COLLINS',
            'PROYEK PEMBANGUNAN GEDUNG BSSN',
            'PROYEK APARTEMEN EMBARCADERO',
            'PROYEK RUMAH SAKIT UMUM DAERAH CENGKARENG',
            'PROYEK TAMANSARI URBANO',
            'PROYEK APARTEMEN TAMANSARI ISWARA',
            'PROYEK PITK BOJONG GEDE TAHAP 2',
            'PROYEK HOTEL T3',
            'PROYEK APARTEMEN CORNELL & DENVER',
            'PROYEK GRESIK GRAND MALL',
            'PROYEK HOTEL INNA SANUR',
            'PROYEK RUKO PUNCAK CBD WIYUNG SURABAYA',
            'PROYEK KAJATI NTB',
            'PROYEK NAYUMI',
            'PROYEK KAMPUS UC CITRALAND SURABAYA',
            'PROYEK TAMANSARI EMERALD',
            'PROYEK BANDARA MAKASSAR',
            'PROYEK LRT',
            'PROYEK UIN BANTEN',
            'PROYEK BANDARA BANJARMASIN',
            'PROYEK TAMANSARI CENDEKIA',
            'PROYEK SITE OFFICE KCIC',
            'PROYEK PELINDO III OFFICE CENTER',
            'PROYEK GEDUNG SEKOLAH PAKET 5',
            'DIVISI MODULAR DAN KONSESI',
            'PROYEK TRANSMART PEKALONGAN',
            'PROYEK TRANSMART MAJAPAHIT',
            'PROYEK TAMANSARI PROSPERO',
            'PROYEK PASAR RAKYAT PARIAMAN',
            'PROYEK RUMAH SAKIT PELABUHAN PALEMBANG',
            'PROYEK STASIUN MRT LEBAK BULUS',
            'PROYEK GRAND BENOA BALI',
            'PROYEK SAFIRA CITY',
            'PROYEK UNIVERSITAS GADJAH MADA',
            'PROYEK BARSA CITY YOGYAKARTA',
            'PROYEK TELKOM MANYAR',
            'PROYEK THE GRANDSTAND APARTEMEN',
            'PROYEK SUNCITY',
            'PROYEK TERMINAL 3 BANDARA SOEKARNO HATTA',
            'PROYEK GEDUNG PUSAT INOVASI',
            'PROYEK TAMANSARI MAHOGANI KARAWANG',
            'PROYEK MANDIRI PROKLAMASI',
            'PROYEK APARTEMEN PUNCAK MERR',
            'PROYEK RUMAH SUSUN BANTEN',
            'PROYEK GEDUNG MABES POLRI',
            'PROYEK BOARDING HOUSE KUNINGAN',
            'PROYEK PULAU MERAH',
            'PROYEK BSSN',
            'PROYEK SUDIRMAN HILL RESIDENCES',
            'PROYEK TAMANSARI TERA',
            'PROYEK KANTOR MENARA MANDIRI',
            'PROYEK GEDUNG SEKOLAH PAKET 1',
            'PROYEK METRO GALAXY PARK',
            'PT WIJAYA KARYA PRACETAK GEDUNG',
            'FAVE HOTEL',
            'HOTEL BRAGA',
            'UNIT MAHOGANY',
            'UNIT PROSPERO',
            'PROYEK ANTAM MEDIKA',
            'PROYEK GRESIK ICON APARTEMEN',
            'PROYEK BI PALANGKARAYA',
            'PROYEK TOUR DE GOREE SENEGAL',
            'PROYEK PARK KENDARI',
            'PROYEK UNIVERSITAS CIPUTRA',
            'PROYEK RS COVID-19 LAMONGAN',
            'KANTOR PUSAT',
            'PROYEK NASDEM TOWER GONDANGDIA',
            'FUNGSI UMUM',
            'PROYEK RUSUN CIPINANG BESAR UTAMA',
            'PROYEK MANDIRI BALI',
            'PROYEK BRIKS',
            'PROYEK HOTEL PULLMAN MANDALIKA',
            'PROYEK PLBN JAGOI BABANG',
            'PROYEK LABUAN BAJO',
        ];

        $rows = [];
        foreach ($names as $name) {
            $isProyek = str_starts_with($name, 'PROYEK ');
            $rows[] = [
                'biro_name' => $name,
                // request: proyek boleh true/false/null - aku set true untuk PROYEK*, selain itu false
                'is_proyek' => $isProyek,
                // request: divisi di-null-in
                'divisi' => null,
            ];
        }

        // chunk insert biar aman untuk query size
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('biro')->insert($chunk);
        }
    }
}
