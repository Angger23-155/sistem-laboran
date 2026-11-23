<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi WAHA (WhatsApp HTTP API)
    |--------------------------------------------------------------------------
    |
    | Semua konfigurasi untuk integrasi WhatsApp via WAHA Docker.
    | Ubah nilai-nilai ini di file .env, bukan di sini.
    |
    */

    'url' => env('WAHA_URL', 'http://localhost:3000'),
    'api_key' => env('WAHA_API_KEY'),
    'session' => env('WAHA_SESSION', 'default'),

    // URL sistem yang dikirim di pesan WA agar penerima bisa klik langsung
    'system_url' => env('APP_SYSTEM_URL', 'http://127.0.0.1:8000'),

];
