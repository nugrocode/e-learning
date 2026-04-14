<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function ask($prompt, $isJson = false)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash'); 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Konfigurasi dinamis (Jika isJson true, paksa AI output JSON murni)
        $generationConfig = [
            'temperature' => $isJson ? 0.1 : 0.7, 
            'maxOutputTokens' => 8192, 
        ];

        if ($isJson) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => $generationConfig
        ];

        try {
            $response = Http::withoutVerifying()
                            ->withHeaders(['Content-Type' => 'application/json'])
                            ->post($url, $body);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

                if ($isJson) {
                    // Karena sudah pakai Native JSON Mode, teks bisa langsung di-decode
                    // Namun tetap kita bersihkan dari backticks (jaga-jaga)
                    $cleanText = str_replace(['```json', '```', '`'], '', $rawText);
                    $jsonResult = json_decode(trim($cleanText), true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $jsonResult;
                    } else {
                        Log::error('Gemini JSON Decode Error: ' . json_last_error_msg() . ' | Raw: ' . $rawText);
                        return null; 
                    }
                }

                return $rawText;

            } else {
                Log::error('Gemini API Error Status: ' . $response->status() . ' | Body: ' . $response->body());
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Gemini Connection Exception: ' . $e->getMessage());
            return null;
        }
    }
}