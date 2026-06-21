<?php
/**
 * Application Configuration — Lynvaii Hotel Booking System
 */

// Base paths
define('BASE_PATH', dirname(__DIR__, 2));
define('BACKEND_PATH', BASE_PATH . '/backend');
define('FRONTEND_PATH', BASE_PATH . '/frontend');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// URL Configuration
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . '://' . $host . '/web_pemesanan_hotel');
define('ASSET_URL', BASE_URL . '/frontend/assets');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Application Settings
define('APP_NAME', 'Lynvaii');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', true);

// Session Configuration
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_NAME', 'lynvaii_session');

// Booking Configuration
define('BOOKING_EXPIRY_MINUTES', 15);
define('TAX_PERCENTAGE', 10);
define('CURRENCY', 'IDR');
define('CURRENCY_SYMBOL', 'Rp');

// Pagination
define('PER_PAGE', 10);

// Upload limits
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Time zone
date_default_timezone_set('Asia/Jakarta');

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
