<?php
/**
 * Database Configuration — Lynvaii Hotel Booking System
 *
 * FIX #2: Credentials tidak lagi hardcoded di sini.
 * Semua nilai diambil dari .env.php di root project.
 */

// .env.php sudah di-load oleh app.php sebelumnya, tapi kita load lagi
// untuk jaga-jaga jika database.php di-require secara standalone
if (!defined('DB_HOST')) {
    $envFile = dirname(__DIR__, 2) . '/.env.php';
    $env = file_exists($envFile) ? require $envFile : [];

    define('DB_HOST',    $env['DB_HOST']    ?? 'localhost');
    define('DB_NAME',    $env['DB_NAME']    ?? 'lynvaii_hotel');
    define('DB_USER',    $env['DB_USER']    ?? 'root');
    define('DB_PASS',    $env['DB_PASS']    ?? '');
    define('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4');
}

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // FIX #1 (lanjutan): Jangan tampilkan pesan error DB ke user di production
            if (defined('APP_DEBUG') && APP_DEBUG) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                // Log error secara diam-diam dan tampilkan pesan generik
                error_log("[Lynvaii] DB Connection Error: " . $e->getMessage());
                http_response_code(503);
                die("Layanan sementara tidak tersedia. Silakan coba lagi nanti.");
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning
    private function __clone() {}
}

/**
 * Helper function to get PDO instance
 */
function db() {
    return Database::getInstance()->getConnection();
}
