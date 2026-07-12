<?php
/**
 * Review Controller — Lynvaii Hotel Booking System
 */

class ReviewController {

    public function create($bookingId) {
        requireLogin();
        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($bookingId);

        if (!$booking || $booking['user_id'] != $user['id']) {
            setFlash('error', 'Booking tidak ditemukan.');
            redirect('?page=my-bookings');
            return;
        }

        if ($booking['status'] !== 'completed') {
            setFlash('error', 'Review hanya bisa diberikan untuk booking yang sudah selesai.');
            redirect('?page=my-bookings');
            return;
        }

        // Cek sudah ada review
        $db   = db();
        $stmt = $db->prepare("SELECT id FROM reviews WHERE booking_id = ? AND user_id = ?");
        $stmt->execute([$bookingId, $user['id']]);
        if ($stmt->fetch()) {
            setFlash('error', 'Anda sudah memberikan review untuk booking ini.');
            redirect('?page=my-bookings');
            return;
        }

        $pageTitle   = 'Tulis Review — ' . APP_NAME;
        $currentView = 'review';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function store($bookingId) {
        requireLogin();
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token tidak valid.');
            redirect('?page=review&booking_id=' . $bookingId);
            return;
        }

        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($bookingId);

        if (!$booking || $booking['user_id'] != $user['id'] || $booking['status'] !== 'completed') {
            setFlash('error', 'Akses ditolak.');
            redirect('?page=my-bookings');
            return;
        }

        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = sanitize($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            setFlash('error', 'Rating harus antara 1 dan 5.');
            redirect('?page=review&booking_id=' . $bookingId);
            return;
        }

        $db = db();

        // Cek duplikat
        $stmt = $db->prepare("SELECT id FROM reviews WHERE booking_id = ? AND user_id = ?");
        $stmt->execute([$bookingId, $user['id']]);
        if ($stmt->fetch()) {
            setFlash('error', 'Anda sudah memberikan review untuk booking ini.');
            redirect('?page=my-bookings');
            return;
        }

        $stmt = $db->prepare(
            "INSERT INTO reviews (user_id, hotel_id, booking_id, rating, comment, is_approved)
             VALUES (?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([$user['id'], $booking['hotel_id'], $bookingId, $rating, $comment]);

        // Update rating hotel
        $hotelModel = new Hotel();
        $hotelModel->updateRating($booking['hotel_id']);

        setFlash('success', 'Review berhasil dikirim! Terima kasih atas ulasan Anda. ⭐');
        redirect('?page=hotel&id=' . $booking['hotel_id']);
    }

    /**
     * API: ambil reviews untuk satu hotel
     */
    public function getByHotel($hotelId, $limit = 10) {
        $db   = db();
        $stmt = $db->prepare(
            "SELECT r.*, u.name as user_name, u.avatar as user_avatar
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.hotel_id = ? AND r.is_approved = 1
             ORDER BY r.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$hotelId, $limit]);
        return $stmt->fetchAll();
    }
}
