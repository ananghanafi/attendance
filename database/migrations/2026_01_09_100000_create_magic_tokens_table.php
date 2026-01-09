<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('magic_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('token', 64)->unique(); // hashed token
            $table->integer('kalender_kerja_id')->nullable(); // referensi ke kalender kerja
            $table->string('kalender_string', 20)->nullable(); // format "minggu-bulan-tahun"
            $table->timestamp('expires_at'); // expired 24 jam
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('is_used')->default(false); // track apakah sudah dipakai (opsional)
            
            $table->index('token');
            $table->index('user_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magic_tokens');
    }
};
