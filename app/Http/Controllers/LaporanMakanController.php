<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LaporanMakanController extends Controller
{
    /**
     * Cek apakah user adalah HC (Human Capital Division - by biro_name)
     */
    private function isHC(): bool
    {
        $user = Auth::user();
        $biroName = DB::table('biro')->where('id', $user->biro_id)->value('biro_name');
        return $biroName && stripos($biroName, 'Human Capital') !== false;
    }

    /**
     * Cek apakah user adalah admin
     */
    private function isAdmin(): bool
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        return strtoupper($role ?? '') === 'ADMIN';
    }

    /**
     * Check if user is admin only (HC tidak bisa akses)
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display laporan uang makan page
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        // Get all pegawai for dropdown
        $pegawaiList = DB::table('users')
            ->select('nip', 'nama', 'biro_id')
            ->orderBy('nama')
            ->get();

        // Get all biro for dropdown (exclude proyek)
        $biroList = DB::table('biro')
            ->where('is_proyek', false)
            ->select('id', 'biro_name')
            ->orderBy('biro_name')
            ->get();

        // Get uang makan value
        $uangMakan = DB::table('master_uang_makan')->value('uang') ?? 35000;

        return view('laporan-makan.index', [
            'pegawaiList' => $pegawaiList,
            'biroList' => $biroList,
            'uangMakan' => $uangMakan,
        ]);
    }

    /**
     * Fetch laporan data via AJAX
     */
    public function getData(Request $request)
    {
        $this->ensureAdmin();

        $nip = $request->input('nip');
        $biroId = $request->input('biro_id');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');
        $search = $request->input('search');

        // Validate date range
        if (!$tanggalFrom || !$tanggalTo) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal harus diisi'
            ]);
        }

        // Build query for users
        $query = DB::table('users as u')
            ->leftJoin('biro as b', 'u.biro_id', '=', 'b.id')
            ->select(
                'u.nip',
                'u.nama',
                'u.biro_id',
                'b.biro_name',
                'u.jabatan as jabatan_name'
            );

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama', 'ilike', "%{$search}%")
                    ->orWhere('u.nip', 'ilike', "%{$search}%");
            });
        } else {
            if ($nip) {
                $query->where('u.nip', $nip);
            }
            if ($biroId) {
                $query->where('u.biro_id', $biroId);
            }
        }

        $users = $query->orderBy('u.nama')->get();

        // Get working dates (from kalender_kerja_v2, excluding kalender_libur)
        $workingDates = $this->getWorkingDates($tanggalFrom, $tanggalTo);
        $jumlahHariKerja = count($workingDates);

        // Get uang makan value
        $uangMakan = DB::table('master_uang_makan')->value('uang') ?? 35000;

        // Pre-fetch all absen data for all users in date range (batch query)
        $nips = $users->pluck('nip')->toArray();
        $allAbsen = DB::table('absen')
            ->whereIn('nip', $nips)
            ->whereBetween('tanggal', [$tanggalFrom, $tanggalTo])
            ->get()
            ->groupBy('nip');

        // Build report data for each user
        $data = [];
        $grandTotal = 0;

        foreach ($users as $user) {
            $userAbsen = $allAbsen[$user->nip] ?? collect();
            $reportData = $this->buildUserReportDataOptimized($user, $workingDates, $uangMakan, $userAbsen);
            $data[] = $reportData;
            $grandTotal += $reportData['total_uang_makan'];
        }

        // Format dates for header
        $fromDate = Carbon::parse($tanggalFrom);
        $toDate = Carbon::parse($tanggalTo);
        
        // Nama bulan dalam bahasa Indonesia
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulan = $bulanIndonesia[$fromDate->month];
        $tahun = $fromDate->year;
        
        $periodTitle = "LAPORAN KEBUTUHAN UANG MAKAN PER PEGAWAI PERIODE {$bulan} {$tahun} Dari Tanggal {$tanggalFrom} Ke Tanggal {$tanggalTo}";

        return response()->json([
            'success' => true,
            'data' => $data,
            'jumlah_hari_kerja' => $jumlahHariKerja,
            'uang_makan_per_hari' => $uangMakan,
            'grand_total' => $grandTotal,
            'period_title' => $periodTitle,
        ]);
    }

    /**
     * Get working dates from kalender_kerja_v2 (excluding kalender_libur)
     */
    private function getWorkingDates($from, $to)
    {
        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        // Get all dates in kalender_kerja_v2 within the range
        $kalenderDates = [];
        $kalenders = DB::table('kalender_kerja_v2')
            ->where('tgl_awal', '<=', $endDate->format('Y-m-d'))
            ->where('tgl_akhir', '>=', $startDate->format('Y-m-d'))
            ->get();

        foreach ($kalenders as $kalender) {
            $kStart = Carbon::parse($kalender->tgl_awal);
            $kEnd = Carbon::parse($kalender->tgl_akhir);

            // Adjust to filter range
            if ($kStart->lt($startDate)) $kStart = $startDate->copy();
            if ($kEnd->gt($endDate)) $kEnd = $endDate->copy();

            $period = CarbonPeriod::create($kStart, $kEnd);
            foreach ($period as $date) {
                $kalenderDates[] = $date->format('Y-m-d');
            }
        }

        $kalenderDates = array_unique($kalenderDates);

        // Get libur dates
        $liburDates = DB::table('kalender_libur')
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Exclude libur dates
        $workingDates = array_diff($kalenderDates, $liburDates);
        sort($workingDates);

        return array_values($workingDates);
    }

    /**
     * Build report data for a single user
     */
    private function buildUserReportData($user, $workingDates, $uangMakan)
    {
        $jumlahHariKerja = count($workingDates);
        $jumlahHariWfo = 0;
        $jumlahHariWfa = 0;
        $jumlahAbsenWfo = 0;
        $jumlahAbsenWfa = 0;
        $absenDetails = [];

        foreach ($workingDates as $date) {
            // Get work status (WFO/WFA) from pengajuan_wao_detail
            $workStatus = $this->getWorkStatus($user->nip, $user->biro_id, $date);

            if (strtoupper($workStatus) === 'WFO') {
                $jumlahHariWfo++;
            } elseif (strtoupper($workStatus) === 'WFA') {
                $jumlahHariWfa++;
            }

            // Get absen record
            $absen = DB::table('absen')
                ->where('nip', $user->nip)
                ->where('tanggal', $date)
                ->first();

            if ($absen && $absen->scan_masuk) {
                $absenStatus = strtoupper($absen->status ?? $workStatus);

                if ($absenStatus === 'WFO' || $absenStatus === 'KANTOR' || $absenStatus === 'DINAS') {
                    $jumlahAbsenWfo++;
                } elseif ($absenStatus === 'WFA' || $absenStatus === 'RUMAH') {
                    $jumlahAbsenWfa++;
                } else {
                    // Default based on work status
                    if (strtoupper($workStatus) === 'WFO') {
                        $jumlahAbsenWfo++;
                    } else {
                        $jumlahAbsenWfa++;
                    }
                }

                $absenDetails[] = [
                    'tanggal' => $date,
                    'scan_masuk' => $absen->scan_masuk ? substr($absen->scan_masuk, 0, 5) : '-',
                    'scan_pulang' => $absen->scan_pulang ? substr($absen->scan_pulang, 0, 5) : '-',
                    'status' => strtolower($workStatus),
                    'lokasi_wfa' => $absen->lokasi_wfa ?? '-',
                    'alasan_lokasi' => $absen->alasan_lokasi ?? '-',
                ];
            } else {
                $absenDetails[] = [
                    'tanggal' => $date,
                    'scan_masuk' => '-',
                    'scan_pulang' => '-',
                    'status' => strtolower($workStatus),
                    'lokasi_wfa' => '-',
                    'alasan_lokasi' => '-',
                ];
            }
        }

        $jumlahAbsen = $jumlahAbsenWfo + $jumlahAbsenWfa;
        $persenAbsen = $jumlahHariKerja > 0 ? round(($jumlahAbsen / $jumlahHariKerja) * 100, 2) : 0;
        
        // Total uang makan = jumlah absen WFO * uang makan (WFA tidak dapat)
        $totalUangMakan = $jumlahAbsenWfo * $uangMakan;

        return [
            'nip' => $user->nip,
            'nama' => $user->nama,
            'biro_name' => $user->biro_name ?? '-',
            'jabatan_name' => $user->jabatan_name ?? '-',
            'jumlah_hari_kerja' => $jumlahHariKerja,
            'jumlah_hari_wfo' => $jumlahHariWfo,
            'jumlah_hari_wfa' => $jumlahHariWfa,
            'jumlah_absen_wfo' => $jumlahAbsenWfo,
            'jumlah_absen_wfa' => $jumlahAbsenWfa,
            'jumlah_absen' => $jumlahAbsen,
            'persen_absen' => $persenAbsen,
            'uang_makan_per_hari' => $uangMakan,
            'total_uang_makan' => $totalUangMakan,
            'absen_details' => $absenDetails,
        ];
    }

    /**
     * Build report data for a single user - OPTIMIZED VERSION
     * Uses pre-fetched absen data instead of querying for each date
     */
    private function buildUserReportDataOptimized($user, $workingDates, $uangMakan, $userAbsen)
    {
        $jumlahHariKerja = count($workingDates);
        $jumlahAbsenWfo = 0;
        $jumlahAbsenWfa = 0;
        $absenDetails = [];

        // Index absen by tanggal for quick lookup
        $absenByDate = [];
        foreach ($userAbsen as $absen) {
            $tanggal = $absen->tanggal instanceof \DateTime 
                ? $absen->tanggal->format('Y-m-d') 
                : (is_string($absen->tanggal) ? substr($absen->tanggal, 0, 10) : $absen->tanggal);
            $absenByDate[$tanggal] = $absen;
        }

        foreach ($workingDates as $date) {
            $absen = $absenByDate[$date] ?? null;

            if ($absen && $absen->scan_masuk) {
                // Cek apakah WFA atau WFO berdasarkan field wfa
                if ($absen->wfa) {
                    $jumlahAbsenWfa++;
                } else {
                    $jumlahAbsenWfo++;
                }

                $absenDetails[] = [
                    'tanggal' => $date,
                    'scan_masuk' => $absen->scan_masuk ? substr($absen->scan_masuk, 0, 5) : '-',
                    'scan_pulang' => $absen->scan_pulang ? substr($absen->scan_pulang, 0, 5) : '-',
                    'status' => $absen->wfa ? 'wfa' : 'wfo',
                    'lokasi_wfa' => $absen->lokasi_wfa ?? '-',
                    'alasan_lokasi' => $absen->alasan_lokasi ?? '-',
                ];
            } else {
                $absenDetails[] = [
                    'tanggal' => $date,
                    'scan_masuk' => '-',
                    'scan_pulang' => '-',
                    'status' => '-',
                    'lokasi_wfa' => '-',
                    'alasan_lokasi' => '-',
                ];
            }
        }

        $jumlahAbsen = $jumlahAbsenWfo + $jumlahAbsenWfa;
        $persenAbsen = $jumlahHariKerja > 0 ? round(($jumlahAbsen / $jumlahHariKerja) * 100, 2) : 0;
        
        // Total uang makan = jumlah absen WFO * uang makan (WFA tidak dapat)
        $totalUangMakan = $jumlahAbsenWfo * $uangMakan;

        return [
            'nip' => $user->nip,
            'nama' => $user->nama,
            'biro_name' => $user->biro_name ?? '-',
            'jabatan_name' => $user->jabatan_name ?? '-',
            'jumlah_hari_kerja' => $jumlahHariKerja,
            'jumlah_hari_wfo' => $jumlahHariKerja, // Default semua hari kerja dianggap WFO
            'jumlah_hari_wfa' => 0,
            'jumlah_absen_wfo' => $jumlahAbsenWfo,
            'jumlah_absen_wfa' => $jumlahAbsenWfa,
            'jumlah_absen' => $jumlahAbsen,
            'persen_absen' => $persenAbsen,
            'uang_makan_per_hari' => $uangMakan,
            'total_uang_makan' => $totalUangMakan,
            'absen_details' => $absenDetails,
        ];
    }

    /**
     * Get work status (WFO/WFA) for a specific date
     */
    private function getWorkStatus($nip, $biroId, $date)
    {
        $dateObj = Carbon::parse($date);

        // Find pengajuan for this biro and date
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.biro_id', $biroId)
            ->where('kk.tgl_awal', '<=', $dateObj->format('Y-m-d'))
            ->where('kk.tgl_akhir', '>=', $dateObj->format('Y-m-d'))
            ->first();

        if (!$pengajuan) {
            return '-';
        }

        // Get detail for this user and date directly from pengajuan_wao_detail_tanggal
        $detail = DB::table('pengajuan_wao_detail_tanggal')
            ->where('pengajuan_id', $pengajuan->id)
            ->where('nip', $nip)
            ->where('tanggal', $dateObj->format('Y-m-d'))
            ->first();

        if (!$detail) {
            return '-';
        }

        return strtoupper($detail->status ?? '-');
    }

    /**
     * Get detail absen for modal
     */
    public function getDetail(Request $request)
    {
        $this->ensureAdmin();

        $nip = $request->input('nip');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');

        if (!$nip || !$tanggalFrom || !$tanggalTo) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap']);
        }

        $user = DB::table('users')->where('nip', $nip)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan']);
        }

        $workingDates = $this->getWorkingDates($tanggalFrom, $tanggalTo);
        $details = [];

        foreach ($workingDates as $date) {
            $absen = DB::table('absen')
                ->where('nip', $nip)
                ->where('tanggal', $date)
                ->first();

            // Tentukan status berdasarkan logic:
            // 1. WFA → cek absen.wfa = true atau status_izin = 'wfh'
            // 2. Dinas → cek formulir_izin ada record dinas yang approved untuk tanggal ini
            // 3. WFO → sisanya (WFO di kantor)
            $status = '-';
            if ($absen && $absen->scan_masuk) {
                $statusIzin = strtolower($absen->status_izin ?? '');
                
                // Cek WFA dulu
                if ($absen->wfa || $statusIzin === 'wfh') {
                    $status = 'wfa';
                } 
                // Cek izin/sakit
                elseif ($statusIzin === 'izin') {
                    $status = 'izin';
                }
                // Cek apakah ada dinas di formulir_izin (WFO luar kantor)
                else {
                    $hasDinas = DB::table('formulir_izin')
                        ->where('nip', $nip)
                        ->where('status', 'dinas')
                        ->where('is_approval', 1)
                        ->whereDate('from', '<=', $date)
                        ->whereDate('to', '>=', $date)
                        ->exists();
                    
                    if ($hasDinas) {
                        $status = 'dinas';
                    } else {
                        $status = 'wfo'; // WFO di kantor
                    }
                }
            }

            // Tentukan lokasi_wfa dan alasan_lokasi berdasarkan kolom wfa
            // wfa = 1 → Di Rumah, tampilkan wfa_alasan di Lokasi WFA
            // wfa = 2 → Luar Rumah, tampilkan wfa_detail di Lokasi WFA, wfa_alasan di Alasan
            $lokasiWfa = '-';
            $alasanLokasi = '-';
            
            if ($absen && $absen->wfa) {
                $wfaValue = (int) $absen->wfa;
                if ($wfaValue === 1) {
                    // Di rumah
                    $lokasiWfa = 'Rumah';
                    $alasanLokasi = $absen->wfa_alasan ?? '-';
                } elseif ($wfaValue === 2) {
                    // Di luar rumah
                    $lokasiWfa = $absen->wfa_detail ?? '-';
                    $alasanLokasi = $absen->wfa_alasan ?? '-';
                }
            }

            $details[] = [
                'tanggal' => $date,
                'scan_masuk' => $absen && $absen->scan_masuk ? substr($absen->scan_masuk, 0, 5) : '-',
                'scan_pulang' => $absen && $absen->scan_pulang ? substr($absen->scan_pulang, 0, 5) : '-',
                'status' => $status,
                'lokasi_wfa' => $lokasiWfa,
                'alasan_lokasi' => $alasanLokasi,
            ];
        }

        return response()->json([
            'success' => true,
            'nama' => $user->nama,
            'nip' => $user->nip,
            'details' => $details,
        ]);
    }
}
