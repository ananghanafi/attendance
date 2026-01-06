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
        Schema::create('pengajuan_wao', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('biro_id')->nullable();

            $table->string('kalender', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('alasan', 255)->nullable();

            $table->dateTime('created_date')->nullable();
        });

        // Set Postgres sequence so the next inserted id equals 3629 (matching MySQL AUTO_INCREMENT = 3629)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('pengajuan_wao', 'id'), 3629, false);");
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
        Schema::dropIfExists('pengajuan_wao');
    }
};
