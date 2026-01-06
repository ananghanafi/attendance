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
        Schema::create('biro', function (Blueprint $table) {
            // id as serial / auto-increment integer
            $table->increments('id');

            $table->string('biro_name', 255)->nullable();

            // original SQL: tinyint(1) NULL DEFAULT 1
            // map to boolean with nullable and default true to match behavior
            $table->boolean('is_proyek')->nullable()->default(true);

            $table->string('divisi', 100)->nullable();
        });

        // set Postgres sequence so the next inserted id equals 545 (matching MySQL AUTO_INCREMENT = 545)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('biro', 'id'), 545, false);");
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
        Schema::dropIfExists('biro');
    }
};
