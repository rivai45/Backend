<?php
/**
 * Auth Controller — Lynvaii Hotel Booking System
 */

class AuthController {

    public function loginForm() {
        if (isLoggedIn()) redirect('');
        $pageTitle   = __('auth.login_title');
        $currentView = 'auth/login';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function doLogin() {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('error', 'Email dan password harus diisi.');
            redirect('?page=login');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        if (!$user) {
            setFlash('error', 'Email atau password salah.');
            redirect('?page=login');
            return;
        }

        if (!$userModel->verifyPassword($password, $user['password'])) {
            setFlash('error', 'Email atau password salah.');
            redirect('?page=login');
            return;
        }

        if (!$user['is_active']) {
            setFlash('error', 'Akun Anda telah dinonaktifkan.');
            redirect('?page=login');
            return;
        }

        setUserSession($user);

        // Try to update last login (non-fatal if column missing)
        try {
            $userModel->updateLastLogin($user['id']);
        } catch (Exception $e) {
            // Silently ignore
        }

        setFlash('success', 'Selamat datang kembali, ' . $user['name'] . '!');
        redirect('');
    }

    public function registerForm() {
        if (isLoggedIn()) redirect('');
        $pageTitle   = __('auth.register_title');
        $currentView = 'auth/register';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function doRegister() {
        $name            = sanitize($_POST['name']             ?? '');
        $email           = sanitize($_POST['email']            ?? '');
        $phone           = sanitize($_POST['phone']            ?? '');
        $password        = $_POST['password']         ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Server-side validation
        $errors = [];
        if (empty($name))  $errors[] = 'Nama harus diisi.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
        if (strlen($password) < 8)         $errors[] = 'Password minimal 8 karakter.';
        if ($password !== $confirmPassword) $errors[] = 'Konfirmasi password tidak cocok.';

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('?page=register');
            return;
        }

        $userModel = new User();

        // Check duplicate email
        if ($userModel->findByEmail($email)) {
            setFlash('error', 'Email sudah terdaftar. Silakan gunakan email lain atau login.');
            redirect('?page=register');
            return;
        }

        // Create user
        try {
            $userId = $userModel->create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'phone'    => $phone,
                'role'     => 'user',
                'language' => getLang(),
            ]);

            // Get the newly created user
            $user = $userModel->findById($userId);

            if (!$user) {
                // Fallback: find by email
                $user = $userModel->findByEmail($email);
            }

            if (!$user) {
                setFlash('error', 'Terjadi kesalahan. Silakan coba lagi.');
                redirect('?page=register');
                return;
            }

            setUserSession($user);
            setFlash('success', 'Registrasi berhasil! Selamat datang, ' . $name . '!');
            redirect('');

        } catch (Exception $e) {
            setFlash('error', 'Gagal membuat akun: ' . $e->getMessage());
            redirect('?page=register');
        }
    }

    public function adminLoginForm() {
        if (isAdmin()) redirect('?page=admin/dashboard');
        $pageTitle   = __('auth.admin_title');
        $currentView = 'auth/admin-login';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function doAdminLogin() {
        $email    = sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('error', 'Email dan password harus diisi.');
            redirect('?page=admin/login');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        if (!$user) {
            setFlash('error', 'Email atau password salah.');
            redirect('?page=admin/login');
            return;
        }

        if (!$userModel->verifyPassword($password, $user['password'])) {
            setFlash('error', 'Email atau password salah.');
            redirect('?page=admin/login');
            return;
        }

        if (!in_array($user['role'], ['admin', 'super_admin'])) {
            setFlash('error', 'Anda tidak memiliki akses admin.');
            redirect('?page=admin/login');
            return;
        }

        if (!$user['is_active']) {
            setFlash('error', 'Akun Anda telah dinonaktifkan.');
            redirect('?page=admin/login');
            return;
        }

        setUserSession($user);

        try {
            $userModel->updateLastLogin($user['id']);
        } catch (Exception $e) {
            // Silently ignore
        }

        setFlash('success', 'Selamat datang, ' . $user['name'] . '!');
        redirect('?page=admin/dashboard');
    }

    public function logout() {
        $wasAdmin = isAdmin();
        destroyUserSession();
        // Restart session so flash message can be stored
        session_name(SESSION_NAME);
        session_start();
        setFlash('success', 'Anda telah keluar dari sistem.');
        redirect($wasAdmin ? '?page=admin/login' : '?page=login');
    }

    // ─── FORGOT PASSWORD ─────────────────────────────────────────────────────

    public function forgotPasswordForm() {
        if (isLoggedIn()) redirect('');
        $pageTitle   = 'Lupa Password — ' . APP_NAME;
        $currentView = 'auth/forgot-password';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function doForgotPassword() {
        $email = sanitize($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email tidak valid.');
            redirect('?page=forgot-password');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        // Selalu tampilkan pesan sukses (security: jangan bocorkan apakah email terdaftar)
        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 jam

            $db = db();
            // Hapus token lama untuk email ini
            $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            // Simpan token baru
            $db->prepare(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
            )->execute([$email, $token, $expires]);

            // Dalam production: kirim via email. Di sini: tampilkan token di flash (simulasi)
            $resetUrl = BASE_URL . '?page=reset-password&token=' . $token;
            setFlash('success',
                '✅ Link reset password telah dibuat. ' .
                '<br>Untuk simulasi (tanpa email), klik: <a href="' . $resetUrl . '" style="color:var(--color-gold)">' . $resetUrl . '</a>'
            );
        } else {
            setFlash('success', '✅ Jika email terdaftar, Anda akan menerima link reset password.');
        }

        redirect('?page=forgot-password');
    }

    public function showResetForm() {
        if (isLoggedIn()) redirect('');
        $token        = sanitize($_GET['token'] ?? '');
        $isValidToken = false;

        if ($token) {
            $db   = db();
            $stmt = $db->prepare(
                "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL"
            );
            $stmt->execute([$token]);
            $isValidToken = (bool)$stmt->fetch();
        }

        $pageTitle   = 'Reset Password — ' . APP_NAME;
        $currentView = 'auth/reset-password';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function doResetPassword() {
        $token           = sanitize($_POST['token']           ?? '');
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token tidak valid.');
            redirect('?page=reset-password&token=' . $token);
            return;
        }

        if (strlen($newPassword) < 8) {
            setFlash('error', 'Password minimal 8 karakter.');
            redirect('?page=reset-password&token=' . $token);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
            redirect('?page=reset-password&token=' . $token);
            return;
        }

        $db   = db();
        $stmt = $db->prepare(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL"
        );
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            setFlash('error', 'Link reset tidak valid atau sudah kedaluwarsa.');
            redirect('?page=forgot-password');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByEmail($reset['email']);
        if (!$user) {
            setFlash('error', 'Akun tidak ditemukan.');
            redirect('?page=forgot-password');
            return;
        }

        $userModel->updatePassword($user['id'], $newPassword);

        // Tandai token sebagai sudah digunakan
        $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE token = ?")->execute([$token]);

        setFlash('success', '✅ Password berhasil direset! Silakan login dengan password baru Anda.');
        redirect('?page=login');
    }
}
