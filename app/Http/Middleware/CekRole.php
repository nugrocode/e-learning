<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah sudah login?
        if (!Session::has('status') || Session::get('status') != 'login') {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu!');
        }

        // 2. Ambil role user dari session
        $userRole = Session::get('role');

        // 3. Cek apakah role user ada di dalam daftar role yang diizinkan?
        if (in_array($userRole, $roles)) {
            return $next($request); // Boleh lewat
        }

        // 4. Jika role tidak cocok, tendang balik/abort
        // Redirect ke dashboard masing-masing biar tidak stuck di error page
        if ($userRole == 'admin') return redirect('/admin/dashboard');
        if ($userRole == 'mahasiswa') return redirect('/dashboard');
        
        return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}