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
        Schema::create('approval_tokens', function (Blueprint $table) {
            $table->id();
            
            $table->string('token', 100)->unique();
            $table->string('type', 20); // 'izin' atau 'pulang'
            $table->integer('reference_id'); // FK ke formulir_izin atau absen
            
            $table->string('nip_pengaju', 35);
            $table->string('nip_atasan', 35);
            
            $table->dateTime('expired_at');
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, expired
            
            $table->dateTime('processed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Reset sequence start for PostgreSQL
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('approval_tokens', 'id'), 1, false);");
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
        Schema::dropIfExists('approval_tokens');
    }
};
