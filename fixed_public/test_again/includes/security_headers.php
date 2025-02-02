<?php
/**
 * File: security_headers.php
 * Purpose: Security headers configuration
 */

// Load configuration
$config = require_once __DIR__ . '/config.php';

// Define allowed domains
$allowedDomains = [
    'https://check.torproject.org',
    'https://aus1.torproject.org',
    'https://tor.void.gr'
];

// Content Security Policy Construction
$csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline'",
    "style-src 'self' 'unsafe-inline'",
    "font-src 'self' data:",
    "img-src 'self' data: https://jigsaw.w3.org",
    "connect-src 'self' " . implode(' ', $allowedDomains),
    "form-action 'self'",
    "frame-ancestors 'self'",
    "base-uri 'self'",
    "object-src 'none'",
    "media-src 'none'"
];

// Set security headers
header("Content-Security-Policy: " . implode('; ', $csp));
header("Strict-Transport-Security: max-age=31536000");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Configure secure cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');