<?php
/**
 * Inisialisasi SQLite Database Demo untuk Lingkungan Serverless / Vercel
 * Berbagi Rasa — Closing Food Rescue
 */

$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$dbPath = $dataDir . '/database.sqlite';
if (file_exists($dbPath)) {
    unlink($dbPath);
}

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Buat Skema Tabel SQLite
$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nama TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'penerima',
    telepon TEXT NULL,
    alamat TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS donations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    judul TEXT NOT NULL,
    deskripsi TEXT NULL,
    jumlah INTEGER NOT NULL,
    sisa INTEGER NOT NULL,
    satuan TEXT NOT NULL DEFAULT 'porsi',
    harga_asli INTEGER NOT NULL DEFAULT 0,
    harga_diskon INTEGER NOT NULL DEFAULT 0,
    kategori TEXT NOT NULL DEFAULT 'makanan_berat',
    lokasi_ambil TEXT NOT NULL,
    jam_tutup TEXT NULL DEFAULT '21:00 WIB',
    tipe_penawaran TEXT NOT NULL DEFAULT 'diskon_toko',
    jaminan_kualitas TEXT NOT NULL DEFAULT '100% Segar dan Higienis Diproduksi Hari Ini',
    foto TEXT NULL,
    status TEXT NOT NULL DEFAULT 'tersedia',
    expired_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    donation_id INTEGER NOT NULL,
    penerima_id INTEGER NOT NULL,
    jumlah_minta INTEGER NOT NULL,
    catatan TEXT NULL,
    claim_token TEXT NULL,
    status TEXT NOT NULL DEFAULT 'menunggu',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS status_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    request_id INTEGER NULL,
    donation_id INTEGER NOT NULL,
    dari_status TEXT NULL,
    ke_status TEXT NOT NULL,
    diubah_oleh INTEGER NULL,
    catatan TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    request_id INTEGER NOT NULL,
    rating INTEGER NOT NULL DEFAULT 5,
    ulasan TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// Masukkan Data Akun Demo
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

// Masukkan Menu Demo Closing Rescue
$now = date('Y-m-d H:i:s');
$exp1 = date('Y-m-d H:i:s', strtotime('+24 hours'));
$exp2 = date('Y-m-d H:i:s', strtotime('+25 hours'));
$exp3 = date('Y-m-d H:i:s', strtotime('+26 hours'));
$exp4 = date('Y-m-d H:i:s', strtotime('+23 hours'));
$exp5 = date('Y-m-d H:i:s', strtotime('-2 hours'));

$insertDonation = $db->prepare(
    "INSERT INTO donations (
        id, user_id, judul, deskripsi, jumlah, sisa, satuan, harga_asli, harga_diskon, kategori,
        lokasi_ambil, jam_tutup, tipe_penawaran, jaminan_kualitas, foto, status, expired_at, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

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

// Buat Demo Requests
$insertReq = $db->prepare(
    "INSERT INTO requests (id, donation_id, penerima_id, jumlah_minta, catatan, claim_token, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$insertReq->execute([1, 1, 6, 2, 'Untuk makan malam anak kos bareng teman sekamar.', 'CLM-BKR-001', 'disetujui', $now]);
$insertReq->execute([2, 2, 7, 2, 'Sepulang kerja harian, sangat terbantu terima kasih.', 'CLM-PDG-002', 'disetujui', $now]);
$insertReq->execute([3, 4, 8, 3, 'Untuk adik-adik panti asuhan & perantau di posko.', 'CLM-PDL-003', 'disetujui', $now]);

// Buat Demo Status Logs
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

echo "SQLite database generated successfully at: " . realpath($dbPath) . "\n";
