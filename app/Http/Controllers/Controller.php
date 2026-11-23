<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Fungsi Global untuk kirim WhatsApp via WAHA
     */
    public function sendWhatsapp($target, $message)
    {
        //  Bersihkan nomor dari karakter yang tidak perlu 
        $target = str_replace(['+', ' ', '-', '(', ')'], '', $target);

        // Jika nomor dimulai dengan '08', ubah jadi '628'
        if (str_starts_with($target, '08')) {
            $target = '628' . substr($target, 2);
        }

        // Tambahkan suffix @c.us untuk ID WhatsApp
        if (!str_contains($target, '@c.us')) {
            $target .= '@c.us';
        }

        try {
            // Ambil konfigurasi dari config/waha.php (aman dengan config:cache)
            $url = config('waha.url');
            $apiKey = config('waha.api_key');
            $session = config('waha.session');

            // Kirim request ke Docker WAHA
            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url . '/api/sendText', [
                        'session' => $session,
                        'chatId' => $target,
                        'text' => $message,
                    ]);

            return $response->successful();
        } catch (\Exception $e) {
            // Jika gagal (misal Docker mati), aplikasi Laravel tetap jalan
            return false;
        }
    }
}