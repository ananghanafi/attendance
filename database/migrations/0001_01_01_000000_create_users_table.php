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
        Schema::create('users', function (Blueprint $table) {
            // id as serial (Postgres) / auto-increment integer
            $table->id();

            $table->string('username', 35)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('nama', 50)->nullable();
            $table->string('nip', 35)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('telp', 20)->nullable();

            $table->integer('role_id')->nullable();
            $table->integer('biro_id')->nullable();

            $table->string('nip_atasan', 10)->nullable();

            // flags and tinyints
            $table->boolean('isdel')->default(false);

            $table->date('tgl_lahir')->nullable();

            $table->string('transportasi', 255)->nullable();
            $table->string('id_kel', 255)->nullable();

            $table->integer('id_lokasi_car_pooling')->nullable();

            // these were declared as int in the original SQL
            $table->integer('is_covid_ranger')->nullable();
            $table->integer('is_tim_covid')->nullable();

            $table->boolean('is_satgas_covid')->default(false);
            $table->boolean('is_hc')->default(false);
            $table->boolean('is_umum')->nullable();

            $table->string('jabatan', 255)->nullable();

            $table->boolean('is_kirim')->default(false);
            $table->boolean('is_crot')->default(false);
            $table->boolean('is_pulang')->nullable();

            // keep no timestamps to match original SQL
        });

        // Reset sequence start for PostgreSQL so IDs begin from 1 in a fresh database.
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), 1, false);");
            }
        } catch (\Throwable $e) {
            // If DB is not available at migration creation time (or not pgsql), skip silently.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
