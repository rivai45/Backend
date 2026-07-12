<?php
/**
 * User Model — Lynvaii Hotel Booking System
 */

class User {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, phone, role, language) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['phone'] ?? null,
            $data['role'] ?? 'user',
            $data['language'] ?? 'id',
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = $this->db->prepare(
            "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute($values);
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    public function softDelete($id) {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll($filters = []) {
        $where = ["deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT * FROM users WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";

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

        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM users WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    public function countByRole() {
        $stmt = $this->db->query(
            "SELECT role, COUNT(*) as total FROM users WHERE deleted_at IS NULL GROUP BY role"
        );
        return $stmt->fetchAll();
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
