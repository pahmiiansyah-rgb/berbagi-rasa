-- ============================================================
-- Skrip Inisialisasi Database — Berbagi Rasa (Closing Food Rescue)
-- Kompatibel dengan MySQL 8.0+, TiDB Cloud Serverless, Aiven, & Railway
-- ============================================================

-- 1. TABEL PENGGUNA (users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donatur', 'penerima', 'admin') NOT NULL DEFAULT 'penerima',
    telepon VARCHAR(20) NULL,
    alamat TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_email (email),
    INDEX idx_user_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL PENAWARAN MAKANAN (donations)
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NULL,
    jumlah INT NOT NULL,
    sisa INT NOT NULL,
    satuan VARCHAR(20) NOT NULL DEFAULT 'porsi',
    harga_asli INT NOT NULL DEFAULT 0,
    harga_diskon INT NOT NULL DEFAULT 0,
    kategori VARCHAR(50) NOT NULL DEFAULT 'makanan_berat',
    lokasi_ambil VARCHAR(255) NOT NULL,
    jam_tutup VARCHAR(50) NULL DEFAULT '21:00 WIB',
    tipe_penawaran ENUM('diskon_toko', 'gratis_peduli') NOT NULL DEFAULT 'diskon_toko',
    jaminan_kualitas VARCHAR(255) NOT NULL DEFAULT '100% Segar & Higienis Diproduksi Hari Ini',
    foto VARCHAR(255) NULL,
    status ENUM('tersedia', 'dipesan', 'selesai', 'kedaluwarsa', 'dibatalkan') NOT NULL DEFAULT 'tersedia',
    expired_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_donation_status_created (status, created_at),
    INDEX idx_donation_user (user_id),
    INDEX idx_donation_category (kategori),
    CONSTRAINT fk_donations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL PESANAN & KLAIM (requests)
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    penerima_id INT NOT NULL,
    jumlah_minta INT NOT NULL,
    catatan VARCHAR(255) NULL,
    claim_token VARCHAR(50) NULL,
    status ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'diambil') NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_requests_donation_status (donation_id, status),
    INDEX idx_requests_penerima (penerima_id),
    CONSTRAINT fk_requests_donation FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    CONSTRAINT fk_requests_penerima FOREIGN KEY (penerima_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL AUDIT LOG (status_logs)
CREATE TABLE IF NOT EXISTS status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NULL,
    donation_id INT NOT NULL,
    dari_status VARCHAR(50) NULL,
    ke_status VARCHAR(50) NOT NULL,
    diubah_oleh INT NULL,
    catatan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_donation (donation_id),
    INDEX idx_logs_request (request_id),
    CONSTRAINT fk_logs_donation FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    CONSTRAINT fk_logs_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE SET NULL,
    CONSTRAINT fk_logs_user FOREIGN KEY (diubah_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL ULASAN (reviews - opsional)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    ulasan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reviews_request (request_id),
    CONSTRAINT fk_reviews_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA DEMO AWAL (SEEDER)
-- Password untuk admin: admin123
-- Password untuk semua demo user: demo123
-- ============================================================

INSERT IGNORE INTO users (id, nama, email, password, role, telepon, alamat) VALUES
(1, 'Administrator Berbagi Rasa', 'admin@berbagirasa.id', '$2y$10$QjG5x76P6WqE9N.f1VvEPeZkYJ.xY5tV7mO3WcK6Qh8R1eGzO9cOu', 'admin', '081122334455', 'HQ Berbagi Rasa - Sentra Pangan'),
(2, 'Artisan Bakery & Pastry', 'bakery@berbagi.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'donatur', '081298765432', 'Jl. Boulevard Kampus Barat No. 8'),
(3, 'RM Minang Saiyo Jaya', 'padang@berkah.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'donatur', '081377889900', 'Jl. Raya Perintis Kemerdekaan No. 45'),
(4, 'Bento Express Box', 'bento@express.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'donatur', '081244556677', 'Food Court Lt. 1 Gedung Mahasiswa'),
(5, 'Warung Peduli Berkah', 'kantin@kampus.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'donatur', '081288991122', 'Kantin Kejujuran Kampus Blok C'),
(6, 'Andi Pratama (Anak Rantau)', 'andi@mail.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'penerima', '085611223344', 'Kost Putra Melati No. 12B'),
(7, 'Pak Budi (Pekerja Harian Lepas)', 'budi@mail.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'penerima', '085799881122', 'Pondok Singgah Pejuang Nafkah'),
(8, 'Siti Nurhaliza (Komunitas Dapur Rantau)', 'siti@mail.id', '$2y$10$wE8wY0sYwD4rJ7dY4nO2vO6Zz4bE7lU5wX8aP2rL9vT3uY1hN5qCe', 'penerima', '085811334455', 'Posko Relawan Pangan');

INSERT IGNORE INTO donations (id, user_id, judul, deskripsi, jumlah, sisa, satuan, harga_asli, harga_diskon, kategori, lokasi_ambil, jam_tutup, tipe_penawaran, jaminan_kualitas, foto, status, expired_at, created_at) VALUES
(1, 2, 'Croissant Mentega & Aneka Danish Pastry Premium', 'Produksi fresh batch siang tadi, tekstur renyah lembut dengan butter pilihan. Toko bersiap tutup pukul 21:00, dijual murah khusus untuk pejuang hemat dan anak rantau!', 18, 12, 'paket', 28000, 7000, 'snack_roti', 'Artisan Bakery - Kasir Utama (Ambil 20:00 - 21:00)', '21:00 WIB', 'diskon_toko', 'Higienis 100%, dipanggang hari ini, kemasan box kedap udara', NULL, 'tersedia', DATE_ADD(NOW(), INTERVAL 4 HOUR), NOW()),
(2, 3, 'Paket Nasi Rendang Daging Sapi + Daun Singkong & Sambal Ijo', 'Rendang empuk kaya bumbu rempah khas Minang. Batch siang hari masih sangat segar dan terjaga higienis. Solusi makan malam lezat bergizi seharga semangkuk mie instan!', 20, 14, 'porsi', 26000, 6000, 'makanan_berat', 'RM Minang Saiyo, Jl. Raya Perintis No. 45 (Meja Kasir)', '21:30 WIB', 'diskon_toko', 'Dimasak segar tadi siang, dipanaskan berkala & higienis', NULL, 'tersedia', DATE_ADD(NOW(), INTERVAL 5 HOUR), NOW()),
(3, 4, 'Chicken Katsu Rice Bowl Bento + Salad Sayur Segar', 'Katsu ayam tebal renyah dengan nasi pulen hangat dan saus teriyaki gurih. Toko tutup pukul 22:00, porsi mantap penutup hari.', 15, 10, 'kotak', 32000, 9500, 'katering_bento', 'Food Court Lt. 1 Gedung Mahasiswa - Stand No. 04', '22:00 WIB', 'diskon_toko', 'Kemasan bento food-grade bersegel higienis', NULL, 'tersedia', DATE_ADD(NOW(), INTERVAL 6 HOUR), NOW()),
(4, 5, 'Paket Nasi Telur Balado, Sayur Sop Segar & Tahu Tempe', 'Disubsidi khusus dari dapur kejujuran untuk teman-teman perantau yang sedang kehabisan bekal dan saudara pejuang nafkah jalanan. Ambil secara terhormat dan nikmati makanan hangat ini.', 25, 20, 'porsi', 14000, 0, 'makanan_berat', 'Kantin Blok C Kampus - Pos Peduli Pangan', '20:30 WIB', 'gratis_peduli', 'Menu rumahan sehat, baru matang sore hari, tanpa MSG berlebih', NULL, 'tersedia', DATE_ADD(NOW(), INTERVAL 3 HOUR), NOW()),
(5, 2, 'Roti Sosis Keju Panggang Pagi Hari', 'Porsi sisa kemarin yang sudah melewati batas waktu toleransi pengambilan.', 10, 0, 'bungkus', 18000, 5000, 'snack_roti', 'Artisan Bakery - Kasir Utama', '20:00 WIB', 'diskon_toko', 'Sudah kedaluwarsa', NULL, 'kedaluwarsa', DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY));

INSERT IGNORE INTO requests (id, donation_id, penerima_id, jumlah_minta, catatan, claim_token, status, created_at) VALUES
(1, 1, 6, 2, 'Untuk makan malam anak kos bareng teman sekamar.', 'CLM-BKR-001', 'disetujui', NOW()),
(2, 2, 7, 2, 'Sepulang kerja harian, sangat terbantu terima kasih.', 'CLM-PDG-002', 'disetujui', NOW()),
(3, 4, 8, 3, 'Untuk adik-adik panti asuhan & perantau di posko.', 'CLM-PDL-003', 'disetujui', NOW());

INSERT IGNORE INTO status_logs (id, donation_id, request_id, dari_status, ke_status, diubah_oleh, catatan, created_at) VALUES
(1, 1, NULL, NULL, 'tersedia', 2, 'Menu diskon jelang tutup toko diterbitkan: 18 paket @ Rp 7.000 (Hemat 75%).', NOW()),
(2, 1, 1, NULL, 'menunggu', 6, 'Pemesanan diajukan oleh Andi Pratama sebanyak 2 paket (Estimasi tebus: Rp 14.000).', NOW()),
(3, 1, 1, 'menunggu', 'disetujui', 2, 'Disetujui Artisan Bakery. Kode ambil: CLM-BKR-001. Sisa stok: 12 paket.', NOW()),
(4, 2, NULL, NULL, 'tersedia', 3, 'Menu diskon jelang tutup diterbitkan: 20 porsi Nasi Rendang @ Rp 6.000.', NOW()),
(5, 2, 2, NULL, 'menunggu', 7, 'Pemesanan diajukan oleh Pak Budi (2 porsi).', NOW()),
(6, 2, 2, 'menunggu', 'disetujui', 3, 'Disetujui RM Minang Saiyo. Sisa stok: 14 porsi.', NOW()),
(7, 4, NULL, NULL, 'tersedia', 5, 'Menu Peduli Pangan Gratis (Rp 0) diterbitkan sebanyak 25 porsi untuk yang membutuhkan.', NOW()),
(8, 4, 3, NULL, 'menunggu', 8, 'Pemesanan diajukan oleh Siti Nurhaliza (3 porsi).', NOW()),
(9, 4, 3, 'menunggu', 'disetujui', 5, 'Disetujui Warung Peduli Berkah. Sisa stok: 20 porsi.', NOW());
