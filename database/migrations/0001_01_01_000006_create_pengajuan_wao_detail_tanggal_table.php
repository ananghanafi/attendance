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
        Schema::create('pengajuan_wao_detail_tanggal', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pengajuan_id')->nullable();
            $table->string('nip', 255)->nullable();
            $table->string('tanggal', 255)->nullable();

            // status is tinyint in MySQL; map to integer with default 0
            $table->integer('status')->nullable()->default(0);
        });

        // Set Postgres sequence so the next inserted id equals 379887 (matching MySQL AUTO_INCREMENT = 379887)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao_detail_tanggal', 'id'), 379887, false);");
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
        Schema::dropIfExists('pengajuan_wao_detail_tanggal');
    }
};
