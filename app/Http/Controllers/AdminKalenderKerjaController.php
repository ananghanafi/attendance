<?php

namespace App\Http\Controllers;

use App\Models\KalenderKerjaV2;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminKalenderKerjaController extends Controller
{
    private function isHC(): bool
    {
        $user = Auth::user();
        $biroName = DB::table('biro')->where('id', $user->biro_id)->value('biro_name');
        return stripos($biroName ?? '', 'Human Capital') !== false;
    }

    private function isAdmin(): bool
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        return strtoupper($role ?? '') === 'ADMIN';
    }

    /**
     * Pastikan user adalah Admin atau HC (VP tidak bisa akses kalender)
     */
    private function ensureAdminOrHC(): void
    {
        if (!$this->isAdmin() && !$this->isHC()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrHC();

        $page = $request->input('p', session('kalender_page', 1));
        session(['kalender_page' => $page]);

        // Sort by tgl_awal descending 
        $rows = KalenderKerjaV2::query()
            ->orderByDesc('tgl_awal')
            ->paginate(10, ['*'], 'p', $page);

        // Session-based pagination untuk kalender libur
        $liburPage = $request->input('lp', session('kalender_libur_page', 1));
        session(['kalender_libur_page' => $liburPage]);

        $libur = DB::table('kalender_libur')
            ->orderBy('tanggal', 'desc')
            ->paginate(10, ['*'], 'lp', $liburPage);

        $activeTab = $request->input('tab', session('kalender_active_tab', 'form'));
        session(['kalender_active_tab' => $activeTab]);

        return view('admin.kalender.index', [
            'rows' => $rows,
            'libur' => $libur,
            'activeTab' => $activeTab
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrHC();

        $data = $request->validate([
            'minggu' => ['required', 'integer', 'min:1', 'max:6'],
            'tipe_persentase' => ['required', 'in:wfo,wfa'],
            'nilai_persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'tgl_awal' => ['required', 'date'],
            'tgl_akhir' => ['required', 'date', 'after_or_equal:tgl_awal'],
        ], [
            'minggu.required' => 'Minggu harus diisi.',
            'minggu.integer' => 'Minggu harus berupa angka.',
            'minggu.min' => 'Minggu minimal 1.',
            'minggu.max' => 'Minggu maksimal 6.',
            'tipe_persentase.required' => 'Pilih tipe persentase (WFO atau WFA).',
            'tipe_persentase.in' => 'Tipe persentase harus WFO atau WFA.',
            'nilai_persentase.required' => 'Nilai persentase harus diisi.',
            'nilai_persentase.numeric' => 'Nilai persentase harus berupa angka.',
            'nilai_persentase.min' => 'Nilai persentase minimal 0.',
            'nilai_persentase.max' => 'Nilai persentase maksimal 100.',
            'bulan.required' => 'Bulan harus diisi.',
            'bulan.integer' => 'Bulan harus berupa angka.',
            'bulan.min' => 'Bulan minimal 1.',
            'bulan.max' => 'Bulan maksimal 12.',
            'tahun.required' => 'Tahun harus diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun minimal 2000.',
            'tahun.max' => 'Tahun maksimal 2100.',
            'tgl_awal.required' => 'Tanggal awal harus diisi. Silakan pilih tanggal di kalender.',
            'tgl_awal.date' => 'Tanggal awal harus berupa tanggal yang valid.',
            'tgl_akhir.required' => 'Tanggal akhir harus diisi. Silakan pilih tanggal di kalender.',
            'tgl_akhir.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.',
        ]);

        // Simpan WFO atau WFA, yang lain null (persentase_decimal selalu null)
        if ($data['tipe_persentase'] === 'wfo') {
            $wfo = (float) $data['nilai_persentase'];
            $wfa = null;
        } else {
            $wfo = null;
            $wfa = (float) $data['nilai_persentase'];
        }

        $kalenderDb = sprintf('%d-%d-%d', $data['minggu'], $data['bulan'], $data['tahun']);

        $judulText = sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']);

        // block data duplikat
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
            'persentase_decimal' => null,
            'persentase' => $wfo,
            'persentase_wfa' => $wfa,
            'kalender' => $kalenderDb,
            'judul' => $judulText,
            'active' => false,
        ]);
        // get id kalender
        $kalenderId = DB::table('kalender_kerja_v2')
            ->where('kalender', $kalenderDb)
            ->value('id');
        // pengajuan wao biro (is_proyek = false)
        $biros = DB::table('biro')
            ->where('is_proyek', false)
            ->get(['id', 'biro_name']);

        foreach ($biros as $biro) {
            $exists = DB::table('pengajuan_wao')
                ->where('biro_id', $biro->id)
                ->where('kalender', $kalenderDb)
                ->exists();
            if (!$exists) {
                // Create pengajuan WFO
                $pengajuanId = DB::table('pengajuan_wao')->insertGetId([
                    'biro_id' => $biro->id,
                    'kalender' => $kalenderDb,
                    'status' => 'draft', // open = belum edit, close = final
                    'created_date' => now(),
                ]);

                // fetch data biro
                $users = DB::table('users')
                    ->where('biro_id', $biro->id)
                    ->get(['nip']);

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

        // hapus data minggu paling lama (max 4 data di view)
        if ($data['minggu'] == 1) {
            // Get previous month's week 1 kalender
            $prevMonth = $data['bulan'] - 1;
            $prevYear = $data['tahun'];
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }

            $oldKalender = sprintf('1-%d-%d', $prevMonth, $prevYear);

            // Delete pengajuan detail
            DB::table('pengajuan_wao_detail')
                ->whereIn('pengajuan_id', function ($query) use ($oldKalender) {
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

        // Kirim notifikasi WhatsApp ke user dengan is_kirim = true
        try {
            $whatsappService = new WhatsAppNotificationService();
            $notifResult = $whatsappService->sendPengajuanWfoNotification(
                $kalenderId,
                $kalenderDb,
                $data['minggu'],
                $data['bulan'],
                $data['tahun']
            );

            if ($notifResult['sent_count'] > 0) {
                Log::info('WhatsApp notification sent', [
                    'kalender' => $kalenderDb,
                    'sent_count' => $notifResult['sent_count'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send WhatsApp notification', [
                'error' => $e->getMessage(),
                'kalender' => $kalenderDb,
            ]);
        }
        // Tetap di tab form setelah sukses simpan
        session(['kalender_active_tab' => 'form']);

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil disimpan.');
    }

    /**
     * Set session untuk edit kalender kerja
     */
    public function setEdit(Request $request)
    {
        $this->ensureAdminOrHC();
        session(['kalender_edit_id' => $request->input('id')]);
        return redirect()->route('kalender.edit');
    }

    public function edit()
    {
        $this->ensureAdminOrHC();

        $id = session('kalender_edit_id');
        if (!$id) {
            return redirect()->route('admin.kalender')->with('error', 'Silakan pilih kalender dari daftar');
        }

        $row = KalenderKerjaV2::query()->findOrFail($id);

        return view('admin.kalender.edit', ['row' => $row]);
    }

    public function update(Request $request)
    {
        $this->ensureAdminOrHC();

        $id = session('kalender_edit_id');
        if (!$id) {
            return redirect()->route('admin.kalender')->with('error', 'Silakan pilih kalender dari daftar');
        }

        $row = KalenderKerjaV2::query()->findOrFail($id);

        $data = $request->validate([
            'minggu' => ['required', 'integer', 'min:1', 'max:6'],
            'tipe_persentase' => ['required', 'in:wfo,wfa'],
            'nilai_persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'tgl_awal' => ['required', 'date'],
            'tgl_akhir' => ['required', 'date', 'after_or_equal:tgl_awal'],
        ], [
            'minggu.required' => 'Minggu harus diisi.',
            'minggu.integer' => 'Minggu harus berupa angka.',
            'minggu.min' => 'Minggu minimal 1.',
            'minggu.max' => 'Minggu maksimal 6.',
            'tipe_persentase.required' => 'Pilih tipe persentase (WFO atau WFA).',
            'tipe_persentase.in' => 'Tipe persentase harus WFO atau WFA.',
            'nilai_persentase.required' => 'Nilai persentase harus diisi.',
            'nilai_persentase.numeric' => 'Nilai persentase harus berupa angka.',
            'nilai_persentase.min' => 'Nilai persentase minimal 0.',
            'nilai_persentase.max' => 'Nilai persentase maksimal 100.',
            'bulan.required' => 'Bulan harus diisi.',
            'bulan.integer' => 'Bulan harus berupa angka.',
            'bulan.min' => 'Bulan minimal 1.',
            'bulan.max' => 'Bulan maksimal 12.',
            'tahun.required' => 'Tahun harus diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun minimal 2000.',
            'tahun.max' => 'Tahun maksimal 2100.',
            'tgl_awal.required' => 'Tanggal awal harus diisi.',
            'tgl_awal.date' => 'Tanggal awal harus berupa tanggal yang valid.',
            'tgl_akhir.required' => 'Tanggal akhir harus diisi.',
            'tgl_akhir.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.',
        ]);

        // Simpan WFO atau WFA, yang lain null
        if ($data['tipe_persentase'] === 'wfo') {
            $wfo = (float) $data['nilai_persentase'];
            $wfa = null;
        } else {
            $wfa = (float) $data['nilai_persentase'];
            $wfo = null;
        }

        $kalenderDb = sprintf('%d-%d-%d', $data['minggu'], $data['bulan'], $data['tahun']);

        $judulText = sprintf('Minggu ke-%d, %02d/%d', $data['minggu'], $data['bulan'], $data['tahun']);

        // block duplikat data
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
            'persentase_decimal' => null,
            'persentase' => $wfo,
            'persentase_wfa' => $wfa,
            'kalender' => $kalenderDb,
            'judul' => $judulText,
        ]);
        session(['kalender_active_tab' => 'data']);

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil diperbarui.');
    }

    /**
     * Set session untuk delete kalender kerja dan langsung hapus
     */
    public function setDelete(Request $request)
    {
        $this->ensureAdminOrHC();

        $id = $request->input('id');
        if (!$id) {
            return redirect()->route('admin.kalender')->with('error', 'Silakan pilih kalender dari daftar');
        }

        session(['kalender_delete_id' => $id]);

        // Langsung panggil destroy
        return $this->destroy();
    }

    public function destroy()
    {
        $this->ensureAdminOrHC();

        $id = session('kalender_delete_id');
        if (!$id) {
            return redirect()->route('admin.kalender')->with('error', 'Silakan pilih kalender dari daftar');
        }

        $row = KalenderKerjaV2::query()->findOrFail($id);

        // Get kalender string untuk delete pengajuan
        $kalenderString = $row->kalender;

        DB::table('pengajuan_wao_detail_tanggal')
            ->whereIn('pengajuan_id', function ($query) use ($kalenderString) {
                $query->select('id')
                    ->from('pengajuan_wao')
                    ->where('kalender', $kalenderString);
            })
            ->delete();

        DB::table('pengajuan_wao_detail')
            ->whereIn('pengajuan_id', function ($query) use ($kalenderString) {
                $query->select('id')
                    ->from('pengajuan_wao')
                    ->where('kalender', $kalenderString);
            })
            ->delete();

        DB::table('pengajuan_wao')
            ->where('kalender', $kalenderString)
            ->delete();

        $row->delete();

        session()->forget('kalender_delete_id');

        session(['kalender_active_tab' => 'data']);

        return redirect()->route('admin.kalender')->with('status', 'Data kalender kerja berhasil dihapus.');
    }

    // =====================
    // KALENDER LIBUR
    // =====================

    public function liburIndex()
    {
        $this->ensureAdminOrHC();

        $libur = DB::table('kalender_libur')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $libur
        ]);
    }

    /**
     * Simpan tanggal libur (multiple)
     */
    public function liburStore(Request $request)
    {
        $this->ensureAdminOrHC();

        $request->validate([
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'required|date',
        ], [
            'tanggal.required' => 'Tanggal libur harus diisi.',
            'tanggal.array' => 'Format tanggal tidak valid.',
            'tanggal.min' => 'Pilih minimal 1 tanggal libur.',
            'tanggal.*.required' => 'Tanggal libur harus diisi.',
            'tanggal.*.date' => 'Tanggal libur harus berupa tanggal yang valid.',
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

        // Stay di tab "libur" setelah store
        session(['kalender_active_tab' => 'libur']);

        return redirect()->route('admin.kalender')->with('status', $message);
    }

    /**
     * Hapus tanggal libur
     */
    public function liburDestroy(int $id)
    {
        $this->ensureAdminOrHC();

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

    /**
     * Broadcast ulang notifikasi WhatsApp ke semua biro untuk kalender tertentu
     */
    public function broadcast(Request $request)
    {
        $this->ensureAdminOrHC();

        $id = $request->input('id');
        if (!$id) {
            return redirect()->route('admin.kalender')->with('error', 'Kalender tidak ditemukan');
        }

        $kalender = KalenderKerjaV2::find($id);
        if (!$kalender) {
            return redirect()->route('admin.kalender')->with('error', 'Kalender tidak ditemukan');
        }

        // Get all pengajuan for this kalender
        $pengajuans = DB::table('pengajuan_wao')
            ->where('kalender', $kalender->kalender)
            ->get();

        if ($pengajuans->isEmpty()) {
            return redirect()->route('admin.kalender')->with('error', 'Tidak ada pengajuan untuk kalender ini');
        }

        $waService = new WhatsAppNotificationService();
        $successCount = 0;
        $failCount = 0;

        foreach ($pengajuans as $pengajuan) {
            try {
                $result = $waService->sendNotificationForPengajuan($pengajuan->id);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                Log::error('Broadcast error: ' . $e->getMessage());
                $failCount++;
            }
        }

        session(['kalender_active_tab' => 'data']);
        return redirect()->route('admin.kalender')->with('status', 'Broadcast berhasil dikirim');
    }
}
