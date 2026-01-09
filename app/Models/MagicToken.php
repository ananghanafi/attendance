<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagicToken extends Model
{
    protected $table = 'magic_tokens';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'kalender_kerja_id',
        'kalender_string',
        'expires_at',
        'created_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke KalenderKerjaV2
     */
    public function kalenderKerja()
    {
        return $this->belongsTo(KalenderKerjaV2::class, 'kalender_kerja_id');
    }

    /**
     * Cek apakah token sudah expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope untuk token yang masih valid (belum expired)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Generate token baru untuk user
     */
    public static function generateForUser(int $userId, ?int $kalenderKerjaId = null, ?string $kalenderString = null): self
    {
        // Generate random token
        $rawToken = bin2hex(random_bytes(32));
        
        // Hash token untuk disimpan di database
        $hashedToken = hash('sha256', $rawToken);

        $magicToken = self::create([
            'user_id' => $userId,
            'token' => $hashedToken,
            'kalender_kerja_id' => $kalenderKerjaId,
            'kalender_string' => $kalenderString,
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'is_used' => false,
        ]);

        // Simpan raw token sementara untuk di-return (tidak disimpan di DB)
        $magicToken->raw_token = $rawToken;

        return $magicToken;
    }

    /**
     * Cari token berdasarkan raw token
     */
    public static function findByRawToken(string $rawToken): ?self
    {
        $hashedToken = hash('sha256', $rawToken);
        return self::where('token', $hashedToken)->first();
    }
}
