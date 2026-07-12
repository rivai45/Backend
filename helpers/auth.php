<?php
/**
 * Auth Helpers — Lynvaii Hotel Booking System
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user is admin or super_admin
 */
function isAdmin() {
    return isLoggedIn() && in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin']);
}

/**
 * Check if current user is super_admin
 */
function isSuperAdmin() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'super_admin';
}

/**
 * Get current user data from session
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
        'avatar' => $_SESSION['user_avatar'] ?? null,
        'language' => $_SESSION['user_language'] ?? 'id',
    ];
}

/**
 * Set user session after login
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar'];
    $_SESSION['user_language'] = $user['language'] ?? 'id';
    $_SESSION['logged_in_at'] = time();
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Require login — redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        redirect('?page=login');
    }
}

/**
 * Require admin access — redirect if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        setFlash('error', 'Akses ditolak.');
        redirect('?page=admin/login');
    }
}

/**
 * Require super admin access
 */
function requireSuperAdmin() {
    if (!isSuperAdmin()) {
        setFlash('error', 'Akses hanya untuk Super Admin.');
        redirect('?page=admin/dashboard');
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF hidden input field
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect to a page
 * Supports:
 *   redirect('')              → BASE_URL (home)
 *   redirect('login')         → BASE_URL?page=login
 *   redirect('?page=login')   → BASE_URL?page=login (same result)
 *   redirect('?page=x&y=z')  → BASE_URL?page=x&y=z (preserved)
 */
function redirect($page = '') {
    if (empty($page)) {
        header('Location: ' . BASE_URL);
    } elseif (str_starts_with($page, '?')) {
        // Already has query string format
        header('Location: ' . BASE_URL . $page);
    } else {
        // Plain page name → convert to ?page= format
        header('Location: ' . BASE_URL . '?page=' . $page);
    }
    exit;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian
 */
function formatDate($date, $format = 'd M Y') {
    $months_id = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
        'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agu',
        'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
    ];
    $formatted = date($format, strtotime($date));
    if (getLang() === 'id') {
        $formatted = strtr($formatted, $months_id);
    }
    return $formatted;
}

/**
 * Get current language
 */
function getLang() {
    return $_SESSION['user_language'] ?? $_SESSION['lang'] ?? 'id';
}

/**
 * Set language
 */
function setLang($lang) {
    $_SESSION['lang'] = $lang;
    $_SESSION['user_language'] = $lang;
}
