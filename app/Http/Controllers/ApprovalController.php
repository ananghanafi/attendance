<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    /**
     * Generate token untuk approval
     */
    public static function generateToken(): string
    {
        return Str::random(40);
    }

    /**
     * Buat approval token untuk izin
     *
     * @param int $formulirIzinId
     * @param string $nipPengaju
     * @param string $nipAtasan
     * @return string Token yang dibuat
     */
    public static function createIzinApprovalToken(int $formulirIzinId, string $nipPengaju, string $nipAtasan): string
    {
        $token = self::generateToken();

        // Expired jam 23:59 hari ini
        $expiredAt = now()->setTime(23, 59, 59);

        DB::table('approval_tokens')->insert([
            'token' => $token,
            'type' => 'izin',
            'reference_id' => $formulirIzinId,
            'nip_pengaju' => $nipPengaju,
            'nip_atasan' => $nipAtasan,
            'expired_at' => $expiredAt,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $token;
    }

    /**
     * Buat approval token untuk absen pulang telat
     *
     * @param int $absenId
     * @param string $nipPengaju
     * @param string $nipAtasan
     * @return string Token yang dibuat
     */
    public static function createPulangApprovalToken(int $absenId, string $nipPengaju, string $nipAtasan): string
    {
        $token = self::generateToken();

        // Expired jam 23:59 hari ini
        $expiredAt = now()->setTime(23, 59, 59);

        DB::table('approval_tokens')->insert([
            'token' => $token,
            'type' => 'pulang',
            'reference_id' => $absenId,
            'nip_pengaju' => $nipPengaju,
            'nip_atasan' => $nipAtasan,
            'expired_at' => $expiredAt,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $token;
    }

    /**
     * Halaman approval (diakses via link WA)
     */
    public function show(string $token)
    {
        $approvalToken = DB::table('approval_tokens')
            ->where('token', $token)
            ->first();

        if (!$approvalToken) {
            return view('absen.approval-invalid', [
                'message' => 'Link tidak valid atau tidak ditemukan.',
            ]);
        }

        // Cek status token
        if ($approvalToken->status !== 'pending') {
            // Sudah diproses sebelumnya - ambil data detail
            if ($approvalToken->type === 'izin') {
                $data = $this->getIzinDetail($approvalToken);
            } else {
                $data = $this->getPulangDetail($approvalToken);
            }

            return view('absen.approval-success', [
                'token' => $approvalToken,
                'status' => $approvalToken->status,
                'processed_at' => $approvalToken->processed_at,
                'already_processed' => true,
                'type' => $approvalToken->type,
                'data' => $data,
            ]);
        }

        // Cek expired
        if (now()->gt($approvalToken->expired_at)) {
            // Update status ke expired
            DB::table('approval_tokens')
                ->where('id', $approvalToken->id)
                ->update(['status' => 'expired']);

            return view('absen.approval-invalid', [
                'message' => 'Link sudah expired. Batas waktu approval adalah 23:59 pada hari pengajuan.',
            ]);
        }

        // Ambil detail data berdasarkan type
        if ($approvalToken->type === 'izin') {
            $data = $this->getIzinDetail($approvalToken);
        } else {
            $data = $this->getPulangDetail($approvalToken);
        }

        return view('absen.approval-page', [
            'approval' => $approvalToken,
            'data' => $data,
        ]);
    }

    /**
     * Proses approval (approve/reject)
     */
    public function process(Request $request, string $token)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $approvalToken = DB::table('approval_tokens')
            ->where('token', $token)
            ->first();

        if (!$approvalToken) {
            return redirect()->route('absen.approval.show', $token)
                ->with('error', 'Token tidak valid.');
        }

        // Cek status
        if ($approvalToken->status !== 'pending') {
            return redirect()->route('absen.approval.show', $token);
        }

        // Cek expired
        if (now()->gt($approvalToken->expired_at)) {
            return redirect()->route('absen.approval.show', $token)
                ->with('error', 'Link sudah expired.');
        }

        $isApproved = $request->action === 'approve';
        $newStatus = $isApproved ? 'approved' : 'rejected';

        try {
            DB::beginTransaction();

            // Update token status
            DB::table('approval_tokens')
                ->where('id', $approvalToken->id)
                ->update([
                    'status' => $newStatus,
                    'processed_at' => now(),
                ]);

            // Update data asli berdasarkan type
            if ($approvalToken->type === 'izin') {
                $this->processIzinApproval($approvalToken, $isApproved);
            } else {
                $this->processPulangApproval($approvalToken, $isApproved);
            }

            DB::commit();

            // Kirim notifikasi WA ke user
            $this->sendResultNotification($approvalToken, $isApproved);

            return redirect()->route('absen.approval.show', $token);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('absen.approval.show', $token)
                ->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }

    /**
     * Get detail izin
     */
    private function getIzinDetail($token): array
    {
        $izin = DB::table('formulir_izin')
            ->where('id', $token->reference_id)
            ->first();

        $pengaju = DB::table('users')
            ->where('nip', $token->nip_pengaju)
            ->first();

        return [
            'type' => 'izin',
            'nama_pengaju' => $pengaju->nama ?? '-',
            'nip' => $token->nip_pengaju,
            'status' => $izin->status ?? '-',
            'from' => $izin->from ?? '-',
            'to' => $izin->to ?? '-',
            'alasan' => $izin->alasan ?? '-',
        ];
    }

    /**
     * Get detail absen pulang
     */
    private function getPulangDetail($token): array
    {
        $absen = DB::table('absen')
            ->where('id', $token->reference_id)
            ->first();

        $pengaju = DB::table('users')
            ->where('nip', $token->nip_pengaju)
            ->first();

        return [
            'type' => 'pulang',
            'nama_pengaju' => $pengaju->nama ?? '-',
            'nip' => $token->nip_pengaju,
            'tanggal' => $absen->tanggal ?? '-',
            'jam_pulang' => $absen->jam_temp ?? '-',
            'alasan_pulang' => $absen->alasan_pulang ?? '-',
        ];
    }

    /**
     * Proses approval izin
     */
    private function processIzinApproval($token, bool $isApproved): void
    {
        $updateData = [
            'tanggal_approve' => now()->format('Y-m-d'),
            'timestamp_approval' => now(),
        ];

        if ($isApproved) {
            $updateData['is_approval'] = 1;

            // Ambil data formulir izin untuk insert ke absen
            $izin = DB::table('formulir_izin')
                ->where('id', $token->reference_id)
                ->first();

            if ($izin) {
                // Ambil data user untuk biro_name
                $user = DB::table('users')
                    ->where('nip', $token->nip_pengaju)
                    ->first();

                $biroName = DB::table('biro')
                    ->where('id', $user->biro_id ?? null)
                    ->value('biro_name');

                // Insert absen untuk setiap tanggal dalam range
                $fromDate = \Carbon\Carbon::parse($izin->from);
                $toDate = \Carbon\Carbon::parse($izin->to);

                while ($fromDate->lte($toDate)) {
                    $tanggal = $fromDate->format('Y-m-d');
                    
                    // Cek apakah sudah ada absen untuk tanggal ini
                    $existingAbsen = DB::table('absen')
                        ->where('nip', $token->nip_pengaju)
                        ->where('tanggal', $tanggal)
                        ->first();

                    // Hitung minggu, bulan, tahun
                    $minggu = (int) ceil($fromDate->day / 7);
                    $bulan = (int) $fromDate->month;
                    $tahun = (int) $fromDate->year;

                    $absenData = [
                        'scan_masuk' => '08:00',
                        'scan_masuk_awal' => '08:00',
                        'scan_pulang' => '17:00',
                        'status_izin' => $izin->status,
                        'izin_id' => $izin->id,
                    ];

                    if ($existingAbsen) {
                        // Update existing
                        DB::table('absen')
                            ->where('id', $existingAbsen->id)
                            ->update($absenData);
                    } else {
                        // Insert new
                        $absenData = array_merge($absenData, [
                            'nip' => $token->nip_pengaju,
                            'tanggal' => $tanggal,
                            'biro_name' => $biroName,
                            'minggu' => $minggu,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                        ]);

                        DB::table('absen')->insert($absenData);
                    }

                    $fromDate->addDay();
                }
            }
        } else {
            $updateData['is_reject'] = 1;
            $updateData['timestamp_reject'] = now();
        }

        DB::table('formulir_izin')
            ->where('id', $token->reference_id)
            ->update($updateData);
    }

    /**
     * Proses approval absen pulang
     */
    private function processPulangApproval($token, bool $isApproved): void
    {
        $absen = DB::table('absen')
            ->where('id', $token->reference_id)
            ->first();

        if ($isApproved) {
            // Pindahkan jam_temp ke scan_pulang
            DB::table('absen')
                ->where('id', $token->reference_id)
                ->update([
                    'scan_pulang' => $absen->jam_temp,
                    'jam_temp' => null,
                    'approved_date' => now(),
                    'is_confirm' => true,
                ]);
        } else {
            // Hapus jam_temp (ditolak)
            DB::table('absen')
                ->where('id', $token->reference_id)
                ->update([
                    'jam_temp' => null,
                    'alasan_pulang' => null,
                ]);
        }
    }

    /**
     * Kirim notifikasi hasil approval ke user
     */
    private function sendResultNotification($token, bool $isApproved): void
    {
        $waService = new WhatsAppNotificationService();
        $user = DB::table('users')->where('nip', $token->nip_pengaju)->first();
        $atasan = DB::table('users')->where('nip', $token->nip_atasan)->first();

        if ($token->type === 'izin') {
            $izin = DB::table('formulir_izin')
                ->where('id', $token->reference_id)
                ->first();

            $waService->sendApprovalResultIzinToUser([
                'nip_user' => $token->nip_pengaju,
                'nama_user' => $user->nama ?? '-',
                'status' => $izin->status ?? '-',
                'alasan' => $izin->alasan ?? '-',
                'from' => $izin->from ?? '-',
                'to' => $izin->to ?? '-',
                'result' => $isApproved ? 'approved' : 'rejected',
                'processed_by' => $atasan->nama ?? 'Atasan',
            ]);
        } else {
            $absen = DB::table('absen')
                ->where('id', $token->reference_id)
                ->first();

            $waService->sendApprovalResultPulangToUser([
                'nip_user' => $token->nip_pengaju,
                'nama_user' => $user->nama ?? '-',
                'tanggal' => $absen->tanggal ?? '-',
                'jam_pulang' => $absen->scan_pulang ?? $absen->jam_temp ?? '-',
                'alasan' => $absen->alasan_pulang ?? '-',
                'result' => $isApproved ? 'approved' : 'rejected',
                'processed_by' => $atasan->nama ?? 'Atasan',
            ]);
        }
    }
}
