<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class GoogleController extends Controller
{
    // Fungsi Private untuk Setup Konfigurasi Client
    private function getClient()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        
        // Scope Wajib: Akses File Drive
        $client->addScope("https://www.googleapis.com/auth/drive.file");
        
        // Penting: Agar kita dapat Refresh Token (akses jangka panjang)
        $client->setAccessType('offline'); 
        $client->setPrompt('consent select_account'); 
        
        return $client;
    }

    // 1. Redirect Dosen ke Halaman Login Google
    public function redirectToGoogle()
    {
        // Cek apakah user adalah dosen
        if (Session::get('role') != 'dosen') {
            return redirect('/login')->with('error', 'Hanya Dosen yang bisa menghubungkan Drive.');
        }

        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        
        // Arahkan user keluar aplikasi menuju Google
        return redirect()->away($authUrl);
    }

    // 2. Proses Callback (Setelah Dosen klik 'Allow' di Google)
    public function handleGoogleCallback(Request $request)
    {
        // Jika user membatalkan / klik cancel
        if (!$request->code) {
            return redirect('/dosen/profil')->with('error', 'Koneksi Google Drive dibatalkan.');
        }

        try {
            $client = $this->getClient();
            
            // Tukar "Code" dengan "Token"
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            // Cek apakah token valid
            if (isset($token['error'])) {
                return redirect('/dosen/profil')->with('error', 'Gagal login ke Google: ' . $token['error']);
            }

            // Simpan Token ke Database User yang sedang login
            // Pastikan session user_id masih ada
            $userId = Session::get('user_id');
            if (!$userId) {
                return redirect('/login')->with('error', 'Sesi habis, silakan login ulang.');
            }

            $user = User::find($userId);
            
            // Simpan token akses utama
            $user->google_token = json_encode($token);
            
            // Simpan Refresh Token (Hanya muncul saat pertama kali connect / prompt consent)
            // Ini SANGAT PENTING untuk upload jangka panjang tanpa login ulang terus
            if (isset($token['refresh_token'])) {
                $user->google_refresh_token = $token['refresh_token'];
            }
            
            $user->save();

            return redirect('/dosen/profil')->with('success', 'Berhasil! Akun Google Drive terhubung.');

        } catch (\Exception $e) {
            return redirect('/dosen/profil')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}