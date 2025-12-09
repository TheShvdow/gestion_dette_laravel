<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'oauth/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',  // Vite dev server
        'http://localhost:3000',  // Alternative dev port
        env('FRONTEND_URL', '*'), // Production frontend URL
    ],

    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.vercel\.app$/',  // Vercel preview deployments
        '/^https:\/\/.*\.netlify\.app$/', // Netlify deployments
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true, // Important pour OAuth2

];
