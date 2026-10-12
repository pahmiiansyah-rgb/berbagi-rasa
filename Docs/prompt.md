# Master Prompt Super Jelas — Berbagi Rasa v1.0 (MVP)

> Status: FINAL | Tanggal: 2026-09-30 | Untuk: manusia + AI, awam sampai senior.
> Fungsi: surat perintah + rambu agar asisten cepat tapi pelupa tetap konsisten. Urutan menang: `Prd.md` > `structure.md` > `styleguide.md` > `task.md` > file ini.

## Daftar Isi
- §0 Apa Ini + Kenapa Perlu (untuk awam)
- §1 Konteks Tempel Utuh (copy-paste ke AI)
- §2 Larangan Mati (ditolak jika langgar)
- §3 Template S1–S4 Super Detail + Variabel
- §4 Bentuk Output + Contoh Baik vs Buruk
- §5 Checklist + Troubleshooting + FAQ

## §0 Apa Ini? (Analogi 1 Menit)
Anda menyuruh asisten super cepat tetapi pelupa dan suka ngarang. Tanpa surat perintah, ia pasang Bootstrap (dilarang), tambah tabel ngawur, lupa keamanan. File ini adalah surat perintah baku: konteks proyek, batasan, template per sprint, bentuk output, checklist. Tempel §1 + salah satu template §3 setiap minta kode, lalu cek §5 sebelum bilang selesai.

## §1 Konteks Tempel Utuh (Jangan Dipotong)
```
Bangun modul "Berbagi Rasa" — website donasi makanan layak makan untuk Laragon.
STACK KUNCI (harga mati): PHP 8 native TANPA framework/composer/ORM, pola MVC manual,
HTML/CSS/JS murni TANPA Bootstrap/Tailwind/jQuery, MySQL utf8mb4,
pintu tunggal index.php routing ?url=controller/method/param.
DATABASE: berbagi_rasa; tabel users(id,nama,email,password_hash,role,telepon,alamat),
donations(id,user_id,judul,deskripsi,jumlah,sisa,satuan,lokasi_ambil,foto,status,expired_at),
requests(id,donation_id,penerima_id,jumlah_minta,catatan,status),
status_logs(id,request_id,donation_id,dari,ke,oleh,catatan,waktu). Detail Docs/structure.md §4.
ATURAN KERAS: PDO prepare 100% (no concat), tampil pakai e() (htmlspecialchars),
validasi server PHP (JS hanya bantuan), 1 model=1 tabel, no SQL di view/controller,
no HTML di model/echo, upload 1 foto JPG/PNG/WebP ≤2MB ke assets/uploads/ rename uniqid('mkn_'),
cek MIME+getimagesize, auth session user_id+role hash BCRYPT, semua POST CSRF,
stok via TRANSACTION SELECT...FOR UPDATE, alur status Prd.md §3.2, bahasa Indonesia.
```
Penjelasan tiap baris untuk awam: tanpa framework = tanpa instalasi; MVC manual = pisah dapur/pelayan/etalase; PDO prepare = anti-bobol; e() = anti-script nakal; validasi server = jangan percaya browser; uniqid = nama acak anti-tabrakan; transaction = kunci laci saat rebutan; CSRF = token anti-jebakan form.

## §2 Larangan Mati (7 + 3 Tambahan)
1. Tambah library/framework/ORM/composer. 2. Tambah tabel/kolom tanpa revisi PRD+structure. 3. Fitur Fase 2 (chat, peta, WA/email, rating, multi-foto, API, payment, grafik). 4. SQL concat (`"WHERE id=$id"`). 5. Echo input mentah. 6. Password asli/log. 7. Stok tanpa transaction. 8. Buka view langsung tanpa index. 9. Logika bisnis di JS. 10. Placeholder `// lanjutkan sendiri` (harus runnable).

## §3 Template S1–S4 (Copy-Paste, Isi [BRACKET])
**Cara pakai:** salin konteks §1 + satu template, ganti [NAMA], kirim ke AI/dev, terima kode, jalankan checklist §5 di Laragon.

### S1 Pondasi + Auth — variabel: [APP_URL mis http://berbagi-rasa.test]
```
Ikuti structure §1-§3 + styleguide §1-§2.
Buatkan runnable di [APP_URL]:
- config/database.php: class Database::get() PDO singleton ERRMODE_EXCEPTION + komentar cara ganti host.
- config/config.php: BASE_URL, UPLOAD_DIR, MAX_FILE_SIZE=2097152, ALLOWED_MIME.
- include/helpers.php: redirect,e,isLoggedIn,requireLogin,view($p,$d),csrf_token,csrf_check,flash,old.
- include/auth.php: currentUser,requireRole,isOwner.
- models/User.php: create,findByEmail,findById (prepare semua).
- controllers/AuthController.php: register(GET+POST validasi email unik+pw≥6+sama+peran), login(verify+session), logout(destroy).
- views/auth/register.php,login.php + layouts/header+footer (nav dinamis + flash).
Syarat: 7 kasus uji S1 task.md lolos, nol warning, tanpa framework CSS.
Output: // FILE: path + kode + SQL (jika ada) + cara test 5 langkah + akun demo.
```

### S2 Donasi + Foto + Katalog — variabel: [BATAS 2MB], [12/halaman]
```
Ikuti PRD US-03–05 + FR-03 + structure §4–§5.
Buatkan: include/upload.php handleUpload() 7 langkah, models/Donation.php 8 method,
DonationController index/show/create/store/edit/update/destroy/mine, 5 view donations/.
Aturan: sisa=jumlah saat insert, expired masa depan, owner-only, hapus tolak jika aktif + unlink,
katalog hanya tersedia+depan+terbaru+search/filter+[12]/page+markExpired, foto rusak placeholder.
Output: kode + 5 test upload + 4 test katalog + cara demo posting <2 menit.
```

### S3 Minta + Status — variabel: [DONASI_ID contoh 1]
```
Ikuti PRD US-06–08 + FR-04/05/07 + styleguide §6.
Buatkan: models/Request.php class ClaimRequest 6 method, StatusLog 2 method,
RequestController store (5 tolak + 1 sukses), approve/reject/cancel/complete (transaction FOR UPDATE + log),
views incoming/mine + timeline show.
Aturan: cegah sendiri/duplikat/>sisa, tombol per peran, pesan Indonesia jelas.
Output: kode + skenario rebutan 2 tab + tabel tombol + cara test 6 kasus.
```

### S4 Riwayat + Hardening — variabel: [10/halaman]
```
Ikuti PRD US-09 + NFR-02 + structure §6.
Buatkan: HistoryController::index (2 mode + filter + [10]/page dari logs),
audit: e() semua, CSRF semua POST, uploads/index.html, hapus debug, nol warning,
UAT 9 stories + serangan dasar (injection,XSS,exe,CSRF skip) harus gagal baik,
skenario demo 5 menit + akun demo.
Output: kode + checklist centang + bug yang diperbaiki + skrip demo.
```

## §4 Bentuk Output + Baik vs Buruk
Wajib: `// FILE: controllers/X.php` per blok, `<?php` penuh tanpa `?>` akhir (murni PHP), komentar SQL 1 baris + cara test 3 baris, tanpa placeholder, tutup dengan URL+akun+klik 1-2-3.
Buruk: "buatkan donasi" (tanpa acuan, tanpa syarat, tanpa test) → tolak. Baik: "Ikuti S2 template, buat Donation::store + create.php, input judul/jumlah/lokasi/expired/foto, syarat FR-03, output kode + test 5 langkah Laragon." → terima.

## §5 Checklist, Troubleshooting, FAQ
Checklist 8: US/FR disebut? prepare? e()? validasi+izin? upload/transaction? naming? Laragon nol error? demo 5 menit? Troubleshooting: AI pasang Bootstrap → tegur "langgar §2.1, ulangi CSS murni"; tambah kolom → "revisi structure §4 dulu"; stok minus → "tambah FOR UPDATE+rollback"; view langsung dibuka → "wajib via index + requireLogin". FAQ: boleh potong template? Boleh tambah variabel, dilarang hapus syarat keamanan. AI ngawur? Kirim ulang konteks + template + checklist. Masih gagal? Kerjakan manual 1 file kecil dulu, baru batch.
