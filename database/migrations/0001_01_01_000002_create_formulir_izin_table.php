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
        Schema::create('formulir_izin', function (Blueprint $table) {
            $table->id();

            $table->string('nip', 255)->nullable();
            $table->string('status', 255)->nullable();

            // note: column names 'from' and 'to' kept to match original SQL
            $table->date('from')->nullable();
            $table->date('to')->nullable();

            $table->text('alasan')->nullable();
            $table->date('tanggal_input')->nullable();

            $table->integer('is_approval')->nullable();
            $table->date('tanggal_approve')->nullable();
            $table->integer('auto_reject')->nullable();

            $table->timestamp('timestamp_pengajuan')->nullable();
            $table->timestamp('timestamp_approval')->nullable();
            $table->timestamp('timestamp_reject')->nullable();

            $table->integer('is_reject')->nullable();
        });

        // Reset sequence start for PostgreSQL so IDs begin from 1 in a fresh database.
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('formulir_izin', 'id'), 1, false);");
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
        Schema::dropIfExists('formulir_izin');
    }
};
