<?php

// config/themabinti.php

return [
    /**
     * Define the different seller packages available on the platform.
     * Each package specifies its price, service limits, and media upload limits.
     * 'null' for limits indicates unlimited.
     */
    'seller_packages' => [
        'test' => [
            'price' => 1.00, // Monthly fee in KES
            'services_limit' => 5, // Max number of services a seller can list
            'photos_per_service' => 2, // Max photos per single service listing
            'video_per_service' => 2, // Max videos per single service listing
            'listing_visibility' => 'featured', // Describes visibility level (e.g., standard search results)
            'support_level' => 'testing', // Level of customer support
        ],
        'basic' => [
            'price' => 1.00, // Monthly fee in KES
            'services_limit' => 1, // Max number of services a seller can list
            'photos_per_service' => 2, // Max photos per single service listing
            'video_per_service' => 0,  // Max videos per single service listing
            'listing_visibility' => 'standard', // Describes visibility level (e.g., standard search results)
            'support_level' => 'standard', // Level of customer support
        ],
        'standard' => [
            'price' => 1500.00,
            'services_limit' => 2,
            'photos_per_service' => 3,
            'video_per_service' => 1,
            'listing_visibility' => 'enhanced', // Better placement/prominence
            'support_level' => 'priority',
        ],
        'premium' => [
            'price' => 2500.00,
            'services_limit' => 3, // Unlimited services
            'photos_per_service' => 5, // Unlimited photos
            'video_per_service' => null, // Unlimited videos
            'listing_visibility' => 'featured', // Featured on homepage, top search results
            'support_level' => 'premium',
        ],
    ],

    /**
     * Duration of seller packages before renewal is required, in months.
     */
    'package_duration_months' => 1, // All packages are renewable on a monthly basis
    
    'admin_defaults' => [
        'site_name' => 'Themabinti Services Hub',
        'support_email' => 'support@themabinti.com',
        // Package prices (overrides 'seller_packages' defaults if set in DB)
        'seller_package_price_basic' => 1000.00,
        'seller_package_price_standard' => 1500.00,
        'seller_package_price_premium' => 2500.00,
        // M-Pesa API Credentials (sensitive - stored in DB for admin UI control)
        'mpesa_consumer_key' => 'X97c2JdMTLOALSAOCGr6KGEhHacHd4mMFsTQGfhQMX5qi6dI', // Placeholder. Real value from .env, but can be updated via UI
        'mpesa_consumer_secret' => 'ShYR8sl3rvqmIWAsYIunEHr8bGSvQfNZU0Z4vD6LP5IskxkjBeihXFufOIqpD2p1', // Placeholder
        'mpesa_passkey' => '2dee4cc493a9e30543cf148be8cf048bd54180405d757c1e86be4', // Placeholder
        'mpesa_shortcode' => 3533715, // Placeholder
    ],
];