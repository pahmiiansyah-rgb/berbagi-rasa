<?php
/**
 * View Etalase Toko Saya (Dashboard Mitra Toko/UMKM) - Berbagi Rasa
 * Mobile-First & Responsive Dual Layout (Mobile Cards + Desktop Table)
 */
$items = $donations ?? $myDonations ?? [];
$isAdmin = ($isAdmin ?? false) || (($_SESSION['role'] ?? '') === 'admin');

// Hitung Statistik Toko untuk KPI Dashboard
$totalItems    = count($items);
$activeItems   = array_filter($items, fn($i) => ($i['status'] ?? '') === 'tersedia');
$activeCount   = count($activeItems);
$closedCount   = $totalItems - $activeCount;
$totalSisa     = array_sum(array_column($activeItems, 'sisa'));
$totalPending  = array_sum(array_column($items, 'pending_requests_count'));
$totalRequests = array_sum(array_column($items, 'total_requests_count'));

$storeName = $isAdmin ? 'Panel Kontrol Administrator' : (currentUser()['nama'] ?? $_SESSION['nama'] ?? 'Mitra Toko');
?>

<div class="store-page-wrapper">
    <!-- 1. Header Toko (Mobile-First) -->
    <div class="store-dashboard-header">
        <div class="store-profile-wrap">
            <div class="store-avatar-box <?= $isAdmin ? 'admin-avatar' : '' ?>" style="<?= $isAdmin ? 'background: linear-gradient(135deg, #7c3aed, #9333ea); box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4);' : '' ?>">
                <i class="fa-solid <?= $isAdmin ? 'fa-shield-halved' : 'fa-store' ?>"></i>
                <span class="store-status-dot" title="<?= $isAdmin ? 'Moderasi Admin Aktif' : 'Toko Buka & Aktif' ?>"></span>
            </div>
            <div class="store-profile-info">
                <div class="store-badge-verified <?= $isAdmin ? 'admin-badge' : '' ?>" style="<?= $isAdmin ? 'background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.35);' : '' ?>">
                    <i class="fa-solid <?= $isAdmin ? 'fa-user-shield' : 'fa-circle-check' ?>"></i> <?= $isAdmin ? 'Hak Akses Administrator Sistem' : 'Mitra UMKM Resmi' ?>
                </div>
                <h1 class="store-title"><?= e($storeName) ?></h1>
                <p class="store-subtitle">
                    <i class="fa-solid <?= $isAdmin ? 'fa-sliders' : 'fa-utensils' ?>"></i> 
                    <?= $isAdmin ? 'Moderasi & Kelola Seluruh Menu Makanan Hemat dari Semua Mitra Toko' : 'Etalase Menu Makanan Hemat Jelang Tutup Toko' ?>
                </p>
            </div>
        </div>

        <div class="store-header-actions">
            <a href="<?= BASE_URL ?>?url=donation/create" class="btn-store-primary">
                <i class="fa-solid fa-plus"></i>
                <span>+ Pasang Menu Baru</span>
            </a>
            <a href="<?= BASE_URL ?>?url=request/incoming" class="btn-store-secondary <?= ($totalPending > 0) ? 'has-pending' : '' ?>">
                <i class="fa-solid fa-receipt"></i>
                <span><?= $isAdmin ? 'Semua Pesanan' : 'Periksa Pesanan' ?></span>
                <?php if ($totalPending > 0): ?>
                    <span class="pending-pill-pulse"><?= $totalPending ?> Baru</span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- 2. Ringkasan Statistik Toko (Mobile-Friendly 2x2 Grid / 4-Col Desktop) -->
    <div class="store-stats-grid">
        <div class="store-stat-card">
            <div class="stat-icon-wrap stat-icon-green">
                <i class="fa-solid fa-bowl-food"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= $activeCount ?></span>
                <span class="stat-label">Menu Aktif</span>
            </div>
        </div>

        <div class="store-stat-card">
            <div class="stat-icon-wrap stat-icon-blue">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= $totalSisa ?></span>
                <span class="stat-label">Porsi Siap Tebus</span>
            </div>
        </div>

        <div class="store-stat-card <?= ($totalPending > 0) ? 'stat-card-alert' : '' ?>">
            <div class="stat-icon-wrap stat-icon-amber">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= $totalPending ?></span>
                <span class="stat-label">Pesanan Menunggu</span>
            </div>
        </div>

        <div class="store-stat-card">
            <div class="stat-icon-wrap stat-icon-purple">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= $totalRequests ?></span>
                <span class="stat-label">Total Pesanan</span>
            </div>
        </div>
    </div>

    <!-- 3. Alert Callout jika Ada Pesanan Masuk Menunggu Persetujuan -->
    <?php if ($totalPending > 0): ?>
        <div class="store-pending-callout">
            <div class="callout-icon">
                <i class="fa-solid fa-bell fa-bounce"></i>
            </div>
            <div class="callout-body">
                <strong>Ada <?= $totalPending ?> pesanan baru menunggu persetujuan kasir!</strong>
                <p>Kunci porsi makanan dan terbitkan kode ambil kasir untuk pembeli.</p>
            </div>
            <a href="<?= BASE_URL ?>?url=request/incoming" class="callout-btn">
                <span>Periksa Sekarang</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    <?php endif; ?>

    <!-- 4. Bar Saring & Cari Menu -->
    <div class="store-toolbar-card">
        <div class="store-tabs-wrapper">
            <button type="button" class="store-filter-tab active" data-filter="all">
                Semua (<?= $totalItems ?>)
            </button>
            <button type="button" class="store-filter-tab" data-filter="tersedia">
                Aktif (<?= $activeCount ?>)
            </button>
            <button type="button" class="store-filter-tab" data-filter="tutup">
                Selesai / Tutup (<?= $closedCount ?>)
            </button>
        </div>

        <div class="store-search-wrap">
            <i class="fa-solid fa-magnifying-glass store-search-icon"></i>
            <input type="text" id="storeSearchInput" class="store-search-input" placeholder="Cari nama menu / lokasi..." autocomplete="off">
            <button type="button" id="storeSearchClear" class="store-search-clear" style="display: none;" title="Hapus Pencarian">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- 5. Tampilan Utama Menu Promo -->
    <?php if (!empty($items)): ?>

        <!-- A. TAMPILAN KHUSUS MOBILE (< 768px): KARTU ETALASE TOKO MODERN -->
        <div class="store-mobile-cards-list" id="storeMobileCards">
            <?php foreach ($items as $item): ?>
                <?php 
                    $isTersedia    = ($item['status'] === 'tersedia');
                    $filterCategory = $isTersedia ? 'tersedia' : 'tutup';
                    $hargaAsli     = (int)($item['harga_asli'] ?? 0);
                    $hargaDiskon   = (int)($item['harga_diskon'] ?? 0);
                    $isFree        = (($item['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
                    $jumlahPorsi   = max(1, (int)$item['jumlah']);
                    $sisaPorsi     = max(0, (int)$item['sisa']);
                    $stockPercent  = min(100, max(0, round(($sisaPorsi / $jumlahPorsi) * 100)));
                    $pendingCount  = (int)($item['pending_requests_count'] ?? 0);
                    $totalCount    = (int)($item['total_requests_count'] ?? 0);
                ?>
                <div class="store-mobile-card" data-category="<?= $filterCategory ?>" data-title="<?= strtolower(e($item['judul'] . ' ' . $item['lokasi_ambil'])) ?>">
                    <!-- Card Top: Badges & Sisa Stok -->
                    <div class="mobile-card-topbar">
                        <div class="mobile-badge-group">
                            <?= statusBadge($item['status']) ?>
                            <?= discountBadge($hargaAsli, $hargaDiskon, $item['tipe_penawaran'] ?? 'diskon_toko') ?>
                        </div>
                        <div class="mobile-stock-pill <?= ($sisaPorsi <= 3 && $sisaPorsi > 0) ? 'stock-critical' : '' ?>">
                            <i class="fa-solid fa-boxes-stacked"></i> Sisa <strong><?= $sisaPorsi ?></strong>/<?= $jumlahPorsi ?> <?= e($item['satuan']) ?>
                        </div>
                    </div>

                    <!-- Card Body: Foto & Informasi Menu -->
                    <div class="mobile-card-body">
                        <div class="mobile-card-media">
                            <?php if (!empty($item['foto']) && file_exists(UPLOAD_DIR . $item['foto'])): ?>
                                <img src="<?= APP_URL ?>/assets/uploads/<?= e($item['foto']) ?>" alt="<?= e($item['judul']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="mobile-media-fallback">
                                    <i class="fa-solid fa-utensils"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mobile-card-info">
                            <?php if ($isAdmin && !empty($item['nama_donatur'])): ?>
                                <div style="font-size: 0.76rem; font-weight: 750; color: #a855f7; margin-bottom: 3px; display: flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-store"></i> <?= e($item['nama_donatur']) ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="mobile-card-title">
                                <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$item['id'] ?>">
                                    <?= e($item['judul']) ?>
                                </a>
                            </h3>

                            <div class="mobile-card-location">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><?= e($item['lokasi_ambil']) ?></span>
                            </div>

                            <div class="mobile-card-pricing">
                                <?php if ($isFree): ?>
                                    <span class="price-free-badge">
                                        <i class="fa-solid fa-gift"></i> Gratis Peduli
                                    </span>
                                <?php else: ?>
                                    <span class="price-deal"><?= formatRupiah($hargaDiskon) ?></span>
                                    <del class="price-original"><?= formatRupiah($hargaAsli) ?></del>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card Schedule & Progress Bar -->
                    <div class="mobile-card-meta-row">
                        <div class="meta-schedule-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>Tutup: <strong><?= e($item['jam_tutup'] ?? '21:00 WIB') ?></strong></span>
                        </div>
                        <div class="meta-countdown-item">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <span><?= timeRemaining($item['expired_at']) ?></span>
                        </div>
                    </div>

                    <div class="mobile-progress-wrapper">
                        <div class="mobile-progress-labels">
                            <span>Ketersediaan Stok</span>
                            <span><?= $sisaPorsi ?> dari <?= $jumlahPorsi ?> <?= e($item['satuan']) ?></span>
                        </div>
                        <div class="mobile-progress-track">
                            <div class="mobile-progress-fill <?= ($stockPercent <= 20) ? 'fill-low' : '' ?>" style="width: <?= $stockPercent ?>%;"></div>
                        </div>
                    </div>

                    <!-- Banner Notifikasi Pesanan Masuk (Jika ada) -->
                    <?php if ($pendingCount > 0): ?>
                        <a href="<?= BASE_URL ?>?url=request/incoming" class="mobile-order-alert-banner">
                            <div class="alert-banner-left">
                                <i class="fa-solid fa-bell fa-shake"></i>
                                <span><strong><?= $pendingCount ?> Pesanan Menunggu!</strong> Ketuk untuk konfirmasi</span>
                            </div>
                            <i class="fa-solid fa-chevron-right alert-arrow-icon"></i>
                        </a>
                    <?php elseif ($totalCount > 0): ?>
                        <div class="mobile-orders-stat-row">
                            <i class="fa-solid fa-receipt"></i>
                            <span><?= $totalCount ?> Total pesanan pembeli tersimpan</span>
                        </div>
                    <?php endif; ?>

                    <!-- Card Actions (Thumb-Friendly Buttons) -->
                    <div class="mobile-card-actions">
                        <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$item['id'] ?>" class="btn-card-touch btn-touch-view" title="Lihat Tampilan Publik">
                            <i class="fa-solid fa-eye"></i>
                            <span>Lihat</span>
                        </a>

                        <?php if ($item['status'] !== 'selesai' && $item['status'] !== 'kedaluwarsa' || $isAdmin): ?>
                            <a href="<?= BASE_URL ?>?url=donation/edit/<?= (int)$item['id'] ?>" class="btn-card-touch btn-touch-edit" title="Ubah Data Menu">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Ubah</span>
                            </a>
                        <?php endif; ?>

                        <form action="<?= BASE_URL ?>?url=donation/destroy/<?= (int)$item['id'] ?>" method="POST" class="mobile-delete-form" data-confirm="Hapus menu promo '<?= e($item['judul']) ?>' secara permanen?">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <button type="submit" class="btn-card-touch btn-touch-delete" title="Hapus Menu Promo">
                                <i class="fa-solid fa-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- B. TAMPILAN KHUSUS DESKTOP (>= 768px): TABEL LENGKAP & RAPI -->
        <div class="store-desktop-table-card">
            <div class="table-responsive">
                <table class="data-table-3d store-data-table" id="storeDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 75px;">Foto</th>
                            <th style="min-width: 240px;">Menu & Lokasi</th>
                            <th style="width: 140px;">Harga Tebus</th>
                            <th style="width: 130px;">Sisa Stok</th>
                            <th style="width: 140px;">Jam Tutup</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 120px;">Pesanan</th>
                            <th style="width: 140px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php 
                                $isTersedia    = ($item['status'] === 'tersedia');
                                $filterCategory = $isTersedia ? 'tersedia' : 'tutup';
                                $hargaAsli     = (int)($item['harga_asli'] ?? 0);
                                $hargaDiskon   = (int)($item['harga_diskon'] ?? 0);
                                $isFree        = (($item['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
                            ?>
                            <tr data-category="<?= $filterCategory ?>" data-title="<?= strtolower(e($item['judul'] . ' ' . $item['lokasi_ambil'])) ?>">
                                <td>
                                    <?php if (!empty($item['foto']) && file_exists(UPLOAD_DIR . $item['foto'])): ?>
                                        <img src="<?= APP_URL ?>/assets/uploads/<?= e($item['foto']) ?>" alt="<?= e($item['judul']) ?>" class="table-thumb-img">
                                    <?php else: ?>
                                        <div class="table-thumb-fallback">
                                            <i class="fa-solid fa-utensils"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($isAdmin && !empty($item['nama_donatur'])): ?>
                                        <div style="font-size: 0.76rem; font-weight: 750; color: #a855f7; margin-bottom: 3px; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-store"></i> Toko: <?= e($item['nama_donatur']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <strong class="table-item-title">
                                        <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$item['id'] ?>">
                                            <?= e($item['judul']) ?>
                                        </a>
                                    </strong>
                                    <span class="table-item-location">
                                        <i class="fa-solid fa-location-dot"></i> <?= e($item['lokasi_ambil']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($isFree): ?>
                                        <span class="badge-discount-3d badge-free">Gratis Peduli</span>
                                    <?php else: ?>
                                        <strong class="table-deal-price">
                                            <?= formatRupiah($hargaDiskon) ?>
                                        </strong>
                                        <del class="table-original-price"><?= formatRupiah($hargaAsli) ?></del>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <strong class="table-stock-num">
                                        <?= (int)$item['sisa'] ?>
                                    </strong> 
                                    <span class="table-stock-satuan">/ <?= (int)$item['jumlah'] ?> <?= e($item['satuan']) ?></span>
                                </td>

                                <td>
                                    <div class="table-closing-time">
                                        <i class="fa-solid fa-clock"></i> <?= e($item['jam_tutup'] ?? '21:00 WIB') ?>
                                    </div>
                                    <span class="countdown-tag" style="margin-top: 4px; display: inline-block;">
                                        <?= timeRemaining($item['expired_at']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= statusBadge($item['status']) ?>
                                </td>

                                <td>
                                    <?php if ((int)($item['pending_requests_count'] ?? 0) > 0): ?>
                                        <a href="<?= BASE_URL ?>?url=request/incoming" class="badge badge-menunggu" style="text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-bell fa-shake"></i> <?= (int)$item['pending_requests_count'] ?> Menunggu
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.85rem; color: var(--text-muted);">
                                            <?= (int)($item['total_requests_count'] ?? 0) ?> Pesanan
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end;">
                                        <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$item['id'] ?>" class="btn-3d btn-3d-secondary btn-sm" title="Lihat Tampilan Publik">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($item['status'] !== 'selesai' && $item['status'] !== 'kedaluwarsa' || $isAdmin): ?>
                                            <a href="<?= BASE_URL ?>?url=donation/edit/<?= (int)$item['id'] ?>" class="btn-3d btn-3d-secondary btn-sm" title="Ubah Data Menu">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        <?php endif; ?>

                                        <form action="<?= BASE_URL ?>?url=donation/destroy/<?= (int)$item['id'] ?>" method="POST" style="display: inline;" data-confirm="Hapus menu promo '<?= e($item['judul']) ?>' secara permanen?">
                                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                            <button type="submit" class="btn-3d btn-3d-danger btn-sm" title="Hapus Menu">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Indikator Pencarian Tidak Ditemukan -->
        <div id="storeSearchEmpty" class="store-search-empty-state" style="display: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h4>Menu Promo Tidak Ditemukan</h4>
            <p>Tidak ada menu yang sesuai dengan kata kunci pencarian atau filter yang dipilih.</p>
            <button type="button" class="btn-reset-filter" id="btnResetSearch">Reset Pencarian</button>
        </div>

    <?php else: ?>
        <!-- Empty State Toko Belum Ada Menu -->
        <div class="store-empty-card">
            <div class="store-empty-icon">
                <i class="fa-solid fa-store"></i>
            </div>
            <h3 class="store-empty-title">Belum Ada Menu Promo yang Dipasang</h3>
            <p class="store-empty-desc">
                Pasang menu makanan berkualitas prima yang belum terjual dengan potongan harga hemat sebelum toko Anda tutup. Cegah food waste dan bantu sesama!
            </p>
            <a href="<?= BASE_URL ?>?url=donation/create" class="btn-store-primary" style="margin-top: 8px;">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Pasang Menu Promo Pertama</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- STYLES KHUSUS ETALASE TOKO (MOBILE-FIRST SHOPIFY / GOBIZ STYLE) -->
<style>
/* Wrapper Utama */
.store-page-wrapper {
    width: 100%;
    margin-bottom: 36px;
}

/* 1. Header Toko */
.store-dashboard-header {
    background: linear-gradient(135deg, rgba(14, 19, 29, 0.95) 0%, rgba(20, 27, 41, 0.95) 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 22px 24px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 18px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.store-profile-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1 1 320px;
}

.store-avatar-box {
    position: relative;
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
    flex-shrink: 0;
}

.store-status-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    background: #22c55e;
    border: 2px solid #0e131d;
    border-radius: 50%;
    box-shadow: 0 0 8px #22c55e;
}

.store-profile-info {
    min-width: 0;
}

.store-badge-verified {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.72rem;
    font-weight: 750;
    color: #34d399;
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.25);
    padding: 2px 8px;
    border-radius: 9999px;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.store-title {
    font-size: 1.55rem;
    font-weight: 850;
    color: #ffffff;
    margin: 0 0 3px 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.store-subtitle {
    font-size: 0.86rem;
    color: #94a3b8;
    margin: 0;
    line-height: 1.35;
    display: flex;
    align-items: center;
    gap: 6px;
}

.store-subtitle i {
    color: #10b981;
}

.store-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-store-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    font-size: 0.92rem;
    font-weight: 750;
    color: #ffffff;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35);
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-store-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(16, 185, 129, 0.5);
    color: #ffffff;
}

.btn-store-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    font-size: 0.92rem;
    font-weight: 750;
    color: #f8fafc;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
    position: relative;
}

.btn-store-secondary:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.24);
    color: #ffffff;
    transform: translateY(-2px);
}

.btn-store-secondary.has-pending {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fbbf24;
}

.pending-pill-pulse {
    background: #f59e0b;
    color: #1c1917;
    font-size: 0.72rem;
    font-weight: 850;
    padding: 2px 7px;
    border-radius: 9999px;
    animation: pulseGlow 1.8s infinite;
}

@keyframes pulseGlow {
    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.6); }
    70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

/* 2. Ringkasan KPI Toko */
.store-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.store-stat-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.2);
    transition: all 0.2s ease;
}

.store-stat-card:hover {
    border-color: rgba(255, 255, 255, 0.16);
    transform: translateY(-2px);
}

.store-stat-card.stat-card-alert {
    border-color: rgba(245, 158, 11, 0.4);
    background: linear-gradient(135deg, #0e131d 0%, rgba(245, 158, 11, 0.08) 100%);
}

.stat-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon-green  { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.stat-icon-blue   { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
.stat-icon-amber  { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
.stat-icon-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; }

.stat-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.stat-value {
    font-size: 1.45rem;
    font-weight: 850;
    color: #ffffff;
    line-height: 1.15;
}

.stat-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3. Alert Callout Pesanan Menunggu */
.store-pending-callout {
    background: linear-gradient(90deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 100%);
    border: 1px solid rgba(245, 158, 11, 0.35);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.12);
}

.callout-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #f59e0b;
    color: #1e1b18;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.callout-body {
    flex: 1;
    min-width: 0;
}

.callout-body strong {
    font-size: 0.96rem;
    color: #fef3c7;
    display: block;
    margin-bottom: 2px;
}

.callout-body p {
    font-size: 0.84rem;
    color: #cbd5e1;
    margin: 0;
}

.callout-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    background: #f59e0b;
    color: #1c1917;
    font-size: 0.85rem;
    font-weight: 800;
    border-radius: 10px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.callout-btn:hover {
    background: #d97706;
    color: #ffffff;
    transform: translateX(2px);
}

/* 4. Bar Tab & Pencarian */
.store-toolbar-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.store-tabs-wrapper {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow-x: auto;
    max-width: 100%;
    scrollbar-width: none;
}

.store-tabs-wrapper::-webkit-scrollbar {
    display: none;
}

.store-filter-tab {
    padding: 8px 14px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 9999px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.store-filter-tab:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}

.store-filter-tab.active {
    color: #06090e;
    background: #10b981;
    border-color: #10b981;
    font-weight: 800;
}

.store-search-wrap {
    position: relative;
    flex: 1 1 240px;
    max-width: 380px;
}

.store-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.88rem;
    pointer-events: none;
}

.store-search-input {
    width: 100%;
    padding: 9px 36px 9px 38px;
    font-size: 0.88rem;
    color: #f8fafc;
    background: #141b29;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
    outline: none;
    transition: all 0.2s ease;
}

.store-search-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
}

.store-search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 0.82rem;
    padding: 4px;
}

.store-search-clear:hover {
    color: #ffffff;
}

/* 5. TAMPILAN MOBILE CARDS (< 768px) */
.store-mobile-cards-list {
    display: none; /* Default desktop sembunyikan */
    flex-direction: column;
    gap: 16px;
}

.store-mobile-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: transform 0.2s ease, border-color 0.2s ease;
}

.store-mobile-card:hover {
    border-color: rgba(255, 255, 255, 0.16);
}

.mobile-card-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.mobile-badge-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.mobile-stock-pill {
    font-size: 0.76rem;
    font-weight: 750;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 4px 9px;
    border-radius: 9999px;
    white-space: nowrap;
}

.mobile-stock-pill strong {
    color: #10b981;
}

.mobile-stock-pill.stock-critical strong {
    color: #f87171;
}

.mobile-card-body {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.mobile-card-media {
    width: 82px;
    height: 82px;
    border-radius: 14px;
    overflow: hidden;
    background: #141b29;
    border: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.mobile-card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.mobile-media-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1.8rem;
    background: #141b29;
}

.mobile-card-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.mobile-card-title {
    font-size: 1.02rem;
    font-weight: 750;
    line-height: 1.35;
    margin: 0;
}

.mobile-card-title a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.mobile-card-title a:hover {
    color: #10b981;
}

.mobile-card-location {
    font-size: 0.8rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
    line-height: 1.3;
}

.mobile-card-location i {
    color: #10b981;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.mobile-card-pricing {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-top: 3px;
    flex-wrap: wrap;
}

.price-deal {
    font-size: 1.15rem;
    font-weight: 850;
    color: #10b981;
    line-height: 1;
}

.price-original {
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1;
}

.price-free-badge {
    font-size: 0.82rem;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 3px 8px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.mobile-card-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #141b29;
    border-radius: 12px;
    font-size: 0.8rem;
}

.meta-schedule-item {
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-schedule-item i {
    color: #f59e0b;
}

.meta-countdown-item {
    color: #f59e0b;
    font-weight: 750;
    display: flex;
    align-items: center;
    gap: 5px;
}

.mobile-progress-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mobile-progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.74rem;
    color: #94a3b8;
}

.mobile-progress-track {
    width: 100%;
    height: 6px;
    background: #1e293b;
    border-radius: 9999px;
    overflow: hidden;
}

.mobile-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    border-radius: 9999px;
    transition: width 0.4s ease;
}

.mobile-progress-fill.fill-low {
    background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
}

.mobile-order-alert-banner {
    background: linear-gradient(90deg, rgba(245, 158, 11, 0.18) 0%, rgba(217, 119, 6, 0.12) 100%);
    border: 1px dashed rgba(245, 158, 11, 0.45);
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: #fef3c7;
    transition: all 0.2s ease;
}

.mobile-order-alert-banner:hover {
    background: rgba(245, 158, 11, 0.26);
    border-color: #f59e0b;
}

.alert-banner-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.82rem;
}

.alert-banner-left i {
    color: #f59e0b;
    font-size: 1rem;
}

.alert-banner-left strong {
    color: #ffffff;
}

.alert-arrow-icon {
    font-size: 0.8rem;
    color: #f59e0b;
}

.mobile-orders-stat-row {
    font-size: 0.78rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Tombol Aksi Mobile (Thumb Touch Target > 40px) */
.mobile-card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-top: 4px;
    padding-top: 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.btn-card-touch {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 42px;
    padding: 0 12px;
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 10px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    box-sizing: border-box;
}

.btn-touch-view {
    background: rgba(255, 255, 255, 0.07);
    color: #e2e8f0;
    border-color: rgba(255, 255, 255, 0.12);
}

.btn-touch-view:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff;
}

.btn-touch-edit {
    background: rgba(59, 130, 246, 0.12);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.25);
}

.btn-touch-edit:hover {
    background: rgba(59, 130, 246, 0.22);
    color: #ffffff;
}

.btn-touch-delete {
    background: rgba(239, 68, 68, 0.12);
    color: #fca5a5;
    border-color: rgba(239, 68, 68, 0.25);
}

.btn-touch-delete:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #ffffff;
}

.mobile-delete-form {
    margin: 0;
    padding: 0;
    width: 100%;
}

/* 6. TAMPILAN DESKTOP TABLE (>= 768px) */
.store-desktop-table-card {
    display: block;
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

.store-data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 820px;
}

.table-thumb-img {
    width: 65px;
    height: 50px;
    object-fit: cover;
    border-radius: 10px;
    display: block;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.table-thumb-fallback {
    width: 65px;
    height: 50px;
    background: #141b29;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.table-item-title {
    font-size: 0.95rem;
    display: block;
    line-height: 1.35;
    margin-bottom: 3px;
}

.table-item-title a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.15s ease;
}

.table-item-title a:hover {
    color: #10b981;
}

.table-item-location {
    font-size: 0.8rem;
    color: #94a3b8;
    display: block;
}

.table-item-location i {
    color: #10b981;
}

.table-deal-price {
    color: #10b981;
    font-size: 0.98rem;
    display: block;
}

.table-original-price {
    display: block;
    font-size: 0.76rem;
    color: #64748b;
}

.table-stock-num {
    color: #ffffff;
    font-size: 1rem;
}

.table-stock-satuan {
    color: #94a3b8;
    font-size: 0.82rem;
}

.table-closing-time {
    font-size: 0.85rem;
    font-weight: 700;
    color: #fbbf24;
}

/* 7. Empty State */
.store-empty-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 56px 24px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
}

.store-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 24px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-size: 2.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.store-empty-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
}

.store-empty-desc {
    color: #94a3b8;
    font-size: 0.92rem;
    max-width: 480px;
    margin: 0 auto 20px;
    line-height: 1.55;
}

.store-search-empty-state {
    background: #0e131d;
    border: 1px dashed rgba(255, 255, 255, 0.12);
    border-radius: 18px;
    padding: 42px 20px;
    text-align: center;
    margin-top: 10px;
}

.store-search-empty-state i {
    font-size: 2.2rem;
    color: #64748b;
    margin-bottom: 12px;
}

.store-search-empty-state h4 {
    font-size: 1.1rem;
    font-weight: 750;
    color: #ffffff;
    margin-bottom: 6px;
}

.store-search-empty-state p {
    font-size: 0.88rem;
    color: #94a3b8;
    margin-bottom: 16px;
}

.btn-reset-filter {
    padding: 8px 18px;
    font-size: 0.84rem;
    font-weight: 750;
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-reset-filter:hover {
    background: #10b981;
    color: #06090e;
    border-color: #10b981;
}

/* ============================================================
   RESPONSIVE BREAKPOINTS: MOBILE SPECIFIC ADJUSTMENTS (< 768px)
   ============================================================ */
@media (max-width: 767px) {
    .store-dashboard-header {
        padding: 16px;
        border-radius: 16px;
        gap: 14px;
        flex-direction: column;
        align-items: stretch;
    }

    .store-profile-wrap {
        flex: none;
        width: 100%;
        gap: 12px;
    }

    .store-avatar-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        font-size: 1.35rem;
    }

    .store-title {
        font-size: 1.3rem;
    }

    .store-subtitle {
        font-size: 0.8rem;
    }

    .store-header-actions {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .btn-store-primary,
    .btn-store-secondary {
        width: 100% !important;
        justify-content: center;
        padding: 12px 14px;
        font-size: 0.9rem;
    }

    /* Stats Grid jadi 2 Kolom di Mobile */
    .store-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .store-stat-card {
        padding: 12px 14px;
        gap: 10px;
        border-radius: 14px;
    }

    .stat-icon-wrap {
        width: 38px;
        height: 38px;
        font-size: 1.05rem;
        border-radius: 10px;
    }

    .stat-value {
        font-size: 1.25rem;
    }

    .stat-label {
        font-size: 0.72rem;
    }

    .store-pending-callout {
        flex-direction: column;
        align-items: stretch;
        padding: 14px;
        gap: 12px;
    }

    .callout-btn {
        width: 100%;
        justify-content: center;
    }

    .store-toolbar-card {
        padding: 12px;
        gap: 10px;
        flex-direction: column;
        align-items: stretch;
    }

    .store-tabs-wrapper {
        width: 100%;
        justify-content: flex-start;
    }

    .store-search-wrap {
        max-width: 100%;
        width: 100%;
    }

    /* TUKAR DISPLAY: Sembunyikan Tabel, Tampilkan Kartu Mobile */
    .store-desktop-table-card {
        display: none !important;
    }

    .store-mobile-cards-list {
        display: flex !important;
    }
}

/* Penyesuaian Ekstra untuk Layar Sangat Kecil (< 380px) */
@media (max-width: 380px) {
    .mobile-card-actions {
        grid-template-columns: 1fr;
    }
    
    .mobile-card-body {
        gap: 10px;
    }

    .mobile-card-media {
        width: 72px;
        height: 72px;
    }
}
</style>

<!-- JAVASCRIPT: INSTANT CLIENT-SIDE FILTER & SEARCH -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterTabs   = document.querySelectorAll('.store-filter-tab');
    const searchInput  = document.getElementById('storeSearchInput');
    const clearBtn     = document.getElementById('storeSearchClear');
    const resetBtn     = document.getElementById('btnResetSearch');
    const mobileCards  = document.querySelectorAll('.store-mobile-card');
    const tableRows    = document.querySelectorAll('#storeDesktopTable tbody tr');
    const emptyState   = document.getElementById('storeSearchEmpty');
    const desktopTable = document.querySelector('.store-desktop-table-card');

    let activeFilter = 'all';

    function applyFilterAndSearch() {
        const query = (searchInput.value || '').trim().toLowerCase();
        let visibleCount = 0;

        // Toggle tombol clear
        if (clearBtn) {
            clearBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        // Filter Mobile Cards
        mobileCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const title    = card.getAttribute('data-title') || '';
            const matchFilter = (activeFilter === 'all') || (category === activeFilter);
            const matchSearch = query === '' || title.includes(query);

            if (matchFilter && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Filter Desktop Table Rows
        tableRows.forEach(row => {
            const category = row.getAttribute('data-category');
            const title    = row.getAttribute('data-title') || '';
            const matchFilter = (activeFilter === 'all') || (category === activeFilter);
            const matchSearch = query === '' || title.includes(query);

            if (matchFilter && matchSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Tampilkan State Kosong jika tidak ada hasil
        if (emptyState) {
            if (visibleCount === 0 && (mobileCards.length > 0 || tableRows.length > 0)) {
                emptyState.style.display = 'block';
                if (desktopTable) desktopTable.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                if (desktopTable && window.innerWidth >= 768) {
                    desktopTable.style.display = 'block';
                }
            }
        }
    }

    // Tab Clicks
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeFilter = tab.getAttribute('data-filter') || 'all';
            applyFilterAndSearch();
        });
    });

    // Search Input
    if (searchInput) {
        searchInput.addEventListener('input', applyFilterAndSearch);
    }

    // Clear Button
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
            applyFilterAndSearch();
        });
    }

    // Reset Button di Empty State
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            activeFilter = 'all';
            filterTabs.forEach(t => {
                if (t.getAttribute('data-filter') === 'all') t.classList.add('active');
                else t.classList.remove('active');
            });
            applyFilterAndSearch();
        });
    }
});
</script>
