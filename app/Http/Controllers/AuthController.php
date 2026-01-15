<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');

            // Simpan role_name ke session
            $request->session()->put('role_name', $role);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau kata sandi tidak cocok.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        $roleUpper = strtoupper($role ?? '');

        // Cek role
        $isAdmin = $roleUpper === 'ADMIN';
        $isVP = $roleUpper === 'VP';

        // Ambil nama biro
        $biroName = null;
        if ($user->biro_id) {
            $biroName = DB::table('biro')->where('id', $user->biro_id)->value('biro_name');
        }

        // Cek apakah user dari Human Capital Division
        $isHC = $biroName && stripos($biroName, 'Human Capital') !== false;

        // Determine akses menu
        $canAccessPengajuanWfo = true; // Semua bisa akses
        $canBroadcast = $isAdmin; // Hanya admin bisa broadcast
        $canAccessAllBiro = $isAdmin || $isVP || $isHC; // Admin, VP, HC bisa akses semua biro
        $canAccessKalender = $isAdmin || $isHC; // Admin dan HC
        $canAccessReport = $isAdmin || $isHC; // Admin dan HC
        $canAccessSettings = $isAdmin; // Hanya Admin

        return view('dashboard', [
            'user' => $user,
            'role' => $role,
            'biroName' => $biroName,
            // Role flags
            'isAdmin' => $isAdmin,
            'isVP' => $isVP,
            'isHC' => $isHC,
            // Access flags
            'canAccessPengajuanWfo' => $canAccessPengajuanWfo,
            'canBroadcast' => $canBroadcast,
            'canAccessAllBiro' => $canAccessAllBiro,
            'canAccessKalender' => $canAccessKalender,
            'canAccessReport' => $canAccessReport,
            'canAccessSettings' => $canAccessSettings,
        ]);
    }
}
