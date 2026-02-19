<?php

namespace App\Helpers;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Google\Service\Drive\DriveFile;

class DriveHelper
{
    // Tambahkan parameter ke-3: $folderId
    public static function uploadToDosenDrive($file, $dosen, $folderId = null)
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        
        $token = json_decode($dosen->google_token, true);
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            if ($dosen->google_refresh_token) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($dosen->google_refresh_token);
                $dosen->update(['google_token' => json_encode($newToken)]);
            } else {
                return null;
            }
        }

        $service = new GoogleDrive($client);
        
        // SETUP METADATA FILE
        $fileMetadataConfig = [
            'name' => time() . '_' . $file->getClientOriginalName(),
        ];

        // LOGIKA BARU: Jika ada ID Folder, masukkan file ke folder itu
        if ($folderId) {
            $fileMetadataConfig['parents'] = [$folderId];
        }

        $fileMetadata = new DriveFile($fileMetadataConfig);

        $content = file_get_contents($file->getRealPath());
        
        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink'
        ]);

        return $uploadedFile->webViewLink;
    }
}