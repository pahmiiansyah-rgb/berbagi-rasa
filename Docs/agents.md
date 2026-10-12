# Tim & Aturan Main Super Jelas — Berbagi Rasa v1.0

> Status: MENGIKAT | Tanggal: 2026-09-30 | Pembaca: semua kontributor manusia + AI, termasuk yang baru gabung 5 menit lalu.
> Satu kalimat: siapa kerjakan apa, bagaimana cara benar, kapan boleh bilang selesai, dan apa yang dilarang.

## Daftar Isi
- §0 Kenapa Perlu Aturan Tim (cerita 1 menit)
- §1 Peran Detail + RACI + Contoh Harian
- §2 Alur Kerja 7 Langkah (dari baca sampai demo)
- §3 Aturan Emas + Contoh Pelanggaran
- §4 Review Kode + Syarat Lolos + Cara Menolak Baik
- §5 Komunikasi + Template Lapor/Bug/Minta Tolong
- §6 Onboarding 30 Menit Anggota Baru + FAQ

## §0 Kenapa Perlu? (Analogi Warung 30 Detik)
Warung tanpa peran: semua masak, semua kasir, stok kacau, pembeli bingung. Warung rapi: pemilik jaga menu (PM), dapur masak (Backend), etalase+pelayan layani (Frontend), pencicip awasi rasa+racun (QA). Di proyek ini sama. Aturan ini membuat 1 dev tidak burnout dan 4 dev tidak tabrakan. Fase sekarang = dokumentasi: hanya sentuh `Docs/*.md`, dilarang scaffold `controllers/models/views/config/assets` sebelum perintah "lanjut kode".

## §1 Peran Detail + Tabel RACI
| Peran | Fokus harian (contoh) | Patokan | Dilarang | Selesai jika | RACI (R=pelaksana,A=penanggung,J=C konsultasi,I=info) |
|---|---|---|---|---|---|
| PM/Arsitek (Pemilik) | Kunci scope, tolak Fase 2, pecah sprint, demo | Prd.md | Tambah fitur diam-diam, ubah status seenaknya | PRD dikunci + sprint jalan + demo mulus | A semua, R scope |
| Backend (Dapur) | Router, PDO, Model, transaction, upload, auth hash | structure §4–§5, styleguide §1–§4 | Concat SQL, query di view, HTML di model, stok tanpa kunci | CRUD jalan + rebutan tidak minus + nol warning | R logika+DB |
| Frontend (Etalase) | Layout, card, badge, form, CSS variabel, JS confirm | styleguide §5–§7 | Bootstrap/jQuery, logika stok di JS, tombol palsu CSS-only | Rapi HP+desktop, e() semua, tombol per peran | R tampilan |
| QA (Pencicip) | Uji US-01–09, rebutan, serangan dasar, UAT awam | Prd §4/§8, task DoD | Centang tanpa coba Laragon, sembunyikan bug | 9/9 lolos + bukti + 0 kritis | R kualitas, A tolak rilis |
| Semua | Baca Docs 15 mnt, 1 task 1 fokus, lapor template | prompt.md | Kerja lompat sprint, keputusan di chat saja | Laporan ada US/FR+test | J+R masing-masing |

Contoh harian: PM pagi cek task.md (apa tertunda?), Backend siang kerjakan S3-03 transaction + test 2 tab, Frontend sore rapikan show.php timeline + badge, QA malam coba injection + catat bug template. Tidak ada yang kerjakan Fase 2 (peta/chat/WA) walau "kelihatan gampang".

## §2 Alur Kerja 7 Langkah (Wajib Berurutan)
1. **Baca (15 mnt):** Prd → structure → styleguide → prompt → task. Catat yang tidak paham, tanya sebelum coding.
2. **Ambil (5 mnt):** pilih 1 item task.md (mis S2-03), ubah `[ ]` jadi `[/]` (dikerjakan), tulis target hari ini.
3. **Rancang (10 mnt):** tulis di kertas: URL apa, method apa, tabel apa, validasi apa, tombol siapa. Cocokkan PRD §3.2 + kontrak structure §5.
4. **Kerja (fokus 2 jam):** ikut template prompt §3 + gaya styleguide. Satu file selesai → test langsung, jangan tumpuk 10 file baru test.
5. **Uji (30 mnt):** Laragon `http://berbagi-rasa.test`, coba jalur sukses + 3 jalur gagal (kosong, kebanyakan, bukan pemilik). Catat URL+akun+klik+hasil.
6. **Rapikan (15 mnt):** hapus debug, pastikan e/CSRF/naming, checklist styleguide §8, update `[x]` + laporan.
7. **Minta review (10 mnt):** kirim laporan + file diubah + cara test ke reviewer (PM/QA). Lolos → merge. Gagal → perbaiki 1 putaran, tidak debat panjang.

## §3 Aturan Emas (7, Dilanggar = Rework Tanpa Diskusi)
1. Satu waktu satu task. Jangan campur auth + katalog.
2. Ubah DB → revisi structure §4 + PRD §10 + naik versi dulu.
3. Fase docs → hanya Docs/*.md. Fase kode → baru sentuh code (tunggu perintah).
4. Keputusan penting tertulis di Docs (chat hilang, Docs abadi).
5. Aman > cepat: prepare, e, validasi, izin, transaction, CSRF tidak boleh disunat demi deadline.
6. Pesan error bahasa Indonesia jelas, jangan "error" saja.
7. Dilarang copy-paste tanpa paham: tiap baris harus bisa dijelaskan saat review.

Contoh pelanggaran + perbaikan: tambah kolom `rating` diam-diam → kembalikan + tulis proposal Fase 2; pasang Bootstrap CDN → hapus + pakai style.css; tombol Hapus link GET → ganti form POST+CSRF+confirm; stok `UPDATE sisa=sisa-x` tanpa kunci → tambah FOR UPDATE+rollback test.

## §4 Review Kode (Cara Lolos 10 Menit)
Reviewer cek berurutan: (1) US/FR disebut? (2) MVC benar? (3) prepare semua? (4) e() semua? (5) validasi server + izin owner? (6) upload/transaction jika terkait? (7) naming + token CSS? (8) Laragon nol warning + test sukses+gagal? Lolos 8/8 → approve. Gagal 1 kritis (injection/XSS/stok minus/izin jebol) → tolak langsung dengan kalimat baik: "Bagus strukturnya, tetapi S3-03 belum pakai FOR UPDATE (risiko minus, lihat structure §4). Tambahkan transaction + test 2 tab, lalu minta review lagi." Non-kritis (nama kurang jelas, komentar kurang) → approve dengan catatan.

## §5 Komunikasi + Template (Copy-Paste)
**Lapor progres:** `[Sprint X] Judul — Selesai/Diproses/Tertunda | US/FR: ... | File: ... | Test: buka ... login ... klik ... hasil ... | Kendala: ... | Butuh: ...`
**Lapor bug:** `Judul jelas | Langkah 1-2-3 | Harap: ... | Nyata: ... + screenshot | Akun/peran: ... | US terkait: ...`
**Minta tolong baik:** "Saya kerjakan S2-02 upload (acuan FR-03 + styleguide §4). Sudah coba X, error Y (screenshot), butuh Z. File: include/upload.php baris 20-40." **Buruk:** "Upload error, tolong." (tanpa acuan, tanpa yang dicoba).
**Etika:** tanya setelah 30 menit stuck + baca Docs + coba minimal; jawab <1 hari; kritik kode bukan orang; konflik → rujuk PRD (menang) bukan opini.

## §6 Onboarding 30 Menit + FAQ
Menit 0–5: install Laragon, buka `http://berbagi-rasa.test`, baca readme. 5–15: baca PRD §1–§3 + glosarium. 15–25: baca structure §0–§3 + buka folder Docs. 25–30: ambil 1 task kecil (S1-01) + tulis laporan pertama. FAQ: boleh kerja 2 sprint paralel? Tidak (kecuali PM bagi frontend/backend jelas). Boleh ubah warna? Boleh via variabel, badge konsisten. Lupa password demo? Minta admin reset (MVP manual). Mau usul fitur? Tulis 1 paragraf (masalah, solusi, US, risiko) → PM putuskan MVP/Fase 2. Kapan Fase 2? Setelah MVP dipakai 1 bulan + 10 umpan balik.
