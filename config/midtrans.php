<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans Payment Gateway pada sistem SAKTI.
    | Pastikan mengisi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di file .env
    |
    */

    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'        => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Sandbox / Production URLs
    |--------------------------------------------------------------------------
    */
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
