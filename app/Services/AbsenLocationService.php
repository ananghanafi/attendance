<?php

namespace App\Services;

/**
 * Service untuk mengelola logic lokasi & waktu absen
 * 
 * BY CASE VERSION - Baca dari file config/absen_test.php
 * 
 * Untuk testing, ubah nilai di config/absen_test.php:
 * - jam: '16:20' atau null untuk jam real
 * - posisi: 'kantor' atau 'luar'
 */
class AbsenLocationService
{
    private array $config;

    public function __construct()
    {
        // Load config dari file
        $this->config = config('absen_test', [
            'jam' => null,
            'posisi' => 'kantor',
        ]);
    }

    /**
     * Dapatkan jam sekarang (dari config atau real)
     * 
     * @return array ['hour' => int, 'minute' => int, 'formatted' => string]
     */
    public function getCurrentTime(): array
    {
        $jam = $this->config['jam'] ?? null;

        if ($jam !== null) {
            // Pakai jam dari config
            $parts = explode(':', $jam);
            $hour = (int) ($parts[0] ?? 0);
            $minute = (int) ($parts[1] ?? 0);
        } else {
            // Pakai jam real dari server
            $now = now();
            $hour = (int) $now->format('H');
            $minute = (int) $now->format('i');
            $jam = $now->format('H:i');
        }

        return [
            'hour' => $hour,
            'minute' => $minute,
            'formatted' => $jam,
        ];
    }

    /**
     * Cek apakah user berada di dalam kantor
     * 
     * @return bool True jika di kantor, False jika di luar
     */
    public function isInsideOffice(): bool
    {
        $posisi = $this->config['posisi'] ?? 'kantor';
        return $posisi === 'kantor';
    }

    /**
     * Dapatkan posisi user (string)
     * 
     * @return string 'kantor' atau 'luar'
     */
    public function getPosisi(): string
    {
        return $this->config['posisi'] ?? 'kantor';
    }

    /**
     * Tentukan tipe form yang harus ditampilkan
     * 
     * @param bool $isWfo True jika jadwal user adalah WFO
     * @return string 'masuk' atau 'izin'
     */
    public function getFormType(bool $isWfo): string
    {
        $isInsideOffice = $this->isInsideOffice();

        // WFO + Di Kantor = Formulir Masuk
        if ($isWfo && $isInsideOffice) {
            return 'masuk';
        }
        
        // Semua kondisi lain = Formulir Izin
        // - WFO + Di Luar Kantor
        // - WFA + Di Kantor
        // - WFA + Di Luar Kantor
        return 'izin';
    }

    /**
     * Tentukan opsi status yang tersedia
     * 
     * @param string $formType 'masuk' atau 'izin'
     * @param bool $isWfo True jika jadwal user adalah WFO
     * @return array Array opsi status
     */
    public function getStatusOptions(string $formType, bool $isWfo = true): array
    {
        $options = [
            [
                'value' => 'dinas', 
                'label' => 'Dinas',
                'icon' => '💼',
                'desc' => 'Hadir untuk bekerja / dinas'
            ],
            [
                'value' => 'sakit_izin', 
                'label' => 'Sakit/Izin',
                'icon' => '🏥',
                'desc' => 'Tidak hadir karena sakit atau keperluan lain'
            ],
        ];

        // Opsi WFA hanya muncul jika jadwal WFA (bukan WFO)
        if ($formType === 'izin' && !$isWfo) {
            $options[] = [
                'value' => 'wfa', 
                'label' => 'WFA',
                'icon' => '🏠',
                'desc' => 'Work From Anywhere'
            ];
        }

        return $options;
    }

    /**
     * Cek apakah waktu sekarang adalah waktu absen masuk
     * 
     * Waktu absen masuk: 04:00 - 16:59
     * 
     * @return bool
     */
    public function isAbsenMasukTime(): bool
    {
        $time = $this->getCurrentTime();
        $hour = $time['hour'];
        
        // 04:00 - 16:59
        return ($hour >= 4 && $hour < 17);
    }

    /**
     * Cek apakah waktu sekarang adalah waktu absen pulang
     * 
     * Waktu absen pulang: 17:00 - 23:59
     * 
     * @return bool
     */
    public function isAbsenPulangTime(): bool
    {
        $time = $this->getCurrentTime();
        $hour = $time['hour'];
        
        // 17:00 - 23:59
        return ($hour >= 17 && $hour <= 23);
    }

    /**
     * Dapatkan tipe button yang harus ditampilkan
     * 
     * @return string 'masuk', 'pulang', atau 'none'
     */
    public function getButtonType(): string
    {
        if ($this->isAbsenMasukTime()) {
            return 'masuk';
        }
        
        if ($this->isAbsenPulangTime()) {
            return 'pulang';
        }
        
        return 'none'; // Di luar jam absen (00:00 - 03:59)
    }

    /**
     * Dapatkan info lengkap untuk ditampilkan di view
     * 
     * @return array
     */
    public function getInfo(): array
    {
        $time = $this->getCurrentTime();
        
        return [
            'jam' => $time['formatted'],
            'posisi' => $this->getPosisi(),
            'posisi_label' => $this->isInsideOffice() ? 'Di Kantor' : 'Di Luar Kantor',
            'button_type' => $this->getButtonType(),
            'is_absen_masuk' => $this->isAbsenMasukTime(),
            'is_absen_pulang' => $this->isAbsenPulangTime(),
        ];
    }

    /**
     * Cek apakah absen pulang perlu approval (WFO tapi di luar kantor)
     * 
     * @param bool $isWfo True jika jadwal user adalah WFO
     * @return bool
     */
    public function needsPulangApproval(bool $isWfo): bool
    {
        // WFO tapi posisi di luar kantor = perlu approval
        return $isWfo && !$this->isInsideOffice();
    }

    /**
     * Dapatkan IP address user
     * 
     * @return string
     */
    public function getClientIp(): string
    {
        $ip = request()->ip();
        
        // Check for proxy headers
        if (request()->header('X-Forwarded-For')) {
            $ips = explode(',', request()->header('X-Forwarded-For'));
            $ip = trim($ips[0]);
        }
        
        return $ip ?? '0.0.0.0';
    }
}
