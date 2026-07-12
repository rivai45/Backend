<?php
/**
 * Admin Property Controller — Lynvaii Hotel Booking System
 */

class AdminPropertyController {

    public function index() {
        $hotelModel = new Hotel();
        $roomModel  = new Room();

        $hotels = $hotelModel->getAll(['search' => $_GET['search'] ?? '']);
        $rooms  = $roomModel->getAll(['search'  => $_GET['search'] ?? '']);

        $pageTitle = __('admin.properties') . ' — ' . APP_NAME;
        $adminPage = 'properti';
        include FRONTEND_PATH . '/views/layouts/admin.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/properti');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/properti');
            return;
        }

        $type = $_POST['type'] ?? 'hotel';

        if ($type === 'hotel') {
            $hotelModel = new Hotel();
            $name = sanitize($_POST['name'] ?? '');

            if (empty($name)) {
                setFlash('error', 'Nama hotel harus diisi.');
                redirect('?page=admin/properti');
                return;
            }

            // Generate unique slug (Bug 13 fix)
            $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $baseSlug = trim($baseSlug, '-');
            $slug     = $baseSlug;
            $counter  = 1;

            $db = db();
            while (true) {
                $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM hotels WHERE slug = ? AND deleted_at IS NULL");
                $stmt->execute([$slug]);
                if ($stmt->fetch()['cnt'] == 0) break;
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $hotelModel->create([
                'name'           => $name,
                'slug'           => $slug,
                'city'           => sanitize($_POST['city']),
                'address'        => sanitize($_POST['address']),
                'description_id' => sanitize($_POST['description_id']),
                'description_en' => sanitize($_POST['description_en'] ?? ''),
                'price_start'    => (float)$_POST['price_start'],
                'rating'         => 0,
                'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
            ]);
            setFlash('success', 'Hotel berhasil ditambahkan.');

        } else {
            $roomModel = new Room();

            $hotelId       = (int)$_POST['hotel_id'];
            $pricePerNight = (float)$_POST['price_per_night'];

            if ($hotelId <= 0 || $pricePerNight <= 0) {
                setFlash('error', 'Data kamar tidak valid.');
                redirect('?page=admin/properti');
                return;
            }

            $roomModel->create([
                'hotel_id'       => $hotelId,
                'type_name'      => sanitize($_POST['type_name']),
                'description_id' => sanitize($_POST['description_id'] ?? ''),
                'description_en' => sanitize($_POST['description_en'] ?? ''),
                'price_per_night'=> $pricePerNight,
                'capacity'       => (int)($_POST['capacity'] ?? 2),
                'stock'          => (int)($_POST['stock'] ?? 1),
            ]);
            setFlash('success', 'Kamar berhasil ditambahkan.');
        }

        redirect('?page=admin/properti');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?page=admin/properti');
        if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Invalid token.');
            redirect('?page=admin/properti');
            return;
        }

        $type = $_POST['type'] ?? 'hotel';
        if ($type === 'hotel') {
            $hotelModel = new Hotel();
            $hotelModel->update($id, [
                'name'           => sanitize($_POST['name']),
                'city'           => sanitize($_POST['city']),
                'address'        => sanitize($_POST['address']),
                'description_id' => sanitize($_POST['description_id']),
                'price_start'    => (float)$_POST['price_start'],
                'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
                'is_active'      => isset($_POST['is_active'])   ? 1 : 0,
            ]);
        } else {
            $roomModel = new Room();
            $roomModel->update($id, [
                'type_name'       => sanitize($_POST['type_name']),
                'price_per_night' => (float)$_POST['price_per_night'],
                'capacity'        => (int)$_POST['capacity'],
                'stock'           => (int)$_POST['stock'],
                'is_active'       => isset($_POST['is_active']) ? 1 : 0,
            ]);
        }

        setFlash('success', 'Data berhasil diperbarui.');
        redirect('?page=admin/properti');
    }

    public function delete($id) {
        $type = $_GET['type'] ?? 'hotel';
        if ($type === 'hotel') {
            $hotelModel = new Hotel();
            // Check if hotel has active bookings
            $db   = db();
            $stmt = $db->prepare(
                "SELECT COUNT(*) as cnt FROM bookings WHERE hotel_id = ? AND status IN ('pending','confirmed','checked_in')"
            );
            $stmt->execute([$id]);
            if ($stmt->fetch()['cnt'] > 0) {
                setFlash('error', 'Hotel tidak dapat dihapus karena masih ada booking aktif.');
                redirect('?page=admin/properti');
                return;
            }
            $hotelModel->softDelete($id);
        } else {
            $roomModel = new Room();
            $roomModel->softDelete($id);
        }
        setFlash('success', 'Data berhasil dihapus.');
        redirect('?page=admin/properti');
    }
}
