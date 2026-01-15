<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    /**
     * Check if user is admin or VP
     */
    private function ensureAdminOrVp(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        if (!in_array(strtoupper($role ?? ''), ['ADMIN', 'VP'])) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display report page
     */
    public function index(Request $request)
    {
        $this->ensureAdminOrVp();

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

        return view('report.index', [
            'pegawaiList' => $pegawaiList,
            'biroList' => $biroList,
        ]);
    }

    /**
     * Fetch report data via AJAX
     */
    public function getData(Request $request)
    {
        $this->ensureAdminOrVp();

        $nip = $request->input('nip');
        $biroId = $request->input('biro_id');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');
        $search = $request->input('search');
        $fetchAll = $request->input('fetch_all') === '1';
        $page = $request->input('page', 1);
        $perPage = 20;

        // Build query for users
        $query = DB::table('users as u')
            ->leftJoin('biro as b', 'u.biro_id', '=', 'b.id')
            ->leftJoin('users as atasan', 'u.nip_atasan', '=', 'atasan.nip')
            ->select(
                'u.nip',
                'u.nama',
                'u.biro_id',
                'u.nip_atasan',
                'b.biro_name',
                'atasan.nama as nama_atasan',
                'u.jabatan as jabatan_pegawai',
                'atasan.jabatan as jabatan_atasan'
            );

        // Search filter - if search is active, ignore NIP and Biro filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama', 'ilike', "%{$search}%")
                    ->orWhere('u.nip', 'ilike', "%{$search}%");
            });
        } else {
            // Filter by NIP (if not fetch all)
            if ($nip && !$fetchAll) {
                $query->where('u.nip', $nip);
            }

            // Filter by Biro (if not fetch all)
            if ($biroId && !$fetchAll) {
                $query->where('u.biro_id', $biroId);
            }
        }

        // Get total count for pagination
        $totalUsers = $query->count();

        // Paginate users
        $users = $query
            ->orderBy('u.nama')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'data' => [],
                'dates' => [],
                'pagination' => null,
            ]);
        }

        // Determine date range
        $dates = $this->getDateRange($tanggalFrom, $tanggalTo);

        // Get libur dates to exclude
        $liburDates = $this->getLiburDates($dates);

        // Filter out libur dates
        $workingDates = array_filter($dates, function ($date) use ($liburDates) {
            return !in_array($date, $liburDates);
        });
        $workingDates = array_values($workingDates);

        // Build report data for each user
        $reportData = [];
        foreach ($users as $user) {
            $userData = $this->buildUserReportData($user, $workingDates);
            $reportData[] = $userData;
        }

        // Pagination info
        $pagination = [
            'current_page' => (int) $page,
            'per_page' => $perPage,
            'total' => $totalUsers,
            'last_page' => ceil($totalUsers / $perPage),
        ];

        return response()->json([
            'data' => $reportData,
            'dates' => $workingDates,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Get date range for report
     */
    private function getDateRange($from, $to)
    {
        if ($from && $to) {
            $start = Carbon::parse($from);
            $end = Carbon::parse($to);
        } else {
            // Default: last 5 working days
            $end = Carbon::today();
            $start = Carbon::today()->subDays(6); // Get more days to account for weekends
        }

        $period = CarbonPeriod::create($start, $end);
        $dates = [];

        foreach ($period as $date) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            $dates[] = $date->format('Y-m-d');
        }

        // If no date filter, limit to 5 days
        if (!$from || !$to) {
            $dates = array_slice($dates, -5);
        }

        return $dates;
    }

    /**
     * Get libur dates from kalender_libur
     */
    private function getLiburDates($dates)
    {
        if (empty($dates)) {
            return [];
        }

        $libur = DB::table('kalender_libur')
            ->whereIn('tanggal', $dates)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        return $libur;
    }

    /**
     * Build report data for a single user
     */
    private function buildUserReportData($user, $dates)
    {
        $absenData = [];
        $totalMinutesWorked = 0;
        $totalWorkingDays = count($dates); // Total hari kerja yang ditampilkan di tabel
        $absenOnTime = 0; // ≤08:00
        $absenLateOrAbsent = 0; // >08:00 or tidak absen
        $violations = []; // Detail pelanggaran untuk teguran/peringatan

        foreach ($dates as $date) {
            $dateData = $this->getAbsenDataForDate($user->nip, $user->biro_id, $date);
            $absenData[$date] = $dateData;

            // Calculate stats
            if ($dateData['scan_masuk']) {
                try {
                    // Handle various time formats (H:i:s, H:i, etc)
                    $scanTimeStr = substr($dateData['scan_masuk'], 0, 5);
                    $scanTime = Carbon::createFromFormat('H:i', $scanTimeStr);
                    $threshold = Carbon::createFromFormat('H:i', '08:00');

                    if ($scanTime->lte($threshold)) {
                        $absenOnTime++;
                    } else {
                        $absenLateOrAbsent++;
                        // Add to violations (telat) - tanpa work status
                        $violations[] = [
                            'date' => $date,
                            'reason' => "TELAT ({$scanTimeStr})",
                        ];
                    }

                    // Calculate work hours if scan_pulang exists
                    if ($dateData['scan_pulang']) {
                        $pulangTimeStr = substr($dateData['scan_pulang'], 0, 5);
                        $pulangTime = Carbon::createFromFormat('H:i', $pulangTimeStr);
                        $minutesWorked = $scanTime->diffInMinutes($pulangTime);
                        $totalMinutesWorked += $minutesWorked;
                    }
                } catch (\Exception $e) {
                    // If time parsing fails, count as late/absent
                    $absenLateOrAbsent++;
                    $violations[] = [
                        'date' => $date,
                        'reason' => 'TIDAK ABSEN',
                    ];
                }
            } else {
                $absenLateOrAbsent++;
                // Add to violations - tanpa work status
                // Untuk rejected izin/dinas, ambil reason dari violation_reason jika ada
                $violationReason = $dateData['violation_reason'] ?? 'TIDAK ABSEN';
                $violations[] = [
                    'date' => $date,
                    'reason' => $violationReason,
                ];
            }
        }

        // Calculate average work time based on total working days (not just days with absen)
        $avgWorkTime = '-';
        if ($totalWorkingDays > 0) {
            $avgMinutes = $totalMinutesWorked / $totalWorkingDays;
            $hours = floor($avgMinutes / 60);
            $minutes = round($avgMinutes % 60);
            $avgWorkTime = sprintf('%d Jam %d Menit', $hours, $minutes);
        }

        return [
            'nip' => $user->nip,
            'nama' => $user->nama,
            'nama_atasan' => $user->nama_atasan ?? '-',
            'nip_atasan' => $user->nip_atasan ?? null,
            'jabatan_pegawai' => $user->jabatan_pegawai ?? '-',
            'jabatan_atasan' => $user->jabatan_atasan ?? '-',
            'biro_name' => $user->biro_name ?? '-',
            'biro_id' => $user->biro_id,
            'absen_data' => $absenData,
            'absen_on_time' => $absenOnTime,
            'absen_late_or_absent' => $absenLateOrAbsent,
            'avg_work_time' => $avgWorkTime,
            'violation_count' => count($violations),
            'violations' => $violations,
        ];
    }

    /**
     * Get absen data for a specific date
     */
    private function getAbsenDataForDate($nip, $biroId, $date)
    {
        $dateObj = Carbon::parse($date);
        
        // Get work status (WFO/WFA) from pengajuan_wao_detail
        $workStatus = $this->getWorkStatus($nip, $biroId, $dateObj);

        // Get absen record
        $absen = DB::table('absen')
            ->where('nip', $nip)
            ->where('tanggal', $date)
            ->first();

        // Check for rejected izin
        $rejectedIzin = $this->checkRejectedIzin($nip, $date);
        if ($rejectedIzin) {
            return [
                'display' => $rejectedIzin['display'],
                'scan_masuk' => null,
                'scan_pulang' => null,
                'status' => 'rejected',
                'work_status' => $workStatus,
                'violation_reason' => $rejectedIzin['violation_reason'] ?? $rejectedIzin['display'],
            ];
        }

        // No absen record
        if (!$absen) {
            return [
                'display' => "TIDAK ABSEN | {$workStatus}",
                'scan_masuk' => null,
                'scan_pulang' => null,
                'status' => 'absent',
                'work_status' => $workStatus,
                'violation_reason' => 'TIDAK ABSEN',
            ];
        }

        // Check status_izin
        $statusIzin = $absen->status_izin;

        // Dinas or Izin
        if (in_array($statusIzin, ['dinas', 'izin'])) {
            $formulir = DB::table('formulir_izin')
                ->where('id', $absen->izin_id)
                ->first();

            $statusLabel = strtoupper($statusIzin === 'izin' ? 'IZIN' : $statusIzin);
            $alasan = $formulir ? strtoupper($formulir->alasan) : '-';

            $display = sprintf(
                '%s (%s) | %s | %s',
                $absen->scan_masuk ?? '08:00',
                $statusLabel,
                $alasan,
                $workStatus
            );

            return [
                'display' => $display,
                'scan_masuk' => $absen->scan_masuk,
                'scan_pulang' => $absen->scan_pulang,
                'status' => $statusIzin,
                'work_status' => $workStatus,
            ];
        }

        // WFA (wfh in database)
        if ($statusIzin === 'wfh') {
            $alasan = $absen->wfa_alasan ? strtoupper($absen->wfa_alasan) : '-';
            
            if ($absen->scan_pulang) {
                $display = sprintf('%s (WFA) | %s | WFA', $absen->scan_masuk, $alasan);
            } else {
                $display = sprintf('%s (WFA) | TIDAK ABSEN PULANG | %s | WFA', $absen->scan_masuk, $alasan);
            }

            return [
                'display' => $display,
                'scan_masuk' => $absen->scan_masuk,
                'scan_pulang' => $absen->scan_pulang,
                'status' => 'wfa',
                'work_status' => 'WFA',
            ];
        }

        // Regular hadir (WFO)
        if ($absen->scan_masuk) {
            if ($absen->scan_pulang) {
                $display = sprintf('%s | %s', $absen->scan_masuk, $workStatus);
            } else {
                $display = sprintf('%s | TIDAK ABSEN PULANG | %s', $absen->scan_masuk, $workStatus);
            }

            return [
                'display' => $display,
                'scan_masuk' => $absen->scan_masuk,
                'scan_pulang' => $absen->scan_pulang,
                'status' => 'hadir',
                'work_status' => $workStatus,
            ];
        }

        // Fallback
        return [
            'display' => "TIDAK ABSEN | {$workStatus}",
            'scan_masuk' => null,
            'scan_pulang' => null,
            'status' => 'absent',
            'work_status' => $workStatus,
        ];
    }

    /**
     * Get work status (WFO/WFA) for a date
     */
    private function getWorkStatus($nip, $biroId, $dateObj)
    {
        $dayOfWeek = strtolower($dateObj->format('l'));
        $dayMap = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
        ];

        $dayColumn = $dayMap[$dayOfWeek] ?? null;
        if (!$dayColumn) {
            return 'WFO'; // Default for weekend (shouldn't happen)
        }

        // Find active pengajuan
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.biro_id', $biroId)
            ->whereDate('kk.tgl_awal', '<=', $dateObj->format('Y-m-d'))
            ->whereDate('kk.tgl_akhir', '>=', $dateObj->format('Y-m-d'))
            ->where('pw.status', 'final')
            ->select('pw.id')
            ->first();

        if (!$pengajuan) {
            return 'WFO'; // Default
        }

        // Get detail
        $detail = DB::table('pengajuan_wao_detail')
            ->where('pengajuan_id', $pengajuan->id)
            ->where('nip', $nip)
            ->first();

        if (!$detail) {
            return 'WFO';
        }

        return $detail->$dayColumn ? 'WFO' : 'WFA';
    }

    /**
     * Check for rejected izin
     */
    private function checkRejectedIzin($nip, $date)
    {
        // Check auto-rejected
        $autoRejected = DB::table('formulir_izin')
            ->where('nip', $nip)
            ->where('auto_reject', 1)
            ->whereDate('from', '<=', $date)
            ->whereDate('to', '>=', $date)
            ->first();

        if ($autoRejected) {
            $statusLabel = strtoupper($autoRejected->status === 'izin' ? 'SAKIT/IZIN' : $autoRejected->status);
            $alasan = strtoupper($autoRejected->alasan ?? '-');
            return [
                'display' => "DITOLAK OTOMATIS OLEH SISTEM MELEBIHI BATAS WAKTU APPROVAL | ({$statusLabel} | {$alasan})",
                'type' => 'auto_reject',
                'violation_reason' => "DITOLAK OTOMATIS ({$statusLabel} | {$alasan})",
            ];
        }

        // Check rejected by atasan
        $rejected = DB::table('formulir_izin as fi')
            ->leftJoin('users as atasan', function ($join) use ($nip) {
                $join->on('atasan.nip', '=', DB::raw("(SELECT nip_atasan FROM users WHERE nip = '{$nip}')"));
            })
            ->where('fi.nip', $nip)
            ->where('fi.is_reject', 1)
            ->whereDate('fi.from', '<=', $date)
            ->whereDate('fi.to', '>=', $date)
            ->select('fi.*', 'atasan.nama as nama_atasan')
            ->first();

        if ($rejected) {
            $statusLabel = strtoupper($rejected->status === 'izin' ? 'SAKIT/IZIN' : $rejected->status);
            $alasan = strtoupper($rejected->alasan ?? '-');
            $atasanNama = strtoupper($rejected->nama_atasan ?? 'ATASAN');
            return [
                'display' => "DITOLAK OLEH BAPAK/IBU <strong>{$atasanNama}</strong> | {$statusLabel} | {$alasan}",
                'type' => 'rejected',
                'violation_reason' => "DITOLAK OLEH {$atasanNama} ({$statusLabel} | {$alasan})",
            ];
        }

        return null;
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $this->ensureAdminOrVp();

        // Get data same as getData
        $data = $this->getExportData($request);

        // Generate CSV
        $filename = 'WG Absen — Laporan Absen.csv';

        return response()->streamDownload(function () use ($data) {
            $this->generateExcel($data);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $this->ensureAdminOrVp();

        // Get data
        $data = $this->getExportData($request);

        // For now, return a simple HTML that can be printed as PDF
        return view('report.export-pdf', [
            'reportData' => $data['data'],
            'dates' => $data['dates'],
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Get data for export
     */
    private function getExportData(Request $request)
    {
        $nip = $request->input('nip');
        $biroId = $request->input('biro_id');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');

        // Handle "all" value - treat as empty (fetch all)
        if ($nip === 'all') {
            $nip = null;
        }
        if ($biroId === 'all') {
            $biroId = null;
        }

        // Build query for users (no pagination for export)
        $query = DB::table('users as u')
            ->leftJoin('biro as b', 'u.biro_id', '=', 'b.id')
            ->leftJoin('users as atasan', 'u.nip_atasan', '=', 'atasan.nip')
            ->select(
                'u.nip',
                'u.nama',
                'u.biro_id',
                'b.biro_name',
                'atasan.nama as nama_atasan'
            );

        if ($nip) {
            $query->where('u.nip', $nip);
        }
        if ($biroId) {
            $query->where('u.biro_id', $biroId);
        }

        $users = $query->orderBy('u.nama')->get();

        // Get dates
        $dates = $this->getDateRange($tanggalFrom, $tanggalTo);
        $liburDates = $this->getLiburDates($dates);
        $workingDates = array_filter($dates, fn($d) => !in_array($d, $liburDates));
        $workingDates = array_values($workingDates);

        // Build report data
        $reportData = [];
        foreach ($users as $user) {
            $reportData[] = $this->buildUserReportData($user, $workingDates);
        }

        return [
            'data' => $reportData,
            'dates' => $workingDates,
            'filters' => [
                'tanggal_from' => $tanggalFrom,
                'tanggal_to' => $tanggalTo,
            ]
        ];
    }

    /**
     * Generate CSV file
     */
    private function generateExcel($data)
    {
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        $headers = ['NIP', 'Nama', 'Atasan', 'Unit Kerja'];
        foreach ($data['dates'] as $date) {
            $headers[] = $date;
        }
        $headers[] = 'ABSEN S.D 08:00';
        $headers[] = 'ABSEN >08:00 / TIDAK ABSEN';
        $headers[] = 'RATA² JAM KERJA';

        fputcsv($output, $headers);

        // Data rows
        foreach ($data['data'] as $row) {
            $rowData = [
                $row['nip'],
                $row['nama'],
                $row['nama_atasan'],
                $row['biro_name'],
            ];

            foreach ($data['dates'] as $date) {
                $rowData[] = strip_tags($row['absen_data'][$date]['display'] ?? '-');
            }

            $rowData[] = $row['absen_on_time'];
            $rowData[] = $row['absen_late_or_absent'];
            $rowData[] = $row['avg_work_time'];

            fputcsv($output, $rowData);
        }

        fclose($output);
    }

    /**
     * Send Teguran via WhatsApp
     */
    public function sendTeguran(Request $request)
    {
        $this->ensureAdminOrVp();

        $nip = $request->input('nip');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');

        // Get user data
        $user = DB::table('users as u')
            ->leftJoin('biro as b', 'u.biro_id', '=', 'b.id')
            ->leftJoin('users as atasan', 'u.nip_atasan', '=', 'atasan.nip')
            ->select(
                'u.nip',
                'u.nama',
                'u.telp',
                'u.biro_id',
                'u.nip_atasan',
                'b.biro_name',
                'atasan.nama as nama_atasan',
                'atasan.telp as telp_atasan',
                'u.jabatan as jabatan_pegawai',
                'atasan.jabatan as jabatan_atasan'
            )
            ->where('u.nip', $nip)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Get violations data
        $dates = $this->getDateRange($tanggalFrom, $tanggalTo);
        $liburDates = $this->getLiburDates($dates);
        $workingDates = array_filter($dates, fn($d) => !in_array($d, $liburDates));
        $workingDates = array_values($workingDates);

        $reportData = $this->buildUserReportData($user, $workingDates);
        $violations = $reportData['violations'];
        $violationCount = count($violations);

        if ($violationCount === 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pelanggaran untuk dikirim'], 400);
        }

        // Convert number to words
        $numberWords = $this->numberToWords($violationCount);

        // Format dates
        $fromFormatted = Carbon::parse($tanggalFrom)->format('d-m-Y');
        $toFormatted = Carbon::parse($tanggalTo)->format('d-m-Y');

        // Build message using templates
        $templateData = [
            'nama_pegawai' => $user->nama,
            'violation_count' => $violationCount,
            'violation_words' => $numberWords,
            'from' => $fromFormatted,
            'to' => $toFormatted,
            'violations' => $violations,
        ];

        $messagePegawai = view('templates.wa.teguran', $templateData)->render();
        $messageAtasan = view('templates.wa.teguran-tembusan', $templateData)->render();

        // Send WhatsApp
        $waService = new \App\Services\WhatsAppNotificationService();
        $results = [];

        // Send to pegawai
        if ($user->telp) {
            $sent = $waService->sendRawMessage($user->telp, $messagePegawai);
            $results['pegawai'] = $sent;
        }

        // Send to atasan (tembusan)
        if ($user->telp_atasan) {
            $sent = $waService->sendRawMessage($user->telp_atasan, $messageAtasan);
            $results['atasan'] = $sent;
        }

        return response()->json([
            'success' => true,
            'message' => 'Teguran berhasil dikirim',
            'results' => $results,
        ]);
    }

    /**
     * Send Peringatan via WhatsApp
     */
    public function sendPeringatan(Request $request)
    {
        $this->ensureAdminOrVp();

        $nip = $request->input('nip');
        $tanggalFrom = $request->input('tanggal_from');
        $tanggalTo = $request->input('tanggal_to');

        // Get user data
        $user = DB::table('users as u')
            ->leftJoin('biro as b', 'u.biro_id', '=', 'b.id')
            ->leftJoin('users as atasan', 'u.nip_atasan', '=', 'atasan.nip')
            ->leftJoin('biro as b_atasan', 'atasan.biro_id', '=', 'b_atasan.id')
            ->select(
                'u.nip',
                'u.nama',
                'u.telp',
                'u.biro_id',
                'u.nip_atasan',
                'b.biro_name',
                'atasan.nama as nama_atasan',
                'atasan.telp as telp_atasan',
                'b_atasan.biro_name as biro_atasan',
                'u.jabatan as jabatan_pegawai',
                'atasan.jabatan as jabatan_atasan'
            )
            ->where('u.nip', $nip)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Get violations data
        $dates = $this->getDateRange($tanggalFrom, $tanggalTo);
        $liburDates = $this->getLiburDates($dates);
        $workingDates = array_filter($dates, fn($d) => !in_array($d, $liburDates));
        $workingDates = array_values($workingDates);

        $reportData = $this->buildUserReportData($user, $workingDates);
        $violations = $reportData['violations'];
        $violationCount = count($violations);

        if ($violationCount < 5) {
            return response()->json(['success' => false, 'message' => 'Peringatan hanya untuk pelanggaran >= 5 kali'], 400);
        }

        // Convert number to words
        $numberWords = $this->numberToWords($violationCount);

        // Format dates
        $fromFormatted = Carbon::parse($tanggalFrom)->format('d-m-Y');
        $toFormatted = Carbon::parse($tanggalTo)->format('d-m-Y');

        // Build message using template
        $templateData = [
            'nama_atasan' => $user->nama_atasan ?? '-',
            'jabatan_atasan' => $user->jabatan_atasan ?? '-',
            'biro_atasan' => $user->biro_atasan ?? $user->biro_name ?? '-',
            'nama_pegawai' => $user->nama,
            'jabatan_pegawai' => $user->jabatan_pegawai ?? '-',
            'biro_pegawai' => $user->biro_name ?? '-',
            'violation_count' => $violationCount,
            'violation_words' => $numberWords,
            'from' => $fromFormatted,
            'to' => $toFormatted,
            'violations' => $violations,
        ];

        $message = view('templates.wa.peringatan', $templateData)->render();

        // Send WhatsApp to atasan only
        $waService = new \App\Services\WhatsAppNotificationService();
        $results = [];

        if ($user->telp_atasan) {
            $sent = $waService->sendRawMessage($user->telp_atasan, $message);
            $results['atasan'] = $sent;
        } else {
            return response()->json(['success' => false, 'message' => 'Nomor HP atasan tidak tersedia'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peringatan berhasil dikirim ke atasan',
            'results' => $results,
        ]);
    }

    /**
     * Convert number to Indonesian words
     */
    private function numberToWords($number)
    {
        $words = [
            0 => 'nol', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat',
            5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan',
            10 => 'sepuluh', 11 => 'sebelas', 12 => 'dua belas', 13 => 'tiga belas',
            14 => 'empat belas', 15 => 'lima belas', 16 => 'enam belas', 17 => 'tujuh belas',
            18 => 'delapan belas', 19 => 'sembilan belas', 20 => 'dua puluh',
        ];

        if ($number <= 20) {
            return $words[$number] ?? (string)$number;
        } elseif ($number < 100) {
            $tens = floor($number / 10);
            $units = $number % 10;
            return $words[$tens] . ' puluh' . ($units > 0 ? ' ' . $words[$units] : '');
        }

        return (string)$number;
    }
}
