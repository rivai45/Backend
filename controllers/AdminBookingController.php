<?php
/**
 * Admin Booking Controller — Lynvaii Hotel Booking System
 */

class AdminBookingController {

    public function index() {
        $bookingModel = new Booking();
        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
            'limit'  => PER_PAGE,
            'page'   => $_GET['p'] ?? 1,
        ];

        $bookings     = $bookingModel->getAll($filters);
        $totalBookings = $bookingModel->count($filters);
        $totalPages   = ceil($totalBookings / PER_PAGE);

        // For dropdowns in modal
        $hotelModel = new Hotel();
        $userModel  = new User();
        $hotels     = $hotelModel->getAll(['active_only' => true]);
        $users      = $userModel->getAll(['role' => 'user']);

        $pageTitle = __('admin.booking') . ' — ' . APP_NAME;
        $adminPage = 'booking';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }

    /**
     * create() — placeholder so the route doesn't Fatal Error
     * The actual form is embedded in a modal on the index page.
     */
    public function create() {
        redirect('?page=admin/booking');
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/booking');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/booking');
            return;
        }

        $bookingModel = new Booking();
        $roomModel    = new Room();

        $room = $roomModel->findById((int)$_POST['room_id']);
        if (!$room) {
            setFlash('error', 'Kamar tidak ditemukan.');
            redirect('?page=admin/booking');
            return;
        }

        // Check availability
        $checkIn  = sanitize($_POST['check_in']);
        $checkOut = sanitize($_POST['check_out']);
        if (!$bookingModel->checkAvailability($room['id'], $checkIn, $checkOut)) {
            setFlash('error', 'Kamar tidak tersedia untuk tanggal tersebut.');
            redirect('?page=admin/booking');
            return;
        }

        $bookingModel->create([
            'user_id'       => (int)$_POST['user_id'],
            'room_id'       => (int)$_POST['room_id'],
            'hotel_id'      => $room['hotel_id'],
            'check_in'      => $checkIn,
            'check_out'     => $checkOut,
            'guests'        => (int)($_POST['guests'] ?? 1),
            'room_price'    => $room['price_per_night'],
            'guest_name'    => sanitize($_POST['guest_name']),
            'guest_email'   => sanitize($_POST['guest_email']),
            'guest_phone'   => sanitize($_POST['guest_phone'] ?? ''),
        ]);

        setFlash('success', 'Booking berhasil ditambahkan.');
        redirect('?page=admin/booking');
    }

    public function edit($id) {
        $bookingModel = new Booking();
        $booking = $bookingModel->findById($id);
        if (!$booking) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan.']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $booking]);
        exit;
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/booking');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/booking');
            return;
        }

        $db     = db();
        $fields = [];
        $values = [];

        foreach (['guest_name', 'guest_email', 'guest_phone', 'check_in', 'check_out', 'guests', 'status'] as $field) {
            if (isset($_POST[$field])) {
                $fields[] = "$field = ?";
                $values[] = sanitize($_POST[$field]);
            }
        }

        if (empty($fields)) {
            setFlash('error', 'Tidak ada data yang diubah.');
            redirect('?page=admin/booking');
            return;
        }

        $values[] = $id;
        $stmt = $db->prepare("UPDATE bookings SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?");
        $stmt->execute($values);

        // Update status timestamps
        if (isset($_POST['status'])) {
            $bookingModel = new Booking();
            $bookingModel->updateStatus($id, sanitize($_POST['status']));
        }

        setFlash('success', 'Booking berhasil diperbarui.');
        redirect('?page=admin/booking');
    }

    public function updateStatus($id) {
        $status = sanitize($_GET['status'] ?? '');
        $allowed = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            setFlash('error', 'Status tidak valid.');
            redirect('?page=admin/booking');
            return;
        }
        $bookingModel = new Booking();
        $bookingModel->updateStatus($id, $status);
        setFlash('success', 'Status booking diperbarui menjadi ' . $status . '.');
        redirect('?page=admin/booking');
    }

    public function delete($id) {
        $bookingModel = new Booking();
        $booking = $bookingModel->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking tidak ditemukan.');
            redirect('?page=admin/booking');
            return;
        }
        $bookingModel->delete($id);
        setFlash('success', 'Booking berhasil dihapus.');
        redirect('?page=admin/booking');
    }
}
