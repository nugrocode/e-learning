<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Tangkap Input
        $nim = $request->input('nim');
        $password = md5($request->input('password')); // Hash inputan dengan MD5

        // 2. Cari User berdasarkan nim_nidn DAN password
        $user = User::where('nim_nidn', $nim)
                    ->where('password', $password)
                    ->first();

        // 3. Cek apakah user ditemukan
        if ($user) {
            // Set Session Manual (Mirip $_SESSION native)
            Session::put('user_id', $user->id);
            Session::put('nama', $user->nama_lengkap);
            Session::put('role', $user->role);
            Session::put('status', 'login');
            Session::put('foto', $user->foto_profil); // Tambahan buat foto profil

            // Redirect sesuai Role
            if ($user->role == 'mahasiswa') {
                return redirect('/dashboard');
            } else {
                return redirect('/dashboard'); // Atau arahkan ke dashboard dosen/admin
            }
        } else {
            return redirect('/login')->with('error', 'NIM atau Password salah!');
        }
    }

    public function logout()
    {
        Session::flush(); // Hapus semua session
        return redirect('/login');
    }
}
