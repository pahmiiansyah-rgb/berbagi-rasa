<?php
/**
 * View Riwayat Aktivitas & Log Transaksi - Berbagi Rasa
 * Mobile-First & Responsive Dual Layout (Mobile History Cards + Desktop Table)
 */
?>

<div class="history-page-wrapper">
    <div class="history-header-bar">
        <div>
            <h1 class="history-title">
                <i class="fa-solid fa-clock-rotate-left text-emerald"></i> Buku Riwayat &amp; Jejak Transaksi
            </h1>
            <p class="history-subtitle">
                Transparansi dan jejak audit transaksi penyelamatan makanan hemat jelang tutup toko
            </p>
        </div>
    </div>

    <!-- Filter Status Bar -->
    <div class="history-filter-card">
        <form action="<?= BASE_URL ?>" method="GET" class="history-filter-form">
            <input type="hidden" name="url" value="history/index">

            <label class="history-filter-label">
                <i class="fa-solid fa-filter text-emerald"></i> Saring Status:
            </label>

            <div class="history-select-wrap">
                <select name="status" class="history-select" onchange="this.form.submit()">
                    <option value="semua" <?= ($filterStatus === 'semua') ? 'selected' : '' ?>>Semua Status Transaksi</option>
                    <option value="tersedia" <?= ($filterStatus === 'tersedia') ? 'selected' : '' ?>>Tersedia (Dibuat)</option>
                    <option value="menunggu" <?= ($filterStatus === 'menunggu') ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                    <option value="disetujui" <?= ($filterStatus === 'disetujui') ? 'selected' : '' ?>>Disetujui Kasir</option>
                    <option value="diambil" <?= ($filterStatus === 'diambil') ? 'selected' : '' ?>>Sudah Diambil</option>
                    <option value="ditolak" <?= ($filterStatus === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                    <option value="dibatalkan" <?= ($filterStatus === 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                    <option value="selesai" <?= ($filterStatus === 'selesai') ? 'selected' : '' ?>>Selesai Tersalurkan</option>
                    <option value="kedaluwarsa" <?= ($filterStatus === 'kedaluwarsa') ? 'selected' : '' ?>>Kedaluwarsa</option>
                </select>
            </div>

            <?php if ($filterStatus !== 'semua'): ?>
                <a href="<?= BASE_URL ?>?url=history/index" class="btn-history-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="history-content-card">
        <div class="history-card-header">
            <h3 class="history-card-title">
                Catatan Jejak Audit (<?= $totalLogs ?> Total Catatan)
            </h3>
            <span class="history-card-desc">
                Data tersimpan otomatis di tabel log transaksi
            </span>
        </div>

        <?php if (!empty($logs)): ?>
            <!-- A. TAMPILAN KHUSUS MOBILE (< 768px): KARTU RIWAYAT AUDIT -->
            <div class="mobile-history-list">
                <?php foreach ($logs as $row): ?>
                    <div class="mobile-history-card">
                        <div class="history-card-top">
                            <span class="history-time-chip">
                                <i class="fa-regular fa-clock"></i> <?= formatDate($row['created_at']) ?>
                            </span>
                            <?php if (!empty($row['jumlah_minta'])): ?>
                                <span class="history-portion-chip">
                                    <strong><?= (int)$row['jumlah_minta'] ?></strong> <?= e($row['satuan']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="history-card-body">
                            <h4 class="history-food-title">
                                <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$row['donation_id'] ?>">
                                    <?= e($row['judul_donasi']) ?>
                                </a>
                            </h4>
                            <span class="history-food-loc">
                                <i class="fa-solid fa-location-dot text-emerald"></i> <?= e($row['lokasi_ambil']) ?>
                            </span>

                            <div class="history-transition-strip">
                                <span class="transition-label">Perubahan:</span>
                                <div class="transition-badges">
                                    <?php if (!empty($row['dari_status'])): ?>
                                        <span class="badge-old-status"><?= e($row['dari_status']) ?></span>
                                        <i class="fa-solid fa-arrow-right arrow-icon"></i>
                                    <?php endif; ?>
                                    <?= statusBadge($row['ke_status']) ?>
                                </div>
                            </div>

                            <div class="history-note-box">
                                <i class="fa-regular fa-comment-dots"></i>
                                <div>
                                    <span><?= e($row['catatan'] ?: 'Perubahan status sistem.') ?></span>
                                    <?php if (!empty($row['nama_aktor'])): ?>
                                        <small class="history-actor">Oleh: <strong><?= e($row['nama_aktor']) ?></strong></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- B. TAMPILAN KHUSUS DESKTOP (>= 768px): TABEL LENGKAP -->
            <div class="desktop-history-table-wrap">
                <div class="table-responsive">
                    <table class="data-table-3d">
                        <thead>
                            <tr>
                                <th style="min-width: 170px;">Waktu Kejadian</th>
                                <th style="min-width: 220px;">Makanan Donasi</th>
                                <th style="min-width: 180px;">Transisi Status</th>
                                <th style="min-width: 120px;">Porsi Terkait</th>
                                <th style="min-width: 200px;">Catatan Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $row): ?>
                                <tr>
                                    <td style="white-space: nowrap; font-size: 0.85rem; color: #94a3b8;">
                                        <i class="fa-regular fa-clock text-emerald"></i> <?= formatDate($row['created_at']) ?>
                                    </td>

                                    <td>
                                        <strong style="font-size: 0.95rem; display: block;">
                                            <a href="<?= BASE_URL ?>?url=donation/show/<?= (int)$row['donation_id'] ?>" style="color: #ffffff; text-decoration: none;">
                                                <?= e($row['judul_donasi']) ?>
                                            </a>
                                        </strong>
                                        <span style="font-size: 0.8rem; color: #94a3b8;">
                                            <i class="fa-solid fa-location-dot text-emerald"></i> <?= e($row['lokasi_ambil']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
                                            <?php if (!empty($row['dari_status'])): ?>
                                                <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: #cbd5e1;">
                                                    <?= e($row['dari_status']) ?>
                                                </span>
                                                <i class="fa-solid fa-arrow-right" style="color: #64748b; font-size: 0.75rem;"></i>
                                            <?php endif; ?>
                                            
                                            <?= statusBadge($row['ke_status']) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['jumlah_minta'])): ?>
                                            <strong style="color: #10b981; font-size: 0.95rem;">
                                                <?= (int)$row['jumlah_minta'] ?>
                                            </strong> <?= e($row['satuan']) ?>
                                        <?php else: ?>
                                            <span style="color: #64748b;">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span style="font-size: 0.88rem; color: #f8fafc;">
                                            <?= e($row['catatan'] ?: 'Perubahan status sistem.') ?>
                                        </span>
                                        <?php if (!empty($row['nama_aktor'])): ?>
                                            <small style="display: block; color: #94a3b8; font-size: 0.78rem; margin-top: 2px;">
                                                Oleh: <strong><?= e($row['nama_aktor']) ?></strong>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-wrap" style="padding: 20px 0;">
                    <?php if ($page > 1): ?>
                        <a href="<?= BASE_URL ?>?url=history/index&status=<?= urlencode($filterStatus) ?>&page=<?= $page - 1 ?>" class="page-btn">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= BASE_URL ?>?url=history/index&status=<?= urlencode($filterStatus) ?>&page=<?= $i ?>" class="page-btn <?= ($page === $i) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= BASE_URL ?>?url=history/index&status=<?= urlencode($filterStatus) ?>&page=<?= $page + 1 ?>" class="page-btn">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="history-empty-wrap">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <h4>Belum Ada Riwayat Tercatat</h4>
                <p>Setiap persetujuan donasi, klaim, atau perubahan status akan tercatat otomatis di sini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.history-page-wrapper {
    width: 100%;
    margin-bottom: 40px;
}

.history-header-bar {
    margin-bottom: 24px;
}

.history-title {
    font-size: 1.65rem;
    font-weight: 850;
    color: #ffffff;
    letter-spacing: -0.02em;
    margin-bottom: 4px;
}

.history-subtitle {
    font-size: 0.92rem;
    color: #94a3b8;
    margin: 0;
}

.history-filter-card {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 20px;
}

.history-filter-form {
    display: flex;
    gap: 12px;
    align-items: center;
    width: 100%;
    flex-wrap: wrap;
}

.history-filter-label {
    font-weight: 700;
    font-size: 0.88rem;
    color: #f8fafc;
    display: flex;
    align-items: center;
    gap: 6px;
}

.history-select-wrap {
    flex: 1;
    min-width: 220px;
}

.history-select {
    width: 100%;
    padding: 10px 16px;
    font-size: 0.88rem;
    color: #f8fafc;
    background: #1c2436;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    outline: none;
    cursor: pointer;
}

.btn-history-reset {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 14px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    text-decoration: none;
}

.btn-history-reset:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.12);
}

.history-content-card {
    background: #131924;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
}

.history-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.history-card-title {
    font-size: 1.1rem;
    font-weight: 750;
    color: #ffffff;
    margin: 0;
}

.history-card-desc {
    font-size: 0.82rem;
    color: #94a3b8;
}

/* Mobile List (< 768px) */
.mobile-history-list {
    display: none;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
}

.mobile-history-card {
    background: #1c2436;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.history-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.history-time-chip {
    font-size: 0.76rem;
    color: #94a3b8;
}

.history-portion-chip {
    font-size: 0.78rem;
    color: #10b981;
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.25);
    padding: 2px 8px;
    border-radius: 9999px;
}

.history-card-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.history-food-title {
    font-size: 0.98rem;
    font-weight: 750;
    margin: 0;
    line-height: 1.35;
}

.history-food-title a {
    color: #ffffff;
    text-decoration: none;
}

.history-food-loc {
    font-size: 0.8rem;
    color: #94a3b8;
}

.history-transition-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
    flex-wrap: wrap;
}

.transition-label {
    font-size: 0.76rem;
    color: #94a3b8;
    font-weight: 700;
}

.transition-badges {
    display: flex;
    align-items: center;
    gap: 6px;
}

.badge-old-status {
    font-size: 0.74rem;
    padding: 2px 7px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.08);
    color: #94a3b8;
}

.arrow-icon {
    font-size: 0.7rem;
    color: #64748b;
}

.history-note-box {
    display: flex;
    gap: 8px;
    padding: 8px 10px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
    font-size: 0.8rem;
    color: #cbd5e1;
    margin-top: 4px;
}

.history-note-box i {
    color: #94a3b8;
    margin-top: 2px;
}

.history-actor {
    display: block;
    color: #94a3b8;
    font-size: 0.74rem;
    margin-top: 2px;
}

.desktop-history-table-wrap {
    display: block;
}

.history-empty-wrap {
    text-align: center;
    padding: 56px 20px;
}

.history-empty-wrap i {
    font-size: 2.8rem;
    color: #64748b;
    margin-bottom: 12px;
    display: block;
}

.history-empty-wrap h4 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 6px;
}

.history-empty-wrap p {
    font-size: 0.88rem;
    color: #94a3b8;
}

@media (max-width: 767px) {
    .history-card-header {
        padding: 16px;
    }

    .desktop-history-table-wrap {
        display: none !important;
    }

    .mobile-history-list {
        display: flex !important;
    }
}
</style>
