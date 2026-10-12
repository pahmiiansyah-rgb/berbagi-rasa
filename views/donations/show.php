<?php
/**
 * View Detail Menu Makanan Hemat Jelang Tutup Toko - Berbagi Rasa
 */
$currentUser = currentUser();
$userRole = $currentUser['role'] ?? '';
$sisa = (int)$donation['sisa'];
$total = (int)$donation['jumlah'];
$claimed = $total - $sisa;
$progressPercent = ($total > 0) ? round(($claimed / $total) * 100) : 0;
$isExpired = strtotime($donation['expired_at']) <= time();

$hargaAsli = (int)($donation['harga_asli'] ?? 0);
$hargaDiskon = (int)($donation['harga_diskon'] ?? 0);
$isFree = (($donation['tipe_penawaran'] ?? '') === 'gratis_peduli' || $hargaDiskon === 0);
$hematPerPorsi = max(0, $hargaAsli - $hargaDiskon);
$persenHemat = discountPercent($hargaAsli, $hargaDiskon);
?>

<div style="margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>?url=donation/index" class="btn-3d btn-3d-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog Menu
    </a>
</div>

<div class="detail-grid">
    <!-- Left Column: Photo & Timeline -->
    <div style="display: flex; flex-direction: column; gap: 28px;">
        <div class="detail-gallery-3d">
            <?php if (!empty($donation['foto']) && file_exists(UPLOAD_DIR . $donation['foto'])): ?>
                <img src="<?= APP_URL ?>/assets/uploads/<?= e($donation['foto']) ?>" alt="<?= e($donation['judul']) ?>" class="detail-image-main">
            <?php else: ?>
                <div style="width: 100%; aspect-ratio: 16/11; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); color: #64748b; font-size: 4rem;">
                    <i class="fa-solid fa-utensils"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Jaminan Mutu & Higienitas Box -->
        <div class="quality-assurance-card-3d">
            <div class="qa-icon">
                <i class="fa-solid fa-certificate"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 6px 0; font-size: 1.05rem; font-weight: 800; color: #34d399;">
                    Jaminan Kualitas & Martabat Makanan
                </h4>
                <p style="margin: 0; font-size: 0.88rem; color: #cbd5e1; line-height: 1.5;">
                    <?= e($donation['jaminan_kualitas'] ?? '100% Segar & Higienis Diproduksi Hari Ini.') ?>
                    Makanan ini adalah batch baru yang belum terjual saat toko menjelang tutup, bukan sisa makan orang lain.
                </p>
            </div>
        </div>

        <!-- 3D Vertical Status & Activity Timeline -->
        <div class="table-card-3d" style="padding: 28px;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Jejak Aktivitas & Pembaruan
            </h3>

            <?php if (!empty($logs)): ?>
                <div class="timeline-3d">
                    <?php foreach ($logs as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">
                                    <i class="fa-regular fa-clock"></i> <?= formatDate($log['created_at']) ?>
                                </div>
                                <div class="timeline-text">
                                    <strong>Status: <?= ucfirst(e($log['ke_status'])) ?></strong>
                                    <?php if (!empty($log['catatan'])): ?>
                                        <p style="margin-top: 4px; color: var(--text-muted); font-size: 0.85rem;">
                                            <?= e($log['catatan']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada catatan aktivitas.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Details & Claim Form -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="detail-info-card">
            <!-- Header & Badges -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <?= statusBadge($donation['status']) ?>
                    <?= discountBadge($hargaAsli, $hargaDiskon, $donation['tipe_penawaran'] ?? 'diskon_toko') ?>
                </div>
                <div class="countdown-tag">
                    <i class="fa-regular fa-clock"></i> <?= timeRemaining($donation['expired_at']) ?>
                </div>
            </div>

            <!-- Title & Store -->
            <div>
                <div style="display: flex; align-items: center; gap: 8px; color: var(--primary-dark); font-weight: 700; font-size: 0.95rem; margin-top: 8px;">
                    <i class="fa-solid fa-store"></i> <?= e($donation['pemilik']) ?>
                </div>
                <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); line-height: 1.25; margin-top: 6px;">
                    <?= e($donation['judul']) ?>
                </h1>
            </div>

            <!-- Price Breakdown Box 3D -->
            <div class="price-breakdown-box-3d">
                <div class="price-item">
                    <span class="label">Harga Normal Toko</span>
                    <span class="value strikethrough"><?= formatRupiah($hargaAsli) ?></span>
                </div>
                <div class="price-item highlight">
                    <span class="label">
                        <?php if ($isFree): ?>
                            Tebusan Khusus Peduli Pangan
                        <?php else: ?>
                            Harga Khusus Jelang Tutup
                        <?php endif; ?>
                    </span>
                    <span class="value price-deal">
                        <?php if ($isFree): ?>
                            Rp 0 <small style="font-size: 0.85rem; font-weight: 600;">(Gratis)</small>
                        <?php else: ?>
                            <?= formatRupiah($hargaDiskon) ?> <small style="font-size: 0.85rem; font-weight: 600;">/ <?= e($donation['satuan']) ?></small>
                        <?php endif; ?>
                    </span>
                </div>
                <?php if ($hargaAsli > 0 && !$isFree): ?>
                    <div class="price-item save-badge">
                        <span class="label"><i class="fa-solid fa-piggy-bank"></i> Anda Menghemat</span>
                        <span class="value save-amount"><?= formatRupiah($hematPerPorsi) ?> (<?= $persenHemat ?>%)</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quota Progress Gauge 3D -->
            <div class="quota-gauge-3d">
                <div class="gauge-header">
                    <span>Ketersediaan Porsi:</span>
                    <strong style="color: var(--primary-dark); font-size: 1rem;">
                        Sisa <?= $sisa ?> dari <?= $total ?> <?= e($donation['satuan']) ?>
                    </strong>
                </div>
                <div class="gauge-progress-bar">
                    <div class="gauge-fill" style="width: <?= 100 - $progressPercent ?>%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 6px;">
                    <span><?= $claimed ?> <?= e($donation['satuan']) ?> dipesan</span>
                    <span><?= $sisa ?> <?= e($donation['satuan']) ?> siap diambil</span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                    Informasi Menu & Kemasan:
                </h4>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    <?= nl2br(e($donation['deskripsi'] ?: 'Tidak ada keterangan tambahan dari toko.')) ?>
                </p>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color);">

            <!-- Location, Closing Time, and Pickup Instructions -->
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <i class="fa-solid fa-door-closed" style="color: #f59e0b; font-size: 1.1rem; margin-top: 3px;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.9rem;">Jam Operasional Tutup Toko:</strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">
                            <?= e($donation['jam_tutup'] ?? '21:00 WIB') ?> (Harap datang sebelum toko tutup)
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <i class="fa-solid fa-location-dot" style="color: var(--primary); font-size: 1.1rem; margin-top: 3px;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.9rem;">Alamat & Lokasi Kasir Penjemputan:</strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem;"><?= e($donation['lokasi_ambil']) ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <i class="fa-solid fa-hourglass-end" style="color: var(--accent-dark); font-size: 1.1rem; margin-top: 3px;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.9rem;">Batas Akhir Penjemputan:</strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem;"><?= formatDate($donation['expired_at']) ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <i class="fa-solid fa-phone" style="color: var(--info); font-size: 1.1rem; margin-top: 3px;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.9rem;">Kontak Mitra Toko:</strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem;">
                            <?= e($donation['pemilik']) ?> 
                            <?php if (!empty($donation['telepon_pemilik'])): ?>
                                • <i class="fa-brands fa-whatsapp"></i> <?= e($donation['telepon_pemilik']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contextual Action Buttons / Claim Form -->
            <div style="margin-top: 12px;">
                <?php if ($isMyDonation): ?>
                    <!-- Owner / Administrator Controls -->
                    <div style="background: var(--bg-surface-muted); padding: 18px; border-radius: var(--radius-md); border: 1px solid <?= ($isAdmin ?? false) ? 'rgba(168, 85, 247, 0.4)' : 'var(--border-color)' ?>;">
                        <strong style="display: block; font-size: 0.95rem; margin-bottom: 12px; color: var(--text-main);">
                            <?php if ($isAdmin ?? false): ?>
                                <i class="fa-solid fa-shield-halved" style="color: #a855f7;"></i> Panel Moderasi Administrator Sistem
                            <?php else: ?>
                                <i class="fa-solid fa-user-shield" style="color: var(--primary);"></i> Anda adalah pemilik menu promo ini
                            <?php endif; ?>
                        </strong>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="<?= BASE_URL ?>?url=donation/edit/<?= (int)$donation['id'] ?>" class="btn-3d btn-3d-secondary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Ubah Data Promo
                            </a>
                            <a href="<?= BASE_URL ?>?url=request/incoming" class="btn-3d btn-3d-primary btn-sm">
                                <i class="fa-solid fa-receipt"></i> <?= ($isAdmin ?? false) ? 'Periksa Pesanan Toko' : 'Cek Pesanan Masuk' ?>
                            </a>
                            <form action="<?= BASE_URL ?>?url=donation/destroy/<?= (int)$donation['id'] ?>" method="POST" style="display: inline;" data-confirm="Apakah Anda yakin ingin menghapus menu promo '<?= e($donation['judul']) ?>' secara permanen?">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button type="submit" class="btn-3d btn-3d-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i> Hapus Menu Ini
                                </button>
                            </form>
                        </div>
                    </div>

                <?php elseif ($myActiveRequest): ?>
                    <!-- Active Request Notice for Requester -->
                    <div class="alert-box alert-info" style="margin-bottom: 0;">
                        <div class="alert-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="alert-content">
                            <strong>Pesanan Anda Sedang Berlangsung!</strong>
                            <p style="margin-top: 4px;">
                                Anda memesan sebanyak <strong><?= (int)$myActiveRequest['jumlah_minta'] ?> <?= e($donation['satuan']) ?></strong>.
                                Total tebusan kasir: <strong><?= ($hargaDiskon === 0) ? 'Gratis Peduli (Rp 0)' : formatRupiah($hargaDiskon * (int)$myActiveRequest['jumlah_minta']) ?></strong>.
                            </p>
                            <p style="margin-top: 4px;">
                                Status saat ini: <?= requestBadge($myActiveRequest['status']) ?>
                                <?php if (!empty($myActiveRequest['claim_token'])): ?>
                                    — Kode Ambil: <strong style="color: var(--primary-dark); font-family: monospace; font-size: 1.05rem;"><?= e($myActiveRequest['claim_token']) ?></strong>
                                <?php endif; ?>
                            </p>
                            <a href="<?= BASE_URL ?>?url=request/mine" class="btn-3d btn-3d-primary btn-sm" style="margin-top: 10px;">
                                <i class="fa-solid fa-bag-shopping"></i> Buka Pesanan Saya
                            </a>
                        </div>
                    </div>

                <?php elseif ($isExpired || $donation['status'] !== 'tersedia' || $sisa <= 0): ?>
                    <!-- Closed / Expired Alert -->
                    <div class="alert-box alert-warning" style="margin-bottom: 0;">
                        <div class="alert-icon"><i class="fa-solid fa-ban"></i></div>
                        <div class="alert-content">
                            <strong>Menu Sudah Tidak Tersedia</strong>
                            <p>Penawaran makanan ini sudah habis dipesan atau toko sudah melewati batas waktu operasional penjemputan.</p>
                        </div>
                    </div>

                <?php elseif (!isLoggedIn()): ?>
                    <!-- Must Login to Order -->
                    <div style="background: var(--bg-surface-muted); padding: 20px; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-color);">
                        <p style="font-weight: 700; margin-bottom: 12px; color: var(--text-main);">
                            Tertarik memesan menu hemat ini sebelum toko tutup?
                        </p>
                        <a href="<?= BASE_URL ?>?url=auth/login" class="btn-3d btn-3d-primary btn-block">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk Akun untuk Memesan
                        </a>
                        <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 8px;">
                            Belum punya akun? <a href="<?= BASE_URL ?>?url=auth/register" style="color: var(--primary); font-weight: 600;">Daftar gratis di sini</a>.
                        </p>
                    </div>

                <?php else: ?>
                    <!-- Order / Reservation Form -->
                    <form action="<?= BASE_URL ?>?url=request/store" method="POST" class="claim-form-3d">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="donation_id" value="<?= (int)$donation['id'] ?>">
                        <input type="hidden" id="unit_price" value="<?= $hargaDiskon ?>">
                        <input type="hidden" id="original_price" value="<?= $hargaAsli ?>">

                        <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
                            <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i> Form Reservasi Penjemputan di Toko
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px;">
                            Setelah pesanan dikonfirmasi oleh toko, cukup datang ke kasir sesuai jam tutup dan tebus makanan ini.
                        </p>

                        <div class="form-group">
                            <label class="form-label" for="jumlah_minta">
                                <span>Jumlah Porsi yang Dipesan <span style="color: var(--danger);">*</span></span>
                                <span class="hint">Tersedia <?= $sisa ?> <?= e($donation['satuan']) ?></span>
                            </label>
                            <div class="input-group-3d">
                                <i class="fa-solid fa-cubes-stacked input-icon"></i>
                                <input 
                                    type="number" 
                                    id="jumlah_minta" 
                                    name="jumlah_minta" 
                                    class="form-control-3d" 
                                    min="1" 
                                    max="<?= $sisa ?>" 
                                    value="1" 
                                    required
                                >
                            </div>
                        </div>

                        <!-- Live Cost & Savings Calculator -->
                        <div class="calculator-box-3d" id="calc_box">
                            <div class="calc-row">
                                <span>Estimasi Total Tebusan Kasir:</span>
                                <strong id="calc_total_bayar" class="calc-highlight">
                                    <?= ($isFree) ? 'Rp 0 (Gratis)' : formatRupiah($hargaDiskon) ?>
                                </strong>
                            </div>
                            <?php if (!$isFree && $hargaAsli > 0): ?>
                                <div class="calc-row savings">
                                    <span>Total Penghematan Anda:</span>
                                    <span id="calc_total_hemat" class="calc-save">
                                        <?= formatRupiah($hematPerPorsi) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <small class="calc-note">
                                <i class="fa-solid fa-circle-info"></i> Pembayaran dilakukan langsung di meja kasir saat pengambilan fisik.
                            </small>
                        </div>

                        <div class="form-group" style="margin-top: 14px;">
                            <label class="form-label" for="catatan">
                                <span>Catatan untuk Mitra Toko (Opsional)</span>
                                <span class="hint">Maks. 255 huruf</span>
                            </label>
                            <textarea 
                                id="catatan" 
                                name="catatan" 
                                class="form-control-3d" 
                                rows="2" 
                                placeholder="Contoh: Saya anak rantau kost dekat sini, akan ambil jam 20.30 WIB..." 
                                maxlength="255"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn-3d btn-3d-primary btn-block btn-lg" style="margin-top: 14px;">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Pesanan Sekarang
                        </button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const qtyInput = document.getElementById('jumlah_minta');
                            const unitPrice = parseInt(document.getElementById('unit_price').value) || 0;
                            const origPrice = parseInt(document.getElementById('original_price').value) || 0;
                            const totalDisplay = document.getElementById('calc_total_bayar');
                            const saveDisplay = document.getElementById('calc_total_hemat');

                            function updateCalculation() {
                                const qty = Math.max(1, parseInt(qtyInput.value) || 1);
                                const total = qty * unitPrice;
                                const totalOrig = qty * origPrice;
                                const totalSave = Math.max(0, totalOrig - total);

                                if (unitPrice === 0) {
                                    totalDisplay.textContent = 'Rp 0 (Gratis Peduli)';
                                } else {
                                    totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                                }

                                if (saveDisplay && origPrice > 0) {
                                    saveDisplay.textContent = 'Rp ' + totalSave.toLocaleString('id-ID');
                                }
                            }

                            if (qtyInput) {
                                qtyInput.addEventListener('input', updateCalculation);
                            }
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
