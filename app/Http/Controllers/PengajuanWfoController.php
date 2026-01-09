<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanWfoController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        if ($role !== 'admin' && $role !== 'ADMIN') {
            abort(403);
        }
    }

    /**
     * Dashboard pengajuan WFO - List per biro dengan filter
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        // Filter parameters
        $search = $request->query('search', '');
        $minggu = $request->query('minggu', '');
        $bulan = $request->query('bulan', '');
        $tahun = $request->query('tahun', '');

        // Get dropdown data for filters - hanya biro yang is_proyek = false (bukan proyek)
        $biros = DB::table('biro')
            ->where('is_proyek', false)
            ->orderBy('biro_name')
            ->get(['id', 'biro_name']);

        // Query: Show ALL pengajuan (semua biro x semua minggu)
        // JOIN via kolom kalender yang sama format "minggu-bulan-tahun"
        $query = DB::table('pengajuan_wao as pw')
            ->join('biro as b', 'pw.biro_id', '=', 'b.id')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender') // JOIN langsung via format string
            ->where('b.is_proyek', false)  // Filter hanya biro non-proyek
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
                'kk.persentase_decimal'
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

        // Pagination - order by kalender desc, then biro name
        $pengajuans = $query
            ->orderBy('kk.tgl_awal', 'desc')
            ->orderBy('b.biro_name')
            ->paginate(10)
            ->withQueryString();

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
        ]);
    }

    /**
     * Set session untuk view pengajuan
     */
    public function setView(Request $request)
    {
        $this->ensureAdmin();
        session(['pengajuan_id' => $request->input('id')]);
        return redirect()->route('pengajuan.show');
    }

    /**
     * View detail pengajuan (untuk lihat absensi) - read only
     */
    public function show()
    {
        $this->ensureAdmin();

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

        return view('admin.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'details' => $details,
            'readOnly' => true, // View mode - radio buttons disabled
        ]);
    }

    /**
     * Set session untuk edit pengajuan
     */
    public function setEdit(Request $request)
    {
        $this->ensureAdmin();
        session(['pengajuan_id' => $request->input('id')]);
        return redirect()->route('pengajuan.edit');
    }

    /**
     * Edit pengajuan (form untuk edit WFO/WFA)
     */
    public function edit()
    {
        $this->ensureAdmin();

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

        return view('admin.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'details' => $details,
            'readOnly' => false, // Edit mode - radio buttons enabled
        ]);
    }

    /**
     * Update pengajuan WFO/WFA per pegawai
     */
    public function update(Request $request)
    {
        $this->ensureAdmin();

        $id = session('pengajuan_id');
        if (!$id) {
            return redirect()->route('pengajuan.index')->with('error', 'Silakan pilih pengajuan dari daftar');
        }

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

            // Get attendance data from request
            $attendance = $request->input('attendance', []);
            
            // Get total pegawai
            $totalPegawai = count($attendance);
            $maxPersentase = $pengajuan->persentase_decimal ?? 100;
            
            // Validasi persentase WFO per hari tidak melebihi batas
            if ($totalPegawai > 0) {
                $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
                $dayNames = ['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat'];
                
                foreach ($days as $day) {
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

            // Update each employee's attendance
            foreach ($attendance as $nip => $days) {
                // Update pengajuan_wao_detail (existing table)
                DB::table('pengajuan_wao_detail')
                    ->where('pengajuan_id', $id)
                    ->where('nip', $nip)
                    ->update([
                        'senin' => isset($days['senin']) ? (bool)$days['senin'] : false,
                        'selasa' => isset($days['selasa']) ? (bool)$days['selasa'] : false,
                        'rabu' => isset($days['rabu']) ? (bool)$days['rabu'] : false,
                        'kamis' => isset($days['kamis']) ? (bool)$days['kamis'] : false,
                        'jumat' => isset($days['jumat']) ? (bool)$days['jumat'] : false,
                    ]);
                
                // Save to pengajuan_wao_detail_tanggal (new table with dates)
                if ($tglAwal) {
                    foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat'] as $day) {
                        $status = isset($days[$day]) && $days[$day] ? 1 : 0; // 1 = WFO, 0 = WFA
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
                                'status' => $status, // 1 = WFO, 0 = WFA
                            ]);
                        }
                    }
                }
            }
            
            // Update status pengajuan_wao dari 'draft' menjadi 'final' setelah edit
            DB::table('pengajuan_wao')
                ->where('id', $id)
                ->update(['status' => 'final']);

            DB::commit();

            return redirect()->route('pengajuan.edit')->with('success', 'Pengajuan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
