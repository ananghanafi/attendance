<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanWfoController extends Controller
{
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
     * Cek apakah user adalah admin atau VP
     */
    private function isAdminOrVP(): bool
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        return in_array(strtoupper($role ?? ''), ['ADMIN', 'VP']);
    }

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
     * Cek apakah user dapat mengakses semua biro (Admin, VP, atau HC)
     */
    private function canAccessAllBiro(): bool
    {
        return $this->isAdminOrVP() || $this->isHC();
    }

    /**
     * Pastikan user adalah admin atau VP, jika tidak abort 403
     */
    private function ensureAdminOrVP(): void
    {
        if (!$this->isAdminOrVP()) {
            abort(403);
        }
    }

    /**
     * Cek apakah user punya akses ke pengajuan (admin/VP/HC ATAU user dari biro yang sama)
     */
    private function ensureCanAccessPengajuan(int $pengajuanId): void
    {
        $user = Auth::user();
        
        // Admin/VP/HC selalu bisa akses
        if ($this->canAccessAllBiro()) {
            return;
        }
        
        // Cek apakah user dari biro yang sama dengan pengajuan
        $pengajuan = DB::table('pengajuan_wao')->where('id', $pengajuanId)->first();
        
        if ($pengajuan && $pengajuan->biro_id === $user->biro_id) {
            return;
        }
        
        abort(403);
    }

    /**
     * Dashboard pengajuan WFO
     * - Admin/VP/HC: List semua biro dengan filter
     * - User biasa: Hanya biro sendiri
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $canAccessAllBiro = $this->canAccessAllBiro();
        $canBroadcast = $this->isAdmin(); // Hanya admin bisa broadcast
        $isAdminOrVP = $this->isAdminOrVP(); // Untuk edit akses

        // Filter parameters
        $search = $request->query('search', '');
        $minggu = $request->query('minggu', '');
        $bulan = $request->query('bulan', '');
        $tahun = $request->query('tahun', '');

        // Check if any filter is applied
        $hasFilter = !empty($search) || !empty($minggu) || !empty($bulan) || !empty($tahun);

        // Get dropdown data for filters - hanya biro yang is_proyek = false (bukan proyek)
        // User biasa hanya lihat biro sendiri
        if ($canAccessAllBiro) {
            $biros = DB::table('biro')
                ->where('is_proyek', false)
                ->orderBy('biro_name')
                ->get(['id', 'biro_name']);
        } else {
            $biros = DB::table('biro')
                ->where('id', $user->biro_id)
                ->get(['id', 'biro_name']);
        }

        // USER BIASA: Hanya tampilkan pengajuan dari biro sendiri
        if (!$canAccessAllBiro) {
            $query = DB::table('pengajuan_wao as pw')
                ->join('biro as b', 'pw.biro_id', '=', 'b.id')
                ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('pw.biro_id', $user->biro_id)
                ->select([
                    'pw.id',
                    'pw.biro_id',
                    'b.biro_name',
                    'pw.kalender',
                    'pw.status',
                    'pw.created_date',
                    'kk.kalender as kalender_string',
                    'kk.periode',
                    'kk.tgl_awal',
                    'kk.tgl_akhir',
                    'kk.persentase',
                    'kk.persentase_wfa'
                ]);

            // Apply filters
            if ($minggu) {
                $query->where('kk.kalender', 'like', $minggu . '-%');
            }
            if ($bulan) {
                $query->where('kk.kalender', 'like', '%-' . $bulan . '-%');
            }
            if ($tahun) {
                $query->where('kk.kalender', 'like', '%-' . $tahun);
            }

            $pengajuans = $query
                ->orderBy('kk.tgl_awal', 'desc')
                ->paginate(10)
                ->withQueryString();
        }
        // ADMIN/VP: Tampilkan semua biro
        elseif (!$hasFilter) {
            // Query dengan ROW_NUMBER untuk limit 4 per biro
            $subQuery = DB::table('pengajuan_wao as pw')
                ->join('biro as b', 'pw.biro_id', '=', 'b.id')
                ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('b.is_proyek', false)
                ->selectRaw("
                    pw.id,
                    pw.biro_id,
                    b.biro_name,
                    pw.kalender,
                    pw.status,
                    pw.created_date,
                    kk.kalender as kalender_string,
                    kk.periode,
                    kk.tgl_awal,
                    kk.tgl_akhir,
                    kk.persentase,
                    kk.persentase_wfa,
                    ROW_NUMBER() OVER (PARTITION BY pw.biro_id ORDER BY kk.tgl_awal DESC) as row_num
                ");

            $pengajuans = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
                ->mergeBindings($subQuery)
                ->where('row_num', '<=', 4)
                ->orderBy('tgl_awal', 'desc')
                ->orderBy('biro_name')
                ->paginate(10)
                ->withQueryString();
        } else {
            // Query normal dengan filter - tampilkan semua data
            $query = DB::table('pengajuan_wao as pw')
                ->join('biro as b', 'pw.biro_id', '=', 'b.id')
                ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('b.is_proyek', false)
                ->select([
                    'pw.id',
                    'pw.biro_id',
                    'b.biro_name',
                    'pw.kalender',
                    'pw.status',
                    'pw.created_date',
                    'kk.kalender as kalender_string',
                    'kk.periode',
                    'kk.tgl_awal',
                    'kk.tgl_akhir',
                    'kk.persentase',
                    'kk.persentase_wfa'
                ]);

            // Apply filters
            if ($search) {
                $query->where('b.biro_name', 'ilike', "%{$search}%");
            }
            
            if ($minggu) {
                $query->where('kk.kalender', 'like', $minggu . '-%');
            }
            
            if ($bulan) {
                $query->where('kk.kalender', 'like', '%-' . $bulan . '-%');
            }
            
            if ($tahun) {
                $query->where('kk.kalender', 'like', '%-' . $tahun);
            }

            $pengajuans = $query
                ->orderBy('kk.tgl_awal', 'desc')
                ->orderBy('b.biro_name')
                ->paginate(10)
                ->withQueryString();
        }

        // Enrich data dengan bulan/tahun extracted
        foreach ($pengajuans as $p) {
            // Extract dari kk.kalender (format: "1-8-2025")
            if ($p->kalender_string) {
                $parts = explode('-', $p->kalender_string);
                if (count($parts) === 3) {
                    $p->minggu = (int) $parts[0];
                    $p->bulan = (int) $parts[1];
                    $p->tahun = (int) $parts[2];
                }
            } elseif ($p->tgl_awal) {
                // Fallback jika kalender_string kosong
                $date = new \DateTime($p->tgl_awal);
                $p->bulan = (int) $date->format('n');
                $p->tahun = (int) $date->format('Y');
                $p->minggu = null;
            } else {
                $p->bulan = null;
                $p->tahun = null;
                $p->minggu = null;
            }

            // Cek apakah periode ini bisa diedit (tanggal sekarang dalam rentang tgl_awal - tgl_akhir)
            $p->canEdit = $this->isPeriodeEditable($p->tgl_awal, $p->tgl_akhir);
        }

        // Get ALL eligible IDs for broadcast (canEdit = true) across all pages - hanya jika bisa broadcast
        $allEligibleIds = [];
        if ($canBroadcast) {
            $eligibleQuery = DB::table('pengajuan_wao as pw')
                ->join('biro as b', 'pw.biro_id', '=', 'b.id')
                ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('b.is_proyek', false)
                ->whereRaw("CURRENT_DATE >= kk.tgl_awal AND CURRENT_DATE <= kk.tgl_akhir")
                ->select('pw.id', 'b.biro_name');

            $allEligible = $eligibleQuery->get();
            foreach ($allEligible as $item) {
                $allEligibleIds[] = [
                    'id' => $item->id,
                    'biro_name' => $item->biro_name
                ];
            }
        }

        return view('admin.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'biros' => $biros,
            'filters' => [
                'search' => $search,
                'minggu' => $minggu,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            'isAdminOrVP' => $isAdminOrVP,
            'canBroadcast' => $canBroadcast,
            'canAccessAllBiro' => $canAccessAllBiro,
            'allEligibleIds' => $allEligibleIds,
        ]);
    }

    /**
     * Set session untuk view pengajuan
     */
    public function setView(Request $request)
    {
        $id = $request->input('id');
        $this->ensureCanAccessPengajuan($id);
        session(['pengajuan_id' => $id]);
        return redirect()->route('pengajuan.show');
    }

    /**
     * View detail pengajuan (untuk lihat absensi) - read only
     */
    public function show()
    {
        $id = session('pengajuan_id');
        if (!$id) {
            return redirect()->route('pengajuan.index')->with('error', 'Silakan pilih pengajuan dari daftar');
        }

        // Cek akses
        $this->ensureCanAccessPengajuan($id);

        // Get pengajuan master
        // JOIN langsung via kolom kalender yang sama format "minggu-bulan-tahun"
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('biro as b', 'pw.biro_id', '=', 'b.id')
            ->leftJoin('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.id', $id)
            ->select([
                'pw.*',
                'b.biro_name',
                'kk.periode',
                'kk.tgl_awal',
                'kk.tgl_akhir',
                'kk.persentase_decimal',
                'kk.kalender'
            ])
            ->first();

        if (!$pengajuan) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
        }

        // Extract bulan/tahun/minggu dari kk.kalender (format: "minggu-bulan-tahun" e.g. "1-8-2025")
        if ($pengajuan->kalender) {
            $parts = explode('-', $pengajuan->kalender);
            if (count($parts) === 3) {
                $pengajuan->minggu = (int) $parts[0];
                $pengajuan->bulan = (int) $parts[1];
                $pengajuan->tahun = (int) $parts[2];
            }
        } else {
            // Fallback: extract dari tgl_awal jika kalender tidak ada
            if ($pengajuan->tgl_awal) {
                $date = new \DateTime($pengajuan->tgl_awal);
                $pengajuan->bulan = (int) $date->format('n');
                $pengajuan->tahun = (int) $date->format('Y');
                $pengajuan->minggu = null;
            }
        }

        // Get detail per pegawai dari pengajuan_wao_detail
        $details = DB::table('pengajuan_wao_detail as pwd')
            ->join('users as u', 'pwd.nip', '=', 'u.nip')
            ->where('pwd.pengajuan_id', $id)
            ->select([
                'pwd.*',
                'u.nama',
                'u.jabatan'
            ])
            ->orderBy('u.nama')
            ->get();
        
        // Jika tidak ada detail sama sekali, ambil semua pegawai dari biro tersebut
        // Ini hanya untuk backward compatibility jika ada pengajuan lama tanpa detail
        if ($details->isEmpty()) {
            $details = DB::table('users')
                ->where('biro_id', $pengajuan->biro_id)
                ->select([
                    'nip',
                    'nama',
                    'jabatan'
                ])
                ->orderBy('nama')
                ->get()
                ->map(function($user) {
                    // Set default values untuk hari kerja (semua WFA)
                    $user->senin = false;
                    $user->selasa = false;
                    $user->rabu = false;
                    $user->kamis = false;
                    $user->jumat = false;
                    return $user;
                });
        }

        // Get tanggal libur dalam periode pengajuan
        $hariLibur = $this->getHariLiburDalamPeriode($pengajuan->tgl_awal, $pengajuan->tgl_akhir);

        return view('admin.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'details' => $details,
            'readOnly' => true, // View mode - radio buttons disabled
            'hariLibur' => $hariLibur, // Array hari yang libur: ['senin' => true, 'selasa' => false, ...]
            'isAdminOrVP' => $this->isAdminOrVP(),
        ]);
    }

    /**
     * Set session untuk edit pengajuan
     * Admin/VP bisa edit kapan saja (selama periode aktif)
     * User biasa HANYA bisa edit jika login via magic link dan status belum 'final'
     */
    public function setEdit(Request $request)
    {
        $id = $request->input('id');
        
        // Cek apakah periode bisa diedit
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->leftJoin('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.id', $id)
            ->select(['pw.biro_id', 'pw.status', 'kk.tgl_awal', 'kk.tgl_akhir'])
            ->first();
        
        if (!$pengajuan) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
        }
        
        // Cek apakah periode masih dalam rentang aktif
        if (!$this->isPeriodeEditable($pengajuan->tgl_awal, $pengajuan->tgl_akhir)) {
            return redirect()->route('pengajuan.index')->with('error', 'Periode pengajuan ini sudah tidak dapat diedit. Hanya pengajuan dalam rentang tanggal aktif yang dapat diedit.');
        }

        // Admin/VP bisa edit kapan saja
        if ($this->isAdminOrVP()) {
            session(['pengajuan_id' => $id]);
            return redirect()->route('pengajuan.edit');
        }

        // User biasa: HARUS punya magic_token_id di session (login via link broadcast)
        if (!session('magic_token_id')) {
            return redirect()->route('pengajuan.index')->with('error', 'Anda tidak memiliki akses untuk mengedit pengajuan ini. Silakan gunakan link yang dikirim via WhatsApp.');
        }

        // User biasa: cek apakah dari biro yang sama
        $user = Auth::user();
        if ($pengajuan->biro_id !== $user->biro_id) {
            abort(403);
        }

        // User biasa: cek status - hanya bisa edit jika belum final
        if (strtolower($pengajuan->status ?? '') === 'final') {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan ini sudah disimpan permanen dan tidak dapat diedit lagi.');
        }
        
        session(['pengajuan_id' => $id]);
        return redirect()->route('pengajuan.edit');
    }

    /**
     * Edit pengajuan (form untuk edit WFO/WFA)
     * Admin/VP bisa edit kapan saja
     * User biasa HANYA bisa edit jika login via magic link dan status belum 'final'
     */
    public function edit()
    {
        $id = session('pengajuan_id');
        if (!$id) {
            return redirect()->route('pengajuan.index')->with('error', 'Silakan pilih pengajuan dari daftar');
        }

        // Get pengajuan master
        // JOIN langsung via kolom kalender yang sama format "minggu-bulan-tahun"
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('biro as b', 'pw.biro_id', '=', 'b.id')
            ->leftJoin('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.id', $id)
            ->select([
                'pw.*',
                'b.biro_name',
                'kk.periode',
                'kk.tgl_awal',
                'kk.tgl_akhir',
                'kk.persentase_decimal',
                'kk.kalender'
            ])
            ->first();

        if (!$pengajuan) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
        }

        // Cek akses
        $user = Auth::user();
        $isAdminOrVP = $this->isAdminOrVP();
        
        // Jika bukan admin/VP
        if (!$isAdminOrVP) {
            // HARUS punya magic_token_id di session (login via link broadcast)
            if (!session('magic_token_id')) {
                return redirect()->route('pengajuan.index')->with('error', 'Anda tidak memiliki akses untuk mengedit pengajuan ini. Silakan gunakan link yang dikirim via WhatsApp.');
            }

            // Cek apakah dari biro yang sama
            if ($pengajuan->biro_id !== $user->biro_id) {
                abort(403);
            }

            // Cek status - jika sudah final, redirect ke view only
            if (strtolower($pengajuan->status ?? '') === 'final') {
                return redirect()->route('pengajuan.show')->with('info', 'Pengajuan ini sudah disimpan permanen. Anda hanya dapat melihat data.');
            }
        }

        // Cek lagi apakah periode masih bisa diedit (untuk keamanan)
        if (!$this->isPeriodeEditable($pengajuan->tgl_awal, $pengajuan->tgl_akhir)) {
            return redirect()->route('pengajuan.index')->with('error', 'Periode pengajuan ini sudah tidak dapat diedit. Hanya pengajuan dalam rentang tanggal aktif yang dapat diedit.');
        }

        // Extract bulan/tahun/minggu dari kk.kalender (format: "minggu-bulan-tahun" e.g. "1-8-2025")
        if ($pengajuan->kalender) {
            $parts = explode('-', $pengajuan->kalender);
            if (count($parts) === 3) {
                $pengajuan->minggu = (int) $parts[0];
                $pengajuan->bulan = (int) $parts[1];
                $pengajuan->tahun = (int) $parts[2];
            }
        } else {
            if ($pengajuan->tgl_awal) {
                $date = new \DateTime($pengajuan->tgl_awal);
                $pengajuan->bulan = (int) $date->format('n');
                $pengajuan->tahun = (int) $date->format('Y');
                $pengajuan->minggu = null;
            }
        }

        // Get detail per pegawai
        $details = DB::table('pengajuan_wao_detail as pwd')
            ->join('users as u', 'pwd.nip', '=', 'u.nip')
            ->where('pwd.pengajuan_id', $id)
            ->select([
                'pwd.*',
                'u.nama',
                'u.jabatan'
            ])
            ->orderBy('u.nama')
            ->get();
        
        if ($details->isEmpty()) {
            $details = DB::table('users')
                ->where('biro_id', $pengajuan->biro_id)
                ->select([
                    'nip',
                    'nama',
                    'jabatan'
                ])
                ->orderBy('nama')
                ->get()
                ->map(function($user) {
                    $user->senin = false;
                    $user->selasa = false;
                    $user->rabu = false;
                    $user->kamis = false;
                    $user->jumat = false;
                    return $user;
                });
        }

        // Get tanggal libur dalam periode pengajuan
        $hariLibur = $this->getHariLiburDalamPeriode($pengajuan->tgl_awal, $pengajuan->tgl_akhir);

        return view('admin.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'details' => $details,
            'readOnly' => false,
            'hariLibur' => $hariLibur, // Array hari yang libur: ['senin' => true, 'selasa' => false, ...]
            'isAdminOrVP' => $this->isAdminOrVP(),
        ]);
    }

    /**
     * Update pengajuan WFO/WFA per pegawai
     * Admin/VP atau user dengan akses bisa update
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id = session('pengajuan_id');
        if (!$id) {
            return redirect()->route('pengajuan.index')->with('error', 'Silakan pilih pengajuan dari daftar');
        }

        // Cek apakah ini save permanen atau draft
        $isFinal = $request->input('save_final', false);

        try {
            DB::beginTransaction();

            // Validate pengajuan exists and get kalender info
            // JOIN langsung via kolom kalender yang sama format "minggu-bulan-tahun"
            $pengajuan = DB::table('pengajuan_wao as pw')
                ->leftJoin('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('pw.id', $id)
                ->select(['pw.*', 'kk.tgl_awal', 'kk.tgl_akhir', 'kk.persentase_decimal'])
                ->first();
                
            if (!$pengajuan) {
                return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
            }

            // Cek akses
            $user = Auth::user();
            $isAdminOrVP = $this->isAdminOrVP();
            
            // Jika bukan admin/VP, cek apakah dari biro yang sama
            if (!$isAdminOrVP && $pengajuan->biro_id !== $user->biro_id) {
                abort(403);
            }

            // Jika bukan admin/VP dan status sudah final, tolak update
            if (!$isAdminOrVP && strtolower($pengajuan->status ?? '') === 'final') {
                return redirect()->route('pengajuan.show')->with('error', 'Pengajuan ini sudah disimpan permanen dan tidak dapat diedit lagi.');
            }

            // Get attendance data from request
            $attendance = $request->input('attendance', []);
            
            // Get total pegawai
            $totalPegawai = count($attendance);
            $maxPersentase = $pengajuan->persentase_decimal ?? 100;
            
            // Get hari libur untuk validasi
            $hariLiburValidasi = $this->getHariLiburDalamPeriode($pengajuan->tgl_awal, $pengajuan->tgl_akhir);
            
            // Validasi persentase WFO per hari tidak melebihi batas (skip hari libur)
            if ($totalPegawai > 0) {
                $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
                $dayNames = ['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat'];
                
                foreach ($days as $day) {
                    // Skip validasi untuk hari libur
                    if ($hariLiburValidasi[$day]) {
                        continue;
                    }
                    
                    $wfoCount = 0;
                    foreach ($attendance as $nip => $dayData) {
                        if (isset($dayData[$day]) && $dayData[$day]) {
                            $wfoCount++;
                        }
                    }
                    
                    $wfoPercentage = ($wfoCount / $totalPegawai) * 100;
                    
                    if ($wfoPercentage > $maxPersentase) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', "Persentase WFO hari {$dayNames[$day]} ({$wfoCount}/{$totalPegawai} = " . round($wfoPercentage) . "%) melebihi batas maksimal {$maxPersentase}%")
                            ->withInput();
                    }
                }
            }
            
            // Calculate dates for each day (Senin-Jumat) from tgl_awal
            $tglAwal = $pengajuan->tgl_awal ? new \DateTime($pengajuan->tgl_awal) : null;
            $dayDates = [];
            if ($tglAwal) {
                $senin = clone $tglAwal;
                $selasa = clone $tglAwal;
                $rabu = clone $tglAwal;
                $kamis = clone $tglAwal;
                $jumat = clone $tglAwal;
                
                $dayDates = [
                    'senin' => $senin->format('Y-m-d'),
                    'selasa' => $selasa->modify('+1 day')->format('Y-m-d'),
                    'rabu' => $rabu->modify('+2 days')->format('Y-m-d'),
                    'kamis' => $kamis->modify('+3 days')->format('Y-m-d'),
                    'jumat' => $jumat->modify('+4 days')->format('Y-m-d'),
                ];
            }

            // Get hari libur untuk periode ini
            $hariLibur = $this->getHariLiburDalamPeriode($pengajuan->tgl_awal, $pengajuan->tgl_akhir);

            // Update each employee's attendance
            foreach ($attendance as $nip => $days) {
                // Update pengajuan_wao_detail (existing table)
                // Jika hari libur, simpan null. Jika tidak, simpan true/false
                DB::table('pengajuan_wao_detail')
                    ->where('pengajuan_id', $id)
                    ->where('nip', $nip)
                    ->update([
                        'senin' => $hariLibur['senin'] ? null : (isset($days['senin']) ? (bool)$days['senin'] : false),
                        'selasa' => $hariLibur['selasa'] ? null : (isset($days['selasa']) ? (bool)$days['selasa'] : false),
                        'rabu' => $hariLibur['rabu'] ? null : (isset($days['rabu']) ? (bool)$days['rabu'] : false),
                        'kamis' => $hariLibur['kamis'] ? null : (isset($days['kamis']) ? (bool)$days['kamis'] : false),
                        'jumat' => $hariLibur['jumat'] ? null : (isset($days['jumat']) ? (bool)$days['jumat'] : false),
                    ]);
                
                // Save to pengajuan_wao_detail_tanggal (new table with dates)
                // Jika hari libur, simpan null sebagai status
                if ($tglAwal) {
                    foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat'] as $day) {
                        // Jika hari libur, status = null. Jika tidak, 1 = WFO, 0 = WFA
                        $status = $hariLibur[$day] ? null : (isset($days[$day]) && $days[$day] ? 1 : 0);
                        $tanggal = $dayDates[$day];
                        
                        // Upsert: update if exists, insert if not
                        $existing = DB::table('pengajuan_wao_detail_tanggal')
                            ->where('pengajuan_id', $id)
                            ->where('nip', $nip)
                            ->where('tanggal', $tanggal)
                            ->first();
                        
                        if ($existing) {
                            DB::table('pengajuan_wao_detail_tanggal')
                                ->where('id', $existing->id)
                                ->update(['status' => $status]);
                        } else {
                            DB::table('pengajuan_wao_detail_tanggal')->insert([
                                'pengajuan_id' => $id,
                                'nip' => $nip,
                                'tanggal' => $tanggal,
                                'status' => $status, // 1 = WFO, 0 = WFA, null = Libur
                            ]);
                        }
                    }
                }
            }
            
            // Update status pengajuan_wao
            // Jika save_final = true, ubah ke 'final'
            // Jika tidak, tetap 'draft' (untuk auto-save)
            if ($isFinal) {
                DB::table('pengajuan_wao')
                    ->where('id', $id)
                    ->update(['status' => 'final']);

                // Expired magic token jika ada (setelah save permanen, link tidak bisa edit lagi)
                $magicTokenId = session('magic_token_id');
                if ($magicTokenId) {
                    DB::table('magic_tokens')
                        ->where('id', $magicTokenId)
                        ->update(['is_used' => true]);
                    
                    // Hapus dari session
                    session()->forget('magic_token_id');
                }
            }

            DB::commit();

            // Response berbeda untuk draft vs final
            if ($isFinal) {
                // Redirect ke view mode (bukan edit mode) setelah simpan permanen
                return redirect()->route('pengajuan.show')->with('success', 'Pengajuan berhasil disimpan.');
            } else {
                // Untuk draft/auto-save, tetap di halaman edit
                return redirect()->route('pengajuan.edit')->with('success', 'Progress berhasil disimpan sementara. Anda masih bisa mengedit sampai "Simpan Permanen".');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Save draft via AJAX (auto-save)
     * Menyimpan progress tanpa mengubah status menjadi final
     */
    public function saveDraft(Request $request)
    {
        $id = session('pengajuan_id');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Session expired'], 401);
        }

        try {
            DB::beginTransaction();

            $pengajuan = DB::table('pengajuan_wao as pw')
                ->leftJoin('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
                ->where('pw.id', $id)
                ->select(['pw.*', 'kk.tgl_awal', 'kk.tgl_akhir'])
                ->first();
                
            if (!$pengajuan) {
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan'], 404);
            }

            // Cek akses
            $user = Auth::user();
            $isAdminOrVP = $this->isAdminOrVP();
            
            if (!$isAdminOrVP && $pengajuan->biro_id !== $user->biro_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            if (!$isAdminOrVP && strtolower($pengajuan->status ?? '') === 'final') {
                return response()->json(['success' => false, 'message' => 'Pengajuan sudah final'], 403);
            }

            $attendance = $request->input('attendance', []);
            
            // Calculate dates for each day
            $tglAwal = $pengajuan->tgl_awal ? new \DateTime($pengajuan->tgl_awal) : null;
            $dayDates = [];
            if ($tglAwal) {
                $senin = clone $tglAwal;
                $selasa = clone $tglAwal;
                $rabu = clone $tglAwal;
                $kamis = clone $tglAwal;
                $jumat = clone $tglAwal;
                
                $dayDates = [
                    'senin' => $senin->format('Y-m-d'),
                    'selasa' => $selasa->modify('+1 day')->format('Y-m-d'),
                    'rabu' => $rabu->modify('+2 days')->format('Y-m-d'),
                    'kamis' => $kamis->modify('+3 days')->format('Y-m-d'),
                    'jumat' => $jumat->modify('+4 days')->format('Y-m-d'),
                ];
            }

            $hariLibur = $this->getHariLiburDalamPeriode($pengajuan->tgl_awal, $pengajuan->tgl_akhir);

            // Update each employee's attendance
            foreach ($attendance as $nip => $days) {
                DB::table('pengajuan_wao_detail')
                    ->where('pengajuan_id', $id)
                    ->where('nip', $nip)
                    ->update([
                        'senin' => $hariLibur['senin'] ? null : (isset($days['senin']) ? (bool)$days['senin'] : false),
                        'selasa' => $hariLibur['selasa'] ? null : (isset($days['selasa']) ? (bool)$days['selasa'] : false),
                        'rabu' => $hariLibur['rabu'] ? null : (isset($days['rabu']) ? (bool)$days['rabu'] : false),
                        'kamis' => $hariLibur['kamis'] ? null : (isset($days['kamis']) ? (bool)$days['kamis'] : false),
                        'jumat' => $hariLibur['jumat'] ? null : (isset($days['jumat']) ? (bool)$days['jumat'] : false),
                    ]);
                
                if ($tglAwal) {
                    foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat'] as $day) {
                        $status = $hariLibur[$day] ? null : (isset($days[$day]) && $days[$day] ? 1 : 0);
                        $tanggal = $dayDates[$day];
                        
                        $existing = DB::table('pengajuan_wao_detail_tanggal')
                            ->where('pengajuan_id', $id)
                            ->where('nip', $nip)
                            ->where('tanggal', $tanggal)
                            ->first();
                        
                        if ($existing) {
                            DB::table('pengajuan_wao_detail_tanggal')
                                ->where('id', $existing->id)
                                ->update(['status' => $status]);
                        } else {
                            DB::table('pengajuan_wao_detail_tanggal')->insert([
                                'pengajuan_id' => $id,
                                'nip' => $nip,
                                'tanggal' => $tanggal,
                                'status' => $status,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Progress tersimpan',
                'saved_at' => now()->format('H:i')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get hari yang libur dalam periode pengajuan
     * 
     * @param string|null $tglAwal Tanggal awal periode (format: Y-m-d)
     * @param string|null $tglAkhir Tanggal akhir periode (format: Y-m-d)
     * @return array Associative array ['senin' => bool, 'selasa' => bool, ...]
     */
    private function getHariLiburDalamPeriode(?string $tglAwal, ?string $tglAkhir): array
    {
        // Default: tidak ada hari yang libur
        $hariLibur = [
            'senin' => false,
            'selasa' => false,
            'rabu' => false,
            'kamis' => false,
            'jumat' => false,
        ];

        if (!$tglAwal || !$tglAkhir) {
            return $hariLibur;
        }

        // Ambil semua tanggal libur dari database
        $tanggalLibur = DB::table('kalender_libur')
            ->pluck('tanggal')
            ->map(function ($tgl) {
                return $tgl instanceof \DateTime ? $tgl->format('Y-m-d') : $tgl;
            })
            ->toArray();

        if (empty($tanggalLibur)) {
            return $hariLibur;
        }

        // Hitung tanggal untuk setiap hari kerja dalam periode
        // Asumsi: tgl_awal adalah Senin, periode 5 hari kerja (Senin-Jumat)
        $startDate = new \DateTime($tglAwal);
        $dayOfWeek = (int) $startDate->format('N'); // 1=Monday, 7=Sunday
        
        // Jika start date bukan Senin, cari Senin terdekat
        if ($dayOfWeek !== 1) {
            $startDate->modify('last monday');
        }

        // Map hari ke tanggal
        $dayNames = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $dayDates = [];
        
        foreach ($dayNames as $index => $day) {
            $currentDate = clone $startDate;
            $currentDate->modify("+{$index} days");
            $dayDates[$day] = $currentDate->format('Y-m-d');
        }

        // Cek apakah tanggal tersebut libur
        foreach ($dayNames as $day) {
            if (in_array($dayDates[$day], $tanggalLibur)) {
                $hariLibur[$day] = true;
            }
        }

        return $hariLibur;
    }

    /**
     * Cek apakah periode pengajuan bisa diedit
     * Periode hanya bisa diedit jika tanggal hari ini berada dalam rentang tgl_awal - tgl_akhir
     * 
     * @param string|null $tglAwal Tanggal awal periode (format: Y-m-d)
     * @param string|null $tglAkhir Tanggal akhir periode (format: Y-m-d)
     * @return bool
     */
    private function isPeriodeEditable(?string $tglAwal, ?string $tglAkhir): bool
    {
        if (!$tglAwal || !$tglAkhir) {
            return false;
        }

        $today = new \DateTime('today');
        $startDate = new \DateTime($tglAwal);
        $endDate = new \DateTime($tglAkhir);

        // Tanggal hari ini harus >= tgl_awal DAN <= tgl_akhir
        return $today >= $startDate && $today <= $endDate;
    }

    /**
     * Broadcast ulang notifikasi WhatsApp ke biro tertentu
     */
    public function broadcast(Request $request)
    {
        $this->ensureAdminOrVP();

        $id = $request->input('id');
        if (!$id) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
        }

        $pengajuan = DB::table('pengajuan_wao')->where('id', $id)->first();
        if (!$pengajuan) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak ditemukan');
        }

        try {
            $waService = new \App\Services\WhatsAppNotificationService();
            $result = $waService->sendNotificationForPengajuan($pengajuan->id);
            
            if ($result) {
                $biroName = DB::table('biro')->where('id', $pengajuan->biro_id)->value('biro_name') ?? 'Biro';
                return redirect()->route('pengajuan.index')->with('status', "Notifikasi berhasil dikirim ke {$biroName}");
            } else {
                return redirect()->route('pengajuan.index')->with('error', 'Gagal mengirim notifikasi (tidak ada penerima)');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcast error: ' . $e->getMessage());
            return redirect()->route('pengajuan.index')->with('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Broadcast notifikasi WhatsApp ke multiple biro sekaligus
     */
    public function broadcastMultiple(Request $request)
    {
        $this->ensureAdminOrVP();

        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('pengajuan.index')->with('error', 'Tidak ada biro yang dipilih');
        }

        $successCount = 0;
        $failCount = 0;
        $successBiro = [];

        try {
            $waService = new \App\Services\WhatsAppNotificationService();
            
            foreach ($ids as $id) {
                $pengajuan = DB::table('pengajuan_wao')->where('id', $id)->first();
                if (!$pengajuan) {
                    continue;
                }

                $waService->sendNotificationForPengajuan($pengajuan->id);
            }

            return redirect()->route('pengajuan.index')->with('status', 'Broadcast berhasil dikirim');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcast multiple error: ' . $e->getMessage());
            return redirect()->route('pengajuan.index')->with('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }
}
