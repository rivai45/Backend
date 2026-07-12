<?php
/**
 * Admin Revenue Controller — Lynvaii Hotel Booking System
 */

class AdminRevenueController {
    public function index() {
        $bookingModel = new Booking();
        $stats = $bookingModel->getStats();

        // Monthly data for chart
        $monthlyData = $stats['monthly_revenue'] ?? [];

        // Calculate totals
        $totalRevenue = $stats['total_revenue'];
        $totalBookings = $stats['total_bookings'];

        $pageTitle = __('admin.revenue') . ' — ' . APP_NAME;
        $adminPage = 'pendapatan';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }
}
