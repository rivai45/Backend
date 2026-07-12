<?php
/**
 * Admin Member Controller — Lynvaii Hotel Booking System
 */

class AdminMemberController {

    public function index() {
        $userModel = new User();
        $filters   = [
            'search' => $_GET['search'] ?? '',
            'role'   => $_GET['role']   ?? '',
        ];
        $users      = $userModel->getAll($filters);
        $totalUsers = $userModel->count($filters);

        $pageTitle = __('admin.members') . ' — ' . APP_NAME;
        $adminPage = 'anggota';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/anggota');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/anggota');
            return;
        }

        $userModel = new User();
        $email     = sanitize($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email tidak valid.');
            redirect('?page=admin/anggota');
            return;
        }

        // Validate password length (Bug 12 fix)
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            setFlash('error', 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter.');
            redirect('?page=admin/anggota');
            return;
        }

        if ($userModel->findByEmail($email)) {
            setFlash('error', 'Email sudah terdaftar.');
            redirect('?page=admin/anggota');
            return;
        }

        // Only super_admin can create admin/super_admin
        $role = sanitize($_POST['role'] ?? 'user');
        if (in_array($role, ['admin', 'super_admin']) && !isSuperAdmin()) {
            $role = 'user';
        }

        $userModel->create([
            'name'     => sanitize($_POST['name']),
            'email'    => $email,
            'password' => $password,
            'phone'    => sanitize($_POST['phone'] ?? ''),
            'role'     => $role,
        ]);

        setFlash('success', 'Pengguna berhasil ditambahkan.');
        redirect('?page=admin/anggota');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/anggota');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/anggota');
            return;
        }

        $userModel = new User();
        $data = [
            'name'      => sanitize($_POST['name']),
            'email'     => sanitize($_POST['email']),
            'phone'     => sanitize($_POST['phone'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        // Only super_admin can change roles
        if (isSuperAdmin() && isset($_POST['role'])) {
            $data['role'] = sanitize($_POST['role']);
        }

        // Update password if provided
        if (!empty($_POST['password'])) {
            $newPass = $_POST['password'];
            if (strlen($newPass) < PASSWORD_MIN_LENGTH) {
                setFlash('error', 'Password baru minimal ' . PASSWORD_MIN_LENGTH . ' karakter.');
                redirect('?page=admin/anggota');
                return;
            }
            $userModel->updatePassword($id, $newPass);
        }

        $userModel->update($id, $data);
        setFlash('success', 'Data pengguna berhasil diperbarui.');
        redirect('?page=admin/anggota');
    }

    public function delete($id) {
        // Cannot delete self
        if ($id == ($_SESSION['user_id'] ?? 0)) {
            setFlash('error', 'Tidak dapat menghapus akun sendiri.');
            redirect('?page=admin/anggota');
            return;
        }

        $userModel = new User();
        $user      = $userModel->findById($id);

        if (!$user) {
            setFlash('error', 'Pengguna tidak ditemukan.');
            redirect('?page=admin/anggota');
            return;
        }

        // Only super_admin can delete admin
        if (in_array($user['role'], ['admin', 'super_admin']) && !isSuperAdmin()) {
            setFlash('error', 'Tidak memiliki izin untuk menghapus admin.');
            redirect('?page=admin/anggota');
            return;
        }

        $userModel->softDelete($id);
        setFlash('success', 'Pengguna berhasil dihapus.');
        redirect('?page=admin/anggota');
    }
}
