<?php
/**
 * File: config.php
 * Purpose: Configuration settings
 */
return [
    'app' => [
        'environment' => 'production',
        'debug' => false,
        'site_name' => 'Just Do It',
        'domain' => 'your-domain.com'
    ],
    'email' => [
        'admin_email' => 'admin@your-domain.com',
        'noreply_email' => 'noreply@your-domain.com',
        'support_email' => 'support@your-domain.com'
    ],
    'security' => [
        'tor_check_timeout' => 5,
        'tor_cache_expiry' => 3600,
        'challenge_timeout' => 300,
        'max_attempts' => 3
    ],
    'paths' => [
        'logs' => __DIR__ . '/../logs',
        'cache' => __DIR__ . '/../cache',
        'data' => __DIR__ . '/../data/submissions'
    ]
];