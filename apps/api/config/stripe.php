<?php

return [
    'products' => [
        'enterprise' => [
            'id' => env('STRIPE_PRODUCT_ENTERPRISE_ID'),
            'prices' => [
                'default' => env('STRIPE_PRICE_ENTERPRISE_ID')
            ]
        ],
        'gold' => [
            'id' => env('STRIPE_PRODUCT_GOLD_ID'),
            'prices' => [
                'default' => env('STRIPE_PRICE_GOLD_MONTHLY_ID'),
                'monthly' => env('STRIPE_PRICE_GOLD_MONTHLY_ID'),
            ]
        ]
    ]
];
