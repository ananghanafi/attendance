<?php

/**
 * =============================================
 * FILE KONFIGURASI TESTING ABSEN (BY CASE)
 * =============================================
 * 
 * Ubah nilai di bawah ini untuk testing berbagai skenario
 * 
 * Setelah fitur GPS siap, file ini tidak digunakan lagi
 */

return [
    /**
     * Jam sekarang (format 24 jam)
     * 
     * Contoh:
     * - '08:30' = Jam 8 pagi 30 menit (waktu absen masuk)
     * - '16:20' = Jam 4 sore 20 menit (waktu absen masuk)
     * - '17:30' = Jam 5 sore 30 menit (waktu absen pulang)
     * - '20:00' = Jam 8 malam (waktu absen pulang)
     * 
     * Set null untuk menggunakan jam real dari server
     */
    'jam' => '08:30', // Contoh: '16:20' atau null untuk jam real

    /**
     * Posisi user
     * 
     * Nilai:
     * - 'kantor' = User berada di dalam kantor
     * - 'luar'   = User berada di luar kantor
     */
    'posisi' => 'luar', // 'kantor' atau 'luar'
];
