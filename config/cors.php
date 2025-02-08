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

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH'],

    'allowed_origins' => ['http://localhost:5173'], // List origins explicitly in development

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept','Authorization'], // List headers explicitly.  'Authorization' if you use it

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
