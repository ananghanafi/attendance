<?php

namespace App\Http\Controllers;

use App\Models\KalenderKerjaV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminKalenderKerjaController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        if ($role !== 'admin') {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        $rows = KalenderKerjaV2::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.kalender.index', ['rows' => $rows]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'minggu' => ['required','integer','min:1','max:6'],
            'wfo_maks' => ['required','numeric','min:0','max:100'],
            'bulan' => ['required','integer','min:1','max:12'],
            'tahun' => ['required','integer','min:2000','max:2100'],
            'tgl_awal' => ['required','date'],
            'tgl_akhir' => ['required','date','after_or_equal:tgl_awal'],
        ], [
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.',
        ]);

        KalenderKerjaV2::create([
            'periode' => sprintf('Minggu %d', $data['minggu']),
            'tgl_awal' => $data['tgl_awal'],
            'tgl_akhir' => $data['tgl_akhir'],
        // Simpan dua format untuk jaga-jaga kompatibilitas data lama:
            'persentase_decimal' => (float) $data['wfo_maks'],
            'persentase' => ((float) $data['wfo_maks']) / 100.0,
            'judul' => sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']),
            'active' => false,
        ]);

        // data maks 10
        $count = KalenderKerjaV2::query()->count();
        if ($count > 10) {
            $toDelete = $count - 10;
            KalenderKerjaV2::query()
                ->orderBy('id')
                ->limit($toDelete)
                ->delete();
        }

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil disimpan (dummy).');
    }

    public function edit(int $id)
    {
        $this->ensureAdmin();

        $row = KalenderKerjaV2::query()->findOrFail($id);
        
        return view('admin.kalender.edit', ['row' => $row]);
    }

    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();

        $row = KalenderKerjaV2::query()->findOrFail($id);

        $data = $request->validate([
            'minggu' => ['required','integer','min:1','max:6'],
            'wfo_maks' => ['required','numeric','min:0','max:100'],
            'bulan' => ['required','integer','min:1','max:12'],
            'tahun' => ['required','integer','min:2000','max:2100'],
            'tgl_awal' => ['required','date'],
            'tgl_akhir' => ['required','date','after_or_equal:tgl_awal'],
        ], [
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.',
        ]);

        $row->update([
            'periode' => sprintf('Minggu %d', $data['minggu']),
            'tgl_awal' => $data['tgl_awal'],
            'tgl_akhir' => $data['tgl_akhir'],
            'persentase_decimal' => (float) $data['wfo_maks'],
            'persentase' => ((float) $data['wfo_maks']) / 100.0,
            'judul' => sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']),
        ]);

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->ensureAdmin();

        $row = KalenderKerjaV2::query()->findOrFail($id);
        $row->delete();

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil dihapus.');
    }
}
