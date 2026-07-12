<?php
/**
 * Uploads directory placeholder — Lynvaii Hotel Booking System
 * File ini hanya penjaga agar folder tidak diabaikan Git.
 * Semua file upload hotel akan disimpan di sini.
 */
// Cegah akses langsung ke file ini
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit;
}
