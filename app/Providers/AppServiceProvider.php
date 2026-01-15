<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share role flags dan access flags ke semua views
        View::composer('*', function ($view) {
            $isAdmin = false;
            $isVP = false;
            $isHC = false;
            $hasSubordinates = false;
            $canBroadcast = false;
            $canAccessAllBiro = false;
            $canAccessKalender = false;
            $canAccessReport = false;
            $canAccessSettings = false;
            
            if (Auth::check()) {
                $user = Auth::user();
                $roleName = DB::table('roles')->where('id', $user->role_id)->value('role_name');
                $roleUpper = strtoupper($roleName ?? '');
                $biroName = DB::table('biro')->where('id', $user->biro_id)->value('biro_name');
                
                // Role flags
                $isAdmin = $roleUpper === 'ADMIN';
                $isVP = $roleUpper === 'VP';
                $isHC = $biroName && stripos($biroName, 'Human Capital') !== false;
                
                // Access flags
                $canBroadcast = $isAdmin;
                $canAccessAllBiro = $isAdmin || $isVP || $isHC;
                $canAccessKalender = $isAdmin || $isHC;
                $canAccessReport = $isAdmin || $isHC;
                $canAccessSettings = $isAdmin;
                
                // Cek apakah user punya bawahan (ada user lain yang nip_atasannya = user ini)
                $hasSubordinates = DB::table('users')
                    ->where('nip_atasan', $user->nip)
                    ->exists();
            }
            
            $view->with([
                'isAdmin' => $isAdmin,
                'isVP' => $isVP,
                'isHC' => $isHC,
                'hasSubordinates' => $hasSubordinates,
                'canBroadcast' => $canBroadcast,
                'canAccessAllBiro' => $canAccessAllBiro,
                'canAccessKalender' => $canAccessKalender,
                'canAccessReport' => $canAccessReport,
                'canAccessSettings' => $canAccessSettings,
            ]);
        });
    }
}
