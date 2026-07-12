<?php
/**
 * Profile Controller — Lynvaii Hotel Booking System
 */

class ProfileController {

    public function index() {
        requireLogin();
        $userModel = new User();
        $user      = $userModel->findById(currentUser()['id']);

        if (!$user) {
            setFlash('error', 'Pengguna tidak ditemukan.');
            redirect('');
            return;
        }

        $pageTitle   = 'Profil Saya — ' . APP_NAME;
        $currentView = 'profil';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function updateProfile() {
        requireLogin();
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token tidak valid.');
            redirect('?page=profil');
            return;
        }

        $userId    = currentUser()['id'];
        $userModel = new User();

        $name  = sanitize($_POST['name']  ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        $errors = [];
        if (empty($name)) $errors[] = 'Nama tidak boleh kosong.';

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('?page=profil');
            return;
        }

        // Handle avatar upload
        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['avatar'];
            $mimeType = mime_content_type($file['tmp_name']);

            if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
                setFlash('error', 'Format avatar tidak valid. Gunakan JPEG, PNG, atau WebP.');
                redirect('?page=profil');
                return;
            }
            if ($file['size'] > MAX_UPLOAD_SIZE) {
                setFlash('error', 'Ukuran avatar terlalu besar. Maksimal 5MB.');
                redirect('?page=profil');
                return;
            }

            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            $destDir  = UPLOAD_PATH . '/avatars/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);

            if (move_uploaded_file($file['tmp_name'], $destDir . $fileName)) {
                $avatarPath = 'uploads/avatars/' . $fileName;
            }
        }

        $data = ['name' => $name, 'phone' => $phone];
        if ($avatarPath) $data['avatar'] = $avatarPath;

        $userModel->update($userId, $data);

        // Refresh session
        $_SESSION['user_name']   = $name;
        if ($avatarPath) $_SESSION['user_avatar'] = $avatarPath;

        setFlash('success', 'Profil berhasil diperbarui.');
        redirect('?page=profil');
    }

    public function updatePassword() {
        requireLogin();
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token tidak valid.');
            redirect('?page=profil');
            return;
        }

        $userId          = currentUser()['id'];
        $userModel       = new User();
        $user            = $userModel->findById($userId);
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$userModel->verifyPassword($currentPassword, $user['password'])) {
            setFlash('error', 'Password saat ini tidak benar.');
            redirect('?page=profil');
            return;
        }

        if (strlen($newPassword) < 8) {
            setFlash('error', 'Password baru minimal 8 karakter.');
            redirect('?page=profil');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
            redirect('?page=profil');
            return;
        }

        $userModel->updatePassword($userId, $newPassword);
        setFlash('success', 'Password berhasil diubah. Silakan login kembali.');
        redirect('?page=profil');
    }
}
