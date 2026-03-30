<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — Configuration pour WordPress ↔ Laravel (même domaine XAMPP)
    |--------------------------------------------------------------------------
    |
    | WordPress et Laravel tournent tous les deux sur http://localhost via XAMPP.
    | Les appels API (wp_remote_post) sont des appels serveur-à-serveur (pas de
    | navigateur) donc CORS n'est pas techniquement requis pour ces appels.
    | Cependant, les appels AJAX depuis les pages WordPress (navigateur) ont
    | besoin de CORS. On autorise explicitement l'origine WordPress.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost',
        'http://127.0.0.1',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Mairie-Token',
        'X-Requested-With',
    ],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,

];
