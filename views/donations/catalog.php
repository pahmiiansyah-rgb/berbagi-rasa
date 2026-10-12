<?php
/**
 * View Katalog Menu Siap Tebus Jelang Tutup Toko - Berbagi Rasa
 * Dedicated Marketplace / Catalog View
 */
?>

<div class="container" style="padding-top: 36px; padding-bottom: 72px;">
    <!-- Catalog Header -->
    <div class="catalog-page-header" style="margin-bottom: 32px;">
        <div class="catalog-header-badge" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 750; color: #10b981; background: rgba(16, 185, 129, 0.12); padding: 6px 14px; border-radius: 999px; border: 1px solid rgba(16, 185, 129, 0.25); margin-bottom: 12px;">
            <i class="fa-solid fa-store"></i> Menu Segar Hari Ini Jelang Tutup
        </div>
        <h1 style="font-size: 2.1rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; line-height: 1.25; margin-bottom: 8px;">
            Katalog Menu Siap Tebus
        </h1>
        <p style="font-size: 1rem; color: #94a3b8; max-width: 680px; line-height: 1.6; margin: 0;">
            Batch produksi hari ini dari restoran, kafe, dan toko roti sekitar yang belum habis terjual. Pesan lewat web, ambil langsung di kasir toko sebelum jam tutup.
        </p>
    </div>

    <!-- Filter & Search Bar (Sleek Dark Theme) -->
    <div class="filter-bar-cinematic" style="margin-bottom: 32px;">
        <form action="<?= BASE_URL ?>" method="GET" class="filter-form-cinematic">
            <input type="hidden" name="url" value="donation/catalog">

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
                <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-reset-cinematic">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Results Count & Active Filter Tags -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <span style="font-size: 0.92rem; color: #94a3b8; font-weight: 600;">
            Menampilkan <strong style="color: #ffffff;"><?= count($donations) ?></strong> dari <strong style="color: #ffffff;"><?= $totalItems ?></strong> menu aktif
        </span>

        <?php if (!empty($keyword) || !empty($lokasi) || !empty($filterHarga)): ?>
            <div style="display: inline-flex; gap: 8px; flex-wrap: wrap;">
                <?php if (!empty($keyword)): ?>
                    <span style="font-size: 0.78rem; background: rgba(255,255,255,0.08); padding: 4px 10px; border-radius: 6px; color: #cbd5e1;">
                        Kata Kunci: "<?= e($keyword) ?>"
                    </span>
                <?php endif; ?>
                <?php if (!empty($lokasi)): ?>
                    <span style="font-size: 0.78rem; background: rgba(255,255,255,0.08); padding: 4px 10px; border-radius: 6px; color: #cbd5e1;">
                        Lokasi: <?= e($lokasi) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($filterHarga)): ?>
                    <span style="font-size: 0.78rem; background: rgba(16, 185, 129, 0.15); padding: 4px 10px; border-radius: 6px; color: #34d399;">
                        Filter: <?= e($filterHarga) ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Food Cards Grid -->
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
                    <a href="<?= BASE_URL ?>?url=donation/catalog&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $page - 1 ?>" class="page-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>?url=donation/catalog&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $i ?>" class="page-btn <?= ($page === $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= BASE_URL ?>?url=donation/catalog&q=<?= urlencode($keyword) ?>&lokasi=<?= urlencode($lokasi) ?>&harga=<?= urlencode($filterHarga) ?>&page=<?= $page + 1 ?>" class="page-btn">
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
                Saat ini belum ada menu dengan kriteria pencarian tersebut. Resto dan bakery umumnya mulai memasang menu surplus jelang sore hingga malam hari (17.00 - 21.30 WIB).
            </p>
            <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-pill-primary" style="margin-top: 16px;">
                Tampilkan Semua Menu
            </a>
        </div>
    <?php endif; ?>

</div>
