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
        Schema::create('kalender_kerja_v2', function (Blueprint $table) {
            $table->id();

            $table->string('kalender', 255)->nullable();
            $table->date('tgl_awal')->nullable();
            $table->date('tgl_akhir')->nullable();

            $table->string('nomor_surat', 255)->nullable();
            $table->string('nomor_surat_gubernur', 255)->nullable();
            $table->string('periode', 255)->nullable();
            $table->string('tanggal', 255)->nullable();

            // persentase fields - using float; precision from original SQL is atypical so use default
            $table->float('persentase')->nullable();
            $table->boolean('active')->nullable()->default(false);
            $table->float('persentase_decimal')->nullable();

            $table->text('poin_1')->nullable();
            $table->text('poin_2')->nullable();
            $table->text('poin_3')->nullable();
            $table->text('poin_4')->nullable();
            $table->text('poin_5')->nullable();

            $table->string('judul', 255)->nullable();
        });

        // Reset sequence start for PostgreSQL so IDs begin from 1 in a fresh database.
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('kalender_kerja_v2', 'id'), 1, false);");
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
        Schema::dropIfExists('kalender_kerja_v2');
    }
};
