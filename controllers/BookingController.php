<?php
/**
 * Booking Controller — Lynvaii Hotel Booking System
 */

class BookingController {

    public function index() {
        requireLogin();
        $hotelModel = new Hotel();
        $roomModel  = new Room();

        $hotels = $hotelModel->getAll(['active_only' => true]);

        // Pre-fill from hotel detail page
        $selectedHotelId = $_GET['hotel_id'] ?? null;
        $selectedRoomId  = $_GET['room_id']  ?? null;
        $selectedHotel   = $selectedHotelId ? $hotelModel->findById($selectedHotelId) : null;
        $rooms           = $selectedHotelId  ? $roomModel->getByHotel($selectedHotelId) : [];
        $selectedRoom    = $selectedRoomId   ? $roomModel->findById($selectedRoomId) : null;

        $pageTitle   = __('booking.title') . ' — ' . APP_NAME;
        $currentView = 'pemesanan';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function store() {
        requireLogin();
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid security token. Silakan coba lagi.');
            redirect('?page=pemesanan');
            return;
        }

        $bookingModel = new Booking();
        $roomModel    = new Room();

        $roomId   = (int)($_POST['room_id'] ?? 0);
        $checkIn  = sanitize($_POST['check_in'] ?? '');
        $checkOut = sanitize($_POST['check_out'] ?? '');

        // Validate dates
        if (empty($checkIn) || empty($checkOut)) {
            setFlash('error', 'Tanggal check-in dan check-out harus diisi.');
            redirect('?page=pemesanan');
            return;
        }

        if (strtotime($checkOut) <= strtotime($checkIn)) {
            setFlash('error', 'Tanggal check-out harus setelah check-in.');
            redirect('?page=pemesanan');
            return;
        }

        if (strtotime($checkIn) < strtotime(date('Y-m-d'))) {
            setFlash('error', 'Tanggal check-in tidak boleh sebelum hari ini.');
            redirect('?page=pemesanan');
            return;
        }

        // Validate room
        $room = $roomModel->findById($roomId);
        if (!$room) {
            setFlash('error', 'Kamar tidak ditemukan.');
            redirect('?page=pemesanan');
            return;
        }

        // Check availability
        if (!$bookingModel->checkAvailability($roomId, $checkIn, $checkOut)) {
            setFlash('error', 'Maaf, kamar tidak tersedia untuk tanggal yang dipilih.');
            redirect('?page=pemesanan');
            return;
        }

        $user   = currentUser();
        $result = $bookingModel->create([
            'user_id'         => $user['id'],
            'room_id'         => $roomId,
            'hotel_id'        => $room['hotel_id'],
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'guests'          => (int)($_POST['guests'] ?? 1),
            'room_price'      => $room['price_per_night'],
            'guest_name'      => sanitize($_POST['guest_name'] ?? $user['name']),
            'guest_email'     => sanitize($_POST['guest_email'] ?? $user['email']),
            'guest_phone'     => sanitize($_POST['guest_phone'] ?? ''),
            'special_requests'=> sanitize($_POST['special_requests'] ?? ''),
        ]);

        // Create payment record
        $db   = db();
        $stmt = $db->prepare(
            "INSERT INTO payments (booking_id, payment_method, bank_name, account_number, account_name, amount, status, expired_at) 
             VALUES (?, ?, ?, ?, ?, ?, 'pending', DATE_ADD(NOW(), INTERVAL 24 HOUR))"
        );
        $stmt->execute([
            $result['id'],
            sanitize($_POST['payment_method'] ?? 'bank_transfer'),
            sanitize($_POST['bank_name']       ?? ''),
            sanitize($_POST['account_number']  ?? ''),
            sanitize($_POST['account_name']    ?? ''),
            $result['total_price'],
        ]);

        setFlash('success', 'Pemesanan berhasil! Kode booking: ' . $result['booking_code'] . '. Silakan lakukan pembayaran.');
        redirect('?page=my-bookings');
    }

    public function myBookings() {
        $bookingModel = new Booking();
        $user         = currentUser();
        $bookings     = $bookingModel->getByUser($user['id'], 20);

        $pageTitle   = __('nav.my_bookings') . ' — ' . APP_NAME;
        $currentView = 'my-bookings';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function cancel($id) {
        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($id);

        if (!$booking) {
            setFlash('error', 'Booking tidak ditemukan.');
            redirect('?page=my-bookings');
            return;
        }

        // Pastikan booking milik user ini
        if ($booking['user_id'] != $user['id']) {
            setFlash('error', 'Akses ditolak.');
            redirect('?page=my-bookings');
            return;
        }

        // Hanya bisa cancel jika masih pending
        if ($booking['status'] !== 'pending') {
            setFlash('error', 'Booking tidak dapat dibatalkan karena sudah diproses.');
            redirect('?page=my-bookings');
            return;
        }

        $bookingModel->updateStatus($id, 'cancelled');
        setFlash('success', 'Booking berhasil dibatalkan.');
        redirect('?page=my-bookings');
    }

    public function apiCheckAvailability() {
        $bookingModel = new Booking();
        $roomId   = (int)($_GET['room_id']  ?? 0);
        $checkIn  = $_GET['check_in']  ?? '';
        $checkOut = $_GET['check_out'] ?? '';

        if (!$roomId || !$checkIn || !$checkOut) {
            return ['success' => false, 'message' => 'Parameter tidak lengkap'];
        }

        $available = $bookingModel->checkAvailability($roomId, $checkIn, $checkOut);
        return ['success' => true, 'available' => $available];
    }
}
