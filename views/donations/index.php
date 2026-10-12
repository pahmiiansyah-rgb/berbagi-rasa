<?php
/**
 * View Katalog Makanan Berkualitas Murah Jelang Tutup Toko
 * Cinematic Dark Edition (Shopify Aesthetic)
 */
?>

<!-- Full-Bleed Cinematic Hero Section (Shopify Style) -->
<section id="home" class="hero-shopify-section">
    <!-- Background Looping Video with Poster Fallback -->
    <video class="hero-video-bg" autoplay muted loop playsinline poster="<?= ASSET_URL ?>/assets/images/hero-cinematic.jpg">
        <source src="<?= ASSET_URL ?>/assets/videos/hero-bg.mp4" type="video/mp4">
    </video>

    <!-- Dark Ambient Vignette & Gradient Overlays -->
    <div class="hero-video-overlay"></div>

    <!-- Editorial Text & CTA Content (Shopify Left-Aligned Composition) -->
    <div class="hero-editorial-container">
        <div class="hero-editorial-content">
            <h1 class="hero-editorial-title">
                Makanan berkualitas<br>
                Harga ramah di kantong.
            </h1>

            <p class="hero-editorial-subtitle">
                Selamatkan makanan lezat berkualitas prima sebelum restoran dan toko tutup. 100% higienis, hemat hingga 80%, ditebus langsung di kasir toko.
            </p>

            <div class="hero-editorial-actions">
                <a href="#features" class="btn-shopify-white">
                    Jelajah Fitur Menu
                </a>
                <a href="#our-work" class="btn-shopify-pill-ghost">
                    <span class="play-icon-circle"><i class="fa-solid fa-play"></i></span>
                    <span>Karya & Misi Gerakan</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Impact Metrics Ribbon (Dark Glassmorphism) -->
<section class="metrics-ribbon-section">
    <div class="container">
        <div class="metrics-grid-cinematic">
            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap emerald">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= formatRupiah($stats['total_rupiah_dihemat'] ?? 0) ?></span>
                    <span class="metric-desc">Total Rupiah Dihemat Pengguna</span>
                </div>
            </div>

            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap amber">
                    <i class="fa-solid fa-bowl-food"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= number_format($stats['total_makanan_terselamatkan'] ?? 0) ?>+ Porsi</span>
                    <span class="metric-desc">Makanan Segar Terselamatkan</span>
                </div>
            </div>

            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap blue">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= number_format($stats['total_donatur'] ?? 0) ?> Mitra</span>
                    <span class="metric-desc">Resto & Bakery Bergabung</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Features & Catalog Section -->
<div class="container" id="features" style="padding-top: 48px; padding-bottom: 64px;">
    <span id="katalog" style="display:block; position:relative; top:-90px; visibility:hidden;"></span>
    <!-- Filter & Search Bar (Sleek Dark Theme) -->
    <div class="filter-bar-cinematic">
        <form action="<?= BASE_URL ?>" method="GET" class="filter-form-cinematic">
            <input type="hidden" name="url" value="donation/index">

            <!-- Search Input -->
            <div class="input-cinematic-wrap">
                <i class="fa-solid fa-magnifying-glass input-cinematic-icon"></i>
                <input 
                    type="text" 
                    name="q" 
                    class="form-control-cinematic" 
                    placeholder="Cari menu makanan, bakery, bento, resto..." 
                    value="<?= e($keyword) ?>"
                >
            </div>

            <!-- Location Filter Dropdown -->
            <div class="input-cinematic-wrap">
                <i class="fa-solid fa-location-dot input-cinematic-icon"></i>
                <select name="lokasi" class="form-control-cinematic">
                    <option value="">Semua Lokasi Penjemputan</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= e($loc) ?>" <?= ($lokasi === $loc) ? 'selected' : '' ?>>
                            <?= e($loc) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Price Tier Filter Dropdown -->
            <div class="input-cinematic-wrap">
                <i class="fa-solid fa-tags input-cinematic-icon"></i>
                <select name="harga" class="form-control-cinematic">
                    <option value="">Semua Harga</option>
                    <option value="under_10k" <?= ($filterHarga === 'under_10k') ? 'selected' : '' ?>>Maksimal Rp 10.000</option>
                    <option value="diskon_tinggi" <?= ($filterHarga === 'diskon_tinggi') ? 'selected' : '' ?>>Diskon &gt; 70%</option>
                    <option value="gratis" <?= ($filterHarga === 'gratis') ? 'selected' : '' ?>>Gratis Peduli Rantau (Rp 0)</option>
                </select>
            </div>

            <!-- Submit Filter Button -->
            <button type="submit" class="btn-filter-cinematic">
                <i class="fa-solid fa-filter"></i> Saring Menu
            </button>

            <!-- Reset Button -->
            <?php if (!empty($keyword) || !empty($lokasi) || !empty($filterHarga)): ?>
                <a href="<?= BASE_URL ?>?url=donation/index" class="btn-reset-cinematic">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Section Title & Results Count -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.48rem; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; margin-bottom: 6px;">
                Menu Siap Tebus Malam Ini
            </h2>
            <p style="font-size: 0.9rem; color: #94a3b8; margin: 0;">
                Batch segar produksi hari ini yang belum terjual saat toko bersiap tutup. Ambil langsung di kasir toko.
            </p>
        </div>
        <span style="font-size: 0.88rem; color: #64748b; font-weight: 600;">
            Menampilkan <strong style="color: #e2e8f0;"><?= count($donations) ?></strong> dari <strong style="color: #e2e8f0;"><?= $totalItems ?></strong> menu aktif
        </span>
    </div>

    <!-- Dark Luxury Food Cards Grid -->
    <?php if (!empty($donations)): ?>
        <div class="card-grid-cinematic">
            <?php foreach ($donations as $item): ?>
                <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$item['id'] ?>" class="food-card-cinematic">
                    <div class="card-image-wrap">
                        <?php if (!empty($item['foto']) && file_exists(UPLOAD_DIR . $item['foto'])): ?>
                            <img src="<?= APP_URL ?>/assets/uploads/<?= e($item['foto']) ?>" alt="<?= e($item['judul']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="card-image-placeholder">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Quota Badge Floating -->
                        <div class="badge-quota-floating">
                            <i class="fa-solid fa-box"></i> Sisa <?= (int)$item['sisa'] ?> <?= e($item['satuan']) ?>
                        </div>

                        <!-- Discount / Promo Badge Floating -->
                        <div class="badge-discount-floating">
                            <?= discountBadge((int)($item['harga_asli'] ?? 0), (int)($item['harga_diskon'] ?? 0), $item['tipe_penawaran'] ?? 'diskon_toko') ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="store-label">
                            <i class="fa-solid fa-store"></i>
                            <span><?= e($item['nama_donatur']) ?></span>
                        </div>

                        <h3 class="card-title" title="<?= e($item['judul']) ?>"><?= e($item['judul']) ?></h3>

                        <!-- Price Section (Original vs Discounted) -->
                        <div class="price-container-3d">
                            <div class="price-left-block">
                                <span class="price-original-label">
                                    Normal: <del><?= formatRupiah((int)($item['harga_asli'] ?? 0)) ?></del>
                                </span>
                                <?php if (($item['tipe_penawaran'] ?? '') === 'gratis_peduli' || (int)($item['harga_diskon'] ?? 0) === 0): ?>
                                    <div class="price-final-deal free">
                                        Rp 0 <span class="price-unit-text">(Peduli Rantau)</span>
                                    </div>
                                <?php else: ?>
                                    <div class="price-final-deal">
                                        <?= formatRupiah((int)($item['harga_diskon'] ?? 0)) ?>
                                        <span class="price-unit-text">/ <?= e($item['satuan']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="price-right-tag">
                                <span class="price-deal-badge">
                                    <i class="fa-solid fa-bolt"></i> <?= (($item['tipe_penawaran'] ?? '') === 'gratis_peduli' || (int)($item['harga_diskon'] ?? 0) === 0) ? 'Peduli' : 'Closing Deal' ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-meta-list">
                            <div class="meta-item" title="Jam Tutup Toko: <?= e($item['jam_tutup'] ?? '21:00 WIB') ?>">
                                <i class="fa-solid fa-door-closed" style="color: #f59e0b;"></i>
                                <span>Tutup: <strong><?= e($item['jam_tutup'] ?? '21:00 WIB') ?></strong></span>
                            </div>

                            <div class="meta-item" title="<?= e($item['lokasi_ambil']) ?>">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><?= e($item['lokasi_ambil']) ?></span>
                            </div>

                            <div class="meta-item quality-guarantee" title="<?= e($item['jaminan_kualitas'] ?? '100% Segar & Higienis') ?>">
                                <i class="fa-solid fa-shield-heart"></i>
                                <span><?= e($item['jaminan_kualitas'] ?? '100% Segar & Higienis') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer-cinematic">
                        <div class="countdown-tag-cinematic" title="Batas Waktu Penjemputan">
                            <i class="fa-regular fa-clock"></i> <?= timeRemaining($item['expired_at']) ?>
                        </div>
                        <span class="btn-card-action">
                            Tebus Menu <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrap" style="margin-top: 40px;">
                <?php if ($page > 1): ?>
                    <a href="<?= BASE_URL ?>?url=donation/index&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $page - 1 ?>" class="page-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>?url=donation/index&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $i ?>" class="page-btn <?= ($page === $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= BASE_URL ?>?url=donation/index&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $page + 1 ?>" class="page-btn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state-cinematic">
            <div class="empty-icon"><i class="fa-solid fa-store-slash"></i></div>
            <h3>Belum Ada Menu Promo yang Cocok</h3>
            <p>
                Saat ini belum ada menu makanan dengan kriteria pencarian tersebut. Restoran dan bakery biasanya mulai mengunggah penawaran closing deals menjelang sore hingga malam hari (17.00 - 21.00 WIB).
            </p>
            <a href="<?= BASE_URL ?>?url=donation/index" class="btn-pill-primary" style="margin-top: 16px;">
                Tampilkan Semua Penawaran
            </a>
        </div>
    <?php endif; ?>

</div> <!-- /#features container -->

<!-- Section Layanan Ekosistem (#service) -->
<div class="container" id="service" style="padding-top: 36px; padding-bottom: 24px;">
    <section class="services-section-cinematic">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 36px auto;">
            <span class="mission-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                <i class="fa-solid fa-hand-holding-heart"></i> Ekosistem Layanan
            </span>
            <h2 style="font-size: 1.85rem; font-weight: 800; color: #ffffff; letter-spacing: -0.025em; margin: 12px 0 10px 0;">
                Solusi Terintegrasi Penyelamatan Pangan
            </h2>
            <p style="color: #94a3b8; font-size: 0.98rem; line-height: 1.6; margin: 0;">
                Layanan menyeluruh yang dirancang untuk mempertemukan restoran &amp; bakery berintegritas tinggi dengan pembeli yang bijak dan berhemat.
            </p>
        </div>

        <div class="services-grid-cinematic">
            <!-- Service Card 1 -->
            <div class="service-card-cinematic">
                <div class="service-card-header">
                    <div class="service-icon-wrap emerald">
                        <i class="fa-solid fa-bowl-food"></i>
                    </div>
                    <span class="service-tag">Penyelamat Pangan</span>
                </div>
                <div class="service-card-content">
                    <h3 class="service-title">Closing Deals Makanan Berkualitas</h3>
                    <p class="service-desc">
                        Akses menu segar harian dari resto, cafe, dan bakery ternama dengan potongan harga hingga 80% atau gratis subsidi rantau. Makanan dipastikan 100% layak konsumsi dan higienis.
                    </p>
                </div>
                <div class="service-card-footer">
                    <a href="#features" class="service-action-link">
                        <span>Jelajah Menu Promo</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Service Card 2 -->
            <div class="service-card-cinematic">
                <div class="service-card-header">
                    <div class="service-icon-wrap amber">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <span class="service-tag">Mitra Toko &amp; UMKM</span>
                </div>
                <div class="service-card-content">
                    <h3 class="service-title">Kemitraan Zero Food Waste</h3>
                    <p class="service-desc">
                        Bantu pemilik bisnis F&amp;B memonetisasi surplus batch masak hari ini yang belum habis saat jam tutup. Hindari kerugian modal, kurangi limbah pangan, dan ciptakan dampak sosial nyata.
                    </p>
                </div>
                <div class="service-card-footer">
                    <a href="<?= BASE_URL ?>?url=auth/register" class="service-action-link">
                        <span>Daftar Kemitraan Toko</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Service Card 3 -->
            <div class="service-card-cinematic">
                <div class="service-card-header">
                    <div class="service-icon-wrap blue">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="service-tag">Sistem Terverifikasi</span>
                </div>
                <div class="service-card-content">
                    <h3 class="service-title">Tebus Kasir Aman &amp; Bermartabat</h3>
                    <p class="service-desc">
                        Sistem pemesanan dilengkapi kode klaim digital resmi. Tunjukkan kode tebus langsung ke kasir toko tanpa rasa canggung, tanpa antrean rumit, dengan jaminan stok teralokasi.
                    </p>
                </div>
                <div class="service-card-footer">
                    <a href="#our-work" class="service-action-link">
                        <span>Pelajari Alur &amp; Mutu</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Section Karya & Misi Gerakan Kami (#our-work & #tentang-gerakan) -->
<div class="container" id="our-work" style="padding-top: 16px; padding-bottom: 72px;">
    <span id="tentang-gerakan" style="display:block; position:relative; top:-90px; visibility:hidden;"></span>
    <section class="mission-section-cinematic" style="margin-top: 0;">
        <div class="mission-grid">
            <div class="mission-text">
                <span class="mission-badge">Our Work &amp; Gerakan Nyata</span>
                <h2 class="mission-title">
                    Bukan Makanan Sisa.<br>Ini Pilihan Cerdas &amp; Bermartabat.
                </h2>
                <p class="mission-desc">
                    Setiap hari ribuan porsi makanan lezat dan bergizi terpaksa dibuang oleh bakery dan restoran hanya karena jam operasional toko berakhir. Di sisi lain, mahasiswa perantauan dan pekerja harian berjuang berhemat untuk makan sehari-hari.
                </p>
                <div class="mission-checklist">
                    <div class="check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong>100% Batch Masak Hari Ini</strong>
                            <span>Higienis dan disimpan dalam etalase bersih sebelum toko tutup.</span>
                        </div>
                    </div>
                    <div class="check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong>Penebusan Langsung di Kasir</strong>
                            <span>Dapatkan kode ambil resmi di aplikasi dan ambil pesanan Anda secara profesional.</span>
                        </div>
                    </div>
                    <div class="check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong>Saling Menguntungkan (Win-Win)</strong>
                            <span>Toko tidak rugi bahan baku, anak rantau makan kenyang dan bergizi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mission-card-callout">
                <div class="callout-inner">
                    <div class="callout-icon">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <h3>Punya Toko atau Rumah Makan?</h3>
                    <p>
                        Ubah surplus makanan sebelum tutup toko menjadi rezeki tambahan dan berkah untuk anak rantau di sekitar Anda.
                    </p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>?url=auth/register" class="btn-pill-primary btn-block">
                            Daftarkan Toko Anda Gratis
                        </a>
                    <?php elseif (($_SESSION['role'] ?? '') === 'donatur'): ?>
                        <a href="<?= BASE_URL ?>?url=donation/create" class="btn-pill-primary btn-block">
                            + Pasang Menu Promo Malam Ini
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
