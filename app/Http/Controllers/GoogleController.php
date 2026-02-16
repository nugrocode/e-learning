<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// --- BAGIAN INI YANG TADI HILANG ---
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
// -----------------------------------
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // Fungsi untuk menyiapkan Client Google
    private function getClient()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(GoogleDrive::DRIVE_FILE); // Izin akses file
        $client->setAccessType('offline'); // Agar dapat refresh token
        $client->setPrompt('select_account consent'); // Memaksa user login ulang jika perlu
        
        return $client;
    }

    // 1. Mengarahkan Dosen ke Halaman Login Google
    public function redirectToGoogle()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    // 2. Menerima Balikan (Callback) dari Google
    public function handleGoogleCallback(Request $request)
    {
        // Cek jika user membatalkan login (tidak ada kode)
        if (!$request->code) {
            return redirect('/dosen/profil')->with('error', 'Login Google dibatalkan.');
        }

        try {
            $client = $this->getClient();
            
            // Tukar Kode dengan Token Asli
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            // Cek jika ada error di token
            if (isset($token['error'])) {
                return redirect('/dosen/profil')->with('error', 'Gagal mendapatkan akses token.');
            }

            // Simpan Token ke Database User yang sedang login
            $user = User::find(Auth::id()); // Menggunakan Auth::id() agar lebih aman
            
            // Simpan token akses (format JSON)
            $user->google_token = json_encode($token);
            
            // Simpan refresh token (hanya muncul saat pertama kali connect)
            if (isset($token['refresh_token'])) {
                $user->google_refresh_token = $token['refresh_token'];
            }
            
            $user->save();

            return redirect('/dosen/profil')->with('success', 'Berhasil! Akun Google Drive terhubung.');

        } catch (\Exception $e) {
            return redirect('/dosen/profil')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}