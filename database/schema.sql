-- =====================================================
-- LYNVAII HOTEL BOOKING SYSTEM — DATABASE SCHEMA
-- Created: 2026-06-21
-- Engine: MySQL 5.7+ / MariaDB 10.3+
-- =====================================================

CREATE DATABASE IF NOT EXISTS `lynvaii_hotel` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `lynvaii_hotel`;

-- =====================================================
-- TABLE: users
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('user', 'admin', 'super_admin') NOT NULL DEFAULT 'user',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `language` ENUM('id', 'en') NOT NULL DEFAULT 'id',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: hotels
-- =====================================================
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `city` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `description_id` TEXT NOT NULL,
  `description_en` TEXT DEFAULT NULL,
  `rating` DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  `total_reviews` INT UNSIGNED NOT NULL DEFAULT 0,
  `price_start` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `gallery` JSON DEFAULT NULL,
  `amenities` JSON DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_hotels_city` (`city`),
  INDEX `idx_hotels_rating` (`rating`),
  INDEX `idx_hotels_featured` (`is_featured`),
  INDEX `idx_hotels_active` (`is_active`),
  FULLTEXT INDEX `ft_hotels_search` (`name`, `city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: rooms
-- =====================================================
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT UNSIGNED NOT NULL,
  `type_name` VARCHAR(100) NOT NULL,
  `description_id` TEXT DEFAULT NULL,
  `description_en` TEXT DEFAULT NULL,
  `price_per_night` DECIMAL(12,2) NOT NULL,
  `capacity` INT UNSIGNED NOT NULL DEFAULT 2,
  `stock` INT UNSIGNED NOT NULL DEFAULT 1,
  `amenities` JSON DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_rooms_hotel` (`hotel_id`),
  INDEX `idx_rooms_price` (`price_per_night`),
  CONSTRAINT `fk_rooms_hotel` FOREIGN KEY (`hotel_id`) 
    REFERENCES `hotels`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: room_images
-- =====================================================
CREATE TABLE IF NOT EXISTS `room_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_room_images_room` (`room_id`),
  CONSTRAINT `fk_room_images_room` FOREIGN KEY (`room_id`) 
    REFERENCES `rooms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: bookings
-- =====================================================
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NOT NULL,
  `room_id` INT UNSIGNED NOT NULL,
  `hotel_id` INT UNSIGNED NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests` INT UNSIGNED NOT NULL DEFAULT 1,
  `nights` INT UNSIGNED NOT NULL DEFAULT 1,
  `room_price` DECIMAL(12,2) NOT NULL,
  `tax_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(12,2) NOT NULL,
  `guest_name` VARCHAR(100) NOT NULL,
  `guest_email` VARCHAR(150) NOT NULL,
  `guest_phone` VARCHAR(20) DEFAULT NULL,
  `special_requests` TEXT DEFAULT NULL,
  `status` ENUM('pending','confirmed','checked_in','completed','cancelled','expired') NOT NULL DEFAULT 'pending',
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `confirmed_at` TIMESTAMP NULL DEFAULT NULL,
  `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
  `cancel_reason` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_bookings_code` (`booking_code`),
  INDEX `idx_bookings_user` (`user_id`),
  INDEX `idx_bookings_room` (`room_id`),
  INDEX `idx_bookings_hotel` (`hotel_id`),
  INDEX `idx_bookings_status` (`status`),
  INDEX `idx_bookings_dates` (`check_in`, `check_out`),
  INDEX `idx_bookings_expires` (`expires_at`),
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) 
    REFERENCES `rooms`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_hotel` FOREIGN KEY (`hotel_id`) 
    REFERENCES `hotels`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: payments
-- =====================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL,
  `payment_method` ENUM('bank_transfer','credit_card','e_wallet','cash') NOT NULL DEFAULT 'bank_transfer',
  `bank_name` VARCHAR(50) DEFAULT NULL,
  `account_number` VARCHAR(50) DEFAULT NULL,
  `account_name` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `expired_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_payments_booking` (`booking_id`),
  INDEX `idx_payments_status` (`status`),
  CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) 
    REFERENCES `bookings`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: reviews
-- =====================================================
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `hotel_id` INT UNSIGNED NOT NULL,
  `booking_id` INT UNSIGNED DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT DEFAULT NULL,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_reviews_user` (`user_id`),
  INDEX `idx_reviews_hotel` (`hotel_id`),
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_hotel` FOREIGN KEY (`hotel_id`) 
    REFERENCES `hotels`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: activity_logs
-- =====================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `target_type` VARCHAR(50) DEFAULT NULL,
  `target_id` INT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_logs_user` (`user_id`),
  INDEX `idx_logs_action` (`action`),
  INDEX `idx_logs_created` (`created_at`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: settings (App configuration)
-- =====================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT DEFAULT NULL,
  `type` ENUM('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
