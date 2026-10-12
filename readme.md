# Berbagi Rasa — Platform Penyelamatan Makanan Berkualitas Murah Jelang Tutup Toko (Closing Food Rescue)

Platform digital penghubung toko roti (*bakery*), restoran, katering, dan warung makan dengan **anak perantauan/kost dengan uang pas-pasan, tunawisma, dan pejuang nafkah** untuk menebus makanan berkualitas prima dengan harga sangat murah saat toko bersiap tutup.

---

## 🌟 Nilai Utama & Paradigma Baru
- **Bukan Makanan Sisa / Bekas**: Makanan yang diposting adalah batch produksi segar hari itu yang belum terjual (*fresh surplus*), bukan sisa piring orang lain.
- **Harga Sangat Ramah Kantong**: Diskon drastis 50% hingga 90% (misal dari Rp 28.000 menjadi Rp 7.000), serta opsi Rp 0 (Subsidi Peduli Pangan) bagi saudara kita yang sangat membutuhkan.
- **Bermartabat & Tanpa Gengsi**: Konsumen memesan secara mandiri di website dan menebus makanan langsung di meja kasir toko dengan kode ambil (*claim token*), seperti halnya transaksi belanja cerdas.
- **Win-Win Economy**: Mitra UMKM mengurangi kerugian bahan baku, sementara konsumen memperoleh makanan bergizi berkualitas dengan biaya super hemat.

---

## 🚀 Fitur Unggulan
- **Price Transparency & Savings Calculator**: Menampilkan perbandingan harga asli, harga diskon toko, total penghematan rupiah, serta kalkulator harga otomatis saat pemesanan.
- **Closing Deals Pickup Window**: Toko menentukan jam tutup operasional dan batas penjemputan di kasir agar alur penjemputan tepat waktu.
- **Jaminan Higienitas & Mutu 100%**: Setiap penawaran disertai keterangan jaminan kualitas dan kesegaran dari mitra toko.
- **Modern 3D Visual & Tactile UI/UX**: Desain modern berbasis *depth layers*, *glassmorphism*, kartu 3D interaktif (*tilt effect*), dan tombol bertombol taktil responsif.
- **Arsitektur MVC Native PHP 8**: Struktur rapi tanpa framework/library eksternal berat, mematuhi standar *single responsibility* dan *clean code*.
- **Keamanan Berlapis (Zero Vulnerabilities)**:
  - 100% Prepared Statements (Anti SQL Injection).
  - Sanitasi Output `e()` (Anti Cross-Site Scripting / XSS).
  - Perlindungan CSRF Token pada setiap form POST.
  - 7 Lapis Validasi Upload Foto (MIME real check, `getimagesize()`, ekstensi allowlist, batas 2MB, random unique name `mkn_*`).
  - Proteksi *Session Fixation* & enkripsi kata sandi BCRYPT.
- **Transaksi ACID Anti-Rebutan Stok**: Manajemen stok menu promo menggunakan `BEGIN TRANSACTION` dan `SELECT ... FOR UPDATE` (Row Locking) sehingga tidak akan pernah terjadi stok minus ataupun *race condition*.
- **Audit Trail & Timeline Status**: Setiap transisi status pesanan tercatat lengkap di buku riwayat dan `status_logs`.

---

## 🛠️ Panduan Instalasi & Menjalankan (Laragon)

1. **Pastikan Laragon Berjalan**:
   - Nyalakan **Apache** dan **MySQL** di Laragon.

2. **Migrasi Database & Isi Data Demo**:
   - Buka terminal / command prompt di folder `berbagi-rasa`:
   ```bash
   php database_seeder.php
   ```
   *(Database `berbagi_rasa`, tabel-tabel, kolom diskon/jam tutup, dan data akun demo akan otomatis dibuat).*

3. **Akses Website**:
   - Buka browser dan kunjungi:
     - `http://berbagi-rasa.test` (atau `http://localhost/berbagi-rasa`)

---

## 👥 Akun Demo untuk Pengujian Cepat

| Peran | Alamat Email | Kata Sandi | Deskripsi |
|---|---|---|---|
| **Mitra Bakery** | `bakery@berbagi.id` | `demo123` | Artisan Bakery & Pastry (Diskon Croissant 75%) |
| **Mitra Resto** | `padang@berkah.id` | `demo123` | RM Minang Saiyo Jaya (Paket Nasi Rendang Murah) |
| **Mitra Bento** | `bento@express.id` | `demo123` | Bento Express Rice Bowl (Diskon 70% Jelang Tutup) |
| **Dapur Peduli** | `kantin@kampus.id` | `demo123` | Warung Peduli Berkah (Gratis Rp 0 untuk Dhuafa & Rantau) |
| **Anak Rantau** | `andi@mail.id` | `demo123` | Mahasiswa Kos & Penyelamat Pangan Hemat |
| **Pekerja Lepas**| `budi@mail.id` | `demo123` | Pak Budi (Pekerja Harian Lepas) |
| **Admin** | `admin@berbagirasa.id` | `admin123` | Pengelola & Audit Sistem Platform |

*(Catatan: Halaman Login juga menyediakan tombol klik cepat **Demo Fill** untuk mempermudah login otomatis).*

---

## 📁 Struktur Folder Proyek
```
berbagi-rasa/
├── index.php                  # Pintu masuk tunggal & router manual
├── database_seeder.php        # Skrip seeder database & demo data
├── config/
│   ├── config.php             # Base URL dinamis & konstanta sistem
│   └── database.php           # PDO Connection Singleton
├── include/
│   ├── helpers.php            # Sanitasi XSS, CSRF, flash, formatRupiah, discountBadge
│   ├── auth.php               # Helper sesi & hak akses peran
│   └── upload.php             # 7-layer validasi upload foto
├── models/
│   ├── User.php               # Query otentikasi & pengguna
│   ├── Donation.php           # Query katalog, harga diskon, stok, jam tutup
│   ├── Request.php            # Query pesanan, total tebusan & token kasir
│   └── StatusLog.php          # Query jejak audit timeline
├── controllers/
│   ├── AuthController.php     # Controller daftar, masuk, keluar
│   ├── DonationController.php # Controller katalog & kelola menu hemat toko
│   ├── RequestController.php  # Controller alur pemesanan & persetujuan kasir
│   └── HistoryController.php  # Controller buku riwayat transaksi
├── views/
│   ├── layouts/               # Header navbar & footer 3D
│   ├── auth/                  # Halaman login & register
│   ├── donations/             # Katalog, detail, tambah menu promo, etalase toko
│   ├── requests/              # Pesanan masuk & pesanan saya
│   └── history/               # Buku log riwayat transaksi
└── assets/
    ├── css/style.css          # Design system modern 3D & komponen diskon
    ├── js/app.js              # Interaksi 3D, tilt, preview upload, validasi
    └── uploads/               # Direktori penyimpanan foto (terproteksi)
```
