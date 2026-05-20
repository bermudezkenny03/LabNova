<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_merge(
        // Orígenes locales para desarrollo
        [
            'http://localhost:3000',
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ],
        // Orígenes de producción desde variable de entorno (separados por coma)
        // Ejemplo: CORS_ALLOWED_ORIGINS=https://labnova.vercel.app,https://labnova-git-main.vercel.app
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '')))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
