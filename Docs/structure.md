# Struktur Sistem — Berbagi Rasa v1.0 (MVP) — Edisi Super Jelas

> Status: FINAL | Tanggal: 2026-09-30 | Pembaca: awam sampai developer.
> Janji: setelah baca 20 menit, Anda tahu file apa di mana, URL apa artinya, tabel apa isinya, dan kenapa aturannya begitu.

## Daftar Isi
- §0 Analogi + Gambaran 1 Menit
- §1 Alur Hidup Permintaan (Lifecycle) + Contoh Klik
- §2 Peta Folder Super Detail (Tiap File Dijelaskan)
- §3 Routing: Cara Membaca URL + Tabel Lengkap
- §4 Database: Penjelasan Kolom per Kolom + Relasi + Contoh Isi + SQL Final
- §5 Kontrak Model + Contoh Skeleton Aman
- §6 Keamanan, Performa, dan Kesalahan Umum
- §7 FAQ Struktur

## §0 Analogi + Gambaran 1 Menit
Bayangkan warung makan:
- **Model = Dapur + gudang.** Tahu resep (query) dan stok. Tidak ketemu pembeli.
- **View = Etalase + buku menu + meja.** Dilihat, dibaca, diklik. Tidak masak.
- **Controller = Pelayan + kasir.** Dengar pesanan, teriak ke dapur, antar ke meja, terima bayaran (validasi).
- **index.php = Pintu + resepsionis + satpam.** Semua tamu lewat sini, dicek (session), diarahkan ke pelayan yang tepat.
- **config/include = Ruang utilitas.** Stopkontak (koneksi DB), kamus (konstanta), kotak P3K (helper).
Kenapa repot pisah? Agar ganti warna etalase tidak merusak rasa masakan, dan ganti resep tidak merusak meja. Dev baru tidak tersesat: mau ubah tampilan → `views/`, mau ubah aturan stok → `models/`, mau ubah alur klik → `controllers/`.

## §1 Alur Hidup Permintaan (Dari Klik Sampai Tampil)
Contoh: Andi buka `http://berbagi-rasa.test/?url=donation/show/3`.
1. Apache Laragon mengarah ke `index.php` (satu-satunya pintu).
2. `index.php`: `session_start()`, muat `config/config.php` + `database.php` + `helpers.php`, baca `$_GET['url'] = donation/show/3`, pecah jadi controller=donation, method=show, param=3.
3. Router: `DonationController` + `show(3)` dipanggil. Pertama `requireLogin()`? (untuk show boleh publik, untuk mine wajib). Lalu `markExpired()` dijalankan.
4. Controller minta ke Model: `(new Donation())->findById(3)` → Model siapkan PDO `prepare('SELECT ... WHERE id=:id')` + `execute([':id'=>3])` → kembalikan array.
5. Jika tidak ada → `redirect('?url=donation/index')`. Jika ada → ambil `status_logs` via `StatusLog::forDonation(3)` untuk timeline.
6. Controller panggil `view('donations/show', [...])` → `views/layouts/header.php` + `donations/show.php` (pakai `e()` semua) + `footer.php` → HTML dikirim ke browser + CSS/JS.
7. Andi klik Minta (POST ke `?url=request/store`): controller cek login + owner + sisa + duplikat → `Request::create()` + `StatusLog::log()` → redirect ke Permintaan Saya + pesan sukses.
Kunci: browser tidak pernah sentuh DB langsung. Semua lewat pelayan + dapur.

## §2 Peta Folder Super Detail
```
berbagi-rasa/
├── index.php                  # 50-80 baris: session, require config, router 4 controller, 404 aman.
├── config/
│   ├── database.php           # Class Database::get(): PDO mysql host localhost db berbagi_rasa utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC. Satu koneksi dipakai ulang (singleton).
│   └── config.php             # define BASE_URL ('http://berbagi-rasa.test'), UPLOAD_DIR (__DIR__.'/../assets/uploads/'), MAX_FILE_SIZE (2*1024*1024), ALLOWED_MIME (jpg/png/webp).
├── include/
│   ├── helpers.php            # redirect($url), e($s), isLoggedIn(), requireLogin(), view($path,$data), csrf_token(), csrf_check($t), old($k), flash().
│   ├── auth.php               # currentUser() (baca session + query User), requireRole('donatur'), isOwner($owner_id).
│   └── upload.php             # handleUpload($file): return ['ok'=>bool,'nama'=>string,'error'=>string]. Langkah: cek error, size, MIME, getimagesize, ekstensi, rename uniqid('mkn_',true), move_uploaded_file.
├── controllers/               # Tiap method: 10-30 baris. Pola: ambil input → validasi → cek izin → panggil model → redirect/view.
│   ├── AuthController.php     # register (GET form + POST simpan hash), login (verify + session), logout (destroy).
│   ├── DonationController.php # index (katalog publik + search/filter/page + markExpired), show (detail + log), create (form), store (validasi + upload + insert sisa=jumlah), edit/update (owner-only), destroy (owner + no request aktif + unlink foto), mine (milik saya).
│   ├── RequestController.php  # store (5 penolakan + insert menunggu), approve (transaction), reject, cancel (penerima), complete (donatur konfirmasi), mine (penerima), incoming (donatur join requests+donations).
│   └── HistoryController.php  # index: jika donatur → donasi saya + agregat request; jika penerima → request saya + join donasi; filter status + pagination 10.
├── models/                    # Tiap class: property $db, constructor inject PDO. Return array, tanpa echo.
│   ├── User.php               # create(nama,email,hash,role,telepon,alamat), findByEmail, findById. Query 3 saja.
│   ├── Donation.php           # allAvailable(keyword,lokasi,limit,offset) + countAvailable, findById (+join user nama), create, update, delete, myByUser, decSisa, markExpired.
│   ├── Request.php            # class ClaimRequest: create, findById, findActiveByUserAndDonation, incomingForDonatur(donatur_id), myByPenerima, updateStatus.
│   └── StatusLog.php          # log(donation_id,request_id,dari,ke,oleh,catatan), forDonation(donation_id join users nama).
├── views/
│   ├── layouts/header.php     # doctype, meta viewport, link style.css, nav (Katalog, Donasi Saya, Minta Saya, Masuk/Daftar/Keluar + nama), flash pesan.
│   ├── layouts/footer.php     # penutup main, script app.js, footer info + disclaimer makanan.
│   ├── auth/login.php         # form email+password+csrf, link daftar, error.
│   ├── auth/register.php      # form nama+email+password+konfirmasi+peran radio+csrf, syarat jelas.
│   ├── donations/index.php    # hero kecil + form search/filter + grid card (foto, judul, sisa, lokasi, countdown expired, Detail) + pagination.
│   ├── donations/show.php     # foto besar, judul, deskripsi, sisa/progress, lokasi, expired, badge status, form Minta (jumlah+catatan) jika berhak, timeline log, tombol owner (Edit/Hapus) vs admin.
│   ├── donations/create.php   # form lengkap + hint + preview foto JS + error.
│   ├── donations/edit.php     # sama create + nilai lama + info sisa terkunci.
│   ├── donations/mine.php     # tabel milik saya (judul, sisa/jumlah, status badge, expired, aksi).
│   ├── requests/incoming.php  # tabel masuk (peminta, donasi, jumlah, catatan, waktu, Setujui/Tolak POST).
│   ├── requests/mine.php      # tabel saya (donasi, pemilik, jumlah, status, Batalkan/Sudah Diambil).
│   └── history/index.php      # dua mode + filter + tabel + pagination.
├── assets/
│   ├── css/style.css          # variabel, reset ringan, layout max 1080, nav, hero, card grid, badge, btn, form, table, alert, footer, media 720px.
│   ├── js/app.js              # data-confirm, preview foto, validasi ringan (required, min), countdown expired (opsional).
│   └── uploads/               # foto + index.html ("Dilarang intip") + .gitkeep.
└── Docs/                      # Prd, structure (ini), task, prompt, styleguide, agents.
```
Contoh skeleton aman Model:
```php
<?php
class Donation {
    private $db;
    public function __construct($db = null) { $this->db = $db ?? Database::get(); }
    // SELECT ... WHERE id = :id — 1 baris SQL per method, selalu prepare
    public function findById($id) {
        $st = $this->db->prepare('SELECT d.*, u.nama AS pemilik FROM donations d JOIN users u ON u.id=d.user_id WHERE d.id=:id');
        $st->execute([':id' => (int)$id]);
        return $st->fetch();
    }
}
```

## §3 Routing: Tabel Lengkap (Hafalkan Polanya)
Format `?url=controller/method/param`. Method GET = tampil, POST = ubah.
| URL contoh | Controller→Method | Butuh login? | POST/GET | Hasil |
|---|---|---|---|---|
| `?url=donation/index` | Donation→index | tidak | GET | katalog tersedia + search/page |
| `?url=donation/show/3` | Donation→show(3) | tidak | GET | detail id 3 + timeline + form minta |
| `?url=donation/create` | Donation→create | donatur | GET | form tambah |
| `?url=donation/store` | Donation→store | donatur | POST | simpan + upload → redirect mine |
| `?url=donation/edit/3` | Donation→edit | pemilik | GET | form ubah |
| `?url=donation/update/3` | Donation→update | pemilik | POST | simpan ubah |
| `?url=donation/destroy/3` | Donation→destroy | pemilik | POST | hapus + unlink foto |
| `?url=donation/mine` | Donation→mine | ya | GET | tabel milik saya |
| `?url=auth/register`, `auth/login`, `auth/logout` | Auth | tidak/ya | GET+POST | daftar/masuk/keluar |
| `?url=request/store` | Request→store | penerima | POST | buat menunggu |
| `?url=request/approve/7` | Request→approve | pemilik donasi | POST | transaction kurangi sisa |
| `?url=request/reject/7`, `cancel/7`, `complete/7` | Request | sesuai peran | POST | tolak/batal/selesai + log |
| `?url=request/mine`, `request/incoming` | Request | ya | GET | daftar saya / masuk |
| `?url=history/index` | History→index | ya | GET | riwayat per peran + filter |
Semua link pakai `BASE_URL . '?url=...'`. Semua ubah pakai POST + CSRF + `data-confirm`.

## §4 Database Super Jelas (Kenapa 4 Tabel? Kenapa Kolom Ini?)
Prinsip: simpan sekali, pakai berkali-kali. `users` (siapa), `donations` (apa), `requests` (siapa minta apa), `status_logs` (jejak apa terjadi). Tanpa log, riwayat bisa beda dengan kenyataan.

Penjelasan kolom penting (bahasa santai):
- `users.password` HANYA hash 60 karakter (`$2y$10$...`), bukan "123456". Lupa = reset via admin (MVP).
- `donations.jumlah` = total awal (tidak berubah), `sisa` = terkini (berkurang). Contoh 20→15→0. Jangan hitung dari requests tiap buka katalog (lambat).
- `donations.expired_at` DATETIME (tanggal+jam) bukan DATE saja, karena basi soal jam. Contoh `2026-10-01 17:00:00`.
- `requests.jumlah_minta` ≤ `donations.sisa` saat disetujui (dicek transaction).
- `status_logs` wajib 1 baris per transisi. Contoh: Andi menunggu→disetujui oleh Bu Sari jam 14.20 + catatan "ambil jam 16.00".

Relasi teks + contoh isi sudah di §4 versi ringkas; intinya:
`users 1—N donations 1—N requests N—1 users(penerima)`, `donations 1—N status_logs`.
Index `idx_status_created(status,created_at)` membuat `WHERE status='tersedia' ORDER BY created_at DESC` tetap cepat walau 500+ baris. Index `idx_user`, `idx_donation_status` mempercepat mine/incoming.

SQL final (copy-paste ke phpMyAdmin/HeidiSQL, persis, tidak boleh ubah tanpa revisi PRD):
```sql
CREATE DATABASE IF NOT EXISTS berbagi_rasa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE berbagi_rasa;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
  role ENUM('donatur','penerima','admin') NOT NULL DEFAULT 'penerima',
  telepon VARCHAR(20), alamat TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE donations (
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
  judul VARCHAR(150) NOT NULL, deskripsi TEXT,
  jumlah INT NOT NULL CHECK (jumlah > 0), sisa INT NOT NULL,
  satuan VARCHAR(20) NOT NULL DEFAULT 'porsi', lokasi_ambil VARCHAR(255) NOT NULL,
  foto VARCHAR(255),
  status ENUM('tersedia','dipesan','selesai','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'tersedia',
  expired_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_status_created (status, created_at), INDEX idx_user (user_id)
) ENGINE=InnoDB;
CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY, donation_id INT NOT NULL, penerima_id INT NOT NULL,
  jumlah_minta INT NOT NULL CHECK (jumlah_minta > 0), catatan VARCHAR(255),
  status ENUM('menunggu','disetujui','ditolak','dibatalkan','diambil') NOT NULL DEFAULT 'menunggu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
  FOREIGN KEY (penerima_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_donation_status (donation_id, status)
) ENGINE=InnoDB;
CREATE TABLE status_logs (
  id INT AUTO_INCREMENT PRIMARY KEY, request_id INT NULL, donation_id INT NOT NULL,
  dari_status VARCHAR(20), ke_status VARCHAR(20) NOT NULL, diubah_oleh INT, catatan VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE SET NULL,
  FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- Seeder demo (ganti hash dengan password_hash asli):
-- INSERT INTO users (nama,email,password,role) VALUES ('Admin','admin@berbagirasa.id','$2y$...','admin'),('Kantin A','kantin@kampus.id','$2y$...','donatur'),('Andi','andi@mail.id','$2y$...','penerima');
```
Urutan migrasi: buat DB → users → donations → requests → status_logs → seeder → cek `SELECT COUNT(*)`.

## §5 Kontrak Model (Janji Method, Tidak Boleh Kurang)
- User: `create(), findByEmail(), findById()`.
- Donation: `allAvailable($kw,$lok,$limit,$offset), countAvailable($kw,$lok), findById($id), create($d), update($id,$d), delete($id), myByUser($uid), decSisa($id,$n), markExpired()`.
- ClaimRequest: `create($d), findById($id), findActiveByUserAndDonation($uid,$did), incomingForDonatur($donatur_id), myByPenerima($uid), updateStatus($id,$st)`.
- StatusLog: `log($did,$rid,$dari,$ke,$oleh,$cat), forDonation($did)`.
Aturan: method return array/false, lempar Exception jika DB gagal (ditangkap controller jadi pesan ramah).

## §6 Keamanan, Performa, Kesalahan Umum
Keamanan: 100% prepare, 100% `e()`, CSRF POST, upload 4 lapis, owner-check, hash, transaction. Performa: pagination, index, jangan `SELECT *` di loop, foto max 2MB. Kesalahan umum: buka view langsung (harus via index), lupa `session_start`, lupa `exit` setelah header, pakai `$_GET[id]` mentah, simpan password asli, hitung stok di JS, hapus DB tanpa hapus foto.

## §7 FAQ Struktur
Q: Kenapa tanpa framework? A: Tugas kuliah + hosting murah + belajar dasar. Q: Bisa tambah kolom? A: Bisa, tetapi update §4 + PRD §10 + versi. Q: Upload di mana? A: `assets/uploads/` + nama acak. Q: Router error? A: cek `.htaccess`/BASE_URL, pastikan `url` terbaca. Q: Katalog lambat? A: cek index + pagination + foto besar.
