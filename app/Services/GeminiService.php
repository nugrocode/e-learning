<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Kirim prompt ke Google Gemini API.
     *
     * @param string $prompt  Instruksi untuk AI.
     * @param bool   $isJson  Apakah output harus format JSON (Array)?
     * @return mixed          String (jika text), Array (jika JSON), atau null (jika error).
     */
    public function ask($prompt, $isJson = false)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); // Default ke model flash yang cepat
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Payload Body
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            // Opsional: Mengatur kreativitas (temperature)
            'generationConfig' => [
                'temperature' => $isJson ? 0.2 : 0.7, // Kalau JSON butuh presisi (rendah), kalau Materi butuh kreatif (tinggi)
                'maxOutputTokens' => 2048,
            ]
        ];

        try {
            // Kirim Request POST
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                            ->post($url, $body);

            if ($response->successful()) {
                // Ambil teks dari respons Gemini
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // --- LOGIKA CLEANUP ---
                
                if ($isJson) {
                    // Bersihkan format Markdown Code Block (```json ... ```)
                    $cleanText = str_replace(['```json', '```'], '', $rawText);
                    
                    // Decode string JSON menjadi Array PHP
                    $jsonResult = json_decode($cleanText, true);

                    // Cek apakah valid JSON
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $jsonResult;
                    } else {
                        Log::error('Gemini JSON Decode Error: ' . json_last_error_msg() . ' | Raw: ' . $rawText);
                        return null; // Gagal decode
                    }
                }

                // Jika mode teks biasa (untuk materi/soal), kembalikan langsung
                return $rawText;

            } else {
                // Log jika API menolak (Misal: Quota habis, Key salah)
                Log::error('Gemini API Error Status: ' . $response->status() . ' | Body: ' . $response->body());
                return null;
            }

        } catch (\Exception $e) {
            // Tangkap error koneksi/internet
            Log::error('Gemini Connection Exception: ' . $e->getMessage());
            return null;
        }
    }
}