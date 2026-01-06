<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absen', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nip', 255)->nullable();
            $table->date('tanggal')->nullable();

            $table->string('scan_masuk', 255)->nullable();
            $table->string('scan_pulang', 255)->nullable();
            $table->string('terlambat', 255)->nullable();
            $table->string('biro_name', 255)->nullable();

            $table->integer('minggu')->nullable();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();

            $table->boolean('is_confirm')->nullable()->default(false);
            $table->boolean('is_libur')->nullable()->default(false);

            $table->dateTime('approved_date')->nullable();

            $table->string('scan_masuk_awal', 255)->nullable();

            $table->boolean('is_koreksi')->nullable()->default(false);
            $table->boolean('is_scan')->nullable()->default(false);

            $table->string('ip', 255)->nullable();
            $table->string('status_izin', 255)->nullable();
            $table->integer('izin_id')->nullable();

            $table->integer('wfa')->nullable();
            $table->string('wfa_detail', 255)->nullable();
            $table->text('wfa_alasan')->nullable();

            $table->string('jam_temp', 255)->nullable();
            $table->text('alasan_pulang')->nullable();
        });

        // Set Postgres sequence so the next inserted id equals 7822583 (matching MySQL AUTO_INCREMENT = 7822583)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('absen', 'id'), 7822583, false);");
            }
        } catch (\Throwable $e) {
            // ignore if DB not available or not pgsql
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen');
    }
};
