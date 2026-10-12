<?php
/**
 * View Pesanan Masuk (Untuk Mitra Toko/UMKM) - Berbagi Rasa
 * Mobile-First & Responsive Dual Layout (Mobile Order Cards + Desktop Table)
 */
$requestsList = $requests ?? [];
$isAdmin = ($isAdmin ?? false) || (($_SESSION['role'] ?? '') === 'admin');
$totalOrders  = count($requestsList);
$countMenunggu  = count(array_filter($requestsList, fn($r) => $r['status'] === 'menunggu'));
$countDisetujui = count(array_filter($requestsList, fn($r) => $r['status'] === 'disetujui'));
$countSelesai   = count(array_filter($requestsList, fn($r) => in_array($r['status'], ['diambil', 'selesai'])));
?>

<div class="incoming-page-wrapper">
    <!-- Header Halaman Pesanan Masuk -->
    <div class="incoming-header-bar">
        <div>
            <div class="incoming-back-wrap">
                <a href="<?= BASE_URL ?>?url=donation/mine" class="incoming-back-link">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke <?= $isAdmin ? 'Kelola Semua Menu' : 'Etalase Toko' ?>
                </a>
            </div>
            <h1 class="incoming-title">
                <i class="fa-solid <?= $isAdmin ? 'fa-shield-halved' : 'fa-receipt' ?> text-emerald"></i> <?= $isAdmin ? 'Semua Pesanan Masuk (Admin)' : 'Pesanan Masuk Toko' ?>
            </h1>
            <p class="incoming-subtitle">
                <?= $isAdmin ? 'Pantau dan kelola seluruh transaksi dan pesanan makanan hemat dari semua mitra toko' : 'Konfirmasi pesanan menu hemat dan terbitkan kode ambil kasir untuk pembeli sebelum jam tutup toko' ?>
            </p>
        </div>

        <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-store-secondary">
            <i class="fa-solid <?= $isAdmin ? 'fa-list-check' : 'fa-store' ?>"></i> <?= $isAdmin ? 'Kelola Semua Menu' : 'Kelola Etalase' ?>
        </a>
    </div>

    <!-- KPI Ringkasan Pesanan -->
    <div class="incoming-kpi-grid">
        <div class="incoming-kpi-card <?= ($countMenunggu > 0) ? 'kpi-alert' : '' ?>">
            <div class="kpi-icon-box kpi-amber">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countMenunggu ?></span>
                <span class="kpi-text">Menunggu Persetujuan</span>
            </div>
        </div>

        <div class="incoming-kpi-card">
            <div class="kpi-icon-box kpi-blue">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countDisetujui ?></span>
                <span class="kpi-text">Siap Diambil Kasir</span>
            </div>
        </div>

        <div class="incoming-kpi-card">
            <div class="kpi-icon-box kpi-green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countSelesai ?></span>
                <span class="kpi-text">Selesai Ditebus</span>
            </div>
        </div>

        <div class="incoming-kpi-card">
            <div class="kpi-icon-box kpi-purple">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $totalOrders ?></span>
                <span class="kpi-text">Total Pesanan</span>
            </div>
        </div>
    </div>

    <!-- Filter Tab Bar -->
    <div class="incoming-filter-bar">
        <div class="incoming-filter-tabs">
            <button type="button" class="order-filter-tab active" data-status="all">
                Semua (<?= $totalOrders ?>)
            </button>
            <button type="button" class="order-filter-tab" data-status="menunggu">
                Menunggu (<?= $countMenunggu ?>)
            </button>
            <button type="button" class="order-filter-tab" data-status="disetujui">
                Disetujui (<?= $countDisetujui ?>)
            </button>
            <button type="button" class="order-filter-tab" data-status="selesai">
                Selesai (<?= $countSelesai ?>)
            </button>
        </div>
    </div>

    <!-- Konten Pesanan -->
    <?php if (!empty($requestsList)): ?>

        <!-- A. TAMPILAN KHUSUS MOBILE (< 768px): KARTU PESANAN TOUCH-FRIENDLY -->
        <div class="mobile-orders-list" id="mobileOrdersList">
            <?php foreach ($requestsList as $req): ?>
                <?php 
                    $hargaDiskon = (int)($req['harga_diskon'] ?? 0);
                    $jumlahMinta = (int)$req['jumlah_minta'];
                    $totalBayar  = $hargaDiskon * $jumlahMinta;
                    $isFree      = (($req['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
                    $statusGroup = ($req['status'] === 'diambil' || $req['status'] === 'selesai') ? 'selesai' : $req['status'];
                ?>
                <div class="mobile-order-card" data-status="<?= e($statusGroup) ?>">
                    <!-- Header Kartu: Status & Tanggal -->
                    <div class="mobile-order-header">
                        <div class="order-header-badges">
                            <?= requestBadge($req['status']) ?>
                            <?php if ($isFree): ?>
                                <span class="badge-free-mini"><i class="fa-solid fa-gift"></i> Gratis</span>
                            <?php endif; ?>
                        </div>
                        <span class="order-header-time">
                            <i class="fa-regular fa-clock"></i> <?= formatDate($req['created_at']) ?>
                        </span>
                    </div>

                    <!-- Informasi Pembeli -->
                    <div class="order-buyer-box">
                        <div class="order-buyer-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="order-buyer-details">
                            <strong class="order-buyer-name"><?= e($req['nama_penerima']) ?></strong>
                            <?php if (!empty($req['telepon_penerima'])): ?>
                                <?php 
                                    $waNumber = preg_replace('/[^0-9]/', '', $req['telepon_penerima']);
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62' . substr($waNumber, 1);
                                    }
                                ?>
                                <a href="https://wa.me/<?= $waNumber ?>" target="_blank" rel="noopener" class="order-buyer-wa">
                                    <i class="fa-brands fa-whatsapp"></i> Hubungi <?= e($req['telepon_penerima']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Menu & Pembayaran -->
                    <div class="order-food-box">
                        <?php if ($isAdmin && !empty($req['nama_donatur'])): ?>
                            <div style="font-size: 0.76rem; font-weight: 750; color: #c084fc; margin-bottom: 4px; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-store"></i> Toko: <?= e($req['nama_donatur']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="order-food-title">
                            <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>">
                                <?= e($req['judul_donasi']) ?>
                            </a>
                        </div>
                        <div class="order-food-porsi">
                            <span>Jumlah: <strong><?= $jumlahMinta ?> <?= e($req['satuan']) ?></strong></span>
                            <span class="order-food-total">
                                Total Tebus: <strong><?= ($isFree) ? 'Rp 0 (Gratis Peduli)' : formatRupiah($totalBayar) ?></strong>
                            </span>
                        </div>
                        <div class="order-food-stock-info">
                            Sisa stok toko saat ini: <strong><?= (int)$req['sisa_donasi'] ?> <?= e($req['satuan']) ?></strong>
                        </div>
                    </div>

                    <!-- Kode Ambil Kasir (Jika sudah disetujui) -->
                    <?php if (!empty($req['claim_token'])): ?>
                        <div class="order-claim-token-box">
                            <div class="token-label">KODE AMBIL KASIR PEMBELI:</div>
                            <div class="token-value">
                                <i class="fa-solid fa-qrcode"></i> <?= e($req['claim_token']) ?>
                            </div>
                            <small class="token-hint">Cocokkan kode ini saat pembeli datang ke kasir toko</small>
                        </div>
                    <?php endif; ?>

                    <!-- Catatan Pemesan (Jika ada) -->
                    <?php if (!empty($req['catatan'])): ?>
                        <div class="order-note-box">
                            <i class="fa-regular fa-comment-dots"></i>
                            <span>"<?= e($req['catatan']) ?>"</span>
                        </div>
                    <?php endif; ?>

                    <!-- Tombol Keputusan & Aksi Kasir -->
                    <div class="order-actions-wrap">
                        <?php if ($req['status'] === 'menunggu'): ?>
                            <form action="<?= BASE_URL ?>?url=request/approve/<?= (int)$req['id'] ?>" method="POST" style="flex: 1;" data-confirm="Setujui pesanan <?= $jumlahMinta ?> <?= e($req['satuan']) ?> untuk <?= e($req['nama_penerima']) ?>?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-touch-approve">
                                    <i class="fa-solid fa-check"></i> Setujui Pesanan
                                </button>
                            </form>

                            <form action="<?= BASE_URL ?>?url=request/reject/<?= (int)$req['id'] ?>" method="POST" style="flex: 1;" data-confirm="Tolak pesanan makanan ini?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-touch-reject">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                            </form>

                        <?php elseif ($req['status'] === 'disetujui'): ?>
                            <form action="<?= BASE_URL ?>?url=request/complete/<?= (int)$req['id'] ?>" method="POST" style="width: 100%;" data-confirm="Konfirmasi pembeli sudah mengambil makanan di kasir dan menyelesaikan tebusan?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-touch-complete">
                                    <i class="fa-solid fa-hand-holding-heart"></i> Konfirmasi Selesai Diambil Kasir
                                </button>
                            </form>

                        <?php else: ?>
                            <div class="order-processed-tag">
                                <i class="fa-solid fa-circle-check"></i> Pesanan Selesai Diproses
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- B. TAMPILAN KHUSUS DESKTOP (>= 768px): TABEL LENGKAP -->
        <div class="incoming-desktop-table-card">
            <div class="table-responsive">
                <table class="data-table-3d" id="incomingDesktopTable">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;">Pemesan</th>
                            <th style="min-width: 190px;">Menu Makanan</th>
                            <th style="min-width: 140px;">Porsi & Tebusan</th>
                            <th style="min-width: 130px;">Kode Ambil</th>
                            <th style="min-width: 160px;">Catatan</th>
                            <th style="min-width: 120px;">Waktu</th>
                            <th style="min-width: 110px;">Status</th>
                            <th style="min-width: 160px; text-align: right;">Aksi Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requestsList as $req): ?>
                            <?php 
                                $hargaDiskon = (int)($req['harga_diskon'] ?? 0);
                                $jumlahMinta = (int)$req['jumlah_minta'];
                                $totalBayar  = $hargaDiskon * $jumlahMinta;
                                $isFree      = (($req['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
                                $statusGroup = ($req['status'] === 'diambil' || $req['status'] === 'selesai') ? 'selesai' : $req['status'];
                            ?>
                            <tr data-status="<?= e($statusGroup) ?>">
                                <td>
                                    <strong style="display: block; font-size: 0.95rem; color: #ffffff;">
                                        <?= e($req['nama_penerima']) ?>
                                    </strong>
                                    <?php if (!empty($req['telepon_penerima'])): ?>
                                        <span style="font-size: 0.8rem; color: #94a3b8;">
                                            <i class="fa-brands fa-whatsapp text-emerald"></i> <?= e($req['telepon_penerima']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($isAdmin && !empty($req['nama_donatur'])): ?>
                                        <div style="font-size: 0.76rem; font-weight: 750; color: #c084fc; margin-bottom: 3px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-store"></i> <?= e($req['nama_donatur']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <strong style="font-size: 0.95rem; display: block;">
                                        <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>" style="color: #ffffff; text-decoration: none;">
                                            <?= e($req['judul_donasi']) ?>
                                        </a>
                                    </strong>
                                    <span style="font-size: 0.8rem; color: #94a3b8;">
                                        Sisa stok: <strong><?= (int)$req['sisa_donasi'] ?> <?= e($req['satuan']) ?></strong>
                                    </span>
                                </td>

                                <td>
                                    <strong style="font-size: 1rem; color: #ffffff;">
                                        <?= $jumlahMinta ?> <?= e($req['satuan']) ?>
                                    </strong> 
                                    <div style="font-size: 0.86rem; font-weight: 750; color: #10b981; margin-top: 2px;">
                                        <?= ($isFree) ? 'Gratis Peduli (Rp 0)' : formatRupiah($totalBayar) ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if (!empty($req['claim_token'])): ?>
                                        <span style="font-family: monospace; font-size: 0.92rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.12); padding: 4px 8px; border-radius: 8px; border: 1px dashed rgba(16, 185, 129, 0.4); display: inline-block;">
                                            <?= e($req['claim_token']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="font-size: 0.8rem; color: #64748b;">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span style="font-size: 0.86rem; color: #cbd5e1;">
                                        <?= e($req['catatan'] ?: '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <span style="font-size: 0.82rem; color: #94a3b8;">
                                        <?= formatDate($req['created_at']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= requestBadge($req['status']) ?>
                                </td>

                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end;">
                                        <?php if ($req['status'] === 'menunggu'): ?>
                                            <form action="<?= BASE_URL ?>?url=request/approve/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Setujui pesanan <?= $jumlahMinta ?> <?= e($req['satuan']) ?> untuk <?= e($req['nama_penerima']) ?>?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-primary btn-sm">
                                                    <i class="fa-solid fa-check"></i> Setujui
                                                </button>
                                            </form>

                                            <form action="<?= BASE_URL ?>?url=request/reject/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Tolak pesanan makanan ini?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-danger btn-sm">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            </form>

                                        <?php elseif ($req['status'] === 'disetujui'): ?>
                                            <form action="<?= BASE_URL ?>?url=request/complete/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Konfirmasi bahwa pembeli sudah mengambil makanan di kasir dan menyelesaikan tebusan?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-accent btn-sm">
                                                    <i class="fa-solid fa-hand-holding-heart"></i> Konfirmasi Selesai
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <span style="font-size: 0.82rem; color: #64748b; font-style: italic;">
                                                Selesai
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <div class="incoming-empty-card">
            <div class="empty-icon-circle">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <h3>Belum Ada Pesanan Masuk</h3>
            <p>Saat ada pembeli atau anak kos yang memesan menu hemat Anda, daftarnya akan muncul otomatis di sini.</p>
            <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-store-primary" style="margin-top: 10px;">
                <i class="fa-solid fa-store"></i> Kembali ke Etalase Toko
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- STYLES KHUSUS PESANAN MASUK TOKO -->
<style>
.incoming-page-wrapper {
    width: 100%;
    margin-bottom: 40px;
}

.incoming-header-bar {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.incoming-back-wrap {
    margin-bottom: 6px;
}

.incoming-back-link {
    font-size: 0.82rem;
    font-weight: 700;
    color: #10b981;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.incoming-back-link:hover {
    color: #34d399;
    transform: translateX(-2px);
}

.incoming-title {
    font-size: 1.55rem;
    font-weight: 850;
    color: #ffffff;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}

.incoming-subtitle {
    font-size: 0.88rem;
    color: #94a3b8;
    margin: 0;
}

.text-emerald { color: #10b981; }

/* KPI Grid */
.incoming-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.incoming-kpi-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.incoming-kpi-card.kpi-alert {
    border-color: rgba(245, 158, 11, 0.4);
    background: linear-gradient(135deg, #0e131d 0%, rgba(245, 158, 11, 0.08) 100%);
}

.kpi-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.kpi-amber  { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
.kpi-blue   { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
.kpi-green  { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.kpi-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; }

.kpi-num {
    font-size: 1.35rem;
    font-weight: 850;
    color: #ffffff;
    display: block;
    line-height: 1.1;
}

.kpi-text {
    font-size: 0.74rem;
    font-weight: 600;
    color: #94a3b8;
}

/* Filter Bar */
.incoming-filter-bar {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 10px 14px;
    margin-bottom: 20px;
}

.incoming-filter-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
}

.order-filter-tab {
    padding: 7px 14px;
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

.order-filter-tab:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}

.order-filter-tab.active {
    background: #10b981;
    color: #06090e;
    font-weight: 800;
    border-color: #10b981;
}

/* Mobile Orders List (< 768px) */
.mobile-orders-list {
    display: none;
    flex-direction: column;
    gap: 14px;
}

.mobile-order-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
}

.mobile-order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.order-header-badges {
    display: flex;
    align-items: center;
    gap: 6px;
}

.badge-free-mini {
    font-size: 0.72rem;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 2px 7px;
    border-radius: 9999px;
}

.order-header-time {
    font-size: 0.76rem;
    color: #94a3b8;
}

.order-buyer-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #141b29;
    border-radius: 12px;
}

.order-buyer-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.order-buyer-name {
    font-size: 0.92rem;
    color: #ffffff;
    display: block;
}

.order-buyer-wa {
    font-size: 0.78rem;
    color: #34d399;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.order-food-box {
    padding: 12px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.order-food-title {
    font-size: 0.96rem;
    font-weight: 750;
    margin-bottom: 6px;
}

.order-food-title a {
    color: #ffffff;
    text-decoration: none;
}

.order-food-porsi {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.86rem;
    color: #cbd5e1;
    margin-bottom: 4px;
}

.order-food-total strong {
    color: #10b981;
}

.order-food-stock-info {
    font-size: 0.76rem;
    color: #94a3b8;
}

.order-claim-token-box {
    background: rgba(16, 185, 129, 0.08);
    border: 1px dashed rgba(16, 185, 129, 0.4);
    border-radius: 12px;
    padding: 10px 14px;
    text-align: center;
}

.token-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
}

.token-value {
    font-family: monospace;
    font-size: 1.25rem;
    font-weight: 900;
    color: #34d399;
    letter-spacing: 0.08em;
}

.token-hint {
    font-size: 0.72rem;
    color: #94a3b8;
}

.order-note-box {
    font-size: 0.82rem;
    color: #cbd5e1;
    background: #141b29;
    padding: 8px 12px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-style: italic;
}

.order-actions-wrap {
    display: flex;
    gap: 8px;
    margin-top: 4px;
}

.btn-touch-approve {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-size: 0.88rem;
    font-weight: 800;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    transition: all 0.2s ease;
}

.btn-touch-reject {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.3);
    font-size: 0.88rem;
    font-weight: 800;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-touch-complete {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #1e1b18;
    font-size: 0.9rem;
    font-weight: 850;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
}

.order-processed-tag {
    width: 100%;
    text-align: center;
    font-size: 0.82rem;
    color: #64748b;
    padding: 8px;
}

/* Desktop Table (>= 768px) */
.incoming-desktop-table-card {
    display: block;
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

.incoming-empty-card {
    background: #0e131d;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 56px 20px;
    text-align: center;
}

.empty-icon-circle {
    width: 76px;
    height: 76px;
    border-radius: 22px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-size: 2.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.incoming-empty-card h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
}

.incoming-empty-card p {
    font-size: 0.9rem;
    color: #94a3b8;
    max-width: 440px;
    margin: 0 auto;
}

/* Responsive Media Query (< 768px) */
@media (max-width: 767px) {
    .incoming-header-bar {
        padding: 16px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .incoming-title {
        font-size: 1.3rem;
    }

    .incoming-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .incoming-kpi-card {
        padding: 10px 12px;
        gap: 10px;
    }

    .kpi-num {
        font-size: 1.2rem;
    }

    .incoming-desktop-table-card {
        display: none !important;
    }

    .mobile-orders-list {
        display: flex !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs       = document.querySelectorAll('.order-filter-tab');
    const cards      = document.querySelectorAll('.mobile-order-card');
    const tableRows  = document.querySelectorAll('#incomingDesktopTable tbody tr');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const status = tab.getAttribute('data-status');

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            tableRows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
