<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Passport Private Key
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys to generate secure access tokens. The
    | private key is used to sign the tokens. This should be stored in
    | your environment file and kept secret.
    |
    */
    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Passport Public Key
    |--------------------------------------------------------------------------
    |
    | The public key is used to verify the tokens. This can be shared publicly.
    |
    */
    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Passport Personal Access Client ID
    |--------------------------------------------------------------------------
    */
    'personal_access_client_id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Passport Personal Access Client Secret
    |--------------------------------------------------------------------------
    */
    'personal_access_client_secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
];
