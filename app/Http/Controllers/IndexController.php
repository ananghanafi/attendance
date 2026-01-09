<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    /**
     * Menampilkan halaman index.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Panggil API untuk kirim pesan (simulasi)
        $this->example_api();
    }

    /**
     * Kirim pesan WhatsApp ke API eksternal menggunakan JWT.
     *
     * @return void
     */
    public function example_api()
    {
        $key = env('JWT_TOKEN_ENCRYPTION');
        $codeName = env('WHATSAPP_CODE_NAME');
        $endPoint = env('WHATSAPP_URL');

        // Data penerima (bisa diambil dari DB)
        $recipients = [
            [
                "MSISDN"    => "6281000000000",
                "NAMA"      => "ROMA 01",
                "isMedia"   => false,
                "typeMedia" => "text",
                "urlMedia"  => ""
            ],
            // [
            //     "MSISDN"    => "628000006660",
            //     "NAMA"      => "ROMA 02",
            //     "isMedia"   => true,
            //     "typeMedia" => "image",
            //     "urlMedia"  => "https://website.com/upload/image.jpg"
            // ],
            // [
            //     "MSISDN"    => "628000006661",
            //     "NAMA"      => "ROMA 03",
            //     "isMedia"   => true,
            //     "typeMedia" => "document",
            //     "urlMedia"  => "https://website.com/upload/document.pdf"
            // ],
            // [
            //     "MSISDN"    => "628000006662",
            //     "NAMA"      => "ROMA 04",
            //     "isMedia"   => true,
            //     "typeMedia" => "video",
            //     "urlMedia"  => "https://website.com/upload/video.mp4"
            // ],
            // [
            //     "MSISDN"    => "628000006663",
            //     "NAMA"      => "ROMA 05",
            //     "isMedia"   => "",
            //     "typeMedia" => "",
            //     "urlMedia"  => ""
            // ],
        ];

        // Data template pesan
        $data = [
            'company' => 'PT. Roma Teknologi',
            'position' => 'Backend Developer',
            'divisi' => 'IT Development'
        ];

        // Render template pesan sekali
        $message = view('template-wa', $data)->render();

        // Susun pesan untuk setiap penerima (gunakan array_map untuk efisiensi)
        $messageList = array_map(fn($r) => [
            "number"    => $r['MSISDN'],
            "message"   => $message,
            "isMedia"   => $r['isMedia'],
            "typeMedia" => $r['typeMedia'],
            "urlMedia"  => $r['urlMedia']
        ], $recipients);

        // Encode pesan ke JSON
        $data_pesan = json_encode(['messageList' => $messageList]);

        // Buat JWT token
        $access_token = $this->token_request($codeName, $key);

        // Kirim request ke API eksternal
        $response = $this->jwt_request(
            $endPoint,
            ['data' => $data_pesan],
            $access_token
        );

        echo $response;
    }

    /**
     * Membuat JWT access token untuk autentikasi API.
     *
     * @param string $codeName
     * @param string $key
     * @return string
     */
    private function token_request($codeName, $key)
    {
        $token = [
            'iat'     => time(),
            'jti'     => bin2hex(random_bytes(8)),
            'userWeb' => $codeName,
        ];
        return \Firebase\JWT\JWT::encode($token, $key, 'HS256');
    }

    /**
     * Kirim request POST ke API eksternal dengan JWT Authorization.
     *
     * @param string $url
     * @param array $post
     * @param string|false $access_token
     * @return mixed
     */
    private function jwt_request($url, $post = [], $access_token = false)
    {
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        if ($access_token) {
            $headers[] = 'Authorization: Bearer ' . $access_token;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($post),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return json_encode(['error' => $error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        curl_close($ch);
        return $result;
    }
}
