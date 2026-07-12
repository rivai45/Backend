<?php
/**
 * Hotel Controller — Lynvaii Hotel Booking System
 */

class HotelController {
    
    public function index() {
        $hotelModel = new Hotel();
        $filters = [
            'active_only' => true,
            'search' => $_GET['search'] ?? '',
            'city' => $_GET['city'] ?? '',
            'sort' => $_GET['sort'] ?? 'rating',
            'limit' => 8,
            'page' => $_GET['p'] ?? 1,
        ];

        $hotels = $hotelModel->getAll($filters);
        $totalHotels = $hotelModel->count($filters);
        $cities = $hotelModel->getCities();
        $totalPages = ceil($totalHotels / $filters['limit']);
        $currentPage = (int)$filters['page'];

        $pageTitle = __('destinations.title') . ' — ' . APP_NAME;
        $currentView = 'destinasi';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function detail($id) {
        $hotelModel = new Hotel();
        $roomModel = new Room();
        
        $hotel = $hotelModel->findById($id);
        if (!$hotel) {
            setFlash('error', 'Hotel tidak ditemukan.');
            redirect('destinasi');
        }

        $rooms = $roomModel->getByHotel($id);

        // Get reviews
        $db = db();
        $stmt = $db->prepare(
            "SELECT r.*, u.name as user_name, u.avatar 
             FROM reviews r JOIN users u ON r.user_id = u.id 
             WHERE r.hotel_id = ? AND r.is_approved = 1 
             ORDER BY r.created_at DESC LIMIT 10"
        );
        $stmt->execute([$id]);
        $reviews = $stmt->fetchAll();

        $pageTitle = $hotel['name'] . ' — ' . APP_NAME;
        $currentView = 'detail-hotel';
        include FRONTEND_PATH . '/views/layouts/app.php';
    }

    public function apiSearch() {
        $hotelModel = new Hotel();
        $filters = [
            'active_only' => true,
            'search' => $_GET['search'] ?? '',
            'city' => $_GET['city'] ?? '',
        ];
        return ['success' => true, 'data' => $hotelModel->getAll($filters)];
    }

    public function apiGetRooms($hotelId) {
        $roomModel = new Room();
        $rooms = $roomModel->getByHotel($hotelId);
        return ['success' => true, 'data' => $rooms];
    }
}
