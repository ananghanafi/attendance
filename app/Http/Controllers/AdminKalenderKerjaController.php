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
        // accept both legacy 'admin' and new 'ADMIN'
        if ($role !== 'admin' && $role !== 'ADMIN') {
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

        $kalenderDb = sprintf('%d-%d-%d', $data['minggu'], $data['bulan'], $data['tahun']);

        $judulText = sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']);

        // block tanggal duplikat, based minggu+bulan+tahun
        $exists = KalenderKerjaV2::query()->where('kalender', $kalenderDb)->exists();
        if ($exists) {
            return back()
                ->withErrors(['kalender' => "Data untuk {$judulText} sudah ada."])
                ->withInput();
        }

        KalenderKerjaV2::create([
            'periode' => sprintf('Minggu %d', $data['minggu']),
            'tgl_awal' => $data['tgl_awal'],
            'tgl_akhir' => $data['tgl_akhir'],
            'persentase_decimal' => (float) $data['wfo_maks'],
            'persentase' => (float) $data['wfo_maks'],
            'kalender' => $kalenderDb,
            'judul' => $judulText,
            'active' => false,
        ]);

        // Get the created kalender ID
        $kalenderId = DB::table('kalender_kerja_v2')
            ->where('kalender', $kalenderDb)
            ->value('id');

        // Auto-create pengajuan WFO untuk semua biro (is_proyek = false)
        $biros = DB::table('biro')
            ->where('is_proyek', false)
            ->get(['id', 'biro_name']);

        foreach ($biros as $biro) {
            // Check if pengajuan already exists untuk biro + kalender ini
            $exists = DB::table('pengajuan_wao')
                ->where('biro_id', $biro->id)
                ->where('kalender', $kalenderDb) // Simpan format "minggu-bulan-tahun"
                ->exists();

            if (!$exists) {
                // Create pengajuan WFO
                $pengajuanId = DB::table('pengajuan_wao')->insertGetId([
                    'biro_id' => $biro->id,
                    'kalender' => $kalenderDb, // Simpan format "minggu-bulan-tahun"
                    'status' => 'draft', // draft = belum diedit (Open), final = sudah diedit (Close)
                    'created_date' => now(),
                ]);

                // Get all users from this biro
                $users = DB::table('users')
                    ->where('biro_id', $biro->id)
                    ->get(['nip']);

                // Create detail records for each user (default: all WFA = false means WFO)
                foreach ($users as $user) {
                    DB::table('pengajuan_wao_detail')->insert([
                        'pengajuan_id' => $pengajuanId,
                        'nip' => $user->nip,
                        'senin' => false,
                        'selasa' => false,
                        'rabu' => false,
                        'kamis' => false,
                        'jumat' => false,
                    ]);
                }
            }
        }

        // Auto-delete old pengajuan: hapus pengajuan minggu 1 bulan lama saat input minggu 1 bulan baru
        if ($data['minggu'] == 1) {
            // Get previous month's week 1 kalender
            $prevMonth = $data['bulan'] - 1;
            $prevYear = $data['tahun'];
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }
            
            $oldKalender = sprintf('1-%d-%d', $prevMonth, $prevYear);
            
            // Delete pengajuan details first (berdasarkan kalender format "minggu-bulan-tahun")
            DB::table('pengajuan_wao_detail')
                ->whereIn('pengajuan_id', function($query) use ($oldKalender) {
                    $query->select('id')
                        ->from('pengajuan_wao')
                        ->where('kalender', $oldKalender);
                })
                ->delete();
            
            // Delete pengajuan master
            DB::table('pengajuan_wao')
                ->where('kalender', $oldKalender)
                ->delete();
            
            // Optionally delete old kalender_kerja_v2 too
            DB::table('kalender_kerja_v2')
                ->where('kalender', $oldKalender)
                ->delete();
        }

        // data tanggal maks 10
        $count = KalenderKerjaV2::query()->count();
        if ($count > 10) {
            $toDelete = $count - 10;
            KalenderKerjaV2::query()
                ->orderBy('id')
                ->limit($toDelete)
                ->delete();
        }

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil disimpan.');
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

        $kalenderDb = sprintf('%d-%d-%d', $data['minggu'], $data['bulan'], $data['tahun']);

        $judulText = sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']);

        // block duplikat tanggal
        $exists = KalenderKerjaV2::query()
            ->where('kalender', $kalenderDb)
            ->where('id', '!=', $row->id)
            ->exists();
        if ($exists) {
            return back()
                ->withErrors(['kalender' => "Data untuk {$judulText} sudah ada."])
                ->withInput();
        }

        $row->update([
            'periode' => sprintf('Minggu %d', $data['minggu']),
            'tgl_awal' => $data['tgl_awal'],
            'tgl_akhir' => $data['tgl_akhir'],
            // simpan dalam satuan persen (contoh: input 50 => simpan 50, bukan 0.5)
            'persentase_decimal' => (float) $data['wfo_maks'],
            'persentase' => (float) $data['wfo_maks'],
            'kalender' => $kalenderDb,
            'judul' => $judulText,
        ]);

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->ensureAdmin();

        $row = KalenderKerjaV2::query()->findOrFail($id);
        
        // Get kalender string untuk delete pengajuan
        $kalenderString = $row->kalender; // format "minggu-bulan-tahun"
        
        // Cascade delete: hapus pengajuan WFO yang terkait dengan kalender ini
        // 1. Hapus detail tanggal dulu
        DB::table('pengajuan_wao_detail_tanggal')
            ->whereIn('pengajuan_id', function($query) use ($kalenderString) {
                $query->select('id')
                    ->from('pengajuan_wao')
                    ->where('kalender', $kalenderString);
            })
            ->delete();
        
        // 2. Hapus detail dulu
        DB::table('pengajuan_wao_detail')
            ->whereIn('pengajuan_id', function($query) use ($kalenderString) {
                $query->select('id')
                    ->from('pengajuan_wao')
                    ->where('kalender', $kalenderString);
            })
            ->delete();
        
        // 3. Hapus master pengajuan
        DB::table('pengajuan_wao')
            ->where('kalender', $kalenderString)
            ->delete();
        
        // 4. Hapus kalender
        $row->delete();

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil dihapus.');
    }

    // =====================
    // KALENDER LIBUR
    // =====================
    
    /**
     * Tampilkan semua tanggal libur
     */
    public function liburIndex()
    {
        $this->ensureAdmin();

        $libur = DB::table('kalender_libur')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $libur
        ]);
    }

    /**
     * Simpan tanggal libur (bisa multiple)
     */
    public function liburStore(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'required|date',
        ]);

        $tanggalList = $request->input('tanggal');
        $inserted = 0;
        $skipped = 0;

        foreach ($tanggalList as $tgl) {
            // Cek apakah tanggal sudah ada
            $exists = DB::table('kalender_libur')
                ->where('tanggal', $tgl)
                ->exists();

            if (!$exists) {
                DB::table('kalender_libur')->insert([
                    'tanggal' => $tgl,
                ]);
                $inserted++;
            } else {
                $skipped++;
            }
        }

        $message = "Berhasil menambahkan {$inserted} tanggal libur.";
        if ($skipped > 0) {
            $message .= " ({$skipped} tanggal sudah ada sebelumnya)";
        }

        return redirect()->route('admin.kalender')->with('status', $message);
    }

    /**
     * Hapus tanggal libur
     */
    public function liburDestroy(int $id)
    {
        $this->ensureAdmin();

        $deleted = DB::table('kalender_libur')
            ->where('id', $id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Tanggal libur berhasil dihapus.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tanggal libur tidak ditemukan.'
        ], 404);
    }
}