<?php
/**
 * Hotel Model — Lynvaii Hotel Booking System
 */

class Hotel {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE slug = ? AND deleted_at IS NULL AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function getFeatured($limit = 3) {
        $stmt = $this->db->prepare(
            "SELECT * FROM hotels WHERE is_featured = 1 AND is_active = 1 AND deleted_at IS NULL ORDER BY rating DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getPopular($limit = 4) {
        $stmt = $this->db->prepare(
            "SELECT * FROM hotels WHERE is_active = 1 AND deleted_at IS NULL ORDER BY total_reviews DESC, rating DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getAll($filters = []) {
        $where = ["deleted_at IS NULL"];
        $params = [];

        if (isset($filters['active_only']) && $filters['active_only']) {
            $where[] = "is_active = 1";
        }
        if (!empty($filters['city'])) {
            $where[] = "city = ?";
            $params[] = $filters['city'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR city LIKE ? OR address LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $where[] = "price_start >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = "price_start <= ?";
            $params[] = $filters['max_price'];
        }

        $orderBy = "ORDER BY ";
        switch ($filters['sort'] ?? 'rating') {
            case 'price_asc': $orderBy .= "price_start ASC"; break;
            case 'price_desc': $orderBy .= "price_start DESC"; break;
            case 'name': $orderBy .= "name ASC"; break;
            case 'newest': $orderBy .= "created_at DESC"; break;
            default: $orderBy .= "rating DESC, total_reviews DESC"; break;
        }

        $sql = "SELECT * FROM hotels WHERE " . implode(' AND ', $where) . " $orderBy";

        if (!empty($filters['limit'])) {
            $offset = (($filters['page'] ?? 1) - 1) * $filters['limit'];
            $sql .= " LIMIT " . (int)$filters['limit'] . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($filters = []) {
        $where = ["deleted_at IS NULL"];
        $params = [];

        if (isset($filters['active_only']) && $filters['active_only']) {
            $where[] = "is_active = 1";
        }
        if (!empty($filters['city'])) {
            $where[] = "city = ?";
            $params[] = $filters['city'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR city LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM hotels WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    public function getCities() {
        $stmt = $this->db->query(
            "SELECT DISTINCT city FROM hotels WHERE is_active = 1 AND deleted_at IS NULL ORDER BY city"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO hotels (name, slug, city, address, description_id, description_en, rating, price_start, thumbnail, amenities, is_featured) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'], $data['slug'], $data['city'], $data['address'],
            $data['description_id'], $data['description_en'] ?? null,
            $data['rating'] ?? 0, $data['price_start'],
            $data['thumbnail'] ?? null, json_encode($data['amenities'] ?? []),
            $data['is_featured'] ?? 0,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            if ($key === 'amenities' && is_array($value)) {
                $value = json_encode($value);
            }
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = $this->db->prepare(
            "UPDATE hotels SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute($values);
    }

    public function softDelete($id) {
        $stmt = $this->db->prepare("UPDATE hotels SET deleted_at = NOW(), is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateRating($hotelId) {
        $stmt = $this->db->prepare(
            "UPDATE hotels SET 
                rating = (SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE hotel_id = ? AND is_approved = 1),
                total_reviews = (SELECT COUNT(*) FROM reviews WHERE hotel_id = ? AND is_approved = 1)
             WHERE id = ?"
        );
        return $stmt->execute([$hotelId, $hotelId, $hotelId]);
    }
}
