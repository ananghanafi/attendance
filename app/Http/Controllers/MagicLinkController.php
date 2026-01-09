<?php

namespace App\Http\Controllers;

use App\Models\MagicToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MagicLinkController extends Controller
{
    /**
     * Handle magic link login
     * Route: GET /magic-login/{token}
     */
    public function login(string $token)
    {
        // Cari token di database
        $magicToken = MagicToken::findByRawToken($token);

        // Validasi token
        if (!$magicToken) {
            return redirect()->route('login')
                ->withErrors(['magic_link' => 'Link tidak valid atau sudah kedaluwarsa.']);
        }

        // Cek apakah token sudah expired
        if ($magicToken->isExpired()) {
            return redirect()->route('login')
                ->withErrors(['magic_link' => 'Link sudah kedaluwarsa. Silakan hubungi admin untuk link baru.']);
        }

        // Ambil user
        $user = User::find($magicToken->user_id);
        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['magic_link' => 'User tidak ditemukan.']);
        }

        // Login user
        Auth::login($user);

        // Regenerate session untuk keamanan
        request()->session()->regenerate();

        // Ambil biro_id user untuk redirect ke pengajuan edit yang sesuai
        $biroId = $user->biro_id;
        $kalenderString = $magicToken->kalender_string;

        // Cari pengajuan_wao berdasarkan biro_id dan kalender
        $pengajuan = DB::table('pengajuan_wao')
            ->where('biro_id', $biroId)
            ->where('kalender', $kalenderString)
            ->first();

        if (!$pengajuan) {
            // Jika pengajuan tidak ditemukan, redirect ke halaman pengajuan index
            return redirect()->route('pengajuan.index')
                ->with('warning', 'Data pengajuan WFO untuk periode ini tidak ditemukan.');
        }

        // Set session untuk edit pengajuan (sama seperti setEdit di PengajuanWfoController)
        session(['pengajuan_id' => $pengajuan->id]);

        // Redirect ke halaman edit pengajuan WFO
        return redirect()->route('pengajuan.edit')
            ->with('status', 'Berhasil login. Silakan lengkapi pengajuan WFO Anda.');
    }
}
