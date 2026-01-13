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
        // Share isAdmin dan hasSubordinates variable ke semua views
        View::composer('*', function ($view) {
            $isAdmin = false;
            $hasSubordinates = false;
            
            if (Auth::check()) {
                $user = Auth::user();
                $roleName = DB::table('roles')->where('id', $user->role_id)->value('role_name');
                // Cek admin dengan case-insensitive
                $isAdmin = strtolower($roleName ?? '') === 'admin';
                
                // Cek apakah user punya bawahan (ada user lain yang nip_atasannya = user ini)
                $hasSubordinates = DB::table('users')
                    ->where('nip_atasan', $user->nip)
                    ->exists();
            }
            
            $view->with('isAdmin', $isAdmin);
            $view->with('hasSubordinates', $hasSubordinates);
        });
    }
}
