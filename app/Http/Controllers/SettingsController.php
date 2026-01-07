<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Biro;
use App\Models\Jabatan;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        if ($role !== 'admin' && $role !== 'ADMIN') {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        // Fetch all users with enriched data
        $users = User::query()->orderByDesc('id')->get();
        $roleMap = DB::table('roles')->pluck('role_name', 'id');
        $biroMap = DB::table('biro')->pluck('biro_name', 'id');
        
        foreach ($users as $u) {
            $u->role_name = $roleMap[$u->role_id] ?? null;
            $u->biro_name = $biroMap[$u->biro_id] ?? null;
        }

        // Fetch all master data
        $biros = Biro::query()->orderBy('biro_name')->get();
        $jabatans = Jabatan::query()->orderBy('jabatan')->get();
        $roles = Role::query()->orderBy('role_name')->get();

        return view('settings.index', [
            'users' => $users,
            'biros' => $biros,
            'jabatans' => $jabatans,
            'roles' => $roles,
        ]);
    }
}
