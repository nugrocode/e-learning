<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class GoogleController extends Controller
{
    private function getClient()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        
        $client->addScope("https://www.googleapis.com/auth/drive.file");
        
        $client->setAccessType('offline'); 
        $client->setPrompt('consent select_account'); 
        
        return $client;
    }

    public function redirectToGoogle()
    {
        if (Session::get('role') != 'dosen') {
            return redirect('/login')->with('error', 'Hanya Dosen yang bisa menghubungkan Drive.');
        }

        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if (!$request->code) {
            return redirect('/dosen/profil')->with('error', 'Koneksi Google Drive dibatalkan.');
        }

        try {
            $client = $this->getClient();
            
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                return redirect('/dosen/profil')->with('error', 'Gagal login ke Google: ' . $token['error']);
            }

            $userId = Session::get('user_id');
            if (!$userId) {
                return redirect('/login')->with('error', 'Sesi habis, silakan login ulang.');
            }

            $user = User::find($userId);
            
            $user->google_token = json_encode($token);
            
            if (isset($token['refresh_token'])) {
                $user->google_refresh_token = $token['refresh_token'];
            }
            
            $user->save();

            return redirect('/dosen/profil');

        } catch (\Exception $e) {
            return redirect('/dosen/profil')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function disconnectGoogle()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect('/login')->with('error', 'Sesi habis, silakan login ulang.');
        }

        $user = User::find($userId);
        
        $user->google_token = null;
        $user->google_refresh_token = null;
        $user->save();

        // Notifikasi sukses dihilangkan, hanya redirect biasa
        return redirect('/dosen/profil');
    }
}