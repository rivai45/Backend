<?php
/**
 * Booking Detail Controller — Lynvaii Hotel Booking System
 */

class BookingDetailController {

    public function index($id) {
        requireLogin();

        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($id);

        // Guard: booking harus ada & milik user ini
        if (!$booking || $booking['user_id'] != $user['id']) {
            setFlash('error', 'Booking tidak ditemukan atau akses ditolak.');
            redirect('?page=my-bookings');
            return;
        }

        // Ambil data payment
        $db   = db();
        $stmt = $db->prepare(
            "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$id]);
        $payment = $stmt->fetch();

        // Cek sudah ada review atau belum
        $reviewStmt = $db->prepare(
            "SELECT id FROM reviews WHERE booking_id = ? AND user_id = ? LIMIT 1"
        );
        $reviewStmt->execute([$id, $user['id']]);
        $existingReview = $reviewStmt->fetch();

        $pageTitle   = 'Detail Booking ' . $booking['booking_code'] . ' — ' . APP_NAME;
        $currentView = 'booking-detail';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }
}
