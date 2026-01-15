<?php

namespace App\Services;

use App\Models\MagicToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Kirim notifikasi pengajuan WFO ke semua user dengan is_kirim = true
     *
     * @param int $kalenderKerjaId ID dari kalender kerja yang baru dibuat
     * @param string $kalenderString Format "minggu-bulan-tahun"
     * @param int $minggu Minggu ke-
     * @param int $bulan Bulan (1-12)
     * @param int $tahun Tahun
     * @return array Hasil pengiriman
     */
    public function sendPengajuanWfoNotification(
        int $kalenderKerjaId,
        string $kalenderString,
        int $minggu,
        int $bulan,
        int $tahun
    ): array {
        // Ambil semua user yang is_kirim = true dan punya nomor telp
        $users = User::where('is_kirim', true)
            ->whereNotNull('telp')
            ->where('telp', '!=', '')
            ->where('isdel', false)
            ->get();

        if ($users->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada user dengan is_kirim = true yang memiliki nomor telepon.',
                'sent_count' => 0,
            ];
        }

        $recipients = [];
        $namaBulan = $this->getNamaBulan($bulan);

        foreach ($users as $user) {
            // Generate magic token untuk user ini
            $magicToken = MagicToken::generateForUser(
                $user->id,
                $kalenderKerjaId,
                $kalenderString
            );

            $magicLink = url('/pengajuan-wfo/' . $magicToken->raw_token);

            // Ambil nama biro
            $biro = DB::table('biro')
                ->where('id', $user->biro_id)
                ->value('biro_name') ?? 'Unknown';

            // Render template pesan
            $message = view('templates.wa.pengajuan-wfo', [
                'nama' => $user->nama ?? 'User',
                'biro' => $biro,
                'minggu' => $minggu,
                'bulan' => $namaBulan,
                'tahun' => $tahun,
                'magic_link' => $magicLink,
            ])->render();

            // Format nomor telepon (pastikan format Indonesia)
            $phoneNumber = $this->formatPhoneNumber($user->telp);

            $recipients[] = [
                'number' => $phoneNumber,
                'message' => $message,
                'isMedia' => false,
                'typeMedia' => 'text',
                'urlMedia' => '',
            ];
        }

        // Kirim ke API WhatsApp
        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim.',
            'sent_count' => count($recipients),
            'response' => $response,
        ];
    }

    /**
     * Format nomor telepon ke format Indonesia (62xxx)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak dimulai dengan 62, tambahkan 62
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Konversi nomor bulan ke nama bulan
     */
    private function getNamaBulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? 'Unknown';
    }

    /**
     * Kirim request ke API WhatsApp eksternal
     */
    private function sendToWhatsAppApi(array $recipients): mixed
    {
        $key = env('JWT_TOKEN_ENCRYPTION');
        $codeName = env('WHATSAPP_CODE_NAME');
        $endPoint = env('WHATSAPP_URL');

        if (empty($key) || empty($codeName) || empty($endPoint)) {
            Log::warning('WhatsApp API credentials not configured. Skipping notification.');
            return [
                'status' => 'skipped',
                'message' => 'WhatsApp API credentials not configured.',
            ];
        }

        $data_pesan = json_encode(['messageList' => $recipients]);

        $access_token = $this->createJwtToken($codeName, $key);

        return $this->sendJwtRequest(
            $endPoint,
            ['data' => $data_pesan],
            $access_token
        );
    }

    /**
     * Membuat JWT access token untuk autentikasi API
     */
    private function createJwtToken(string $codeName, string $key): string
    {
        $token = [
            'iat' => time(),
            'jti' => bin2hex(random_bytes(8)),
            'userWeb' => $codeName,
        ];
        return \Firebase\JWT\JWT::encode($token, $key, 'HS256');
    }

    /**
     * Kirim request POST ke API eksternal dengan JWT Authorization
     */
    private function sendJwtRequest(string $url, array $post = [], string|false $access_token = false): mixed
    {
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        if ($access_token) {
            $headers[] = 'Authorization: Bearer ' . $access_token;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error('WhatsApp API Error: ' . $error);
            return json_encode(['error' => $error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        curl_close($ch);

        // Log response untuk debugging
        Log::info('WhatsApp API Response: ' . $result);

        return json_decode($result, true) ?? $result;
    }

    /**
     * kirim notif tertentu berdasarkan pengajuan ID
     * kirim ke user is_kirim = true
     *
     * @param int $pengajuanId ID dari pengajuan_wao
     * @return bool True jika berhasil kirim ke setidaknya 1 user
     */
    public function sendNotificationForPengajuan(int $pengajuanId): bool
    {
        // Ambil data pengajuan dengan info kalender
        $pengajuan = DB::table('pengajuan_wao as pw')
            ->join('kalender_kerja_v2 as kk', 'pw.kalender', '=', 'kk.kalender')
            ->where('pw.id', $pengajuanId)
            ->select([
                'pw.id',
                'pw.biro_id',
                'pw.kalender',
                'kk.id as kalender_id',
                'kk.tgl_awal',
                'kk.tgl_akhir',
                'kk.persentase',
                'kk.persentase_wfa'
            ])
            ->first();

        if (!$pengajuan) {
            Log::warning("Pengajuan not found: {$pengajuanId}");
            return false;
        }

        // Parse kalender string (format: "minggu-bulan-tahun")
        $parts = explode('-', $pengajuan->kalender);
        if (count($parts) !== 3) {
            Log::warning("Invalid kalender format: {$pengajuan->kalender}");
            return false;
        }

        $minggu = (int) $parts[0];
        $bulan = (int) $parts[1];
        $tahun = (int) $parts[2];

        // Ambil user dari biro yang sama, dengan is_kirim = true
        $users = User::where('biro_id', $pengajuan->biro_id)
            ->where('is_kirim', true)
            ->whereNotNull('telp')
            ->where('telp', '!=', '')
            ->where('isdel', false)
            ->get();

        if ($users->isEmpty()) {
            Log::info("No users with is_kirim=true found for biro_id: {$pengajuan->biro_id}");
            return false;
        }

        $recipients = [];
        $namaBulan = $this->getNamaBulan($bulan);

        // Ambil nama biro
        $biroName = DB::table('biro')
            ->where('id', $pengajuan->biro_id)
            ->value('biro_name') ?? 'Unknown';

        foreach ($users as $user) {
            // Generate magic token untuk user ini
            $magicToken = MagicToken::generateForUser(
                $user->id,
                $pengajuan->kalender_id,
                $pengajuan->kalender
            );

            // Buat magic link dengan format /pengajuan-wfo/{token}
            $magicLink = url('/pengajuan-wfo/' . $magicToken->raw_token);

            // Render template pesan
            $message = view('templates.wa.pengajuan-wfo', [
                'nama' => $user->nama ?? 'User',
                'biro' => $biroName,
                'minggu' => $minggu,
                'bulan' => $namaBulan,
                'tahun' => $tahun,
                'magic_link' => $magicLink,
            ])->render();

            // Format nomor telepon (pastikan format Indonesia)
            $phoneNumber = $this->formatPhoneNumber($user->telp);

            $recipients[] = [
                'number' => $phoneNumber,
                'message' => $message,
                'isMedia' => false,
                'typeMedia' => 'text',
                'urlMedia' => '',
            ];
        }

        // Kirim ke API WhatsApp
        $response = $this->sendToWhatsAppApi($recipients);

        Log::info("Broadcast sent to {$biroName}: " . count($recipients) . " users");

        return count($recipients) > 0;
    }

    /**
     * Kirim notifikasi approval izin ke atasan
     *
     * @param array $data Data izin
     * @param string $token Token approval
     * @return array Hasil pengiriman
     */
    public function sendApprovalIzinToAtasan(array $data): array
    {
        // Ambil data atasan
        $atasan = DB::table('users')
            ->where('nip', $data['nip_atasan'])
            ->first();

        if (!$atasan || empty($atasan->telp)) {
            return [
                'success' => false,
                'message' => 'Atasan tidak ditemukan atau tidak punya nomor telepon.',
            ];
        }

        // Render template pesan dari blade
        $message = view('templates.wa.approval-izin', [
            'status' => $data['status'],
            'nama_pengaju' => $data['nama_pengaju'],
            'from' => $data['from'],
            'to' => $data['to'],
            'alasan' => $data['alasan'],
            'expired_date' => date('d-m-Y', strtotime($data['expired_at'])),
            'approval_link' => url('/absen/approval/' . $data['token']),
        ])->render();

        $phoneNumber = $this->formatPhoneNumber($atasan->telp);

        $recipients = [[
            'number' => $phoneNumber,
            'message' => $message,
            'isMedia' => false,
            'typeMedia' => 'text',
            'urlMedia' => '',
        ]];

        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Notifikasi approval izin berhasil dikirim ke atasan.',
            'response' => $response,
        ];
    }

    /**
     * Kirim notifikasi approval absen pulang telat ke atasan
     *
     * @param array $data Data absen pulang
     * @return array Hasil pengiriman
     */
    public function sendApprovalPulangToAtasan(array $data): array
    {
        // Ambil data atasan
        $atasan = DB::table('users')
            ->where('nip', $data['nip_atasan'])
            ->first();

        if (!$atasan || empty($atasan->telp)) {
            return [
                'success' => false,
                'message' => 'Atasan tidak ditemukan atau tidak punya nomor telepon.',
            ];
        }

        // Render template pesan dari blade
        $message = view('templates.wa.approval-pulang', [
            'nama_atasan' => $atasan->nama ?? 'ATASAN',
            'nama_pengaju' => $data['nama_pengaju'],
            'alasan' => $data['alasan'],
            'expired_date' => date('d-m-Y', strtotime($data['expired_at'])),
            'approval_link' => url('/absen/approval/' . $data['token']),
        ])->render();

        $phoneNumber = $this->formatPhoneNumber($atasan->telp);

        $recipients = [[
            'number' => $phoneNumber,
            'message' => $message,
            'isMedia' => false,
            'typeMedia' => 'text',
            'urlMedia' => '',
        ]];

        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Notifikasi approval absen pulang berhasil dikirim ke atasan.',
            'response' => $response,
        ];
    }

    /**
     * Kirim notifikasi hasil approval izin ke user
     *
     * @param array $data Data hasil approval
     * @return array Hasil pengiriman
     */
    public function sendApprovalResultIzinToUser(array $data): array
    {
        // Ambil data user
        $user = DB::table('users')
            ->where('nip', $data['nip_user'])
            ->first();

        if (!$user || empty($user->telp)) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan atau tidak punya nomor telepon.',
            ];
        }

        // Render template pesan dari blade
        $message = view('templates.wa.result-izin', [
            'nama_user' => $data['nama_user'],
            'status' => $data['status'],
            'from' => $data['from'],
            'to' => $data['to'],
            'alasan' => $data['alasan'],
            'result' => $data['result'],
            'processed_by' => $data['processed_by'],
        ])->render();

        $phoneNumber = $this->formatPhoneNumber($user->telp);

        $recipients = [[
            'number' => $phoneNumber,
            'message' => $message,
            'isMedia' => false,
            'typeMedia' => 'text',
            'urlMedia' => '',
        ]];

        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Notifikasi hasil approval izin berhasil dikirim ke user.',
            'response' => $response,
        ];
    }

    /**
     * Kirim notifikasi hasil approval absen pulang telat ke user
     *
     * @param array $data Data hasil approval
     * @return array Hasil pengiriman
     */
    public function sendApprovalResultPulangToUser(array $data): array
    {
        // Ambil data user
        $user = DB::table('users')
            ->where('nip', $data['nip_user'])
            ->first();

        if (!$user || empty($user->telp)) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan atau tidak punya nomor telepon.',
            ];
        }

        // Render template pesan dari blade
        $message = view('templates.wa.result-pulang', [
            'nama_user' => $data['nama_user'],
            'alasan' => $data['alasan'],
            'result' => $data['result'],
            'processed_by' => $data['processed_by'],
        ])->render();

        $phoneNumber = $this->formatPhoneNumber($user->telp);

        $recipients = [[
            'number' => $phoneNumber,
            'message' => $message,
            'isMedia' => false,
            'typeMedia' => 'text',
            'urlMedia' => '',
        ]];

        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Notifikasi hasil approval absen pulang berhasil dikirim ke user.',
            'response' => $response,
        ];
    }

    /**
     * Kirim pesan raw/manual ke nomor telepon tertentu
     */
    public function sendRawMessage(string $phoneNumber, string $message): array
    {
        // Format nomor telepon
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak valid.',
            ];
        }

        // Siapkan data pesan - format sama seperti method lain yang sudah working
        $recipients = [
            [
                'number' => $formattedPhone,
                'message' => $message,
                'isMedia' => false,
                'typeMedia' => 'text',
                'urlMedia' => '',
            ]
        ];

        // Kirim ke API
        $response = $this->sendToWhatsAppApi($recipients);

        return [
            'success' => true,
            'message' => 'Pesan berhasil dikirim.',
            'response' => $response,
        ];
    }
}
