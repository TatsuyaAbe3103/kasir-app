<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL Domain Deploy untuk QR Code Meja
    |--------------------------------------------------------------------------
    | Atur alamat domain online atau IP server yang digunakan untuk link QR meja.
    | Contoh: 'https://pos.namacafe.com' atau 'http://192.168.1.100:8000'
    | Jika kosong / null, sistem otomatis mendeteksi IP lokal jaringan WiFi.
    |
    | Cukup ubah di file .env:
    | POS_DEPLOY_URL=https://pos.domainanda.com
    |--------------------------------------------------------------------------
    */
    'deploy_url' => env('POS_DEPLOY_URL', null),
];
