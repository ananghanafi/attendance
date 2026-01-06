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
        Schema::create('pengajuan_wao_detail', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pengajuan_id')->nullable();
            $table->string('nip', 255)->nullable();

            // days: tinyint(1) NULL DEFAULT 0 -> map to boolean nullable default false
            $table->boolean('senin')->nullable()->default(false);
            $table->boolean('selasa')->nullable()->default(false);
            $table->boolean('rabu')->nullable()->default(false);
            $table->boolean('kamis')->nullable()->default(false);
            $table->boolean('jumat')->nullable()->default(false);
        });

        // Set Postgres sequence so the next inserted id equals 79712 (matching MySQL AUTO_INCREMENT = 79712)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao_detail', 'id'), 79712, false);");
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
        Schema::dropIfExists('pengajuan_wao_detail');
    }
};
