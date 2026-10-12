<?php
/**
 * Seeder Data Demo & Uji Coba - Berbagi Rasa
 * Tema Baru: Platform Penyelamatan Makanan Berkualitas Murah Jelang Tutup Toko (Closing Food Rescue)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = Database::get();

echo "Memperbarui skema dan mengisi data demo Berbagi Rasa (Closing Food Rescue Deals)...\n";

// 0. Pastikan seluruh tabel dasar telah dibuat
$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donatur', 'penerima', 'admin') NOT NULL DEFAULT 'penerima',
    telepon VARCHAR(20) NULL,
    alamat TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NULL,
    jumlah INT NOT NULL,
    sisa INT NOT NULL,
    satuan VARCHAR(20) NOT NULL DEFAULT 'porsi',
    lokasi_ambil VARCHAR(255) NOT NULL,
    foto VARCHAR(255) NULL,
    status ENUM('tersedia', 'dipesan', 'selesai', 'kedaluwarsa', 'dibatalkan') NOT NULL DEFAULT 'tersedia',
    expired_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    FOREIGN KEY (penerima_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NULL,
    donation_id INT NOT NULL,
    dari_status VARCHAR(50) NULL,
    ke_status VARCHAR(50) NOT NULL,
    diubah_oleh INT NULL,
    catatan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE SET NULL,
    FOREIGN KEY (diubah_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    ulasan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 1. Pastikan kolom baru tersedia di tabel donations
$existingCols = $db->query("DESCRIBE donations")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('harga_asli', $existingCols)) {
    $db->exec("ALTER TABLE donations ADD COLUMN harga_asli INT NOT NULL DEFAULT 0 AFTER satuan;");
}
if (!in_array('harga_diskon', $existingCols)) {
    $db->exec("ALTER TABLE donations ADD COLUMN harga_diskon INT NOT NULL DEFAULT 0 AFTER harga_asli;");
}
if (!in_array('jam_tutup', $existingCols)) {
    $db->exec("ALTER TABLE donations ADD COLUMN jam_tutup VARCHAR(50) NULL DEFAULT '21:00 WIB' AFTER lokasi_ambil;");
}
if (!in_array('tipe_penawaran', $existingCols)) {
    $db->exec("ALTER TABLE donations ADD COLUMN tipe_penawaran ENUM('diskon_toko', 'gratis_peduli') NOT NULL DEFAULT 'diskon_toko' AFTER jam_tutup;");
}
if (!in_array('jaminan_kualitas', $existingCols)) {
    $db->exec("ALTER TABLE donations ADD COLUMN jaminan_kualitas VARCHAR(255) NOT NULL DEFAULT '100% Segar & Higienis Diproduksi Hari Ini' AFTER tipe_penawaran;");
}

// 2. Bersihkan tabel
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE status_logs;");
$db->exec("TRUNCATE TABLE requests;");
$db->exec("TRUNCATE TABLE donations;");
$db->exec("TRUNCATE TABLE users;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

// 3. Buat Demo Users (Toko/Resto UMKM & Penyelamat Makanan/Anak Rantau)
$pwAdmin = password_hash('admin123', PASSWORD_BCRYPT);
$pwDemo  = password_hash('demo123', PASSWORD_BCRYPT);

$insertUser = $db->prepare("INSERT INTO users (id, nama, email, password, role, telepon, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insertUser->execute([1, 'Administrator Berbagi Rasa', 'admin@berbagirasa.id', $pwAdmin, 'admin', '081122334455', 'HQ Berbagi Rasa - Sentra Pangan']);
$insertUser->execute([2, 'Artisan Bakery & Pastry', 'bakery@berbagi.id', $pwDemo, 'donatur', '081298765432', 'Jl. Boulevard Kampus Barat No. 8']);
$insertUser->execute([3, 'RM Minang Saiyo Jaya', 'padang@berkah.id', $pwDemo, 'donatur', '081377889900', 'Jl. Raya Perintis Kemerdekaan No. 45']);
$insertUser->execute([4, 'Bento Express Box', 'bento@express.id', $pwDemo, 'donatur', '081244556677', 'Food Court Lt. 1 Gedung Mahasiswa']);
$insertUser->execute([5, 'Warung Peduli Berkah', 'kantin@kampus.id', $pwDemo, 'donatur', '081288991122', 'Kantin Kejujuran Kampus Blok C']);

$insertUser->execute([6, 'Andi Pratama (Anak Rantau)', 'andi@mail.id', $pwDemo, 'penerima', '085611223344', 'Kost Putra Melati No. 12B']);
$insertUser->execute([7, 'Pak Budi (Pekerja Harian Lepas)', 'budi@mail.id', $pwDemo, 'penerima', '085799881122', 'Pondok Singgah Pejuang Nafkah']);
$insertUser->execute([8, 'Siti Nurhaliza (Komunitas Dapur Rantau)', 'siti@mail.id', $pwDemo, 'penerima', '085811334455', 'Posko Relawan Pangan']);

// 4. Buat Demo Menu Makanan Closing Deals
$insertDonation = $db->prepare(
    "INSERT INTO donations (
        id, user_id, judul, deskripsi, jumlah, sisa, satuan, harga_asli, harga_diskon, kategori,
        lokasi_ambil, jam_tutup, tipe_penawaran, jaminan_kualitas, foto, status, expired_at, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$now = date('Y-m-d H:i:s');
$exp1 = date('Y-m-d H:i:s', strtotime('+4 hours')); // Jam 21.00
$exp2 = date('Y-m-d H:i:s', strtotime('+5 hours')); // Jam 21.30
$exp3 = date('Y-m-d H:i:s', strtotime('+6 hours')); // Jam 22.00
$exp4 = date('Y-m-d H:i:s', strtotime('+3 hours')); // Jam 20.30
$exp5 = date('Y-m-d H:i:s', strtotime('-2 hours')); // Kemarin (kedaluwarsa)

// Menu 1: Bakery Pastry - Diskon 75%
$insertDonation->execute([
    1, 2,
    'Croissant Mentega & Aneka Danish Pastry Premium',
    'Produksi fresh batch siang tadi, tekstur renyah lembut dengan butter pilihan. Toko bersiap tutup pukul 21:00, dijual murah khusus untuk pejuang hemat dan anak rantau!',
    18, 12, 'paket', 28000, 7000, 'snack_roti',
    'Artisan Bakery - Kasir Utama (Ambil 20:00 - 21:00)',
    '21:00 WIB', 'diskon_toko',
    'Higienis 100%, dipanggang hari ini, kemasan box kedap udara',
    null, 'tersedia', $exp1, $now
]);

// Menu 2: RM Padang - Diskon 77%
$insertDonation->execute([
    2, 3,
    'Paket Nasi Rendang Daging Sapi + Daun Singkong & Sambal Ijo',
    'Rendang empuk kaya bumbu rempah khas Minang. Batch siang hari masih sangat segar dan terjaga higienis. Solusi makan malam lezat bergizi seharga semangkuk mie instan!',
    20, 14, 'porsi', 26000, 6000, 'makanan_berat',
    'RM Minang Saiyo, Jl. Raya Perintis No. 45 (Meja Kasir)',
    '21:30 WIB', 'diskon_toko',
    'Dimasak segar tadi siang, dipanaskan berkala & higienis',
    null, 'tersedia', $exp2, $now
]);

// Menu 3: Bento Box - Diskon 70%
$insertDonation->execute([
    3, 4,
    'Chicken Katsu Rice Bowl Bento + Salad Sayur Segar',
    'Katsu ayam tebal renyah dengan nasi pulen hangat dan saus teriyaki gurih. Toko tutup pukul 22:00, porsi mantap penutup hari.',
    15, 10, 'kotak', 32000, 9500, 'katering_bento',
    'Food Court Lt. 1 Gedung Mahasiswa - Stand No. 04',
    '22:00 WIB', 'diskon_toko',
    'Kemasan bento food-grade bersegel higienis',
    null, 'tersedia', $exp3, $now
]);

// Menu 4: Warung Peduli - Rp 0 (Gratis Peduli Rantau & Homeless)
$insertDonation->execute([
    4, 5,
    'Paket Nasi Telur Balado, Sayur Sop Segar & Tahu Tempe',
    'Disubsidi khusus dari dapur kejujuran untuk teman-teman perantau yang sedang kehabisan bekal dan saudara pejuang nafkah jalanan. Ambil secara terhormat dan nikmati makanan hangat ini.',
    25, 20, 'porsi', 14000, 0, 'makanan_berat',
    'Kantin Blok C Kampus - Pos Peduli Pangan',
    '20:30 WIB', 'gratis_peduli',
    'Menu rumahan sehat, baru matang sore hari, tanpa MSG berlebih',
    null, 'tersedia', $exp4, $now
]);

// Menu 5: Kedaluwarsa demo
$insertDonation->execute([
    5, 2,
    'Roti Sosis Keju Panggang Pagi Hari',
    'Porsi sisa kemarin yang sudah melewati batas waktu toleransi pengambilan.',
    10, 0, 'bungkus', 18000, 5000, 'snack_roti',
    'Artisan Bakery - Kasir Utama',
    '20:00 WIB', 'diskon_toko',
    'Sudah kedaluwarsa',
    null, 'kedaluwarsa', $exp5, date('Y-m-d H:i:s', strtotime('-1 day'))
]);

// 5. Buat Demo Requests
$insertReq = $db->prepare(
    "INSERT INTO requests (id, donation_id, penerima_id, jumlah_minta, catatan, claim_token, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$insertReq->execute([1, 1, 6, 2, 'Untuk makan malam anak kos bareng teman sekamar.', 'CLM-BKR-001', 'disetujui', $now]);
$insertReq->execute([2, 2, 7, 2, 'Sepulang kerja harian, sangat terbantu terima kasih.', 'CLM-PDG-002', 'disetujui', $now]);
$insertReq->execute([3, 4, 8, 3, 'Untuk adik-adik panti asuhan & perantau di posko.', 'CLM-PDL-003', 'disetujui', $now]);

// 6. Buat Demo Status Logs
$insertLog = $db->prepare(
    "INSERT INTO status_logs (donation_id, request_id, dari_status, ke_status, diubah_oleh, catatan, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$insertLog->execute([1, null, null, 'tersedia', 2, 'Menu diskon jelang tutup toko diterbitkan: 18 paket @ Rp 7.000 (Hemat 75%).', $now]);
$insertLog->execute([1, 1, null, 'menunggu', 6, 'Pemesanan diajukan oleh Andi Pratama sebanyak 2 paket (Estimasi tebus: Rp 14.000).', $now]);
$insertLog->execute([1, 1, 'menunggu', 'disetujui', 2, 'Disetujui Artisan Bakery. Kode ambil: CLM-BKR-001. Sisa stok: 12 paket.', $now]);

$insertLog->execute([2, null, null, 'tersedia', 3, 'Menu diskon jelang tutup diterbitkan: 20 porsi Nasi Rendang @ Rp 6.000.', $now]);
$insertLog->execute([2, 2, null, 'menunggu', 7, 'Pemesanan diajukan oleh Pak Budi (2 porsi).', $now]);
$insertLog->execute([2, 2, 'menunggu', 'disetujui', 3, 'Disetujui RM Minang Saiyo. Sisa stok: 14 porsi.', $now]);

$insertLog->execute([4, null, null, 'tersedia', 5, 'Menu Peduli Pangan Gratis (Rp 0) diterbitkan sebanyak 25 porsi untuk yang membutuhkan.', $now]);
$insertLog->execute([4, 3, null, 'menunggu', 8, 'Pemesanan diajukan oleh Siti Nurhaliza (3 porsi).', $now]);
$insertLog->execute([4, 3, 'menunggu', 'disetujui', 5, 'Disetujui Warung Peduli Berkah. Sisa stok: 20 porsi.', $now]);

echo "Seeder berhasil dijalankan! Platform Closing Food Rescue siap dengan 8 akun dan 5 penawaran demo.\n";
