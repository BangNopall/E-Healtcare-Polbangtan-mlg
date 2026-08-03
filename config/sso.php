<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSO Handoff Shared Secret
    |--------------------------------------------------------------------------
    |
    | Kunci rahasia yang dibagikan dengan E-Management untuk menandatangani
    | payload handoff SSO (GET /sso). Kunci ini HARUS identik di kedua sisi.
    |
    | Format canonical string yang ditandatangani (urutan tetap, dipisah "|"):
    |
    |     "{nim}|{expires_at}|{nonce}"
    |
    | - nim         : NIM mahasiswa tujuan, apa adanya (string)
    | - expires_at  : Unix timestamp (detik) batas waktu tiket berlaku
    | - nonce       : String acak unik per tiket, mencegah replay
    |
    | Signature = hash_hmac('sha256', $canonicalString, $secret), dikirim
    | sebagai hex string pada parameter query `signature`.
    |
    */

    'secret' => env('SSO_SHARED_SECRET'),

];
