<?php
/**
 * View Pesanan Hemat Saya (Untuk Pembeli Hemat / Anak Rantau) - Berbagi Rasa
 * Mobile-First & Responsive Dual Layout (Mobile Buyer Cards + Desktop Table)
 */
$requestsList   = $requests ?? [];
$totalOrders    = count($requestsList);
$countMenunggu  = count(array_filter($requestsList, fn($r) => $r['status'] === 'menunggu'));
$countDisetujui = count(array_filter($requestsList, fn($r) => $r['status'] === 'disetujui'));
$countSelesai   = count(array_filter($requestsList, fn($r) => in_array($r['status'], ['diambil', 'selesai'])));
?>

<div class="buyer-page-wrapper">
    <!-- Header Halaman Pesanan Saya -->
    <div class="buyer-header-bar">
        <div>
            <h1 class="buyer-title">
                <i class="fa-solid fa-bag-shopping text-emerald"></i> Pesanan Hemat Saya
            </h1>
            <p class="buyer-subtitle">
                Pantau status pesanan, simpan kode klaim resmi, dan tebus makanan hemat di kasir sebelum toko tutup
            </p>
        </div>

        <a href="<?= BASE_URL ?>?url=donation/index" class="btn-store-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Cari Menu Lain
        </a>
    </div>

    <!-- KPI Ringkasan Pesanan Buyer -->
    <div class="buyer-kpi-grid">
        <div class="buyer-kpi-card <?= ($countDisetujui > 0) ? 'kpi-ready' : '' ?>">
            <div class="kpi-icon-box kpi-green">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countDisetujui ?></span>
                <span class="kpi-text">Siap Ditebus di Kasir</span>
            </div>
        </div>

        <div class="buyer-kpi-card <?= ($countMenunggu > 0) ? 'kpi-waiting' : '' ?>">
            <div class="kpi-icon-box kpi-amber">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countMenunggu ?></span>
                <span class="kpi-text">Menunggu Konfirmasi</span>
            </div>
        </div>

        <div class="buyer-kpi-card">
            <div class="kpi-icon-box kpi-blue">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="kpi-num"><?= $countSelesai ?></span>
                <span class="kpi-text">Berhasil Ditebus</span>
            </div>
        </div>

        <div class="buyer-kpi-card">
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
    <div class="buyer-filter-bar">
        <div class="buyer-filter-tabs">
            <button type="button" class="buyer-tab-btn active" data-status="all">
                Semua (<?= $totalOrders ?>)
            </button>
            <button type="button" class="buyer-tab-btn" data-status="disetujui">
                Siap Tebus (<?= $countDisetujui ?>)
            </button>
            <button type="button" class="buyer-tab-btn" data-status="menunggu">
                Menunggu (<?= $countMenunggu ?>)
            </button>
            <button type="button" class="buyer-tab-btn" data-status="selesai">
                Selesai (<?= $countSelesai ?>)
            </button>
        </div>
    </div>

    <!-- Konten Daftar Pesanan -->
    <?php if (!empty($requestsList)): ?>

        <!-- A. TAMPILAN KHUSUS MOBILE (< 768px): KARTU PESANAN PEMBELI -->
        <div class="mobile-buyer-list" id="mobileBuyerList">
            <?php foreach ($requestsList as $req): ?>
                <?php 
                    $hargaDiskon = (int)($req['harga_diskon'] ?? 0);
                    $jumlahMinta = (int)$req['jumlah_minta'];
                    $totalBayar  = $hargaDiskon * $jumlahMinta;
                    $isFree      = (($req['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
                    $statusGroup = ($req['status'] === 'diambil' || $req['status'] === 'selesai') ? 'selesai' : $req['status'];
                ?>
                <div class="mobile-buyer-card" data-status="<?= e($statusGroup) ?>">
                    <!-- Header Kartu -->
                    <div class="buyer-card-header">
                        <div class="buyer-card-badges">
                            <?= requestBadge($req['status']) ?>
                            <?php if ($isFree): ?>
                                <span class="badge-free-tag"><i class="fa-solid fa-gift"></i> Gratis Peduli</span>
                            <?php endif; ?>
                        </div>
                        <span class="buyer-card-time">
                            <i class="fa-regular fa-clock"></i> <?= formatDate($req['created_at']) ?>
                        </span>
                    </div>

                    <!-- Informasi Menu & Toko -->
                    <div class="buyer-card-food">
                        <h3 class="buyer-card-title">
                            <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>">
                                <?= e($req['judul_donasi']) ?>
                            </a>
                        </h3>

                        <div class="buyer-card-store">
                            <i class="fa-solid fa-store text-emerald"></i>
                            <strong><?= e($req['nama_donatur']) ?></strong>
                        </div>

                        <div class="buyer-card-meta-row">
                            <div class="buyer-meta-item">
                                <i class="fa-solid fa-clock" style="color: #f59e0b;"></i>
                                <span>Tutup Toko: <strong style="color: #fbbf24;"><?= e($req['jam_tutup'] ?? '21:00 WIB') ?></strong></span>
                            </div>
                            <div class="buyer-meta-item">
                                <i class="fa-solid fa-location-dot" style="color: #10b981;"></i>
                                <span><?= e($req['lokasi_ambil']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tebusan & Jumlah Porsi -->
                    <div class="buyer-payment-strip">
                        <div class="payment-porsi">
                            <span>Jumlah Porsi:</span>
                            <strong><?= $jumlahMinta ?> <?= e($req['satuan']) ?></strong>
                        </div>
                        <div class="payment-total">
                            <span>Total Tebus di Kasir:</span>
                            <strong class="total-val"><?= ($isFree) ? 'Rp 0 (Gratis Peduli)' : formatRupiah($totalBayar) ?></strong>
                        </div>
                    </div>

                    <!-- Kotak Kode Ambil Kasir (Bila Disetujui) -->
                    <?php if (!empty($req['claim_token'])): ?>
                        <div class="buyer-claim-token-card">
                            <div class="claim-card-label">KODE AMBIL KASIR RESMI</div>
                            <div class="claim-card-token">
                                <i class="fa-solid fa-qrcode"></i> <?= e($req['claim_token']) ?>
                            </div>
                            <p class="claim-card-instruction">
                                Tunjukkan layar ini atau sebutkan kode di atas ke kasir toko saat mengambil makanan sebelum jam operasional tutup.
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Tombol Aksi Sentuh -->
                    <div class="buyer-card-actions">
                        <?php if ($req['status'] === 'disetujui'): ?>
                            <form action="<?= BASE_URL ?>?url=request/complete/<?= (int)$req['id'] ?>" method="POST" style="flex: 1;" data-confirm="Apakah Anda sudah mengambil makanan di kasir dan menyelesaikan pembayaran?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-buyer-complete">
                                    <i class="fa-solid fa-check-double"></i> Sudah Diambil di Toko
                                </button>
                            </form>

                            <form action="<?= BASE_URL ?>?url=request/cancel/<?= (int)$req['id'] ?>" method="POST" style="flex: 1;" data-confirm="Batalkan pesanan ini?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-buyer-cancel">
                                    <i class="fa-solid fa-xmark"></i> Batalkan
                                </button>
                            </form>

                        <?php elseif ($req['status'] === 'menunggu'): ?>
                            <form action="<?= BASE_URL ?>?url=request/cancel/<?= (int)$req['id'] ?>" method="POST" style="width: 100%;" data-confirm="Batalkan pesanan ini?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-buyer-cancel">
                                    <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
                                </button>
                            </form>

                        <?php else: ?>
                            <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>" class="btn-buyer-detail">
                                <i class="fa-solid fa-eye"></i> Lihat Detail Menu
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- B. TAMPILAN KHUSUS DESKTOP (>= 768px): TABEL RAPI -->
        <div class="buyer-desktop-table-card">
            <div class="table-responsive">
                <table class="data-table-3d" id="buyerDesktopTable">
                    <thead>
                        <tr>
                            <th style="min-width: 220px;">Menu Makanan</th>
                            <th style="min-width: 200px;">Mitra Toko & Jam Tutup</th>
                            <th style="min-width: 140px;">Porsi & Tebusan</th>
                            <th style="min-width: 140px;">Kode Ambil Kasir</th>
                            <th style="min-width: 110px;">Status</th>
                            <th style="min-width: 150px; text-align: right;">Aksi</th>
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
                                    <strong style="font-size: 0.95rem; display: block;">
                                        <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>" style="color: #ffffff; text-decoration: none;">
                                            <?= e($req['judul_donasi']) ?>
                                        </a>
                                    </strong>
                                    <span style="font-size: 0.8rem; color: #94a3b8;">
                                        Dipesan pada <?= formatDate($req['created_at']) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong style="font-size: 0.92rem; display: block; color: #ffffff;">
                                        <i class="fa-solid fa-store text-emerald"></i> <?= e($req['nama_donatur']) ?>
                                    </strong>
                                    <span style="font-size: 0.8rem; color: #fbbf24; font-weight: 700; display: block; margin-top: 2px;">
                                        <i class="fa-solid fa-clock"></i> Tutup: <?= e($req['jam_tutup'] ?? '21:00 WIB') ?>
                                    </span>
                                    <span style="font-size: 0.8rem; color: #94a3b8;">
                                        <i class="fa-solid fa-location-dot"></i> <?= e($req['lokasi_ambil']) ?>
                                    </span>
                                </td>

                                <td>
                                    <div style="font-size: 0.95rem; font-weight: 800; color: #ffffff;">
                                        <?= $jumlahMinta ?> <?= e($req['satuan']) ?>
                                    </div>
                                    <div style="font-size: 0.88rem; font-weight: 750; color: #10b981; margin-top: 2px;">
                                        <?= ($isFree) ? 'Rp 0 (Gratis Peduli)' : formatRupiah($totalBayar) ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if (!empty($req['claim_token'])): ?>
                                        <div style="font-family: monospace; font-size: 0.95rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.12); padding: 4px 10px; border-radius: 8px; border: 1px dashed rgba(16, 185, 129, 0.4); display: inline-block;">
                                            <i class="fa-solid fa-qrcode"></i> <?= e($req['claim_token']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size: 0.8rem; color: #64748b;">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= requestBadge($req['status']) ?>
                                </td>

                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end;">
                                        <?php if ($req['status'] === 'disetujui'): ?>
                                            <form action="<?= BASE_URL ?>?url=request/complete/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Apakah Anda sudah mengambil makanan di kasir toko dan menyelesaikan pembayaran?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-primary btn-sm">
                                                    <i class="fa-solid fa-check-double"></i> Diambil
                                                </button>
                                            </form>

                                            <form action="<?= BASE_URL ?>?url=request/cancel/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Batalkan pesanan ini?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-danger btn-sm">
                                                    <i class="fa-solid fa-xmark"></i> Batal
                                                </button>
                                            </form>

                                        <?php elseif ($req['status'] === 'menunggu'): ?>
                                            <form action="<?= BASE_URL ?>?url=request/cancel/<?= (int)$req['id'] ?>" method="POST" style="display: inline;" data-confirm="Batalkan pesanan ini?">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <button type="submit" class="btn-3d btn-3d-secondary btn-sm">
                                                    <i class="fa-solid fa-xmark"></i> Batal
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$req['donation_id'] ?>" class="btn-3d btn-3d-secondary btn-sm">
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </a>
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
        <div class="buyer-empty-card">
            <div class="buyer-empty-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h3>Belum Ada Pesanan Aktif</h3>
            <p>Jelajahi menu lezat dan hemat malam ini dari toko &amp; bakery terdekat sebelum jam tutup operasional mereka.</p>
            <a href="<?= BASE_URL ?>?url=donation/index" class="btn-store-primary" style="margin-top: 12px;">
                <i class="fa-solid fa-utensils"></i> Jelajahi Menu Siap Tebus
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- STYLES KHUSUS PESANAN SAYA -->
<style>
.buyer-page-wrapper {
    width: 100%;
    margin-bottom: 40px;
}

.buyer-header-bar {
    background: #131924;
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

.buyer-title {
    font-size: 1.55rem;
    font-weight: 850;
    color: #ffffff;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}

.buyer-subtitle {
    font-size: 0.88rem;
    color: #94a3b8;
    margin: 0;
}

.text-emerald { color: #10b981; }

/* KPI Grid */
.buyer-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.buyer-kpi-card {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.buyer-kpi-card.kpi-ready {
    border-color: rgba(16, 185, 129, 0.4);
    background: linear-gradient(135deg, #131924 0%, rgba(16, 185, 129, 0.08) 100%);
}

.buyer-kpi-card.kpi-waiting {
    border-color: rgba(245, 158, 11, 0.35);
}

/* Filter Bar */
.buyer-filter-bar {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 10px 14px;
    margin-bottom: 20px;
}

.buyer-filter-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
}

.buyer-tab-btn {
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

.buyer-tab-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}

.buyer-tab-btn.active {
    background: #10b981;
    color: #06090e;
    font-weight: 800;
    border-color: #10b981;
}

/* Mobile Buyer List (< 768px) */
.mobile-buyer-list {
    display: none;
    flex-direction: column;
    gap: 16px;
}

.mobile-buyer-card {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
}

.buyer-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.buyer-card-badges {
    display: flex;
    align-items: center;
    gap: 6px;
}

.badge-free-tag {
    font-size: 0.74rem;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 2px 8px;
    border-radius: 9999px;
}

.buyer-card-time {
    font-size: 0.76rem;
    color: #94a3b8;
}

.buyer-card-food {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.buyer-card-title {
    font-size: 1.05rem;
    font-weight: 750;
    margin: 0;
    line-height: 1.35;
}

.buyer-card-title a {
    color: #ffffff;
    text-decoration: none;
}

.buyer-card-store {
    font-size: 0.88rem;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
}

.buyer-card-meta-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 2px;
}

.buyer-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.buyer-payment-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #1c2436;
    border-radius: 12px;
    font-size: 0.85rem;
}

.payment-porsi strong {
    color: #ffffff;
}

.total-val {
    color: #10b981;
    font-size: 0.95rem;
}

.buyer-claim-token-card {
    background: rgba(16, 185, 129, 0.1);
    border: 1.5px dashed rgba(16, 185, 129, 0.5);
    border-radius: 14px;
    padding: 14px;
    text-align: center;
}

.claim-card-label {
    font-size: 0.72rem;
    font-weight: 850;
    color: #94a3b8;
    letter-spacing: 0.06em;
    margin-bottom: 4px;
}

.claim-card-token {
    font-family: monospace;
    font-size: 1.45rem;
    font-weight: 900;
    color: #34d399;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}

.claim-card-instruction {
    font-size: 0.76rem;
    color: #cbd5e1;
    margin: 0;
    line-height: 1.4;
}

.buyer-card-actions {
    display: flex;
    gap: 8px;
    margin-top: 4px;
}

.btn-buyer-complete {
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
}

.btn-buyer-cancel {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: rgba(239, 68, 68, 0.12);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.25);
    font-size: 0.88rem;
    font-weight: 800;
    border-radius: 12px;
    cursor: pointer;
}

.btn-buyer-detail {
    width: 100%;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.12);
    font-size: 0.88rem;
    font-weight: 700;
    border-radius: 12px;
    text-decoration: none;
}

/* Desktop Table (>= 768px) */
.buyer-desktop-table-card {
    display: block;
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

.buyer-empty-card {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 56px 20px;
    text-align: center;
}

.buyer-empty-icon {
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

.buyer-empty-card h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
}

.buyer-empty-card p {
    font-size: 0.9rem;
    color: #94a3b8;
    max-width: 440px;
    margin: 0 auto;
}

/* Responsive Media Query (< 768px) */
@media (max-width: 767px) {
    .buyer-header-bar {
        padding: 16px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .buyer-title {
        font-size: 1.3rem;
    }

    .buyer-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .buyer-kpi-card {
        padding: 10px 12px;
        gap: 10px;
    }

    .buyer-desktop-table-card {
        display: none !important;
    }

    .mobile-buyer-list {
        display: flex !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs      = document.querySelectorAll('.buyer-tab-btn');
    const cards     = document.querySelectorAll('.mobile-buyer-card');
    const tableRows = document.querySelectorAll('#buyerDesktopTable tbody tr');

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
