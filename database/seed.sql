-- =====================================================
-- LYNVAII HOTEL BOOKING SYSTEM — SEED DATA
-- Representative dummy data — easily editable
-- =====================================================

USE `lynvaii_hotel`;

-- =====================================================
-- USERS (Super Admin, Admin, and Sample Users)
-- Password for all: 'password123' (bcrypt hashed)
-- =====================================================
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`, `is_active`, `language`) VALUES
('Super Administrator', 'superadmin@lynvaii.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'super_admin', 1, 'id'),
('Admin Hotel', 'admin@lynvaii.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'admin', 1, 'id'),
('Budi Santoso', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'user', 1, 'id'),
('Siti Nurhaliza', 'siti@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', 'user', 1, 'id'),
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567894', 'user', 1, 'en'),
('Rina Wulandari', 'rina@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567895', 'user', 1, 'id'),
('Ahmad Fauzi', 'ahmad@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567896', 'user', 1, 'id');

-- =====================================================
-- HOTELS
-- =====================================================
INSERT INTO `hotels` (`name`, `slug`, `city`, `address`, `description_id`, `description_en`, `rating`, `total_reviews`, `price_start`, `thumbnail`, `amenities`, `is_featured`, `is_active`) VALUES
(
  'The Langham Jakarta',
  'the-langham-jakarta',
  'Jakarta',
  'Jl. Jend. Sudirman Kav. 1, Jakarta Pusat 10220',
  'Rasakan kemewahan yang elegan di jantung ibu kota. Suite Eksekutif kami menawarkan pemandangan kota yang memukau dengan fasilitas kelas dunia. Hotel bintang 5 ini menyediakan layanan concierge pribadi yang siap melayani kebutuhan tamu 24 jam.',
  'Experience elegant luxury in the heart of the capital. Our Executive Suites offer stunning city views with world-class amenities. This 5-star hotel provides personal concierge service ready to serve guests 24 hours.',
  4.8, 156,
  4111000.00,
  'hotels/the-langham-jakarta.jpg',
  '["WiFi Gratis", "Kolam Renang", "Spa & Sauna", "Restaurant", "Bar & Lounge", "Gym", "Concierge 24 Jam", "Valet Parking"]',
  1, 1
),
(
  'InterContinental Jakarta',
  'intercontinental-jakarta',
  'Jakarta',
  'Jl. Pondok Indah No.18, Jakarta Selatan 12310',
  'Hotel mewah dengan desain kontemporer yang memadukan sentuhan modern dan keanggunan klasik. Terletak strategis di kawasan bisnis dan belanja premium Jakarta Selatan.',
  'A luxury hotel with contemporary design blending modern touches and classic elegance. Strategically located in South Jakarta premium business and shopping district.',
  4.6, 203,
  4384000.00,
  'hotels/intercontinental-jakarta.jpg',
  '["WiFi Gratis", "Kolam Renang Infinity", "Spa Premium", "3 Restaurant", "Business Center", "Kids Club", "Airport Shuttle"]',
  1, 1
),
(
  'Pullman Bandung Grand Central',
  'pullman-bandung-grand-central',
  'Bandung',
  'Jl. Diponegoro No.27, Bandung 40115',
  'Hotel premium di pusat kota Bandung dengan pemandangan Gunung Tangkuban Perahu yang menakjubkan. Dilengkapi fasilitas meeting room internasional dan restoran rooftop.',
  'A premium hotel in the center of Bandung with stunning views of Mount Tangkuban Perahu. Equipped with international meeting rooms and rooftop restaurant.',
  4.5, 189,
  4862000.00,
  'hotels/pullman-bandung.jpg',
  '["WiFi Gratis", "Rooftop Bar", "Mountain View", "Meeting Room", "Spa", "Restaurant", "Pool", "Gym"]',
  1, 1
),
(
  'Aston Inn Hotel',
  'aston-inn-hotel',
  'Bandung',
  'Jl. Braga No.99, Bandung 40111',
  'Terletak di kawasan heritage Braga yang legendaris, hotel butik ini menawarkan pengalaman menginap yang unik dengan sentuhan arsitektur Art Deco dan kenyamanan modern.',
  'Located in the legendary Braga heritage area, this boutique hotel offers a unique stay experience with Art Deco architecture and modern comfort.',
  4.3, 127,
  1341000.00,
  'hotels/aston-inn-bandung.jpg',
  '["WiFi Gratis", "Heritage Walk", "Café", "Restaurant", "Laundry", "Room Service 24 Jam"]',
  0, 1
),
(
  'The Langham Bali',
  'the-langham-bali',
  'Bali',
  'Jl. Pantai Kuta No.1, Kuta, Badung, Bali 80361',
  'Resort mewah tepi pantai dengan villa pribadi dan kolam renang infinity menghadap Samudra Hindia. Sempurna untuk liburan romantis atau keluarga.',
  'Beachfront luxury resort with private villas and infinity pool overlooking the Indian Ocean. Perfect for romantic getaways or family vacations.',
  4.9, 312,
  4711000.00,
  'hotels/the-langham-bali.jpg',
  '["Private Beach", "Infinity Pool", "Villa Pribadi", "Sunset Bar", "Spa Tradisional Bali", "Water Sports", "Wedding Venue"]',
  1, 1
),
(
  'Tria Lengkong Jakarta',
  'tria-lengkong-jakarta',
  'Jakarta',
  'Jl. Thamrin Boulevard, Jakarta Pusat 10350',
  'Hotel bisnis modern dengan lokasi premium di koridor Thamrin. Dilengkapi ruang meeting canggih dan akses mudah ke pusat perbelanjaan dan hiburan.',
  'Modern business hotel with premium location on Thamrin corridor. Equipped with sophisticated meeting rooms and easy access to shopping and entertainment centers.',
  4.7, 94,
  4111000.00,
  'hotels/tria-lengkong-jakarta.jpg',
  '["WiFi Gratis", "Business Center", "Restaurant", "Bar", "Gym", "Swimming Pool", "Meeting Room"]',
  0, 1
),
(
  'Swiss-Belhotel Tasikmalaya',
  'swiss-belhotel-tasikmalaya',
  'Tasikmalaya',
  'Jl. HZ Mustofa No.234, Tasikmalaya 46115',
  'Hotel bintang 4 pertama di Tasikmalaya yang menghadirkan standar internasional. Lokasi strategis di pusat kota dengan akses mudah ke destinasi wisata Priangan Timur.',
  'The first 4-star hotel in Tasikmalaya bringing international standards. Strategic location in the city center with easy access to East Priangan tourist destinations.',
  4.4, 78,
  1500000.00,
  'hotels/swiss-belhotel-tasikmalaya.jpg',
  '["WiFi Gratis", "Restaurant", "Meeting Room", "Pool", "Gym", "Parking"]',
  0, 1
),
(
  'Grand Mercure Bandung Setiabudi',
  'grand-mercure-bandung-setiabudi',
  'Bandung',
  'Jl. Dr. Setiabudi No.269-275, Bandung 40154',
  'Hotel resort di kawasan Dago Atas dengan suhu sejuk dan pemandangan kota Bandung yang memukau. Ideal untuk retreat dan acara spesial.',
  'Resort hotel in the Dago Atas area with cool temperatures and stunning views of Bandung city. Ideal for retreats and special events.',
  4.6, 145,
  3200000.00,
  'hotels/grand-mercure-bandung.jpg',
  '["WiFi Gratis", "City View", "Restaurant", "Bar", "Pool", "Spa", "Garden", "Event Space"]',
  1, 1
);

-- =====================================================
-- ROOMS
-- =====================================================
INSERT INTO `rooms` (`hotel_id`, `type_name`, `description_id`, `description_en`, `price_per_night`, `capacity`, `stock`, `amenities`, `is_active`) VALUES
-- The Langham Jakarta (hotel_id: 1)
(1, 'Deluxe Room', 'Kamar luas dengan tempat tidur king size dan pemandangan kota', 'Spacious room with king size bed and city view', 4111000.00, 2, 10, '["King Bed", "City View", "Minibar", "Smart TV 55 inch"]', 1),
(1, 'Executive Suite', 'Suite mewah dengan ruang tamu terpisah dan bathtub premium', 'Luxury suite with separate living room and premium bathtub', 6500000.00, 3, 5, '["King Bed", "Living Room", "Bathtub", "Balcony", "Smart TV 65 inch"]', 1),
(1, 'Presidential Suite', 'Suite tertinggi dengan layanan butler pribadi', 'Top suite with personal butler service', 15000000.00, 4, 2, '["King Bed", "2 Living Rooms", "Kitchen", "Jacuzzi", "Butler Service"]', 1),

-- InterContinental Jakarta (hotel_id: 2)
(2, 'Superior Room', 'Kamar nyaman dengan fasilitas lengkap', 'Comfortable room with complete amenities', 4384000.00, 2, 15, '["Queen Bed", "Garden View", "Minibar", "Smart TV"]', 1),
(2, 'Club InterContinental', 'Kamar premium dengan akses Club Lounge', 'Premium room with Club Lounge access', 7200000.00, 2, 8, '["King Bed", "Club Lounge Access", "Premium Minibar", "City View"]', 1),

-- Pullman Bandung (hotel_id: 3)
(3, 'Deluxe Mountain View', 'Kamar dengan pemandangan gunung yang spektakuler', 'Room with spectacular mountain views', 4862000.00, 2, 12, '["King Bed", "Mountain View", "Minibar", "Rain Shower"]', 1),
(3, 'Junior Suite', 'Suite junior dengan ruang kerja', 'Junior suite with work area', 6800000.00, 3, 6, '["King Bed", "Work Desk", "Bathtub", "Mountain View"]', 1),

-- Aston Inn Bandung (hotel_id: 4)
(4, 'Standard Room', 'Kamar standar yang nyaman dengan sentuhan heritage', 'Comfortable standard room with heritage touch', 1341000.00, 2, 20, '["Double Bed", "AC", "TV", "WiFi"]', 1),
(4, 'Heritage Suite', 'Suite dengan desain Art Deco klasik', 'Suite with classic Art Deco design', 2500000.00, 2, 4, '["King Bed", "Art Deco Interior", "Bathtub", "City View"]', 1),

-- The Langham Bali (hotel_id: 5)
(5, 'Ocean View Room', 'Kamar dengan pemandangan laut yang menakjubkan', 'Room with stunning ocean views', 4711000.00, 2, 15, '["King Bed", "Ocean View", "Balcony", "Minibar"]', 1),
(5, 'Beach Villa', 'Villa pribadi dengan akses langsung ke pantai', 'Private villa with direct beach access', 12000000.00, 4, 5, '["King Bed", "Private Pool", "Beach Access", "Outdoor Shower", "Butler"]', 1),

-- Tria Lengkong Jakarta (hotel_id: 6)
(6, 'Business Room', 'Kamar bisnis dengan meja kerja ergonomis', 'Business room with ergonomic work desk', 4111000.00, 2, 18, '["Queen Bed", "Work Desk", "Minibar", "City View"]', 1),

-- Swiss-Belhotel Tasikmalaya (hotel_id: 7)
(7, 'Deluxe Room', 'Kamar deluxe dengan pemandangan kota', 'Deluxe room with city view', 1500000.00, 2, 25, '["Queen Bed", "City View", "AC", "TV", "WiFi"]', 1),
(7, 'Suite Room', 'Suite luas dengan ruang tamu', 'Spacious suite with living area', 2800000.00, 3, 5, '["King Bed", "Living Area", "Minibar", "Bathtub"]', 1),

-- Grand Mercure Bandung (hotel_id: 8)
(8, 'Deluxe City View', 'Kamar dengan panorama kota Bandung', 'Room with Bandung city panorama', 3200000.00, 2, 14, '["King Bed", "City Panorama", "Minibar", "Rain Shower"]', 1),
(8, 'Panorama Suite', 'Suite premium dengan panorama 180 derajat', 'Premium suite with 180-degree panorama', 5500000.00, 3, 4, '["King Bed", "180° Panorama", "Living Room", "Jacuzzi"]', 1);

-- =====================================================
-- BOOKINGS (Sample)
-- =====================================================
INSERT INTO `bookings` (`booking_code`, `user_id`, `room_id`, `hotel_id`, `check_in`, `check_out`, `guests`, `nights`, `room_price`, `tax_amount`, `total_price`, `guest_name`, `guest_email`, `guest_phone`, `status`, `created_at`) VALUES
('TRX-20260621-001', 3, 1, 1, '2026-07-01', '2026-07-03', 2, 2, 4111000.00, 822200.00, 9044200.00, 'Budi Santoso', 'budi@example.com', '081234567892', 'confirmed', '2026-06-15 10:30:00'),
('TRX-20260621-002', 4, 6, 3, '2026-07-05', '2026-07-08', 2, 3, 4862000.00, 1458600.00, 16044600.00, 'Siti Nurhaliza', 'siti@example.com', '081234567893', 'confirmed', '2026-06-16 14:20:00'),
('TRX-20260621-003', 5, 10, 5, '2026-07-10', '2026-07-14', 2, 4, 4711000.00, 1884400.00, 20728400.00, 'John Smith', 'john@example.com', '081234567894', 'pending', '2026-06-18 09:15:00'),
('TRX-20260621-004', 6, 4, 2, '2026-06-25', '2026-06-27', 2, 2, 4384000.00, 876800.00, 9644800.00, 'Rina Wulandari', 'rina@example.com', '081234567895', 'confirmed', '2026-06-10 11:45:00'),
('TRX-20260621-005', 7, 8, 4, '2026-07-20', '2026-07-22', 1, 2, 1341000.00, 268200.00, 2950200.00, 'Ahmad Fauzi', 'ahmad@example.com', '081234567896', 'pending', '2026-06-20 16:30:00'),
('TRX-20260621-006', 3, 11, 5, '2026-08-01', '2026-08-05', 4, 4, 12000000.00, 4800000.00, 52800000.00, 'Budi Santoso', 'budi@example.com', '081234567892', 'confirmed', '2026-06-12 08:00:00'),
('TRX-20260621-007', 4, 13, 7, '2026-07-15', '2026-07-17', 2, 2, 1500000.00, 300000.00, 3300000.00, 'Siti Nurhaliza', 'siti@example.com', '081234567893', 'cancelled', '2026-06-19 12:00:00'),
('TRX-20260621-008', 5, 15, 8, '2026-07-25', '2026-07-28', 2, 3, 3200000.00, 960000.00, 10560000.00, 'John Smith', 'john@example.com', '081234567894', 'completed', '2026-06-01 10:00:00'),
('TRX-20260621-009', 6, 2, 1, '2026-08-10', '2026-08-13', 2, 3, 6500000.00, 1950000.00, 21450000.00, 'Rina Wulandari', 'rina@example.com', '081234567895', 'confirmed', '2026-06-20 09:30:00'),
('TRX-20260621-010', 7, 7, 3, '2026-07-12', '2026-07-14', 3, 2, 6800000.00, 1360000.00, 14960000.00, 'Ahmad Fauzi', 'ahmad@example.com', '081234567896', 'expired', '2026-06-17 15:00:00');

-- =====================================================
-- PAYMENTS
-- =====================================================
INSERT INTO `payments` (`booking_id`, `payment_method`, `bank_name`, `account_number`, `account_name`, `amount`, `status`, `transaction_id`, `paid_at`) VALUES
(1, 'bank_transfer', 'BCA', '1234567890', 'Budi Santoso', 9044200.00, 'paid', 'PAY-20260615-001', '2026-06-15 10:45:00'),
(2, 'bank_transfer', 'Mandiri', '0987654321', 'Siti Nurhaliza', 16044600.00, 'paid', 'PAY-20260616-002', '2026-06-16 14:35:00'),
(3, 'e_wallet', NULL, NULL, 'John Smith', 20728400.00, 'pending', NULL, NULL),
(4, 'credit_card', 'VISA', '****1234', 'Rina Wulandari', 9644800.00, 'paid', 'PAY-20260610-004', '2026-06-10 12:00:00'),
(5, 'bank_transfer', 'BNI', '1122334455', 'Ahmad Fauzi', 2950200.00, 'pending', NULL, NULL),
(6, 'bank_transfer', 'BCA', '1234567890', 'Budi Santoso', 52800000.00, 'paid', 'PAY-20260612-006', '2026-06-12 08:30:00'),
(7, 'bank_transfer', 'Mandiri', '0987654321', 'Siti Nurhaliza', 3300000.00, 'failed', NULL, NULL),
(8, 'e_wallet', NULL, NULL, 'John Smith', 10560000.00, 'paid', 'PAY-20260601-008', '2026-06-01 10:30:00'),
(9, 'credit_card', 'Mastercard', '****5678', 'Rina Wulandari', 21450000.00, 'paid', 'PAY-20260620-009', '2026-06-20 10:00:00'),
(10, 'bank_transfer', 'BRI', '5566778899', 'Ahmad Fauzi', 14960000.00, 'failed', NULL, NULL);

-- =====================================================
-- REVIEWS
-- =====================================================
INSERT INTO `reviews` (`user_id`, `hotel_id`, `booking_id`, `rating`, `comment`) VALUES
(3, 1, 1, 5, 'Pengalaman menginap yang luar biasa! Pelayanan sangat ramah dan kamar sangat bersih. Pemandangan kota dari lantai 35 sangat memukau. Pasti akan kembali lagi!'),
(4, 3, 2, 4, 'Hotel yang sangat nyaman dengan pemandangan gunung yang indah. Sarapan buffet sangat lengkap. Sedikit masukan untuk koneksi WiFi yang kadang lambat di malam hari.'),
(5, 5, NULL, 5, 'Absolutely stunning resort! The private beach is incredible and the sunset views are breathtaking. The Balinese spa treatment was the highlight of our stay.'),
(6, 2, 4, 5, 'Lokasi sangat strategis, dekat dengan mal dan restoran. Kamar luas dan bersih. Staff sangat membantu dan ramah. Highly recommended!'),
(7, 4, NULL, 4, 'Hotel heritage yang unik di jalan Braga. Suasana Art Deco-nya sangat kental. Cocok untuk yang suka arsitektur klasik. Harga sangat terjangkau untuk kualitas yang ditawarkan.'),
(3, 8, NULL, 5, 'View kota Bandung dari kamar di malam hari sangat indah! Kolam renang infinity-nya amazing. Staf sangat profesional.'),
(5, 1, NULL, 4, 'Great hotel, excellent service. The concierge was very helpful in arranging city tours. Only minor issue was the check-in queue during peak hours.');

-- =====================================================
-- SETTINGS
-- =====================================================
INSERT INTO `settings` (`key_name`, `value`, `type`) VALUES
('site_name', 'Lynvaii', 'string'),
('site_tagline_id', 'Perjalanan akomodasi mewah dan nyaman untuk para pelanggan yang kami hormati.', 'string'),
('site_tagline_en', 'Luxury and comfortable accommodation journey for our valued guests.', 'string'),
('booking_expiry_minutes', '15', 'number'),
('tax_percentage', '10', 'number'),
('currency', 'IDR', 'string'),
('currency_symbol', 'Rp', 'string'),
('admin_email', 'admin@lynvaii.com', 'string'),
('maintenance_mode', 'false', 'boolean'),
('max_guests_per_booking', '10', 'number');
