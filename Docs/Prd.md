# PRD — Berbagi Rasa v1.0 (MVP)

> Status: FINAL | Tanggal: 2026-09-30 | Pemilik: Product Manager | Versi: 1.0
> Pembaca: semua orang — pemilik kantin, mahasiswa, dosen, developer, QA — tanpa latar teknis khusus.
> Cara membaca: baca §0–§3 untuk paham ide besar (15 menit). Baca §4–§6 jika Anda developer/tester. Baca §8–§12 untuk ukuran sukses dan istilah sulit.

## Daftar Isi
- §0 Cara Membaca Dokumen Ini
- §1 Latar Belakang, Masalah, Solusi, dan Visi
- §2 Pengguna, Persona, dan Hak Akses
- §3 Fitur MVP: 7 Permintaan Menjadi Modul Kerja + Alur Status + Fase 2
- §4 User Story Detail + Skenario Uji Given-When-Then
- §5 Kebutuhan Fungsional (FR) Lengkap dengan Kondisi
- §6 Kebutuhan Non-Fungsional (NFR) + Cara Mengukur
- §7 Alur Layar Langkah per Langkah + Contoh Klik
- §8 Ukuran Berhasil + Rencana Uji Terima (UAT)
- §9 Risiko, Asumsi, dan Ketergantungan
- §10 Keputusan Final yang Mengikat Developer
- §11 Glosarium Super Jelas + FAQ

## 0. Cara Membaca Dokumen Ini (2 Menit)
Dokumen PRD (Product Requirements Document) adalah "kontrak" tentang apa yang dibangun. Dokumen ini menjawab:
1. **KENAPA** aplikasi ini ada? → §1.
2. **UNTUK SIAPA?** → §2 (3 peran + 2 persona contoh).
3. **BISA APA?** → §3 dan §4 (7 fitur + 9 user story).
4. **ATURAN MAINNYA APA?** → §5, §6, §10 (tidak boleh dilanggar saat coding).
5. **KAPAN SELESAI?** → §8 (4 syarat lolos).
Jika Anda orang awam, cukup baca §0–§3 dan §11. Jika Anda developer, wajib hafal §3.2 + §5 + §10.

## 1. Latar Belakang, Masalah, Solusi, dan Visi

### 1.1 Cerita masalah (kenapa ini penting)
Setiap hari, restoran, kantin kampus, dan panitia acara menghasilkan makanan berlebih: nasi kotak sisa seminar 30 kotak, ayam goreng sisa 15 potong, snack sisa 40 bungkus. Makanan ini masih layak makan dalam 3–5 jam ke depan, tetapi karena tidak ada yang mengambil cepat, akhirnya dibuang ke tempat sampah. Di waktu yang sama, mahasiswa perantauan, pekerja harian, komunitas sosial, dan panti membutuhkan makanan gratis yang layak. Masalahnya bukan tidak ada makanan, tetapi tidak ada **jembatan informasi yang cepat**.

### 1.2 Analogi sederhana (bayangkan ini)
Bayangkan papan pengumuman di depan kantin. Si pemilik kantin menempel kertas: "Ada 20 nasi kotak sisa seminar, GRATIS, ambil di Kantin Blok A sebelum jam 17.00. Foto terlampir." Orang yang lewat membaca, datang, mengambil. Berbagi Rasa adalah papan pengumuman itu dalam bentuk website yang bisa dibuka dari HP, dengan foto, jumlah stok real-time, tombol Minta, dan catatan riwayat agar tidak ada yang lupa.

### 1.3 Solusi yang ditawarkan (apa yang dibangun)
Website satu pintu di mana:
- **Donatur** (pemberi) memposting donasi: judul, foto, jumlah, lokasi ambil, batas waktu.
- **Penerima** (pengambil) membuka katalog etalase, mencari, mengajukan permintaan jumlah tertentu.
- **Sistem** mengunci stok saat disetujui, melacak status (menunggu → disetujui → diambil), dan mencatat semua di riwayat + buku log.
- **Admin** menghapus konten bermasalah (foto tidak pantas, info palsu).

### 1.4 Visi dan tujuan terukur (SMART)
Visi: "Tidak ada makanan layak yang terbuang di lingkungan kita."
Tujuan MVP (harus bisa diuji):
1. Donatur awam (baru pertama pakai) bisa terbitkan donasi + foto dalam < 2 menit tanpa dipandu (diuji ke 3 orang).
2. Penerima awam bisa menemukan donasi dan menekan Minta tanpa tutorial (diuji ke 3 orang).
3. Kedua pihak selalu tahu posisi barang (menunggu/disetujui/diambil) hanya dengan membuka halaman detail.
4. Setiap perubahan status meninggalkan jejak di `status_logs` sehingga bisa diaudit (tidak ada donasi hilang misterius).

### 1.5 Teknologi yang dikunci (tidak boleh diganti di MVP)
PHP native pola MVC tanpa framework, HTML/CSS/JS murni tanpa Bootstrap/jQuery/Tailwind, MySQL di Laragon (Apache). Alasan profesional yang jujur:
- Ringan dan cepat tanpa instalasi tambahan (cocok untuk tugas kuliah dan hosting murah).
- Mudah dipelajari: semua alur terlihat, tidak disembunyikan framework.
- Konsekuensi: kita harus disiplin manual (aturan di `styleguide.md`) karena tidak ada framework yang menjaga.

## 2. Pengguna, Persona, dan Hak Akses (Siapa Boleh Apa)

### 2.1 Aturan peran (sederhana dan mengikat)
Hanya 3 peran. Satu akun = satu peran, dipilih saat daftar dan tidak bisa ganda. Kenapa? Agar izin jelas dan kode sederhana (1 tabel `users` dengan kolom `role`). Jika Andi ingin memberi sekaligus menerima, ia daftar 2 akun dengan 2 email berbeda (diterima untuk MVP).

| Peran | Contoh nyata | Boleh | Dilarang tegas |
|---|---|---|---|
| Donatur | Kantin Blok A, RM Padang Sederhana, panitia seminar HIMAFOR, ibu kos | Daftar, posting donasi miliknya, edit/hapus miliknya, upload 1 foto, lihat permintaan masuk, Setujui/Tolak, konfirmasi selesai | Minta donasi milik sendiri; ubah/hapus milik orang lain; posting tanpa foto/lokasi/batas waktu |
| Penerima | Andi (mahasiswa), Komunitas Jumat Berbagi, Panti Asuhan X | Daftar, buka katalog, cari/filter, Minta (jumlah + catatan), Batalkan miliknya, konfirmasi Sudah Diambil, lihat riwayat sendiri | Posting donasi; minta melebihi sisa; duplikat permintaan aktif; ambil tanpa persetujuan |
| Admin MVP | Pengelola kampus / ketua komunitas | Login, lihat semua, hapus konten bermasalah, nonaktifkan user nakal | Mengubah alur status; melihat grafik analitik (belum ada); mengintip password (hanya hash) |

### 2.2 Persona detail (bayangkan orangnya)
**Persona 1 — Bu Sari, 45 tahun, pemilik kantin.**
- Keseharian: buka HP Android, sibuk melayani, tidak suka form panjang.
- Kebutuhan: Jumat jam 15.00 ada 30 porsi sisa. Ia buka Berbagi Rasa, klik +Tambah, isi judul "Nasi Ayam Sisa Jumat", jumlah 30, lokasi "Kantin Blok A, ambil 15–17", foto 1 jepretan, simpan. Selesai < 2 menit. Jam 16.00 ia buka Permintaan Masuk, setujui 5 orang @5 porsi, tolak 1 yang minta 50 (melebihi sisa).
- Frustrasi jika: harus isi banyak kolom, upload gagal tanpa pesan jelas, tidak tahu siapa yang minta.

**Persona 2 — Andi, 20 tahun, mahasiswa perantau.**
- Keseharian: hemat uang, aktif di HP, malam lapar.
- Kebutuhan: jam 19.00 buka katalog, ketik "nasi", filter lokasi "kampus", lihat kartu dengan foto + sisa 10 porsi + batas 21.00. Klik detail, klik Minta 2 porsi + catatan "ambil jam 20.00". Pantau status menunggu → disetujui. Datang, ambil, klik Sudah Diambil.
- Frustrasi jika: stok tidak update (datang ternyata habis), tombol membingungkan, tidak tahu lokasi.

### 2.3 Customer journey ringkas
Bu Sari: dengar info → daftar 1 menit → posting 2 menit → dapat notifikasi permintaan (di MVP: cek manual) → setujui 30 detik → konfirmasi selesai → lihat riwayat bangga.
Andi: buka katalog tanpa login bisa? (keputusan: katalog bisa dilihat publik, tetapi Minta wajib login) → daftar 1 menit → minta 1 menit → pantau → ambil → konfirmasi → riwayat bertambah.

## 3. Fitur MVP (Dari 7 Permintaan Anda Menjadi Spesifikasi Kerja)

### 3.1 Spesifikasi per fitur (field, aturan, contoh)
**F1 — Auth (Daftar/Masuk/Keluar).** Field daftar: nama (wajib, 3–100 huruf), email (wajib, format email, unik), password (wajib, min 6, disimpan hash), konfirmasi password (harus sama), peran (wajib pilih donatur/penerima; admin dibuat manual, tidak bisa daftar bebas). Login: email + password, pesan gagal generik ("Email atau password salah") agar tidak bocorkan mana yang salah. Logout: hapus session + kembali ke katalog. Proteksi: semua URL `mine/incoming/history/create/store` redirect ke login jika belum masuk.

**F2 — Data Donasi.** Field: judul (wajib, 5–150 huruf, contoh "Nasi Kotak Seminar Proposal"), deskripsi (opsional, contoh "Isi ayam + tahu, masih hangat jam 14.00"), jumlah (wajib, angka >0, contoh 20), satuan (wajib pilih: porsi/kotak/paket/bungkus/potong), lokasi_ambil (wajib, contoh "Kantin Blok A, meja kasir, 15–17"), expired_at (wajib, tanggal+jam未来, contoh hari ini 17:00, tidak boleh masa lalu), foto (wajib 1 file, lihat F3). Status awal otomatis `tersedia`, `sisa = jumlah`. Edit: hanya pemilik, tidak boleh jika `selesai`, tidak boleh ubah jumlah lebih kecil dari yang sudah disetujui. Hapus: hanya pemilik/admin, hanya jika tidak ada request `menunggu/disetujui`, file foto ikut dihapus.

**F3 — Upload Foto.** Aturan detail: 1 file, ekstensi jpg/jpeg/png/webp, ukuran ≤2MB (2.097.152 byte), dimensi wajar (dicek `getimagesize()` agar bukan file palsu berekstensi jpg), MIME dicek `mime_content_type()`, nama baru `mkn_<uniqid>.ext` agar tidak tabrakan dan tidak bocorkan nama asli, simpan `assets/uploads/`, folder dipasang `index.html` agar tidak bisa diintip daftarnya. Gagal upload: pesan jelas ("Foto maksimal 2MB, format JPG/PNG/WebP.") + form tidak hilang (input teks tetap terisi).

**F4 — Katalog Ketersediaan.** Menampilkan hanya `status='tersedia'` dan `expired_at > NOW()`, urut `created_at` terbaru. Tiap kartu: foto (atau placeholder jika rusak), judul, sisa + satuan, lokasi singkat, batas waktu ("sisa 3 jam"), tombol Detail. Fitur: pencarian judul/deskripsi (LIKE %keyword%), filter lokasi (dropdown/ketik), pagination 12 per halaman (tombol 1-2-3...), otomatis jalankan `markExpired()` tiap dibuka. Katalog boleh dibuka tanpa login (publik) agar cepat menyebar, tetapi tombol Minta mengarah ke login jika belum masuk.

**F5 — Permintaan Donasi.** Form: jumlah_minta (wajib, 1..sisa, contoh 2), catatan (opsional, max 255, contoh "untuk 2 orang"). Validasi tolak dengan pesan: belum login ("Silakan masuk dulu."), milik sendiri ("Tidak bisa meminta donasi sendiri."), melebihi sisa ("Sisa tinggal 5 porsi."), duplikat aktif ("Anda sudah punya permintaan menunggu/disetujui di donasi ini."), donasi tidak tersedia ("Donasi sudah dipesan/selesai/kedaluwarsa."). Sukses: baris baru `status='menunggu'`, tulis log, donasi belum terkunci (tetap `tersedia`) sampai disetujui.

**F6 — Status Pengambilan.** Lihat §3.2 (wajib). UI: timeline vertikal di halaman detail (misal: 14.00 dibuat → 14.10 Andi minta 5 → 14.20 disetujui → 16.00 diambil). Tombol kontekstual (hanya muncul jika berhak, lihat `styleguide.md` §6).

**F7 — Riwayat.** Dua mode: donatur melihat donasi miliknya + daftar peminta + status akhir + tanggal; penerima melihat permintaan miliknya + judul donasi + pemilik + status + tanggal. Filter: status (semua/menunggu/disetujui/ditolak/diambil/selesai), urut terbaru, 10 per halaman. Sumber data: gabungan `donations + requests + status_logs` agar konsisten.

### 3.2 Alur Status (Hukum yang Tidak Boleh Dilanggar)
```
DONASI:  [baru] tersedia ──Setujui──▶ dipesan ──Konfirmasi ambil──▶ selesai
               ├──lewat waktu──▶ kedaluwarsa
               └──batal manual──▶ dibatalkan

REQUEST: [baru] menunggu ──Setujui──▶ disetujui ──Ambil──▶ diambil (selesai)
                ├──Tolak──▶ ditolak
                └──Batal──▶ dibatalkan
```
Aturan dengan contoh angka (hafalkan):
1. Bu Sari posting 20 → `tersedia`, `sisa=20`.
2. Andi minta 5 → request `menunggu`, donasi tetap `tersedia sisa 20`.
3. Bu Sari Setujui Andi → cek `5 <= 20` lolos dalam transaction → `sisa=15`, request `disetujui`, donasi `dipesan`, tulis log "menunggu→disetujui oleh Bu Sari".
4. Budi minta 15 → disetujui → `sisa=0`, tetap `dipesan`.
5. Andi ambil + Bu Sari konfirmasi → request Andi `diambil`, jika semua sudah diambil → donasi `selesai`.
6. Jika Tolak/Batal: request jadi `ditolak/dibatalkan`, `sisa` dikembalikan (+jumlah_minta), donasi kembali `tersedia` jika `expired_at` masih depan.
7. Jika jam 17.01 lewat dan masih `tersedia/dipesan` → `markExpired()` ubah jadi `kedaluwarsa`, tidak bisa diminta lagi.

### 3.3 Fase 2 (Ditulis Agar Tidak Lupa, Dilarang Dikerjakan Sekarang)
Chat antar pengguna, peta + jarak + tombol rute, notifikasi WA/email otomatis, rating bintang + ulasan, banyak foto + video, aplikasi Android, donasi uang/transfer, grafik admin (total porsi terselamatkan per bulan), multi-bahasa, API publik. Alasan ditunda: tiap item +3–7 hari + risiko keamanan. MVP harus demo 2 minggu.

## 4. User Story Detail + Skenario Given-When-Then (Untuk Tester)
| ID | Story | Kriteria selesai (ringkas) | Skenario uji contoh |
|---|---|---|---|
| US-01 | Sebagai pengunjung saya ingin daftar | Email unik, password ≥6 + konfirmasi sama, peran wajib, redirect login + pesan sukses | Given belum punya akun, When daftar email baru + password 123456, Then masuk DB hash + diarahkan login |
| US-02 | Sebagai pengguna saya ingin masuk/keluar | Login benar masuk + session, salah ditolak generik, proteksi redirect, logout bersih | Given sudah daftar, When login salah password, Then tetap di login + pesan "Email atau password salah" |
| US-03 | Sebagai donatur saya ingin posting + foto | Semua wajib terisi, jumlah>0, expired masa depan, foto valid, muncul ≤5 dtk, sisa=jumlah | Given login donatur, When posting 20 + jpg 500KB, Then katalog menampilkan + sisa 20 |
| US-04 | Sebagai donatur saya ingin ubah/hapus milik saya | Owner-only, tidak bisa jika selesai/ada request aktif, file ikut hapus | Given punya donasi + ada peminta menunggu, When hapus, Then ditolak "Masih ada permintaan aktif" |
| US-05 | Sebagai semua saya ingin lihat katalog | Hanya tersedia, terbaru, search/filter/pagination jalan, foto rusak jadi placeholder | Given 3 donasi (1 selesai), When buka katalog, Then hanya 2 tersedia tampil |
| US-06 | Sebagai penerima saya ingin minta | Login, 1..sisa, bukan milik sendiri, no duplikat, sukses jadi menunggu | Given sisa 5, When minta 10, Then ditolak "Sisa tinggal 5 porsi" |
| US-07 | Sebagai donatur saya ingin setujui/tolak | Owner-only, cek sisa transaction, tulis log, tolak kembalikan | Given 2 orang rebutan sisa 5 (minta 5 dan 5), When setujui bersamaan, Then satu sukses sisa 0, satu gagal |
| US-08 | Sebagai keduanya saya ingin lacak status | Timeline lengkap + tombol sesuai peran | Given request disetujui, When penerima buka detail, Then tombol "Sudah Diambil" muncul, tombol Setujui tidak |
| US-09 | Sebagai keduanya saya ingin riwayat | Beda isi per peran, filter + pagination, sesuai log | Given Andi diambil, When buka riwayat Andi + Bu Sari, Then keduanya menampilkan baris yang sama statusnya |

## 5. Kebutuhan Fungsional Lengkap (FR-01 s/d FR-08)
- FR-01 Auth sesi: daftar hash BCRYPT, login verify, session `user_id, nama, role`, logout destroy. Gagal login tidak bocorkan.
- FR-02 Validasi server: semua field dicek ulang di PHP (tipe, panjang, rentang). JS hanya bantuan. Error dikumpulkan `$errors[]` + tampil `.alert-error`, input lama tidak hilang.
- FR-03 Upload: cek `error==0`, `size<=2MB`, MIME + `getimagesize` gambar, rename uniqid, move_uploaded_file, gagal ada pesan + tidak insert DB.
- FR-04 Concurrency: `BEGIN; SELECT sisa FOR UPDATE; if cukup: UPDATE + INSERT log + COMMIT else ROLLBACK + pesan`. Dilarang update sisa tanpa kunci.
- FR-05 Otorisasi: helper `requireLogin()`, `requireRole()`, cek `owner_id == session` di tiap store/update/destroy/approve/reject/complete.
- FR-06 Expired: `markExpired()` = `UPDATE donations SET status='kedaluwarsa' WHERE status IN ('tersedia','dipesan') AND expired_at < NOW()`, dipanggil tiap index/show.
- FR-07 Log wajib: tiap transisi insert `status_logs(donation_id, request_id, dari, ke, oleh, catatan, waktu)`. Riwayat baca dari sini.
- FR-08 Pagination & search: `LIMIT/OFFSET` + `COUNT(*)` total, keyword di-escape LIKE, lokasi filter parsial.

## 6. Kebutuhan Non-Fungsional + Cara Mengukur (NFR-01 s/d NFR-06)
- NFR-01 Performa: katalog ≤1,5 dtk @500 baris. Cara: pagination 12, index `(status, created_at)`, foto dikompres manual sebelum upload (edukasi). Ukur: stopwatch + 500 dummy.
- NFR-02 Keamanan: 100% prepare, 100% `e()`, CSRF semua POST, upload ketat, no eval/exec. Ukur: coba injection + XSS dasar harus gagal.
- NFR-03 Kompatibilitas: PHP 8 Laragon, MySQL 8/MariaDB, Chrome/Edge/Firefox, HP Android 720p. CSS murni. Ukur: buka di 2 browser + 1 HP.
- NFR-04 Keterpeliharaan: 1 model 1 tabel, no SQL di view, no HTML di model, nama ikut styleguide. Ukur: review checklist 5 menit per file.
- NFR-05 Kegunaan: ≤3 klik posting, bahasa Indonesia, error jelas. Ukur: 3 awam tanpa pandu.
- NFR-06 Keandalan: tidak ada sisa minus, tidak ada upload merusak, tidak ada session bocor. Ukur: uji rebutan + upload jahat.

## 7. Alur Layar Detail (Klik per Klik)
**Donatur posting:** Login → menu Donasi Saya → +Tambah Donasi → isi judul/jumlah/satuan/lokasi/batas/foto → Simpan → lihat kartu di katalog + di Donasi Saya (tersedia). Estimasi 6 klik, <2 menit.
**Penerima minta:** Buka katalog (boleh tanpa login) → search "nasi" → klik kartu → baca sisa + batas + lokasi → Minta → (jika belum login → login dulu) → isi jumlah + catatan → Kirim → status menunggu → pantau di Permintaan Saya.
**Donatur kelola:** Buka Permintaan Masuk → lihat nama peminta + jumlah + catatan + waktu → Setujui (jika stok cukup) / Tolak (+alasan) → stok + log update.
**Ambil:** Penerima datang sesuai lokasi → tunjukkan halaman disetujui → ambil fisik → klik Sudah Diambil → donatur klik Konfirmasi Selesai → donasi selesai → masuk riwayat.
**Admin bersih-bersih:** Login admin → buka semua donasi → hapus yang melanggar + catat alasan → user nakal dinonaktifkan (di MVP: hapus/ubah role manual via DB, UI nonaktifkan Fase 2).

## 8. Ukuran Berhasil + Rencana UAT (Kapan Boleh Pesta)
Lolos jika: (1) 9 US lolos 100% oleh 2 penguji beda peran, (2) 0 celah dasar (injection/XSS/upload exe/CSRF skip), (3) 3 awam posting <2 menit, (4) rebutan tidak minus, (5) nol warning PHP di Laragon, (6) demo 5 menit tanpa macet. Skenario demo: donatur posting live → penerima minta live → donatur setujui live → konfirmasi → tunjukkan riwayat.

## 9. Risiko, Asumsi, Ketergantungan (Jujur Profesional)
Risiko: foto jahat (mitigasi validasi + rename + limit), rebutan (transaction), makanan basi diklaim layak (expired wajib + disclaimer "Periksa sebelum makan, admin tidak menjamin" + hapus cepat), scope creep (tolak + Fase 2), HP kentang (CSS ringan + foto kecil), lupa password (MVP: reset manual via admin, lupa otomatis Fase 2).
Asumsi: pengguna jujur soal kelayakan, punya HP + internet, ambil fisik sendiri (tanpa antar), waktu server benar (untuk expired).
Ketergantungan: Laragon Apache+MySQL jalan, folder `assets/uploads` writable, PHP `file_uploads=On, upload_max_filesize>=2M`.

## 10. Keputusan Final Mengikat (Tidak Boleh Improvisasi)
1. DB `berbagi_rasa`, 4 tabel `users, donations, requests, status_logs` persis `structure.md` §4.
2. 1 foto/donasi di `assets/uploads/`, 2MB, rename acak.
3. Auth sesi native, tanpa library.
4. 1 CSS + 1 JS, tanpa framework.
5. Alur status §3.2 harga mati. Ubah = revisi PRD + versi baru.
6. Kerjakan sprint berurutan `task.md`. Dilarang lompat ke riwayat sebelum auth jadi.

## 11. Glosarium + FAQ (Untuk Orang Awam)
**Istilah:** Donatur (pemberi), Penerima (pengambil), Katalog (etalase tersedia), CRUD (tambah/lihat/ubah/hapus), Session (tanda sudah login), PDO prepared (cara aman tanya DB), XSS (suntik script via komentar), CSRF (jebak form dari situs lain), Transaction + FOR UPDATE (kunci laci saat rebutan), Pagination (halaman 1-2-3), MVP (versi paling sederhana yang berguna), Seeder (data contoh), UAT (uji oleh pengguna asli).
**FAQ:** Apakah harus login untuk lihat katalog? Tidak, tetapi wajib untuk Minta/Posting. Apakah bisa antar? MVP tidak, ambil sendiri. Apakah bisa donasi uang? Tidak, hanya makanan. Lupa password? Hubungi admin (reset otomatis Fase 2). Makanan basi? Ada batas waktu + disclaimer, selalu cek fisik. Apakah ada aplikasi HP? Belum, website HP-friendly dulu.

---
*PRD v1.0 dikunci 2026-09-30. Perubahan butuh persetujuan PM + naik versi + update tanggal.*
