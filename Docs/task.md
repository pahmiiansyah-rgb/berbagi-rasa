# Rencana Kerja Super Detail — Berbagi Rasa MVP (2 Minggu)

> Status: FINAL | Tanggal: 2026-09-30 | Pembaca: semua orang.
> Prinsip: seperti membangun rumah — pondasi (S1) → dinding+etalase (S2) → atap+jantung (S3) → finishing+kunci (S4). Dilarang pasang atap sebelum pondasi.

## Daftar Isi
- §0 Peta 1 Menit + Kalender
- §1 Sprint 1 Pondasi + Auth (detail tugas + test)
- §2 Sprint 2 Donasi + Foto + Katalog
- §3 Sprint 3 Minta + Status (jantung)
- §4 Sprint 4 Riwayat + Keamanan + UAT + Demo
- §5 Fase 2 + Definisi Selesai + Template Lapor + FAQ

## §0 Peta 1 Menit + Kalender Teks
```
Minggu 1: [S1 pondasi 3hr][S2 etalase 4hr]
Minggu 2: [S3 jantung 4hr][S4 finishing 3hr] → DEMO
Total 14 hari kalender untuk 1 dev (±4 jam/hari). Jika 2 dev: bagi frontend/backend per sprint.
```
Aturan PM: kerjakan berurutan, satu task satu fokus, tiap selesai ada bukti test (URL + akun + klik + hasil). Fitur Fase 2 dilarang masuk (tulis di §5 agar tidak lupa, bukan dikerjakan).

## §1 Sprint 1 — Pondasi + Pintu Masuk (Hari 1–3)
**Tujuan awam:** website bisa dibuka tanpa error, orang bisa daftar/masuk/keluar, halaman dalam terkunci.
**Tujuan teknis:** router, PDO, helper, auth, layout.

- **S1-01 Setup inti (4 jam).** Buat `index.php` (session_start, require config/helpers, parse `?url=`, map 4 controller, 404 ramah), `config/database.php` (PDO singleton `mysql:host=localhost;dbname=berbagi_rasa;charset=utf8mb4`, `ERRMODE_EXCEPTION`), `config/config.php` (BASE_URL, UPLOAD_DIR, MAX_FILE_SIZE, ALLOWED_MIME), `include/helpers.php` (redirect, e, isLoggedIn, requireLogin, view, csrf_token/check, flash/old), `include/auth.php` (currentUser, requireRole). Test: buka `?url=donation/index` (kosong tapi tidak error), `?url=ngawur` → redirect katalog. Selesai jika nol warning.
- **S1-02 DB + akun demo (2 jam).** Jalankan SQL `structure.md` §4 berurutan, buat admin/donatur/penerima via hash (`password_hash('admin123', PASSWORD_BCRYPT)`), cek 4 tabel + relasi. Test: `SELECT * FROM users` 3 baris, email unik ditolak duplikat. Selesai jika login manual terbukti.
- **S1-03 Auth penuh (6 jam).** `models/User.php` (3 method) + `AuthController` (register GET/POST, login GET/POST, logout) + 2 view + validasi (nama 3–100, email format+unik, password ≥6 + sama, peran wajib). Test 7 kasus: daftar sukses, email kembar ditolak, password beda ditolak, login benar masuk + session, salah ditolak generik, halaman mine tanpa login redirect, logout bersih. Selesai jika 7/7 lolos.
- **S1-04 Bingkai (4 jam).** `header.php` (meta viewport, style.css, nav dinamis: tamu=Katalog+Masuk+Daftar; login=Katalog+Donasi Saya+Minta Saya+Riwayat+Keluar(nama)), `footer.php` (app.js + disclaimer), `style.css` dasar (variabel, nav, form, btn, alert, responsif 720px). Test: desktop + HP rapi, menu berubah sesuai login. DoD Sprint 1: daftar 2 peran + login/logout + proteksi + nol warning.

## §2 Sprint 2 — Etalase Donasi + Foto (Hari 4–7)
**Tujuan awam:** donatur posting <2 menit, semua orang cari dengan mudah.
- **S2-01 Model (3 jam).** `Donation.php` 8 method (lihat structure §5). Aturan: `create` set `sisa=jumlah`, `markExpired` update lewat waktu. Test: insert 20 → sisa 20, expired kemarin → jadi kedaluwarsa.
- **S2-02 Upload aman (4 jam).** `handleUpload()`: cek `error==0`, `size<=2MB`, MIME + `getimagesize` gambar, ekstensi allowlist, rename `uniqid('mkn_',true)`, `move_uploaded_file`, return ok/nama/error. Pasang `uploads/index.html`. Test 5: jpg 500KB lolos, exe ditolak, 5MB ditolak, jpg palsu ditolak, nama acak + tampil.
- **S2-03 CRUD milik saya (6 jam).** Controller 6 method + 3 view + validasi (judul 5–150, jumlah>0, lokasi wajib, expired masa depan, foto wajib saat tambah). Owner-only + tolak hapus jika ada aktif + unlink foto. Test: posting 6 klik <2 menit, edit orang lain ditolak, hapus aktif ditolak, hapus sukses file hilang.
- **S2-04 Katalog + detail (5 jam).** `index`: hanya tersedia + depan waktu + terbaru + search LIKE + filter lokasi + 12/page + COUNT total + `markExpired` dulu. `show`: foto besar, progress sisa, countdown, badge, form Minta jika berhak, timeline log. Test: 3 data (1 selesai) → 2 tampil, search tepat, foto rusak placeholder. DoD: posting cepat + katalog benar + aman.

## §3 Sprint 3 — Minta + Status, Jantung Aplikasi (Hari 8–11)
**Tujuan awam:** klik Minta → disetujui → ambil → tercatat, seperti lacak paket. Tanpa ini aplikasi hanya etalase mati.
- **S3-01 Model transaksi (3 jam).** `ClaimRequest` + `StatusLog` sesuai kontrak. Test: tiap method return benar.
- **S3-02 Minta (4 jam).** `store` POST: 5 penolakan (belum login, milik sendiri, >sisa, duplikat aktif, tidak tersedia) dengan pesan Indonesia + 1 sukses (`menunggu` + log). Test 6 kasus.
- **S3-03 Setujui/Tolak/Batal/Selesai (6 jam).** Transaction `BEGIN; SELECT sisa FOR UPDATE; cek; UPDATE sisa/status; INSERT log; COMMIT/ROLLBACK`. Approve: sisa-=minta, `dipesan` jika sisa>0 else tetap `dipesan` sampai complete → `selesai`. Reject/cancel: kembalikan + `tersedia` jika belum expired. Complete: penerima `diambil` + donatur konfirmasi `selesai`. Test rebutan: 2 tab minta sisa 5 bersamaan → 1 sukses 1 gagal, sisa 0 tidak minus.
- **S3-04 Layar pantau (4 jam).** `incoming.php` (donatur: nama+jumlah+catatan+waktu + Setujui/Tolak POST confirm), `mine.php` (penerima: Batalkan/Sudah Diambil), timeline di `show.php`. Tombol sesuai `styleguide §6`. Test peran: penerima tidak lihat Setujui, donatur tidak lihat Minta miliknya. DoD: alur PRD §3.2 100% + tidak minus.

## §4 Sprint 4 — Riwayat + Kunci + UAT + Demo (Hari 12–14)
**Tujuan awam:** ada buku catatan, tidak bisa dibobol anak iseng, siap tunjukkan ke orang.
- **S4-01 Riwayat (4 jam).** `HistoryController::index`: donatur = donasi saya + join request + peminta; penerima = request saya + join donasi + pemilik; filter status + 10/page + baca log. Test: tiap approve/ambil muncul dua sisi sama.
- **S4-02 Hardening (4 jam).** Sapu: semua `<?= $var ?>` → `<?= e($var) ?>`, semua POST + CSRF + confirm, uploads index.html, `error_reporting` nol warning, hapus `var_dump`, password tidak log. Test serangan: `' OR '1'='1`, `<script>alert(1)</script>`, upload `.php`, buka view langsung, POST tanpa token → semua gagal baik.
- **S4-03 UAT 9 stories (5 jam).** 2 penguji beda peran jalankan skenario `Prd §4`, catat bug (judul, langkah, harap vs nyata, screenshot), perbaiki, ulangi. Test awam 3 orang posting <2 menit. Lolos jika 9/9 + 0 kritis.
- **S4-04 Demo (2 jam).** Siapkan akun `demo-donatur/demo123`, `demo-penerima/demo123`, 3 donasi foto menarik, skenario 5 menit: posting live → minta live → setujui live → ambil → riwayat. + `readme` cara install Laragon 5 langkah. DoD akhir: FR/NFR lolos + demo mulus.

## §5 Fase 2, Definisi Selesai, Template, FAQ
**Fase 2 (dilarang sekarang):** WA/email otomatis, peta+jarak+rute, rating+ulasan, multi-foto/video, chat, API + Android, donasi uang, grafik admin, multi-bahasa, lupa password otomatis, export Excel. Prioritas setelah MVP dipakai 1 bulan + umpan balik 10 pengguna.
**DoD tiap task:** ikut structure (no SQL di view), ikut styleguide (naming+e+CSRF), lolos Laragon nol warning, sebut US/FR + langkah test. **Template lapor:** `[Sprint X] Judul — Selesai | US/FR | File | Test URL+akun+klik+hasil | Kendala`. **Template bug:** `Judul | Langkah 1-2-3 | Harap vs Nyata | Screenshot | Peran/Akun`. **FAQ:** molor? potong Fase 2, bukan kualitas. Stuck? baca Docs 15 menit, coba minimal, tanya dengan template. Ganti DB? revisi PRD+structure+versi.
