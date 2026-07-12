<?php
/**
 * Home Controller — Lynvaii Hotel Booking System
 */

class HomeController {
    public function index() {
        $hotelModel = new Hotel();
        $featuredHotels = $hotelModel->getFeatured(3);
        $popularHotels = $hotelModel->getPopular(4);
        $pageTitle = APP_NAME . ' — ' . __('hero.subtitle');
        $currentView = 'home';

        include FRONTEND_PATH . '/views/layouts/app.php';
    }
}
