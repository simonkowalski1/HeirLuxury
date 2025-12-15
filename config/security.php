<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Configure CSP header. Set to null to disable.
    | Be careful: a strict CSP may break inline styles/scripts.
    |
    | Example permissive policy (adjust based on your needs):
    | "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;"
    |
    */

    'csp' => env('SECURITY_CSP', null),

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Security settings for file uploads.
    |
    */

    'uploads' => [
        // Maximum file size in kilobytes
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // 10MB

        // Allowed MIME types for images
        'allowed_image_mimes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],

        // Allowed extensions for images
        'allowed_image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],

        // Blocked executable extensions (never allow these)
        'blocked_extensions' => [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
            'exe', 'bat', 'cmd', 'sh', 'bash',
            'js', 'vbs', 'ps1',
            'htaccess', 'htpasswd',
        ],
    ],

];
