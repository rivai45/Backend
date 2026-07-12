<?php
/**
 * Payment Controller — Upload Bukti Pembayaran
 */

class PaymentController {

    public function uploadForm($bookingId) {
        requireLogin();
        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($bookingId);

        if (!$booking || $booking['user_id'] != $user['id']) {
            setFlash('error', 'Booking tidak ditemukan atau akses ditolak.');
            redirect('?page=my-bookings');
            return;
        }

        if ($booking['status'] !== 'pending') {
            setFlash('error', 'Bukti hanya bisa diupload untuk booking yang masih pending.');
            redirect('?page=booking-detail&id=' . $bookingId);
            return;
        }

        // Ambil payment record
        $db   = db();
        $stmt = $db->prepare("SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$bookingId]);
        $payment = $stmt->fetch();

        $pageTitle   = 'Upload Bukti Pembayaran — ' . APP_NAME;
        $currentView = 'upload-bukti';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function upload($bookingId) {
        requireLogin();

        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token tidak valid. Silakan coba lagi.');
            redirect('?page=upload-bukti&booking_id=' . $bookingId);
            return;
        }

        $bookingModel = new Booking();
        $user         = currentUser();
        $booking      = $bookingModel->findById($bookingId);

        if (!$booking || $booking['user_id'] != $user['id'] || $booking['status'] !== 'pending') {
            setFlash('error', 'Akses ditolak atau booking tidak valid.');
            redirect('?page=my-bookings');
            return;
        }

        // Validasi file
        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Silakan pilih file bukti transfer.');
            redirect('?page=upload-bukti&booking_id=' . $bookingId);
            return;
        }

        $file     = $_FILES['payment_proof'];
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            setFlash('error', 'Format file tidak valid. Gunakan JPEG, PNG, atau WebP.');
            redirect('?page=upload-bukti&booking_id=' . $bookingId);
            return;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            setFlash('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
            redirect('?page=upload-bukti&booking_id=' . $bookingId);
            return;
        }

        // Simpan file
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'bukti_' . $booking['booking_code'] . '_' . time() . '.' . $ext;
        $destDir  = UPLOAD_PATH . '/bukti/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $destPath = $destDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', 'Gagal menyimpan file. Silakan coba lagi.');
            redirect('?page=upload-bukti&booking_id=' . $bookingId);
            return;
        }

        $proofUrl = 'uploads/bukti/' . $fileName;

        // Update record payment
        $db = db();
        $stmt = $db->prepare(
            "UPDATE payments SET payment_proof = ?, updated_at = NOW() WHERE booking_id = ?"
        );
        $stmt->execute([$proofUrl, $bookingId]);

        // Jika tidak ada payment record, buat baru
        if ($stmt->rowCount() === 0) {
            $stmt2 = $db->prepare(
                "INSERT INTO payments (booking_id, payment_method, amount, status, payment_proof, expired_at)
                 VALUES (?, 'bank_transfer', ?, 'pending', ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))"
            );
            $stmt2->execute([$bookingId, $booking['total_price'], $proofUrl]);
        }

        setFlash('success', 'Bukti transfer berhasil diupload! Admin akan memverifikasi pembayaran Anda.');
        redirect('?page=booking-detail&id=' . $bookingId);
    }
}
