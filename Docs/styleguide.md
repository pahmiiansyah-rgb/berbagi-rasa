# Style Guide Super Jelas — Berbagi Rasa v1.0 (MVP)

> Status: FINAL-MENGIKAT | Tanggal: 2026-09-30 | Pembaca: pemula sampai senior.
> Motto: "Kode dibaca 10x lebih sering daripada ditulis." Gaya seragam membuat dev baru produktif dalam 1 hari, bug turun setengah.

## Daftar Isi
- §0 Prinsip + Mindset
- §1 Gaya PHP Detail + Benar vs Salah
- §2 Batas MVC + Helper Baku
- §3 Database & Transaction Aman
- §4 Keamanan Dijelaskan Awam (4 lapis)
- §5 Tampilan: Token Desain + Komponen + Responsif + Aksesibilitas
- §6 Status→Warna→Tombol + Contoh HTML
- §7 Error & Validasi Bahasa Manusia
- §8 Checklist + Kesalahan Klasik + FAQ

## §0 Prinsip (Hafalkan 5)
1. Jelas > pintar. Nama panjang jelas (`$sisa_porsi`) lebih baik dari pendek misterius (`$x`). Fungsi ≤30 baris, 1 tugas.
2. Pisah peran (MVC) harga mati. Campur = utang teknis.
3. Inggris untuk kode (`approve`), Indonesia untuk UI/komentar (`Setujui`, `// cek pemilik`).
4. Tanpa framework = disiplin manual. File ini adalah framework-nya.
5. Aman dulu, cantik kemudian. Fitur jalan tetapi bocor = gagal.

## §1 Gaya PHP (Aturan + Alasan + Contoh)
- File: UTF-8, LF, 4 spasi, `<?php` penuh, murni PHP tanpa `?>` akhir (hindari spasi nyasar merusak header).
- Nama: Class `PascalCase` = file (`ClaimRequest.php` berisi `class ClaimRequest`), method `camelCase` (`findActiveByUserAndDonation`), variabel `snake_case` (`$donation_id`), konstanta `UPPER_SNAKE` (`MAX_FILE_SIZE`). Kenapa? Mata langsung tahu jenisnya.
- Perbandingan ketat `===/!==`, tidak `@`. Satu statement satu baris, kurung buka sebaris. Komentar menjelaskan KENAPA, bukan APA (`// kunci agar rebutan tidak minus` bukan `// update sisa`).
```php
// BENAR: jelas, aman, pendek
<?php
class DonationController {
    public function show($id) {
        requireLogin(); // kenapa: detail butuh tahu siapa untuk tombol Minta
        $donation_id = (int) $id; // cast: cegah string aneh
        $donation = (new Donation())->findById($donation_id);
        if (!$donation) { redirect(BASE_URL . '?url=donation/index'); }
        view('donations/show', ['donation' => $donation]);
    }
}
// SALAH: mentah, campur, rawan
<?php
$result = mysqli_query($conn, "SELECT * FROM donations WHERE id = $_GET[id]"); echo $row['judul'];
// masalah: injection + XSS + query di controller + mysqli campur PDO
```
- Include: `require_once __DIR__.'/../config/database.php'` (absolute, tidak relatif `../..` rapuh). Urutan: config → helpers → auth → model → controller.
- Error: kembangkan dengan `error_reporting(E_ALL)` di dev, matikan display di produksi (log saja), jangan tampilkan stack ke pengguna.

## §2 Batas MVC + Helper (Tabel + Contoh)
| Lapisan | Boleh | Dilarang | Contoh benar |
|---|---|---|---|
| Controller 15–30 baris/method | `$_GET/POST`, validasi, `requireLogin/requireRole/isOwner`, panggil 1–2 model, `view/redirect/flash` | SQL, HTML, `new PDO`, hitung stok manual | `$errors = validasi(); if ($errors) view('create',['errors'=>$errors]);` |
| Model return array | `prepare/execute/fetch`, transaction, `CHECK` logika data | `echo/header/$_SESSION/$_POST` | `return $stmt->fetchAll(PDO::FETCH_ASSOC);` |
| View hanya tampil | `foreach/if`, `e()`, `BASE_URL`, `csrf`, `data-confirm`, include layout | `new PDO/query`, `$_POST proses`, `move_uploaded_file` | `<h1><?= e($d['judul']) ?></h1>` |
| include murni | fungsi tanpa DB bisnis | query donasi/request | `function e($s){...}` |
Helper baku + contoh pakai:
```php
function e($s){return htmlspecialchars((string)($s??''),ENT_QUOTES,'UTF-8');}
function redirect($u){header('Location: '.$u);exit;}
function view($p,$d=[]){extract($d);require __DIR__.'/../views/'.$p.'.php';}
// pakai di view form:
<?php if(!empty($errors)): ?><div class="alert-error"><ul><?php foreach($errors as $er):?><li><?= e($er) ?></li><?php endforeach;?></ul></div><?php endif;?>
<input name="judul" value="<?= e($old['judul']??'') ?>" required maxlength="150">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
```

## §3 Database Aman (Pola + Transaction)
- Selalu prepare + named placeholder + cast int. Jangan `LIMIT $limit` mentah (bind int).
```php
// BENAR pagination aman
$st=$this->db->prepare('SELECT * FROM donations WHERE status=:st AND (judul LIKE :kw OR lokasi_ambil LIKE :kw) ORDER BY created_at DESC LIMIT :l OFFSET :o');
$st->bindValue(':st','tersedia'); $st->bindValue(':kw','%'.trim($kw).'%');
$st->bindValue(':l',(int)$l,PDO::PARAM_INT); $st->bindValue(':o',(int)$o,PDO::PARAM_INT); $st->execute();
```
- Transaction rebutan (hafalkan): `beginTransaction → SELECT sisa FOR UPDATE → if jumlah_minta > sisa rollback+pesan → else UPDATE sisa/status + UPDATE request + INSERT log + commit`. Catch → rollback + throw. Dilarang `UPDATE sisa=sisa-5` tanpa kunci (balapan = minus).
- Model terima PDO inject (`__construct($db=null)`) agar bisa unit-test dengan DB palsu.

## §4 Keamanan 4 Lapis (Dijelaskan Seperti ke Adik Kelas)
**Lapis 1 Auth:** daftar `password_hash($pw,PASSWORD_BCRYPT)`, login `password_verify`, session hanya `user_id,nama,role`, `session_regenerate_id(true)` tiap login (cegah bajak), logout `session_unset+destroy`. Pesan gagal generik.
**Lapis 2 CSRF:** token acak per session di tiap POST, cek `hash_equals`. Tanpa token → tolak. Kenapa? Mencegah situs jahat meminjam browser Anda untuk klik diam-diam.
**Lapis 3 Upload:** 7 cek berurutan: `error==UPLOAD_ERR_OK` → `size<=MAX` → MIME allowlist (`image/jpeg,image/png,image/webp`) → `getimagesize!==false` → ekstensi allowlist → rename `uniqid('mkn_',true).ext` → `move_uploaded_file` ke `UPLOAD_DIR` (bukan `copy`). Folder ada `index.html` + (opsional) `.htaccess php_flag engine off`. Gagal → pesan + tidak insert DB + file temp terbuang.
**Lapis 4 Otorisasi:** tiap sensitif: `requireLogin(); $d=find; if((int)$d['user_id']!==(int)$_SESSION['user_id'] && $_SESSION['role']!=='admin') die('Akses ditolak');`. Admin boleh hapus, tidak boleh ubah status sembarang.

## §5 Tampilan: Token + Komponen + Responsif + Aksesibilitas
**Token (ganti 1 tempat, ubah semua):**
```css
:root{--hijau:#2e7d32;--hijau-tua:#1b5e20;--krem:#fff8f0;--teks:#2b2b2b;--abu:#6b7280;
--merah:#c62828;--kuning:#f9a825;--radius:12px;--gap:16px;--max:1080px;--shadow:0 2px 12px rgba(0,0,0,.08);}
body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;background:var(--krem);color:var(--teks);line-height:1.6;}
.wrap{max-width:var(--max);margin:0 auto;padding:0 var(--gap);}
```
**Komponen baku (jangan bikin nama baru jika ada):** `.btn` hijau padding 10-16 radius 10 hover tua + focus outline; `.btn-danger` merah; `.btn-kecil`; `.card` putih radius shadow + `.card img{aspect-ratio:4/3;object-fit:cover}` + `.card .sisa`; `.badge-tersedia/dipesan/selesai/kedaluwarsa/dibatalkan` + request `menunggu/disetujui/ditolak/diambil`; `.form label+input[type=text,number,datetime-local,file]+select+textarea` full + error merah; `.table` header abu + belang + responsif scroll; `.alert-error/sukses`; `.nav` sticky + `.pagination`.
**Responsif:** mobile-first 1 kolom, ≥720px 2 kolom, katalog `grid:auto-fill minmax(240px,1fr)`, tabel dibungkus `.table-scroll{overflow-x:auto}`, foto `max-width:100%`, sentuh ≥44px. **Aksesibilitas:** `label for`, `alt` bermakna ("Nasi kotak ayam 20 porsi"), kontras ≥4.5:1, focus terlihat, tombol bukan `<div onclick>`, bahasa `lang="id"`.
**JS:** hanya `data-confirm`, preview foto (`input[type=file] → img preview via FileReader`), validasi ringan (tetap server final), countdown expired teks ("tersisa 2 jam"). Dilarang: hitung sisa, simpan token di localStorage, ajax ke file view langsung.

## §6 Status→Warna→Tombol + Contoh HTML
| Donasi | Badge | Donatur | Penerima | Contoh |
|---|---|---|---|---|
| tersedia | hijau | Edit/Hapus/Lihat Pemohon | Minta | `<span class="badge-tersedia">Tersedia • sisa 15</span> <a class="btn" href="...store">Minta</a>` |
| dipesan | kuning | Konfirmasi Selesai | Batalkan | kuning = sudah dikunci sebagian |
| selesai/diambil | abu | read-only | read-only | abu = selesai, jangan ganggu |
| kedaluwarsa/ditolak | merah | Hapus | — | merah = berhenti |
Aturan tombol: render di server (`if owner && status`), bukan sembunyi CSS saja (mudah diakali). Tiap aksi merusak POST + CSRF + `data-confirm="Yakin?"`.

## §7 Error Bahasa Manusia + Contoh Pesan
Kumpulkan `$errors[]`, tampil atas form, input lama via `old()`. Baik: "Judul minimal 5 huruf.", "Jumlah harus angka >0.", "Foto maksimal 2MB (file Anda 3,1MB).", "Sisa tinggal 5 porsi.", "Anda sudah punya permintaan menunggu di sini.", "Tidak bisa meminta milik sendiri.", "Donasi sudah selesai/kedaluwarsa." Buruk: "Error", "500", "SQLSTATE". 404 → redirect katalog + flash, bukan stack trace.

## §8 Checklist, Kesalahan Klasik, FAQ
Checklist 10: naming §1? MVC §2? prepare+e+CSRF? upload 7 cek? transaction? token CSS dipakai? responsif 360px? alt/label? nol warning? demo 5 menit? Klasik: lupa `exit` setelah header (kode lanjut jalan), lupa `session_start` (session hilang), `$_GET` mentah, password asli, stok di JS, `SELECT *` di loop (lambat), hapus DB lupa unlink foto (sampah), CSS nama baru tiap halaman (inkonsisten). FAQ: boleh framework? Tidak di MVP. Boleh 2 foto? Tidak, 1 dulu (Fase 2). Warna boleh beda? Boleh asal ubah variabel + badge konsisten. Gelap/light mode? Fase 2.
