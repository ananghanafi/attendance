<?php

namespace Tests\Feature;

use App\Models\KalenderKerjaV2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KalenderKerjaTest extends TestCase
{
    use RefreshDatabase;

    private function seedAdminUser(): array
    {

        $adminRoleId = DB::table('roles')->insertGetId([
            'role_name' => 'admin',
        ]);

        $userId = DB::table('users')->insertGetId([
            'username' => 'admin_test',
            'password' => bcrypt('admin123'),
            'nama' => 'Admin Test',
            'role_id' => $adminRoleId,
        ]);

        return [$userId, $adminRoleId];
    }

    public function test_store_prunes_to_max_10_rows(): void
    {
        [$userId] = $this->seedAdminUser();

        // Seed 10 rows awal
        for ($i = 1; $i <= 10; $i++) {
            KalenderKerjaV2::create([
                'periode' => 'Minggu 1',
                'tgl_awal' => '2026-01-01',
                'tgl_akhir' => '2026-01-02',
                'persentase_decimal' => 50,
                'persentase' => 0.5,
                'judul' => 'Seed ' . $i,
                'active' => false,
            ]);
        }

        $this->actingAs(\App\Models\User::query()->findOrFail($userId));

        $this->post('/kalender-kerja', [
            'minggu' => 2,
            'wfo_maks' => 60,
            'bulan' => 1,
            'tahun' => 2026,
            'tgl_awal' => '2026-01-03',
            'tgl_akhir' => '2026-01-04',
        ])->assertRedirect('/kalender-kerja');

        $this->assertSame(10, KalenderKerjaV2::query()->count());

        // Data paling lama harus terhapus, dan data terbaru ada
        $this->assertDatabaseMissing('kalender_kerja_v2', ['judul' => 'Seed 1']);
        $this->assertDatabaseHas('kalender_kerja_v2', ['periode' => 'Minggu 2']);
        $this->assertDatabaseHas('kalender_kerja_v2', ['judul' => 'Minggu ke-2, 01/2026']);
    }

    public function test_update_edits_row(): void
    {
        [$userId] = $this->seedAdminUser();

        $row = KalenderKerjaV2::create([
            'periode' => 'Minggu 1',
            'tgl_awal' => '2026-01-01',
            'tgl_akhir' => '2026-01-02',
            'persentase_decimal' => 50,
            'persentase' => 0.5,
            'judul' => 'ToEdit',
            'active' => false,
        ]);

        $this->actingAs(\App\Models\User::query()->findOrFail($userId));

        $this->put('/kalender-kerja/' . $row->id, [
            'minggu' => 3,
            'wfo_maks' => 70,
            'bulan' => 1,
            'tahun' => 2026,
            'tgl_awal' => '2026-01-05',
            'tgl_akhir' => '2026-01-06',
        ])->assertRedirect('/kalender-kerja');

        $this->assertDatabaseHas('kalender_kerja_v2', [
            'id' => $row->id,
            'periode' => 'Minggu 3',
        ]);
    }

    public function test_delete_removes_row(): void
    {
        [$userId] = $this->seedAdminUser();

        $row = KalenderKerjaV2::create([
            'periode' => 'Minggu 1',
            'tgl_awal' => '2026-01-01',
            'tgl_akhir' => '2026-01-02',
            'persentase_decimal' => 50,
            'persentase' => 0.5,
            'judul' => 'ToDelete',
            'active' => false,
        ]);

        $this->actingAs(\App\Models\User::query()->findOrFail($userId));

        $this->delete('/kalender-kerja/' . $row->id)
            ->assertRedirect('/kalender-kerja');

        $this->assertDatabaseMissing('kalender_kerja_v2', [
            'id' => $row->id,
        ]);
    }
}
