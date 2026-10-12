# DESIGN.md — Arah Desain & Identitas "Berbagi Rasa"

## 1. Identitas & Misi Produk
**Berbagi Rasa** adalah platform penyelamatan makanan berkualitas prima menjelang toko/restoran tutup (*Closing Food Rescue*). Menghubungkan restoran, bakery, kafe, dan warung makan dengan anak kos, mahasiswa perantauan, dan pejuang nafkah untuk mendapatkan makanan lezat & higienis dengan harga diskon besar (atau gratis peduli), sekaligus mencegah pemborosan makanan (*zero food waste*).

## 2. Nada & Kepribadian Visual (Visual Personality)
- **Karakter**: Hangat (*warm*), bermartabat (*dignified*), jujur (*honest*), segar & menggugah selera (*appetizing*), serta ramah (*welcoming*).
- **Bukan**: Bukan SaaS AI korporat yang kaku, bukan toko e-commerce tiruan Shopify, bukan terminal hacker, dan bukan skema warna cyberpunk/crypto yang dingin.

## 3. Sistem Warna (Color Palette)
- **Background Utama**: `#0b0f17` (Deep Warm Slate — netral gelap yang nyaman di mata, tidak hitam mati/void).
- **Surface / Kartu**: `#131924` (Permukaan solid yang jelas, bukan kaca transparan berlebihan).
- **Surface Elevated**: `#1c2436` (Untuk kartu aktif, dropdown, dan modal).
- **Warna Aksi Utama (Sustainability & Freshness)**: `#10b981` (Fresh Emerald Green — simbol pangan segar dan kepedulian lingkungan).
- **Warna Aksen Penyelamatan (Closing Deals & Waktu Tutup)**: `#f59e0b` (Warm Amber — penanda waktu tutup dan kehangatan kuliner).
- **Teks Utama**: `#f8fafc` (Putih bersih dengan kontras tinggi WCAG AAA).
- **Teks Muted**: `#94a3b8` (Abu-abu terbaca untuk metadata sekunder).
- **Batas / Border**: `rgba(255, 255, 255, 0.08)` (Garis tipis struktural tanpa glow norak).

## 4. Tipografi & Skala
- **Font Utama**: `'Plus Jakarta Sans', system-ui, -apple-system, sans-serif`.
- **Hierarki Jelas**:
  - H1: 2rem – 2.5rem (Desktop), 1.5rem – 1.8rem (Mobile).
  - H2: 1.4rem – 1.7rem.
  - Body: 0.95rem – 1rem, line-height 1.6.
  - Metadata / Badges: 0.75rem – 0.85rem, tebal (font-weight: 700).

## 5. Aturan Anti-Slop (Filtering Rules)
1. **Dilarang Meniru Brand Lain Tanpa Alasan**: Hilangkan semua kelas dan gaya sisa `.brand-icon-shopify`, `.hero-shopify-section`, dll.
2. **Dilarang Video Latar Belakang Berat**: Ganti video looping yang boros kuota/baterai dengan hero editorial yang cepat, tajam, dan langsung ke katalog makanan.
3. **Dilarang Glassmorphism & Glow di Setiap Elemen**: Batasi efek blur hanya pada navbar atas saat scroll. Semua kartu menggunakan warna solid dengan batas yang bersih.
4. **Dilarang Copywriting AI Klise**: Gunakan bahasa Indonesia yang membumi, manusiawi, dan jelas tanpa istilah AI mengada-ada (*"akselerasi sinergi solusi terintegrasi"* dilarang).
5. **Mobile-First & Responsif Seutuhnya**: Setiap halaman (Katalog, Detail, Etalase Toko, Pesanan Saya, Pesanan Masuk Toko, Riwayat) wajib memiliki tampilan khusus mobile yang nyaman dengan target sentuh jari ≥ 42px.
