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
            $table->id();

            $table->integer('pengajuan_id')->nullable();
            $table->string('nip', 255)->nullable();
            $table->string('tanggal', 255)->nullable();

            // status is tinyint in MySQL; map to integer with default 0
            $table->integer('status')->nullable()->default(0);
        });

        // Reset sequence start for PostgreSQL so IDs begin from 1 in a fresh database.
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao_detail_tanggal', 'id'), 1, false);");
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
