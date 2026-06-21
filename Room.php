<?php
/**
 * Room Model — Lynvaii Hotel Booking System
 */

class Room {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT r.*, h.name as hotel_name, h.city as hotel_city 
             FROM rooms r JOIN hotels h ON r.hotel_id = h.id 
             WHERE r.id = ? AND r.deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByHotel($hotelId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM rooms WHERE hotel_id = ? AND is_active = 1 AND deleted_at IS NULL ORDER BY price_per_night ASC"
        );
        $stmt->execute([$hotelId]);
        return $stmt->fetchAll();
    }

    public function getAll($filters = []) {
        $where = ["r.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['hotel_id'])) {
            $where[] = "r.hotel_id = ?";
            $params[] = $filters['hotel_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(r.type_name LIKE ? OR h.name LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT r.*, h.name as hotel_name, h.city as hotel_city 
                FROM rooms r JOIN hotels h ON r.hotel_id = h.id 
                WHERE " . implode(' AND ', $where) . " ORDER BY h.name, r.price_per_night";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO rooms (hotel_id, type_name, description_id, description_en, price_per_night, capacity, stock, amenities) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['hotel_id'], $data['type_name'], $data['description_id'] ?? null,
            $data['description_en'] ?? null, $data['price_per_night'],
            $data['capacity'] ?? 2, $data['stock'] ?? 1,
            json_encode($data['amenities'] ?? [])
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            if ($key === 'amenities' && is_array($value)) $value = json_encode($value);
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE rooms SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function softDelete($id) {
        $stmt = $this->db->prepare("UPDATE rooms SET deleted_at = NOW(), is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count($filters = []) {
        $where = ["deleted_at IS NULL"];
        $params = [];
        if (!empty($filters['hotel_id'])) {
            $where[] = "hotel_id = ?";
            $params[] = $filters['hotel_id'];
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rooms WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
}
