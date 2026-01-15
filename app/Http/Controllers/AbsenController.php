<?php

namespace App\Http\Controllers;

use App\Services\AbsenLocationService;
use App\Services\WhatsAppNotificationService;
use App\Http\Controllers\ApprovalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
{
    protected AbsenLocationService $locationService;

    public function __construct(AbsenLocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Halaman utama absen - menampilkan button absen
     */
    public function index()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        // Get info dari location service (jam, posisi, button type)
        $info = $this->locationService->getInfo();

        // Cek jadwal WFO/WFA dari pengajuan_wao_detail
        $jadwal = $this->getJadwalHariIni($user);

        // Cek absen hari ini
        $absenHariIni = DB::table('absen')
            ->where('nip', $user->nip)
            ->where('tanggal', $today)
            ->first();

        // Tentukan apakah sudah absen masuk
        $sudahAbsenMasuk = $absenHariIni && $absenHariIni->scan_masuk;
        $sudahAbsenPulang = $absenHariIni && $absenHariIni->scan_pulang;

        // Cek apakah ada pending approval (jam_temp tapi belum scan_pulang)
        $pendingApproval = $absenHariIni && $absenHariIni->jam_temp && !$absenHariIni->scan_pulang;

        return view('absen.index', [
            'user' => $user,
            'info' => $info,
            'jadwal' => $jadwal,
            'absenHariIni' => $absenHariIni,
            'sudahAbsenMasuk' => $sudahAbsenMasuk,
            'sudahAbsenPulang' => $sudahAbsenPulang,
            'pendingApproval' => $pendingApproval,
        ]);
    }

    /**
     * Tampilkan formulir absen (masuk atau pulang)
     */
    public function showFormulir(Request $request)
    {
        $user = Auth::user();
        $tipe = $request->query('tipe', 'masuk'); // 'masuk' atau 'pulang'

        // Get info dari location service
        $info = $this->locationService->getInfo();

        // Cek jadwal WFO/WFA
        $jadwal = $this->getJadwalHariIni($user);

        // Jika jadwal belum ditentukan, redirect dengan pesan error
        if (!$jadwal['ada_jadwal']) {
            return redirect()->route('absen.index')
                ->with('error', 'Ketua belum menentukan jadwal pengajuan untuk minggu ini');
        }

        // Tentukan form type berdasarkan WFO/WFA dan posisi
        $isWfo = $jadwal['is_wfo'];
        $isInsideOffice = $this->locationService->isInsideOffice();
        $formType = $this->locationService->getFormType($isWfo);

        // Dapatkan opsi status (pass $isWfo untuk menentukan apakah WFA muncul)
        $statusOptions = $this->locationService->getStatusOptions($formType, $isWfo);

        // Cek absen hari ini
        $absenHariIni = DB::table('absen')
            ->where('nip', $user->nip)
            ->where('tanggal', now()->format('Y-m-d'))
            ->first();

        if ($tipe === 'pulang') {
            // Harus sudah absen masuk
            if (!$absenHariIni || !$absenHariIni->scan_masuk) {
                return redirect()->route('absen.index')
                    ->with('error', 'Anda harus absen masuk terlebih dahulu sebelum absen pulang');
            }

            // Cek apakah perlu approval (WFO tapi di luar kantor)
            $needsApproval = $this->locationService->needsPulangApproval($isWfo);

            return view('absen.formulir', [
                'user' => $user,
                'info' => $info,
                'jadwal' => $jadwal,
                'formType' => $formType,
                'statusOptions' => $statusOptions,
                'tipe' => $tipe,
                'absenHariIni' => $absenHariIni,
                'needsApproval' => $needsApproval,
            ]);
        }

        // Absen masuk - cek sudah absen atau belum
        if ($absenHariIni && $absenHariIni->scan_masuk) {
            return redirect()->route('absen.index')
                ->with('info', 'Anda sudah absen masuk hari ini');
        }

        // WFO + di kantor = langsung proses absen tanpa form
        if ($isWfo && $isInsideOffice) {
            return $this->processDirectAbsenMasuk($user, $jadwal);
        }

        return view('absen.formulir', [
            'user' => $user,
            'info' => $info,
            'jadwal' => $jadwal,
            'formType' => $formType,
            'statusOptions' => $statusOptions,
            'tipe' => $tipe,
            'absenHariIni' => $absenHariIni,
            'needsApproval' => false,
        ]);
    }

    /**
     * Proses absen masuk langsung (WFO + di kantor)
     * Tanpa form, tanpa approval
     */
    private function processDirectAbsenMasuk($user, $jadwal)
    {
        $today = now()->format('Y-m-d');
        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Cek apakah sudah ada absen hari ini
        $existingAbsen = DB::table('absen')
            ->where('nip', $user->nip)
            ->where('tanggal', $today)
            ->first();

        if ($existingAbsen && $existingAbsen->scan_masuk) {
            return redirect()->route('absen.index')
                ->with('info', 'Anda sudah absen masuk hari ini');
        }

        try {
            // Hitung minggu, bulan, tahun
            $tanggalObj = now();
            $minggu = (int) ceil($tanggalObj->day / 7);
            $bulan = (int) $tanggalObj->month;
            $tahun = (int) $tanggalObj->year;

            // Get biro name
            $biroName = DB::table('biro')
                ->where('id', $user->biro_id)
                ->value('biro_name');

            // Data absen
            $absenData = [
                'scan_masuk' => $jamSekarang,
                'scan_masuk_awal' => $jamSekarang,
                'status_izin' => 'hadir', // Status hadir langsung
                'ip' => $this->locationService->getClientIp(),
            ];

            if ($existingAbsen) {
                // Update existing record
                DB::table('absen')
                    ->where('id', $existingAbsen->id)
                    ->update($absenData);
            } else {
                // Buat record baru
                $absenData = array_merge($absenData, [
                    'nip' => $user->nip,
                    'tanggal' => $today,
                    'biro_name' => $biroName,
                    'minggu' => $minggu,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]);

                DB::table('absen')->insert($absenData);
            }

            return redirect()->route('absen.index')
                ->with('success', 'Absen masuk berhasil! Tercatat pada jam ' . $jamSekarang);
        } catch (\Exception $e) {
            return redirect()->route('absen.index')
                ->with('error', 'Gagal menyimpan absen: ' . $e->getMessage());
        }
    }

    /**
     * Simpan absen masuk
     */
    public function storeAbsenMasuk(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Validasi dasar
        $rules = [
            'status' => 'required|string|in:dinas,sakit_izin,wfa',
        ];

        // Validasi tambahan untuk WFA - tidak perlu tanggal
        if ($request->status === 'wfa') {
            $rules['wfa_lokasi'] = 'required|in:1,2'; // 1 = di rumah, 2 = di luar rumah
            $rules['wfa_alasan'] = 'required|string';

            // Jika di luar rumah, wfa_detail wajib
            if ($request->wfa_lokasi === '2') {
                $rules['wfa_detail'] = 'required|string';
            }
        }

        // Validasi untuk sakit_izin dan dinas - perlu tanggal dan alasan
        if ($request->status === 'sakit_izin' || $request->status === 'dinas') {
            $rules['tanggal_from'] = 'required|date|after_or_equal:today';
            $rules['tanggal_to'] = 'required|date|after_or_equal:tanggal_from';
            $rules['alasan'] = 'required|string';
        }

        $request->validate($rules);

        // Cek apakah sudah ada absen hari ini dengan scan_masuk
        $existingAbsen = DB::table('absen')
            ->where('nip', $user->nip)
            ->where('tanggal', $today)
            ->first();

        if ($existingAbsen && $existingAbsen->scan_masuk) {
            return redirect()->route('absen.index')
                ->with('info', 'Anda sudah absen masuk hari ini');
        }

        // Get jadwal
        $jadwal = $this->getJadwalHariIni($user);

        // ============ CASE: WFA langsung (tanpa approval) ============
        if ($request->status === 'wfa') {
            return $this->processDirectWfaAbsen($user, $jadwal, $request, $existingAbsen);
        }

        // ============ CASE: Dinas / Sakit-Izin (perlu approval) ============
        return $this->processIzinAbsen($user, $jadwal, $request, $existingAbsen);
    }

    /**
     * Proses WFA langsung tanpa approval
     */
    private function processDirectWfaAbsen($user, $jadwal, $request, $existingAbsen)
    {
        $today = now()->format('Y-m-d');
        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        try {
            // Hitung minggu, bulan, tahun
            $tanggalObj = now();
            $minggu = (int) ceil($tanggalObj->day / 7);
            $bulan = (int) $tanggalObj->month;
            $tahun = (int) $tanggalObj->year;

            // Get biro name
            $biroName = DB::table('biro')
                ->where('id', $user->biro_id)
                ->value('biro_name');

            // Data absen - langsung tercatat
            $absenData = [
                'scan_masuk' => $jamSekarang,
                'scan_masuk_awal' => $jamSekarang,
                'status_izin' => 'wfh',
                'wfa' => (int) $request->wfa_lokasi,
                'wfa_detail' => $request->wfa_lokasi === '2' ? $request->wfa_detail : null,
                'wfa_alasan' => $request->wfa_alasan,
                'ip' => $this->locationService->getClientIp(),
            ];

            if ($existingAbsen) {
                DB::table('absen')
                    ->where('id', $existingAbsen->id)
                    ->update($absenData);
            } else {
                $absenData = array_merge($absenData, [
                    'nip' => $user->nip,
                    'tanggal' => $today,
                    'biro_name' => $biroName,
                    'minggu' => $minggu,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]);

                DB::table('absen')->insert($absenData);
            }

            return redirect()->route('absen.index')
                ->with('success', 'Absen WFA berhasil! Tercatat pada jam ' . $jamSekarang);
        } catch (\Exception $e) {
            return redirect()->route('absen.index')
                ->with('error', 'Gagal menyimpan absen: ' . $e->getMessage());
        }
    }

    /**
     * Proses Dinas / Sakit-Izin (perlu approval, masuk formulir_izin)
     */
    private function processIzinAbsen($user, $jadwal, $request, $existingAbsen)
    {
        $today = now()->format('Y-m-d');
        $tanggalFrom = $request->tanggal_from;
        $tanggalTo = $request->tanggal_to;

        try {
            DB::beginTransaction();

            // Convert sakit_izin ke izin untuk database
            $dbStatus = $request->status === 'sakit_izin' ? 'izin' : $request->status;

            // Simpan ke formulir_izin
            $formulirId = DB::table('formulir_izin')->insertGetId([
                'nip' => $user->nip,
                'status' => $dbStatus,
                'from' => $tanggalFrom,
                'to' => $tanggalTo,
                'alasan' => $request->alasan,
                'tanggal_input' => $today,
                'timestamp_pengajuan' => now(),
            ]);

            DB::commit();

            // Kirim WA approval ke atasan (jika punya atasan)
            if ($user->nip_atasan) {
                $token = ApprovalController::createIzinApprovalToken(
                    $formulirId,
                    $user->nip,
                    $user->nip_atasan
                );

                $waService = new WhatsAppNotificationService();
                $waService->sendApprovalIzinToAtasan([
                    'token' => $token,
                    'nip_atasan' => $user->nip_atasan,
                    'nama_pengaju' => $user->nama,
                    'status' => $request->status,
                    'from' => $tanggalFrom,
                    'to' => $tanggalTo,
                    'alasan' => $request->alasan,
                    'expired_at' => now()->setTime(23, 59, 59),
                ]);
            }

            $statusLabel = $request->status === 'dinas' ? 'Dinas' : 'Sakit/Izin';
            return redirect()->route('absen.index')
                ->with('success', 'Pengajuan ' . $statusLabel . ' berhasil dikirim. Menunggu approval dari atasan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Simpan absen pulang
     */
    public function storeAbsenPulang(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Cek apakah sudah absen masuk
        $absenHariIni = DB::table('absen')
            ->where('nip', $user->nip)
            ->where('tanggal', $today)
            ->first();

        if (!$absenHariIni || !$absenHariIni->scan_masuk) {
            return redirect()->route('absen.index')
                ->with('error', 'Anda harus absen masuk terlebih dahulu sebelum absen pulang');
        }

        // Cek jadwal untuk determine flow
        $jadwal = $this->getJadwalHariIni($user);
        $isWfo = $jadwal['is_wfo'];
        $needsApproval = $this->locationService->needsPulangApproval($isWfo);

        try {
            if ($needsApproval) {
                // WFO tapi di luar kantor -> masuk ke jam_temp, perlu approval
                $request->validate([
                    'alasan_pulang' => 'required|string|max:500',
                ]);

                DB::table('absen')
                    ->where('id', $absenHariIni->id)
                    ->update([
                        'jam_temp' => $jamSekarang,
                        'alasan_pulang' => $request->alasan_pulang,
                        'ip' => $this->locationService->getClientIp(),
                    ]);

                // Kirim WA approval ke atasan (jika punya atasan)
                if ($user->nip_atasan) {
                    $token = ApprovalController::createPulangApprovalToken(
                        $absenHariIni->id,
                        $user->nip,
                        $user->nip_atasan
                    );

                    $waService = new WhatsAppNotificationService();
                    $waService->sendApprovalPulangToAtasan([
                        'token' => $token,
                        'nip_atasan' => $user->nip_atasan,
                        'nama_pengaju' => $user->nama,
                        'tanggal' => $today,
                        'jam_pulang' => $jamSekarang,
                        'alasan' => $request->alasan_pulang,
                        'expired_at' => now()->setTime(23, 59, 59),
                    ]);
                }

                return redirect()->route('absen.index')
                    ->with('success', 'Absen pulang telah diajukan pada jam ' . $jamSekarang . '. Menunggu approval dari atasan.');
            } else {
                // WFA atau WFO di kantor -> langsung ke scan_pulang
                DB::table('absen')
                    ->where('id', $absenHariIni->id)
                    ->update([
                        'scan_pulang' => $jamSekarang,
                        'ip' => $this->locationService->getClientIp(),
                    ]);

                return redirect()->route('absen.index')
                    ->with('success', 'Absen pulang berhasil disimpan pada jam ' . $jamSekarang);
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan absen pulang: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Helper: Dapatkan jadwal WFO/WFA hari ini dari pengajuan_wao_detail
     */
    private function getJadwalHariIni($user): array
    {
        $today = now();
        $dayOfWeek = strtolower($today->format('l')); // monday, tuesday, etc

        // Map ke nama kolom di database
        $dayMap = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => null,
            'sunday' => null,
        ];

        $hariKolom = $dayMap[$dayOfWeek] ?? null;

        // Weekend tidak ada jadwal
        if (!$hariKolom) {
            return [
                'ada_jadwal' => false,
                'is_wfo' => false,
                'is_wfa' => false,
                'hari' => $dayOfWeek,
                'hari_indo' => $this->getHariIndo($dayOfWeek),
                'pesan' => 'Hari libur (weekend)',
            ];
        }

        // Cari pengajuan yang aktif untuk minggu ini
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.biro_id', $user->biro_id)
            ->whereDate('kk.tgl_awal', '<=', $today->format('Y-m-d'))
            ->whereDate('kk.tgl_akhir', '>=', $today->format('Y-m-d'))
            ->select('pw.id', 'pw.status')
            ->first();

        if (!$pengajuan) {
            return [
                'ada_jadwal' => false,
                'is_wfo' => false,
                'is_wfa' => false,
                'hari' => $hariKolom,
                'hari_indo' => $this->getHariIndo($dayOfWeek),
                'pesan' => 'Belum ada jadwal pengajuan untuk minggu ini',
            ];
        }

        // Cek apakah status pengajuan sudah final (hanya final yang bisa absen)
        // draft = masih diedit, final = sudah disubmit kepala biro
        if (strtolower($pengajuan->status) !== 'final') {
            return [
                'ada_jadwal' => false,
                'is_wfo' => false,
                'is_wfa' => false,
                'hari' => $hariKolom,
                'hari_indo' => $this->getHariIndo($dayOfWeek),
                'status_pengajuan' => $pengajuan->status,
                'pesan' => 'Jadwal pengajuan WFO masih dalam status ' . strtoupper($pengajuan->status) . '. Silakan tunggu hingga kepala biro menyelesaikan pengajuan.',
            ];
        }

        // Cek detail jadwal untuk user ini
        $detail = DB::table('pengajuan_wao_detail')
            ->where('pengajuan_id', $pengajuan->id)
            ->where('nip', $user->nip)
            ->first();

        if (!$detail) {
            return [
                'ada_jadwal' => false,
                'is_wfo' => false,
                'is_wfa' => false,
                'hari' => $hariKolom,
                'hari_indo' => $this->getHariIndo($dayOfWeek),
                'pesan' => 'Data pegawai tidak ditemukan dalam jadwal',
            ];
        }

        // Cek nilai jadwal hari ini (true = WFO, false = WFA, null = libur)
        $nilaiHari = $detail->$hariKolom;

        // Null berarti hari libur
        if ($nilaiHari === null) {
            return [
                'ada_jadwal' => true,
                'is_wfo' => false,
                'is_wfa' => false,
                'is_libur' => true,
                'hari' => $hariKolom,
                'hari_indo' => $this->getHariIndo($dayOfWeek),
                'pesan' => 'Hari libur',
            ];
        }

        $isWfo = (bool) $nilaiHari;

        return [
            'ada_jadwal' => true,
            'is_wfo' => $isWfo,
            'is_wfa' => !$isWfo,
            'is_libur' => false,
            'hari' => $hariKolom,
            'hari_indo' => $this->getHariIndo($dayOfWeek),
            'status_jadwal' => $isWfo ? 'WFO' : 'WFA',
            'status_pengajuan' => $pengajuan->status, // Tambahkan status pengajuan untuk debug
            'pesan' => $isWfo ? 'Jadwal hari ini: WFO (Work From Office)' : 'Jadwal hari ini: WFA (Work From Anywhere)',
        ];
    }

    /**
     * Helper: Konversi nama hari ke bahasa Indonesia
     */
    private function getHariIndo(string $day): string
    {
        $map = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return $map[$day] ?? $day;
    }

    /**
     * Helper: Validasi format tanggal dd-mm-yyyy
     */
    private function isValidTanggalFormat($tanggal): bool
    {
        if (!$tanggal || !is_string($tanggal)) {
            return false;
        }

        // Cek format dengan regex dd-mm-yyyy
        if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggal)) {
            return false;
        }

        // Coba parse untuk validasi tanggal valid
        try {
            $parsed = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggal);
            return $parsed && $parsed->format('d-m-Y') === $tanggal;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Helper: Dapatkan jadwal WFO/WFA berdasarkan NIP dan tanggal (untuk standalone absen)
     */
    private function getJadwalHariIniByNip($nip, $tanggalObj): array
    {
        $dayOfWeek = strtolower($tanggalObj->format('l'));

        $dayMap = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => null,
            'sunday' => null,
        ];

        $hariKolom = $dayMap[$dayOfWeek] ?? null;

        // Weekend
        if (!$hariKolom) {
            return ['ada_jadwal' => false, 'is_wfo' => false];
        }

        // Cari user untuk dapat biro_id
        $user = DB::table('users')->where('nip', $nip)->first();
        if (!$user) {
            return ['ada_jadwal' => false, 'is_wfo' => false];
        }

        // Cari pengajuan aktif
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.biro_id', $user->biro_id)
            ->whereDate('kk.tgl_awal', '<=', $tanggalObj->format('Y-m-d'))
            ->whereDate('kk.tgl_akhir', '>=', $tanggalObj->format('Y-m-d'))
            ->where('pw.status', 'final')
            ->select('pw.id')
            ->first();

        if (!$pengajuan) {
            return ['ada_jadwal' => false, 'is_wfo' => false];
        }

        // Cek detail jadwal
        $detail = DB::table('pengajuan_wao_detail')
            ->where('pengajuan_id', $pengajuan->id)
            ->where('nip', $nip)
            ->first();

        if (!$detail) {
            return ['ada_jadwal' => false, 'is_wfo' => false];
        }

        $nilaiHari = $detail->$hariKolom;
        if ($nilaiHari === null) {
            return ['ada_jadwal' => true, 'is_wfo' => false, 'is_libur' => true];
        }

        return [
            'ada_jadwal' => true,
            'is_wfo' => (bool) $nilaiHari,
            'is_wfa' => !(bool) $nilaiHari,
        ];
    }

    // ============================================================
    // STANDALONE ABSEN (untuk absenin orang lain, tanpa auth)
    // ============================================================

    /**
     * Route 1: /absensi/mywgabsen/{nip}/{tanggal?}
     * Untuk WFO + di kantor - semuanya otomatis
     * Jika jadwal WFA -> redirect ke route 2
     * Jika tanggal tidak valid -> redirect ke tanggal hari ini
     */
    public function absenOtomatis($nip, $tanggal = null)
    {
        // Selalu gunakan tanggal hari ini (abaikan parameter tanggal)
        $tanggalHariIni = now()->format('d-m-Y');
        
        // Jika tanggal tidak sesuai hari ini, redirect ke tanggal hari ini
        if (!$tanggal || $tanggal !== $tanggalHariIni) {
            return redirect()->route('absen.otomatis', ['nip' => $nip, 'tanggal' => $tanggalHariIni]);
        }

        // Parse tanggal format dd-mm-yyyy
        $tanggalObj = now();
        $tanggalDb = $tanggalObj->format('Y-m-d');

        // Cari user berdasarkan NIP
        $user = DB::table('users')->where('nip', $nip)->first();
        if (!$user) {
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'User dengan NIP ' . $nip . ' tidak ditemukan',
            ]);
        }

        // Cek jadwal WFO/WFA - jika WFA redirect ke form-harian
        $jadwal = $this->getJadwalHariIniByNip($nip, $tanggalObj);
        if ($jadwal['ada_jadwal'] && !$jadwal['is_wfo']) {
            // Jadwal WFA -> redirect ke route 2
            return redirect()->route('absen.form.standalone', ['nip' => $nip, 'tanggal' => $tanggal]);
        }

        // Cek lokasi - jika WFO tapi di luar kantor, redirect ke form-harian
        if (!$this->locationService->isInsideOffice()) {
            return redirect()->route('absen.form.standalone', ['nip' => $nip, 'tanggal' => $tanggal]);
        }

        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Cek absen hari ini
        $absenHariIni = DB::table('absen')
            ->where('nip', $nip)
            ->where('tanggal', $tanggalDb)
            ->first();

        // CASE 1: Sudah lengkap (scan_masuk + scan_pulang)
        if ($absenHariIni && $absenHariIni->scan_masuk && $absenHariIni->scan_pulang) {
            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Tidak perlu absen lagi untuk tanggal ini.',
                'user' => $user,
                'type' => 'already_complete',
                'tanggal_formatted' => $tanggalObj->format('d M Y'),
            ]);
        }

        // CASE 2: Sudah absen masuk, belum pulang -> otomatis absen pulang
        if ($absenHariIni && $absenHariIni->scan_masuk && !$absenHariIni->scan_pulang) {
            DB::table('absen')
                ->where('id', $absenHariIni->id)
                ->update([
                    'scan_pulang' => $jamSekarang,
                    'ip' => $this->locationService->getClientIp(),
                ]);

            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Absen pulang berhasil tercatat pada jam ' . $jamSekarang,
                'user' => $user,
                'type' => 'pulang',
                'jam' => $jamSekarang,
            ]);
        }

        // CASE 3: Belum absen masuk -> otomatis absen masuk
        try {
            $minggu = (int) ceil($tanggalObj->day / 7);
            $bulan = (int) $tanggalObj->month;
            $tahun = (int) $tanggalObj->year;

            $biroName = DB::table('biro')
                ->where('id', $user->biro_id)
                ->value('biro_name');

            $absenData = [
                'nip' => $nip,
                'tanggal' => $tanggalDb,
                'scan_masuk' => $jamSekarang,
                'scan_masuk_awal' => $jamSekarang,
                'status_izin' => 'hadir',
                'biro_name' => $biroName,
                'minggu' => $minggu,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'ip' => $this->locationService->getClientIp(),
            ];

            if ($absenHariIni) {
                DB::table('absen')
                    ->where('id', $absenHariIni->id)
                    ->update($absenData);
            } else {
                DB::table('absen')->insert($absenData);
            }

            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Absen masuk berhasil tercatat pada jam ' . $jamSekarang,
                'user' => $user,
                'type' => 'masuk',
                'jam' => $jamSekarang,
            ]);
        } catch (\Exception $e) {
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'Gagal menyimpan absen: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Route 2: /absensi/form-harian/{nip}/{tanggal?}
     * Untuk WFO di luar / WFA - form untuk masuk, otomatis untuk pulang
     * Jika tanggal tidak valid -> redirect ke tanggal hari ini
     * Jika WFO + di kantor -> redirect ke route 1 (absen otomatis)
     */
    public function absenFormStandalone($nip, $tanggal = null)
    {
        // Selalu gunakan tanggal hari ini (abaikan parameter tanggal)
        $tanggalHariIni = now()->format('d-m-Y');
        
        // Jika tanggal tidak sesuai hari ini, redirect ke tanggal hari ini
        if (!$tanggal || $tanggal !== $tanggalHariIni) {
            return redirect()->route('absen.form.standalone', ['nip' => $nip, 'tanggal' => $tanggalHariIni]);
        }

        // Parse tanggal format dd-mm-yyyy
        $tanggalObj = now();
        $tanggalDb = $tanggalObj->format('Y-m-d');

        // Cari user berdasarkan NIP
        $user = DB::table('users')->where('nip', $nip)->first();
        if (!$user) {
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'User dengan NIP ' . $nip . ' tidak ditemukan',
            ]);
        }

        // Cek jadwal WFO/WFA dan lokasi - jika WFO + di kantor, redirect ke absen otomatis
        $jadwal = $this->getJadwalHariIniByNip($nip, $tanggalObj);
        $isWfo = !$jadwal['ada_jadwal'] || $jadwal['is_wfo']; // Default WFO jika tidak ada jadwal
        $isInsideOffice = $this->locationService->isInsideOffice();
        
        if ($isWfo && $isInsideOffice) {
            // WFO + di kantor -> redirect ke route absen otomatis
            return redirect()->route('absen.otomatis', ['nip' => $nip, 'tanggal' => $tanggal]);
        }

        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Cek absen hari ini
        $absenHariIni = DB::table('absen')
            ->where('nip', $nip)
            ->where('tanggal', $tanggalDb)
            ->first();

        // CASE 1: Cek apakah ada izin yang sudah di-approve (dinas/izin)
        if ($absenHariIni && in_array($absenHariIni->status_izin, ['dinas', 'izin'])) {
            $statusLabel = $absenHariIni->status_izin === 'dinas' ? 'Dinas' : 'Sakit/Izin';
            
            // Ambil tanggal from-to dari formulir_izin
            $formulirIzin = DB::table('formulir_izin')
                ->where('nip', $nip)
                ->where('is_approval', 1)
                ->whereDate('from', '<=', $tanggalDb)
                ->whereDate('to', '>=', $tanggalDb)
                ->first();
            
            $tanggalFrom = $formulirIzin ? \Carbon\Carbon::parse($formulirIzin->from)->format('d M Y') : $tanggalObj->format('d M Y');
            $tanggalTo = $formulirIzin ? \Carbon\Carbon::parse($formulirIzin->to)->format('d M Y') : $tanggalObj->format('d M Y');
            
            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Anda dalam status ' . $statusLabel . '. Tidak perlu absen untuk periode ini.',
                'user' => $user,
                'type' => 'already_izin',
                'status' => $statusLabel,
                'tanggal_from' => $tanggalFrom,
                'tanggal_to' => $tanggalTo,
            ]);
        }

        // CASE 2: Sudah lengkap (scan_masuk + scan_pulang) - absen biasa
        if ($absenHariIni && $absenHariIni->scan_masuk && $absenHariIni->scan_pulang) {
            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Tidak perlu absen lagi untuk tanggal ini.',
                'user' => $user,
                'type' => 'already_complete',
                'tanggal_formatted' => $tanggalObj->format('d M Y'),
            ]);
        }

        // CASE 3: Cek apakah ada pending approval di approval_tokens
        $pendingApproval = DB::table('approval_tokens')
            ->where('nip_pengaju', $nip)
            ->where('type', 'izin')
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        if ($pendingApproval) {
            // Ambil data dari formulir_izin
            $formulirIzin = DB::table('formulir_izin')
                ->where('id', $pendingApproval->reference_id)
                ->first();

            $statusLabel = $formulirIzin && $formulirIzin->status === 'dinas' ? 'Dinas' : 'Sakit/Izin';
            $expiredAt = \Carbon\Carbon::parse($pendingApproval->expired_at)->format('d M Y H:i');
            
            // Ambil nama atasan
            $atasan = DB::table('users')->where('nip', $pendingApproval->nip_atasan)->first();
            $namaAtasan = $atasan ? strtoupper($atasan->nama) : 'ATASAN';

            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'Anda sudah mengajukan ' . $statusLabel . ', menunggu approval dari Bapak/Ibu',
                'nama_atasan' => $namaAtasan,
                'sub_message' => 'Anda dapat mengajukan kembali setelah ' . $expiredAt,
                'user' => $user,
                'type' => 'pending_approval',
            ]);
        }

        // CASE 4: Sudah absen masuk (WFA/wfh), belum pulang -> otomatis absen pulang
        if ($absenHariIni && $absenHariIni->scan_masuk && !$absenHariIni->scan_pulang) {
            if ($absenHariIni->status_izin === 'wfh') {
                DB::table('absen')
                    ->where('id', $absenHariIni->id)
                    ->update([
                        'scan_pulang' => $jamSekarang,
                        'ip' => $this->locationService->getClientIp(),
                    ]);

                return view('absen.standalone-result', [
                    'success' => true,
                    'message' => 'Absen pulang WFA berhasil tercatat pada jam ' . $jamSekarang,
                    'user' => $user,
                    'type' => 'pulang',
                    'jam' => $jamSekarang,
                ]);
            }
        }

        // CASE 5: Belum absen masuk, tidak ada pending -> tampilkan form izin
        // Cek jadwal untuk menentukan apakah tampilkan opsi WFA
        $jadwal = $this->getJadwalHariIniByNip($nip, $tanggalObj);
        $isJadwalWfa = $jadwal['ada_jadwal'] && !$jadwal['is_wfo'];

        $statusOptions = [];

        // Hanya tampilkan WFA jika jadwal memang WFA
        if ($isJadwalWfa) {
            $statusOptions[] = [
                'value' => 'wfa',
                'label' => 'WFA',
                'icon' => '',
                'desc' => 'Work From Anywhere'
            ];
        }

        // Dinas dan Sakit/Izin selalu ditampilkan
        $statusOptions[] = [
            'value' => 'dinas',
            'label' => 'Dinas',
            'icon' => '',
            'desc' => 'Perjalanan dinas atau tugas luar kantor'
        ];
        $statusOptions[] = [
            'value' => 'sakit_izin',
            'label' => 'Sakit/Izin',
            'icon' => '',
            'desc' => 'Tidak hadir karena sakit atau keperluan lain'
        ];

        return view('absen.standalone-form', [
            'user' => $user,
            'nip' => $nip,
            'tanggal' => $tanggal,
            'tanggalFormatted' => $tanggalObj->format('d M Y'),
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Process form submission dari standalone form
     */
    public function storeAbsenStandalone(Request $request, $nip, $tanggal)
    {
        // Parse tanggal format dd-mm-yyyy
        try {
            $tanggalObj = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggal);
            $tanggalDb = $tanggalObj->format('Y-m-d');
        } catch (\Exception $e) {
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'Format tanggal tidak valid',
            ]);
        }

        // Cari user berdasarkan NIP
        $user = DB::table('users')->where('nip', $nip)->first();
        if (!$user) {
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'User tidak ditemukan',
            ]);
        }

        $jamSekarang = $this->locationService->getCurrentTime()['formatted'];

        // Validasi
        $rules = [
            'status' => 'required|string|in:dinas,sakit_izin,wfa',
        ];

        if ($request->status === 'wfa') {
            $rules['wfa_lokasi'] = 'required|in:1,2';
            $rules['wfa_alasan'] = 'required|string';
            if ($request->wfa_lokasi === '2') {
                $rules['wfa_detail'] = 'required|string';
            }
        }

        if ($request->status === 'sakit_izin' || $request->status === 'dinas') {
            $rules['tanggal_from'] = 'required|date';
            $rules['tanggal_to'] = 'required|date|after_or_equal:tanggal_from';
            $rules['alasan'] = 'required|string';
        }

        $request->validate($rules);

        // CASE: WFA -> langsung proses
        if ($request->status === 'wfa') {
            try {
                $minggu = (int) ceil($tanggalObj->day / 7);
                $bulan = (int) $tanggalObj->month;
                $tahun = (int) $tanggalObj->year;

                $biroName = DB::table('biro')
                    ->where('id', $user->biro_id)
                    ->value('biro_name');

                $existingAbsen = DB::table('absen')
                    ->where('nip', $nip)
                    ->where('tanggal', $tanggalDb)
                    ->first();

                $absenData = [
                    'scan_masuk' => $jamSekarang,
                    'scan_masuk_awal' => $jamSekarang,
                    'status_izin' => 'wfh',
                    'wfa' => (int) $request->wfa_lokasi,
                    'wfa_detail' => $request->wfa_lokasi === '2' ? $request->wfa_detail : null,
                    'wfa_alasan' => $request->wfa_alasan,
                    'ip' => $this->locationService->getClientIp(),
                ];

                if ($existingAbsen) {
                    DB::table('absen')->where('id', $existingAbsen->id)->update($absenData);
                } else {
                    $absenData = array_merge($absenData, [
                        'nip' => $nip,
                        'tanggal' => $tanggalDb,
                        'biro_name' => $biroName,
                        'minggu' => $minggu,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ]);
                    DB::table('absen')->insert($absenData);
                }

                return view('absen.standalone-result', [
                    'success' => true,
                    'message' => 'Absen WFA berhasil tercatat pada jam ' . $jamSekarang,
                    'user' => $user,
                    'type' => 'masuk',
                    'jam' => $jamSekarang,
                ]);
            } catch (\Exception $e) {
                return view('absen.standalone-result', [
                    'success' => false,
                    'message' => 'Gagal menyimpan absen: ' . $e->getMessage(),
                ]);
            }
        }

        // CASE: Dinas / Sakit-Izin -> simpan ke formulir_izin, kirim approval
        try {
            DB::beginTransaction();

            // Convert sakit_izin ke izin untuk database
            $dbStatus = $request->status === 'sakit_izin' ? 'izin' : $request->status;

            $formulirId = DB::table('formulir_izin')->insertGetId([
                'nip' => $nip,
                'status' => $dbStatus,
                'from' => $request->tanggal_from,
                'to' => $request->tanggal_to,
                'alasan' => $request->alasan,
                'tanggal_input' => $tanggalDb,
                'timestamp_pengajuan' => now(),
            ]);

            DB::commit();

            // Kirim WA approval ke atasan
            if ($user->nip_atasan) {
                $token = ApprovalController::createIzinApprovalToken(
                    $formulirId,
                    $nip,
                    $user->nip_atasan
                );

                $waService = new WhatsAppNotificationService();
                $waService->sendApprovalIzinToAtasan([
                    'token' => $token,
                    'nip_atasan' => $user->nip_atasan,
                    'nama_pengaju' => $user->nama,
                    'status' => $request->status,
                    'from' => $request->tanggal_from,
                    'to' => $request->tanggal_to,
                    'alasan' => $request->alasan,
                    'expired_at' => now()->setTime(23, 59, 59),
                ]);
            }

            $statusLabel = $request->status === 'dinas' ? 'Dinas' : 'Sakit/Izin';
            
            // Ambil nama atasan
            $atasan = DB::table('users')->where('nip', $user->nip_atasan)->first();
            $namaAtasan = $atasan ? strtoupper($atasan->nama) : 'ATASAN';
            
            return view('absen.standalone-result', [
                'success' => true,
                'message' => 'Pengajuan ' . $statusLabel . ' berhasil dikirim, menunggu approval dari Bapak/Ibu',
                'nama_atasan' => $namaAtasan,
                'user' => $user,
                'type' => 'pending_approval',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return view('absen.standalone-result', [
                'success' => false,
                'message' => 'Gagal menyimpan pengajuan: ' . $e->getMessage(),
            ]);
        }
    }
}
