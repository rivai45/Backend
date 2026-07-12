<?php
/**
 * Application Configuration — Lynvaii Hotel Booking System
 *
 * Konfigurasi sensitif diambil dari .env.php
 * Jangan ubah file ini langsung — edit .env.php yang ada di root project
 */

// ─── Load Environment Config ─────────────────────────────────────────────────
$envFile = dirname(__DIR__, 2) . '/.env.php';
$env = file_exists($envFile) ? require $envFile : [];

// ─── Base Paths ───────────────────────────────────────────────────────────────
define('BASE_PATH',     dirname(__DIR__, 2));
define('BACKEND_PATH',  BASE_PATH . '/backend');
define('FRONTEND_PATH', BASE_PATH . '/frontend');
define('UPLOAD_PATH',   BASE_PATH . '/uploads');

// ─── URL Configuration (dinamis, aman untuk lokal & hosting) ─────────────────
$protocol      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host          = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePathSuffix = $env['BASE_PATH_SUFFIX'] ?? '/web_pemesanan_hotel/';
define('BASE_URL',   $protocol . '://' . $host . $basePathSuffix);
define('ASSET_URL',  BASE_URL . 'frontend/assets');
define('UPLOAD_URL', BASE_URL . 'uploads');

// ─── Application Settings ─────────────────────────────────────────────────────
define('APP_NAME',    'Lynvaii');
define('APP_VERSION', '1.0.0');
// FIX #1: APP_DEBUG diambil dari .env.php — WAJIB false di production/hosting!
define('APP_DEBUG', (bool)($env['APP_DEBUG'] ?? false));

// ─── Session Configuration ───────────────────────────────────────────────────
define('SESSION_LIFETIME', 7200); // 2 jam
define('SESSION_NAME',     'lynvaii_session');

// ─── Booking Configuration ───────────────────────────────────────────────────
// FIX #6: Diselaraskan menjadi 1440 menit (24 jam) agar konsisten dengan
//         pesan yang ditampilkan ke tamu di halaman my-bookings
define('BOOKING_EXPIRY_MINUTES', 1440);
define('TAX_PERCENTAGE',  10);
define('CURRENCY',        'IDR');
define('CURRENCY_SYMBOL', 'Rp');

// ─── Pagination ───────────────────────────────────────────────────────────────
define('PER_PAGE', 10);

// ─── Upload Limits ────────────────────────────────────────────────────────────
define('MAX_UPLOAD_SIZE',       5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES',   ['image/jpeg', 'image/png', 'image/webp']);

// ─── Security ─────────────────────────────────────────────────────────────────
define('CSRF_TOKEN_NAME',    'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// ─── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Jakarta');

// ─── Error Reporting ──────────────────────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/error.log');
}

// ─── Start Session ────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    // FIX tambahan: cookie HttpOnly + SameSite untuk keamanan session
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
