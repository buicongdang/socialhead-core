<?php
return [
    'app' => [
        'url_api' => env('APP_API'),
        'url_web' => env('APP_URL'),
        'url_webhook' => env('APP_WEBHOOK'),
        'jwt_token' => env('JWT_TOKEN', 'socialhed')
    ],
    'services' => [
        'crisp' => [
            'verify_secret' => env('CRISP_VERIFY_SECRET', 'socialhed')
        ],
        'shopify' => [
            'api_version' => env('SHOPIFY_API_VERSION', '2021-10'),
            'api_key' => env('SHOPIFY_API_KEY'),
            'api_secret' => env('SHOPIFY_API_SECRET'),
            'scopes' => [
                'write_script_tags', 'write_content', 'write_products', 'write_themes'
            ],
            'callback_url' => env('API_URL').'/shopify/auth/handle',
            'webhooks' => [
                'products/create', 'products/update', 'products/delete',
                'app/uninstalled', 'shop/update',
                'themes/publish'
            ]
        ]
    ]
];
