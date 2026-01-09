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

            // Buat magic link
            $magicLink = url('/magic-login/' . $magicToken->raw_token);

            // Ambil nama biro
            $biro = DB::table('biro')
                ->where('id', $user->biro_id)
                ->value('biro_name') ?? 'Unknown';

            // Render template pesan
            $message = view('template-wa-pengajuan-wfo', [
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

        // Jika env tidak di-set, skip pengiriman
        if (empty($key) || empty($codeName) || empty($endPoint)) {
            Log::warning('WhatsApp API credentials not configured. Skipping notification.');
            return [
                'status' => 'skipped',
                'message' => 'WhatsApp API credentials not configured.',
            ];
        }

        // Encode pesan ke JSON
        $data_pesan = json_encode(['messageList' => $recipients]);

        // Buat JWT token
        $access_token = $this->createJwtToken($codeName, $key);

        // Kirim request
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
}
