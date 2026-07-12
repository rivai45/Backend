<?php
/**
 * Admin Dashboard Controller — Lynvaii Hotel Booking System
 */

class AdminController {
    public function dashboard() {
        $bookingModel = new Booking();
        $userModel    = new User();
        $hotelModel   = new Hotel();
        $db           = db();

        $stats = $bookingModel->getStats();
        $stats['total_users']  = $userModel->count(['role' => 'user']);
        $stats['total_hotels'] = $hotelModel->count(['active_only' => true]);

        // ── Hitung persentase perubahan bulan ini vs bulan lalu ──────────────
        $thisMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        // User baru bulan ini vs bulan lalu
        $r = $db->query("SELECT
            SUM(DATE_FORMAT(created_at,'%Y-%m')='{$thisMonth}') as this_m,
            SUM(DATE_FORMAT(created_at,'%Y-%m')='{$lastMonth}') as last_m
            FROM users WHERE deleted_at IS NULL AND role='user'")->fetch();
        $stats['users_change'] = $this->calcChange($r['this_m'], $r['last_m']);

        // Booking bulan ini vs bulan lalu
        $r = $db->query("SELECT
            SUM(DATE_FORMAT(created_at,'%Y-%m')='{$thisMonth}') as this_m,
            SUM(DATE_FORMAT(created_at,'%Y-%m')='{$lastMonth}') as last_m
            FROM bookings")->fetch();
        $stats['bookings_change'] = $this->calcChange($r['this_m'], $r['last_m']);

        // Revenue bulan ini vs bulan lalu
        $r = $db->query("SELECT
            SUM(CASE WHEN DATE_FORMAT(created_at,'%Y-%m')='{$thisMonth}' AND status IN('confirmed','completed','checked_in') THEN total_price ELSE 0 END) as this_m,
            SUM(CASE WHEN DATE_FORMAT(created_at,'%Y-%m')='{$lastMonth}' AND status IN('confirmed','completed','checked_in') THEN total_price ELSE 0 END) as last_m
            FROM bookings")->fetch();
        $stats['revenue_change'] = $this->calcChange($r['this_m'], $r['last_m']);

        // Cancellation rate bulan ini vs bulan lalu
        $r = $db->query("SELECT
            SUM(CASE WHEN DATE_FORMAT(created_at,'%Y-%m')='{$thisMonth}' AND status='cancelled' THEN 1 ELSE 0 END)*100/NULLIF(SUM(DATE_FORMAT(created_at,'%Y-%m')='{$thisMonth}'),0) as this_m,
            SUM(CASE WHEN DATE_FORMAT(created_at,'%Y-%m')='{$lastMonth}' AND status='cancelled' THEN 1 ELSE 0 END)*100/NULLIF(SUM(DATE_FORMAT(created_at,'%Y-%m')='{$lastMonth}'),0) as last_m
            FROM bookings")->fetch();
        $stats['cancel_change'] = $this->calcChange($r['this_m'], $r['last_m']);

        // Recent bookings
        $recentBookings = $bookingModel->getAll(['limit' => 5, 'page' => 1]);

        $pageTitle = __('admin.dashboard') . ' — ' . APP_NAME;
        $adminPage = 'dashboard';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }

    private function calcChange($now, $prev) {
        $now  = (float)($now  ?? 0);
        $prev = (float)($prev ?? 0);
        if ($prev == 0) return $now > 0 ? ['val' => 100, 'dir' => 'up'] : ['val' => 0, 'dir' => 'none'];
        $pct = round((($now - $prev) / $prev) * 100, 1);
        return ['val' => abs($pct), 'dir' => $pct >= 0 ? 'up' : 'down'];
    }
}
