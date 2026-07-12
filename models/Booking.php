<?php
/**
 * Booking Model — Lynvaii Hotel Booking System
 */

class Booking {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT b.*, h.name as hotel_name, h.city as hotel_city, h.thumbnail as hotel_thumbnail,
                    r.type_name as room_type, r.price_per_night,
                    u.name as user_name, u.email as user_email
             FROM bookings b
             JOIN hotels h ON b.hotel_id = h.id
             JOIN rooms r ON b.room_id = r.id
             JOIN users u ON b.user_id = u.id
             WHERE b.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCode($code) {
        $stmt = $this->db->prepare(
            "SELECT b.*, h.name as hotel_name, h.city as hotel_city,
                    r.type_name as room_type
             FROM bookings b
             JOIN hotels h ON b.hotel_id = h.id
             JOIN rooms r ON b.room_id = r.id
             WHERE b.booking_code = ?"
        );
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public function getByUser($userId, $limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT b.*, h.name as hotel_name, h.city as hotel_city, h.thumbnail as hotel_thumbnail,
                    r.type_name as room_type
             FROM bookings b
             JOIN hotels h ON b.hotel_id = h.id
             JOIN rooms r ON b.room_id = r.id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function getAll($filters = []) {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "b.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['hotel_id'])) {
            $where[] = "b.hotel_id = ?";
            $params[] = $filters['hotel_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(b.booking_code LIKE ? OR b.guest_name LIKE ? OR h.name LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['date_from'])) {
            $where[] = "b.check_in >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "b.check_out <= ?";
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT b.*, h.name as hotel_name, h.city as hotel_city,
                       r.type_name as room_type, u.name as user_name, u.email as user_email
                FROM bookings b
                JOIN hotels h ON b.hotel_id = h.id
                JOIN rooms r ON b.room_id = r.id
                JOIN users u ON b.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY b.created_at DESC";

        if (!empty($filters['limit'])) {
            $offset = (($filters['page'] ?? 1) - 1) * $filters['limit'];
            $sql .= " LIMIT " . (int)$filters['limit'] . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($filters = []) {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "b.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(b.booking_code LIKE ? OR b.guest_name LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM bookings b WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    public function create($data) {
        $bookingCode = $this->generateBookingCode();
        $nights = max(1, (strtotime($data['check_out']) - strtotime($data['check_in'])) / 86400);
        $roomPrice = $data['room_price'];
        $taxAmount = ($roomPrice * $nights) * (TAX_PERCENTAGE / 100);
        $totalPrice = ($roomPrice * $nights) + $taxAmount;

        $stmt = $this->db->prepare(
            "INSERT INTO bookings (booking_code, user_id, room_id, hotel_id, check_in, check_out, 
             guests, nights, room_price, tax_amount, total_price, guest_name, guest_email, 
             guest_phone, special_requests, status, expires_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', DATE_ADD(NOW(), INTERVAL ? MINUTE))"
        );
        $stmt->execute([
            $bookingCode, $data['user_id'], $data['room_id'], $data['hotel_id'],
            $data['check_in'], $data['check_out'], $data['guests'], $nights,
            $roomPrice, $taxAmount, $totalPrice,
            $data['guest_name'], $data['guest_email'], $data['guest_phone'] ?? null,
            $data['special_requests'] ?? null, BOOKING_EXPIRY_MINUTES
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'booking_code' => $bookingCode,
            'total_price' => $totalPrice,
            'tax_amount' => $taxAmount,
            'nights' => $nights,
        ];
    }

    public function updateStatus($id, $status) {
        $extra = '';
        if ($status === 'confirmed') $extra = ', confirmed_at = NOW()';
        if ($status === 'cancelled') $extra = ', cancelled_at = NOW()';

        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = ?{$extra}, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }

    public function checkAvailability($roomId, $checkIn, $checkOut, $excludeBookingId = null) {
        $params = [$roomId, $checkOut, $checkIn];
        $excludeClause = '';
        if ($excludeBookingId) {
            $excludeClause = ' AND id != ?';
            $params[] = $excludeBookingId;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cnt FROM bookings 
             WHERE room_id = ? AND status IN ('pending','confirmed','checked_in') 
             AND check_in < ? AND check_out > ?{$excludeClause}"
        );
        $stmt->execute($params);
        $count = $stmt->fetch()['cnt'];

        // Get room stock
        $roomStmt = $this->db->prepare("SELECT stock FROM rooms WHERE id = ?");
        $roomStmt->execute([$roomId]);
        $stock = $roomStmt->fetch()['stock'] ?? 0;

        return $count < $stock;
    }

    public function expireOldBookings() {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'expired', updated_at = NOW() 
             WHERE status = 'pending' AND expires_at IS NOT NULL AND expires_at < NOW()"
        );
        return $stmt->execute();
    }

    public function generateBookingCode() {
        $date = date('Ymd');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cnt FROM bookings WHERE booking_code LIKE ?"
        );
        $stmt->execute(["TRX-{$date}-%"]);
        $count = $stmt->fetch()['cnt'] + 1;
        return "TRX-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getStats() {
        $stats = [];

        // Total bookings
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings");
        $stats['total_bookings'] = $stmt->fetch()['total'];

        // Total revenue (paid)
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE status IN ('confirmed','completed','checked_in')"
        );
        $stats['total_revenue'] = $stmt->fetch()['total'];

        // Cancellation rate
        $stmt = $this->db->query(
            "SELECT 
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                COUNT(*) as total
             FROM bookings"
        );
        $row = $stmt->fetch();
        $stats['cancellation_rate'] = $row['total'] > 0 
            ? round(($row['cancelled'] / $row['total']) * 100, 1) 
            : 0;

        // Monthly revenue (last 6 months)
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                    COALESCE(SUM(total_price), 0) as revenue,
                    COUNT(*) as bookings
             FROM bookings 
             WHERE status IN ('confirmed','completed','checked_in')
             AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month"
        );
        $stats['monthly_revenue'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Status distribution
        $stmt = $this->db->query(
            "SELECT status, COUNT(*) as count FROM bookings GROUP BY status"
        );
        $stats['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
