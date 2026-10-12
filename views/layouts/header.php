<?php
/**
 * Header & Navbar Layout - Berbagi Rasa
 * Anti-Slop Edition (Clean, Human, Lightweight & Fast)
 */
$currentUser = currentUser();
$userRole = $currentUser['role'] ?? '';
$currentUrl = $_GET['url'] ?? 'donation/index';
$currentUrl = trim($currentUrl, '/');
$isHomePage = ($currentUrl === 'donation/index' || $currentUrl === 'donation' || $currentUrl === '' || str_starts_with($currentUrl, 'donation/index'));
$isActorPage = (
    str_starts_with($currentUrl, 'donation/mine') ||
    str_starts_with($currentUrl, 'donation/create') ||
    str_starts_with($currentUrl, 'donation/edit') ||
    str_starts_with($currentUrl, 'request/') ||
    str_starts_with($currentUrl, 'history/')
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Berbagi Rasa — Makanan Berkualitas Murah Jelang Tutup Toko') ?></title>
    
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Berbagi Rasa Main Stylesheet (Cache-Busted) -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/style.css?v=<?= filemtime(ROOT_DIR . '/assets/css/style.css') ?>">
</head>
<body class="cinematic-dark-body <?= ($isHomePage) ? 'has-cinematic-hero' : '' ?>">

    <!-- Header Navigation Bar -->
    <header class="header-nav">
        <div class="nav-container">
            <div class="nav-inner">
                <!-- Brand & Nav Group -->
                <div class="nav-left-group">
                    <a href="<?= BASE_URL ?>?url=donation/index" class="brand-logo" title="Berbagi Rasa - Halaman Utama">
                        <div class="brand-icon">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <span class="brand-title">Berbagi Rasa<span class="brand-dot">.</span></span>
                    </a>

                    <?php if ($isActorPage && $currentUser): ?>
                        <!-- Actor Portal Workspace Badge -->
                        <?php if ($userRole === 'donatur'): ?>
                            <div class="badge-actor-portal store">
                                <span class="badge-portal-dot"></span>
                                <i class="fa-solid fa-store"></i>
                                <span>Mitra Toko</span>
                            </div>
                        <?php elseif ($userRole === 'penerima'): ?>
                            <div class="badge-actor-portal saver">
                                <span class="badge-portal-dot"></span>
                                <i class="fa-solid fa-shield-heart"></i>
                                <span>Penyelamat</span>
                            </div>
                        <?php else: ?>
                            <div class="badge-actor-portal admin">
                                <span class="badge-portal-dot"></span>
                                <i class="fa-solid fa-user-shield"></i>
                                <span>Portal Admin</span>
                            </div>
                        <?php endif; ?>

                        <!-- Dedicated Actor Navigation (Desktop Only) -->
                        <ul class="nav-links desktop-nav-links">
                            <?php if ($userRole === 'donatur'): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=donation/mine" class="nav-link <?= str_starts_with($currentUrl, 'donation/mine') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-store" style="color: var(--primary);"></i> Etalase Toko
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=request/incoming" class="nav-link <?= str_starts_with($currentUrl, 'request/incoming') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Pesanan Masuk
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=donation/create" class="nav-link <?= (str_starts_with($currentUrl, 'donation/create') || str_starts_with($currentUrl, 'donation/edit')) ? 'active' : '' ?>">
                                        <i class="fa-solid fa-circle-plus" style="color: var(--primary);"></i> Pasang Menu
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=history/index" class="nav-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Riwayat
                                    </a>
                                </li>
                            <?php elseif ($userRole === 'penerima'): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=request/mine" class="nav-link <?= str_starts_with($currentUrl, 'request/mine') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i> Pesanan Saya
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=history/index" class="nav-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Riwayat Tebusan
                                    </a>
                                </li>
                            <?php else: // Admin ?>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=donation/mine" class="nav-link <?= str_starts_with($currentUrl, 'donation/mine') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-list-check" style="color: #a855f7;"></i> Kelola Menu
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=request/incoming" class="nav-link <?= str_starts_with($currentUrl, 'request/incoming') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-receipt" style="color: #a855f7;"></i> Semua Pesanan
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=donation/create" class="nav-link <?= str_starts_with($currentUrl, 'donation/create') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-circle-plus" style="color: #a855f7;"></i> Pasang Menu
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>?url=history/index" class="nav-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                        <i class="fa-solid fa-clock-rotate-left" style="color: #a855f7;"></i> Riwayat Sistem
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                    <?php else: ?>
                        <!-- Public Landing Navigation -->
                        <ul class="nav-links desktop-nav-links">
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/index" class="nav-link <?= ($isHomePage) ? 'active' : '' ?>">
                                    Beranda
                                </a>
                            </li>
                            <li>
                                <a href="<?= $isHomePage ? '#fitur' : BASE_URL . '?url=donation/index#fitur' ?>" class="nav-link">
                                    Fitur Penting
                                </a>
                            </li>
                            <li>
                                <a href="<?= $isHomePage ? '#cara-kerja' : BASE_URL . '?url=donation/index#cara-kerja' ?>" class="nav-link">
                                    Cara Kerja
                                </a>
                            </li>
                            <li>
                                <a href="<?= $isHomePage ? '#tentang' : BASE_URL . '?url=donation/index#tentang' ?>" class="nav-link">
                                    Cerita Gerakan
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/catalog" class="nav-link <?= ($currentUrl === 'donation/catalog') ? 'active' : '' ?>">
                                    <i class="fa-solid fa-utensils" style="color: #10b981;"></i> Katalog Makanan
                                </a>
                            </li>
                            <li>
                                <a href="<?= $isHomePage ? '#mitra' : BASE_URL . '?url=donation/index#mitra' ?>" class="nav-link">
                                    Untuk Pemilik Usaha
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Right Side: Actions & Hamburger -->
                <div class="nav-right-wrap">
                    <div class="nav-actions desktop-nav-actions">
                        <?php if ($currentUser): ?>
                            <?php if ($isActorPage): ?>
                                <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-portal-public-web" title="Lihat Website & Katalog Publik">
                                    <i class="fa-solid fa-utensils"></i>
                                    <span class="btn-portal-text">Katalog Makanan</span>
                                </a>
                            <?php else: ?>
                                <?php if ($userRole === 'donatur'): ?>
                                    <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-pill-emerald">
                                        <i class="fa-solid fa-store"></i> Dashboard Toko
                                    </a>
                                <?php elseif ($userRole === 'penerima'): ?>
                                    <a href="<?= BASE_URL ?>?url=request/mine" class="btn-pill-emerald">
                                        <i class="fa-solid fa-bag-shopping"></i> Pesanan Saya
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-pill-emerald">
                                        <i class="fa-solid fa-gauge-high"></i> Dashboard Admin
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="user-badge-pill" title="<?= e($currentUser['nama']) ?>">
                                <div class="user-avatar-circle">
                                    <?= strtoupper(substr($currentUser['nama'], 0, 1)) ?>
                                </div>
                                <div class="user-info-text">
                                    <strong class="user-name-label"><?= e($currentUser['nama']) ?></strong>
                                    <span class="user-role-label">
                                        <?php 
                                            if ($currentUser['role'] === 'donatur') echo 'Mitra Toko';
                                            elseif ($currentUser['role'] === 'penerima') echo 'Penyelamat';
                                            else echo e($currentUser['role']);
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <a href="<?= BASE_URL ?>?url=auth/logout" class="btn-nav-ghost" data-confirm="Apakah Anda yakin ingin keluar (logout)?">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>?url=auth/login" class="nav-link-login">
                                Masuk
                            </a>
                            <a href="<?= BASE_URL ?>?url=auth/register" class="btn-pill-white">
                                Mulai Sekarang
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Hamburger Button for Mobile -->
                    <button type="button" class="btn-hamburger-cinematic" id="hamburgerBtn" aria-label="Buka Menu Navigasi" aria-expanded="false" aria-controls="mobileNavDrawer">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer Backdrop -->
    <div class="mobile-nav-backdrop" id="mobileNavBackdrop"></div>

    <!-- Mobile Navigation Drawer -->
    <aside class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
        <div class="drawer-header">
            <a href="<?= BASE_URL ?>?url=donation/index" class="drawer-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-bowl-food"></i>
                </div>
                <span class="brand-title">Berbagi Rasa<span class="brand-dot">.</span></span>
            </a>
            <?php if ($isActorPage && $currentUser): ?>
                <?php if ($userRole === 'donatur'): ?>
                    <span class="badge-actor-portal store" style="font-size: 0.68rem; padding: 3px 8px;">Mitra Toko</span>
                <?php elseif ($userRole === 'penerima'): ?>
                    <span class="badge-actor-portal saver" style="font-size: 0.68rem; padding: 3px 8px;">Penyelamat</span>
                <?php endif; ?>
            <?php endif; ?>
            <button type="button" class="btn-drawer-close" id="drawerCloseBtn" aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="drawer-body">
            <?php if ($currentUser): ?>
                <div class="drawer-user-card">
                    <div class="user-avatar-circle" style="width: 44px; height: 44px; font-size: 1.15rem; flex-shrink: 0;">
                        <?= strtoupper(substr($currentUser['nama'], 0, 1)) ?>
                    </div>
                    <div class="user-info-text" style="overflow: hidden;">
                        <strong class="user-name-label" style="font-size: 0.96rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?= e($currentUser['nama']) ?></strong>
                        <span class="user-role-label" style="font-size: 0.72rem; margin-top: 3px; display: block;">
                            <?php 
                                if ($currentUser['role'] === 'donatur') echo '<i class="fa-solid fa-store" style="color: var(--primary);"></i> Mitra Toko / UMKM';
                                elseif ($currentUser['role'] === 'penerima') echo '<i class="fa-solid fa-heart" style="color: #f87171;"></i> Penyelamat Pangan';
                                else echo '<i class="fa-solid fa-shield-halved" style="color: #a855f7;"></i> Administrator Sistem';
                            ?>
                        </span>
                    </div>
                </div>

                <?php if (!$isActorPage): ?>
                    <?php if ($userRole === 'donatur'): ?>
                        <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-pill-emerald" style="justify-content: center; width: 100%; padding: 11px; margin-bottom: 12px;">
                            <i class="fa-solid fa-store"></i> Buka Dashboard Toko
                        </a>
                    <?php elseif ($userRole === 'penerima'): ?>
                        <a href="<?= BASE_URL ?>?url=request/mine" class="btn-pill-emerald" style="justify-content: center; width: 100%; padding: 11px; margin-bottom: 12px;">
                            <i class="fa-solid fa-bag-shopping"></i> Buka Pesanan Saya
                        </a>
                    <?php elseif ($userRole === 'admin'): ?>
                        <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-pill-emerald" style="justify-content: center; width: 100%; padding: 11px; margin-bottom: 12px; background: linear-gradient(135deg, #7c3aed, #9333ea);">
                            <i class="fa-solid fa-shield-halved"></i> Buka Panel Admin
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <nav class="drawer-nav">
                <?php if ($isActorPage && $currentUser): ?>
                    <div class="drawer-section-label">
                        <i class="fa-solid fa-sliders"></i> Menu Operasional
                    </div>
                    <ul class="drawer-nav-list">
                        <?php if ($userRole === 'donatur'): ?>
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/mine" class="drawer-link <?= str_starts_with($currentUrl, 'donation/mine') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-store"></i></span>
                                    <span class="drawer-label">Etalase Toko Saya</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=request/incoming" class="drawer-link <?= str_starts_with($currentUrl, 'request/incoming') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-receipt"></i></span>
                                    <span class="drawer-label">Pesanan Masuk</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/create" class="drawer-link <?= (str_starts_with($currentUrl, 'donation/create') || str_starts_with($currentUrl, 'donation/edit')) ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-circle-plus"></i></span>
                                    <span class="drawer-label">+ Pasang Menu Promo</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=history/index" class="drawer-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                    <span class="drawer-label">Riwayat Transaksi</span>
                                </a>
                            </li>
                        <?php elseif ($userRole === 'penerima'): ?>
                            <li>
                                <a href="<?= BASE_URL ?>?url=request/mine" class="drawer-link <?= str_starts_with($currentUrl, 'request/mine') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-bag-shopping"></i></span>
                                    <span class="drawer-label">Pesanan Saya</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=history/index" class="drawer-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                    <span class="drawer-label">Riwayat Tebusan</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/mine" class="drawer-link <?= str_starts_with($currentUrl, 'donation/mine') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap" style="color: #a855f7;"><i class="fa-solid fa-list-check"></i></span>
                                    <span class="drawer-label">Kelola Semua Menu</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=request/incoming" class="drawer-link <?= str_starts_with($currentUrl, 'request/incoming') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap" style="color: #a855f7;"><i class="fa-solid fa-receipt"></i></span>
                                    <span class="drawer-label">Semua Pesanan Toko</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=donation/create" class="drawer-link <?= (str_starts_with($currentUrl, 'donation/create') || str_starts_with($currentUrl, 'donation/edit')) ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap" style="color: #a855f7;"><i class="fa-solid fa-circle-plus"></i></span>
                                    <span class="drawer-label">+ Pasang Menu Baru</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?url=history/index" class="drawer-link <?= str_starts_with($currentUrl, 'history/') ? 'active' : '' ?>">
                                    <span class="drawer-icon-wrap" style="color: #a855f7;"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                    <span class="drawer-label">Riwayat Sistem</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="drawer-section-label" style="margin-top: 10px;">
                        <i class="fa-solid fa-compass"></i> Akses Publik
                    </div>
                    <ul class="drawer-nav-list">
                        <li>
                            <a href="<?= BASE_URL ?>?url=donation/catalog" class="drawer-link">
                                <span class="drawer-icon-wrap"><i class="fa-solid fa-utensils"></i></span>
                                <span class="drawer-label">Katalog Makanan Siap Tebus</span>
                            </a>
                        </li>
                    </ul>

                <?php else: ?>
                    <ul class="drawer-nav-list">
                        <li>
                            <a href="<?= BASE_URL ?>?url=donation/index" class="drawer-link <?= ($isHomePage) ? 'active' : '' ?>">
                                <span class="drawer-icon-wrap"><i class="fa-solid fa-house"></i></span>
                                <span class="drawer-label">Beranda</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $isHomePage ? '#fitur' : BASE_URL . '?url=donation/index#fitur' ?>" class="drawer-link">
                                <span class="drawer-icon-wrap" style="color: #f59e0b;"><i class="fa-solid fa-sparkles"></i></span>
                                <span class="drawer-label">Fitur Penting</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $isHomePage ? '#cara-kerja' : BASE_URL . '?url=donation/index#cara-kerja' ?>" class="drawer-link">
                                <span class="drawer-icon-wrap"><i class="fa-solid fa-list-check"></i></span>
                                <span class="drawer-label">Cara Kerja</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $isHomePage ? '#tentang' : BASE_URL . '?url=donation/index#tentang' ?>" class="drawer-link">
                                <span class="drawer-icon-wrap"><i class="fa-solid fa-heart"></i></span>
                                <span class="drawer-label">Cerita Gerakan</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>?url=donation/catalog" class="drawer-link <?= ($currentUrl === 'donation/catalog') ? 'active' : '' ?>">
                                <span class="drawer-icon-wrap" style="color: #10b981;"><i class="fa-solid fa-utensils"></i></span>
                                <span class="drawer-label">Katalog Makanan Siap Tebus</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $isHomePage ? '#mitra' : BASE_URL . '?url=donation/index#mitra' ?>" class="drawer-link">
                                <span class="drawer-icon-wrap"><i class="fa-solid fa-store"></i></span>
                                <span class="drawer-label">Untuk Pemilik Usaha</span>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>

        <div class="drawer-footer">
            <?php if ($currentUser): ?>
                <?php if ($isActorPage && $userRole === 'donatur'): ?>
                    <a href="<?= BASE_URL ?>?url=donation/create" class="btn-store-primary" style="width: 100%; margin-bottom: 8px;">
                        <i class="fa-solid fa-plus-circle"></i> + Pasang Menu Promo
                    </a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>?url=auth/logout" class="btn-drawer-logout" data-confirm="Apakah Anda yakin ingin keluar (logout)?">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar (Logout)
                </a>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                    <a href="<?= BASE_URL ?>?url=auth/login" class="btn-drawer-login">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk (Login)
                    </a>
                    <a href="<?= BASE_URL ?>?url=auth/register" class="btn-store-primary" style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i> Mulai Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content <?= ($isHomePage) ? 'has-cinematic-hero' : '' ?>">
        <!-- Flash Alert Notification -->
        <?php $flash = flash(); if ($flash): ?>
            <div class="container" style="padding-top: 16px; margin-bottom: -10px;">
                <div class="alert-box alert-<?= e($flash['type']) ?>">
                    <div class="alert-icon">
                        <?php if ($flash['type'] === 'success'): ?>
                            <i class="fa-solid fa-circle-check"></i>
                        <?php elseif ($flash['type'] === 'error'): ?>
                            <i class="fa-solid fa-circle-exclamation"></i>
                        <?php elseif ($flash['type'] === 'warning'): ?>
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-circle-info"></i>
                        <?php endif; ?>
                    </div>
                    <div class="alert-content">
                        <strong><?= ucfirst(e($flash['type'])) ?>:</strong> <?= e($flash['message']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$isHomePage): ?>
            <div class="container">
        <?php endif; ?>
