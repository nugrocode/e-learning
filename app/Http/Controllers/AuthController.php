<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        // Jika user sudah login, langsung lempar ke dashboard sesuai role
        if (Session::has('status') && Session::get('status') == 'login') {
            $role = Session::get('role');
            if ($role == 'admin') return redirect('/admin/dashboard');
            if ($role == 'dosen') return redirect('/dosen/dashboard');
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Tangkap Input
        $nim = $request->input('nim');
        $password = md5($request->input('password')); 

        // 2. Cek di Database
        $user = User::where('nim_nidn', $nim)
                    ->where('password', $password)
                    ->first();

        // 3. Cek apakah user ditemukan
        if ($user) {
            // Set Session Manual
            Session::put('user_id', $user->id);
            Session::put('nama', $user->nama_lengkap);
            Session::put('role', $user->role); // admin, dosen, atau mahasiswa
            Session::put('status', 'login');
            Session::put('foto', $user->foto_profil);

            // 4. LOGIKA PENGALIHAN (REDIRECT) BERDASARKAN ROLE
            if ($user->role == 'admin') {
                return redirect('/admin/dashboard')->with('success', 'Selamat Datang Admin!');
            } 
            elseif ($user->role == 'dosen') {

                return redirect('/dosen/dashboard')->with('success', 'Selamat Datang Dosen!');
            } 
            else {
                // Default: Mahasiswa
                return redirect('/dashboard')->with('success', 'Selamat Belajar!');
            }

        } else {
            return redirect('/login')->with('error', 'NIM/NIDN atau Password salah!');
        }
    }

    public function logout()
    {
        Session::flush(); // Hapus semua session
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}