<?php
/**
 * Admin Settings Controller — Lynvaii Hotel Booking System
 */

class AdminSettingsController {

    public function index() {
        $user = currentUser();
        $userModel = new User();
        $userData = $userModel->findById($user['id']);

        $pageTitle = __('admin.settings') . ' — ' . APP_NAME;
        $adminPage = 'pengaturan';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }

    public function updateProfile() {
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/pengaturan');
            return; // FIX #3
        }

        $user = currentUser();
        $userModel = new User();
        $userModel->update($user['id'], [
            'name' => sanitize($_POST['name']),
            'email' => sanitize($_POST['email']),
            'phone' => sanitize($_POST['phone'] ?? ''),
        ]);

        // Update session
        $_SESSION['user_name'] = sanitize($_POST['name']);
        $_SESSION['user_email'] = sanitize($_POST['email']);

        setFlash('success', 'Profil berhasil diperbarui.');
        redirect('?page=admin/pengaturan');
    }

    public function updatePassword() {
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/pengaturan');
            return; // FIX #3
        }

        $user = currentUser();
        $userModel = new User();
        $userData = $userModel->findById($user['id']);

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$userModel->verifyPassword($currentPassword, $userData['password'])) {
            setFlash('error', 'Password saat ini salah.');
            redirect('?page=admin/pengaturan');
            return; // FIX #3
        }

        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            setFlash('error', 'Password baru minimal ' . PASSWORD_MIN_LENGTH . ' karakter.');
            redirect('?page=admin/pengaturan');
            return; // FIX #3
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
            redirect('?page=admin/pengaturan');
            return; // FIX #3
        }

        $userModel->updatePassword($user['id'], $newPassword);
        setFlash('success', 'Password berhasil diubah.');
        redirect('?page=admin/pengaturan');
    }
}
