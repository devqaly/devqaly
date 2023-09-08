<?php

return [
    'products' => [
        'enterprise' => [
            'id' => env('STRIPE_PRODUCT_ENTERPRISE_ID'),
            'prices' => [
                'default' => env('STRIPE_PRICE_ENTERPRISE_ID')
            ]
        ]
    ]
];
