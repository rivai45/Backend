<?php
/**
 * Front Controller / Router — Lynvaii Hotel Booking System
 * All requests are routed through this file.
 */
ob_start();

// Load configuration
require_once __DIR__ . '/backend/config/app.php';
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/helpers/auth.php';
require_once __DIR__ . '/backend/helpers/language.php';

// Load models
require_once __DIR__ . '/backend/models/User.php';
require_once __DIR__ . '/backend/models/Hotel.php';
require_once __DIR__ . '/backend/models/Booking.php';
require_once __DIR__ . '/backend/models/Room.php';

// Handle language switch
if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'])) {
    setLang($_GET['lang']);
    // Redirect back without lang param
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    unset($params['lang']);
    $query = http_build_query($params);
    header('Location: ' . $url . ($query ? '?' . $query : ''));
    exit;
}

// Load language
loadLanguage();

// Expire old bookings on each request (lightweight check)
try {
    $bookingModel = new Booking();
    $bookingModel->expireOldBookings();
} catch (Exception $e) {
    // Silently fail — DB might not be set up yet
}

// Get the requested page
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Route handling
switch ($page) {
    // ========== PUBLIC PAGES ==========
    case 'home':
        require_once __DIR__ . '/backend/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'destinasi':
        require_once __DIR__ . '/backend/controllers/HotelController.php';
        $controller = new HotelController();
        $controller->index();
        break;

    case 'hotel':
        require_once __DIR__ . '/backend/controllers/HotelController.php';
        $controller = new HotelController();
        $id = $_GET['id'] ?? null;
        $controller->detail($id);
        break;

    case 'pemesanan':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/BookingController.php';
        $controller = new BookingController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;

    case 'my-bookings':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->myBookings();
        break;

    case 'cancel-booking':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->cancel($_GET['id'] ?? 0);
        break;

    case 'booking-detail':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/BookingDetailController.php';
        $controller = new BookingDetailController();
        $controller->index($_GET['id'] ?? 0);
        break;

    case 'upload-bukti':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/PaymentController.php';
        $controller = new PaymentController();
        if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->upload($_GET['booking_id'] ?? 0);
        } else {
            $controller->uploadForm($_GET['booking_id'] ?? 0);
        }
        break;

    case 'profil':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/ProfileController.php';
        $controller = new ProfileController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch ($action) {
                case 'profile':  $controller->updateProfile(); break;
                case 'password': $controller->updatePassword(); break;
                default: $controller->index(); break;
            }
        } else {
            $controller->index();
        }
        break;

    case 'review':
        requireLogin();
        require_once __DIR__ . '/backend/controllers/ReviewController.php';
        require_once __DIR__ . '/backend/models/Hotel.php';
        $controller = new ReviewController();
        $bookingId  = (int)($_GET['booking_id'] ?? 0);
        if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store($bookingId);
        } else {
            $controller->create($bookingId);
        }
        break;

    // ========== AUTH PAGES ==========
    case 'login':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doLogin();
        } else {
            $controller->loginForm();
        }
        break;

    case 'register':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doRegister();
        } else {
            $controller->registerForm();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'forgot-password':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doForgotPassword();
        } else {
            $controller->forgotPasswordForm();
        }
        break;

    case 'reset-password':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doResetPassword();
        } else {
            $controller->showResetForm();
        }
        break;

    // ========== ADMIN AUTH ==========
    case 'admin/login':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->doAdminLogin();
        } else {
            $controller->adminLoginForm();
        }
        break;

    // ========== ADMIN PAGES ==========
    case 'admin/dashboard':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin/booking':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminBookingController.php';
        $controller = new AdminBookingController();
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'store': $controller->store(); break;
            case 'edit': $controller->edit($_GET['id'] ?? 0); break;
            case 'update': $controller->update($_GET['id'] ?? 0); break;
            case 'delete': $controller->delete($_GET['id'] ?? 0); break;
            case 'status': $controller->updateStatus($_GET['id'] ?? 0); break;
            default: $controller->index(); break;
        }
        break;

    case 'admin/properti':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminPropertyController.php';
        $controller = new AdminPropertyController();
        switch ($action) {
            case 'store': requireSuperAdmin(); $controller->store(); break;
            case 'update': requireSuperAdmin(); $controller->update($_GET['id'] ?? 0); break;
            case 'delete': requireSuperAdmin(); $controller->delete($_GET['id'] ?? 0); break;
            default: $controller->index(); break;
        }
        break;

    case 'admin/anggota':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminMemberController.php';
        $controller = new AdminMemberController();
        switch ($action) {
            case 'store': requireSuperAdmin(); $controller->store(); break;
            case 'update': requireSuperAdmin(); $controller->update($_GET['id'] ?? 0); break;
            case 'delete': requireSuperAdmin(); $controller->delete($_GET['id'] ?? 0); break;
            default: $controller->index(); break;
        }
        break;

    case 'admin/pendapatan':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminRevenueController.php';
        $controller = new AdminRevenueController();
        $controller->index();
        break;

    case 'admin/pengaturan':
        requireAdmin();
        require_once __DIR__ . '/backend/controllers/AdminSettingsController.php';
        $controller = new AdminSettingsController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch ($action) {
                case 'profile': $controller->updateProfile(); break;
                case 'password': $controller->updatePassword(); break;
                default: $controller->index(); break;
            }
        } else {
            $controller->index();
        }
        break;

    // ========== API ENDPOINTS ==========
    case 'api/hotels':
        header('Content-Type: application/json');
        require_once __DIR__ . '/backend/controllers/HotelController.php';
        $controller = new HotelController();
        echo json_encode($controller->apiSearch());
        break;

    case 'api/check-availability':
        header('Content-Type: application/json');
        require_once __DIR__ . '/backend/controllers/BookingController.php';
        $controller = new BookingController();
        echo json_encode($controller->apiCheckAvailability());
        break;

    case 'api/rooms':
        header('Content-Type: application/json');
        require_once __DIR__ . '/backend/controllers/HotelController.php';
        $controller = new HotelController();
        echo json_encode($controller->apiGetRooms($_GET['hotel_id'] ?? 0));
        break;

    // ========== 404 ==========
    default:
        http_response_code(404);
        $pageTitle = '404 — Halaman Tidak Ditemukan — ' . APP_NAME;
        $currentView = '404';
        include FRONTEND_PATH . '/views/layouts/app.php';
        break;
}
