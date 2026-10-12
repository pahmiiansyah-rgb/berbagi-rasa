<?php
/**
 * View Edit Menu Makanan Hemat Jelang Tutup Toko - Berbagi Rasa
 */
?>
<div class="form-card-3d">
    <div class="form-header">
        <div class="brand-icon-3d" style="margin: 0 auto 16px; width: 56px; height: 56px; font-size: 1.6rem;">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <h1>Ubah Data Menu Promo Toko</h1>
        <p>Perbarui informasi harga hemat, stok ketersediaan, atau jam operasional tutup toko</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-box alert-error">
            <div class="alert-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="alert-content">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>?url=donation/update/<?= (int)$donation['id'] ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <!-- Nama Menu -->
        <div class="form-group">
            <label class="form-label" for="judul">
                <span>Nama / Menu Makanan <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-utensils input-icon"></i>
                <input 
                    type="text" 
                    id="judul" 
                    name="judul" 
                    class="form-control-3d" 
                    value="<?= e($donation['judul']) ?>" 
                    required 
                    minlength="5" 
                    maxlength="150"
                >
            </div>
        </div>

        <div class="form-row-2col">
            <!-- Kategori -->
            <div class="form-group">
                <label class="form-label" for="kategori">
                    <span>Kategori Menu <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-list input-icon"></i>
                    <select id="kategori" name="kategori" class="form-control-3d" required>
                        <option value="makanan_berat" <?= (($donation['kategori'] ?? '') === 'makanan_berat') ? 'selected' : '' ?>>Makanan Berat / Nasi</option>
                        <option value="snack_roti" <?= (($donation['kategori'] ?? '') === 'snack_roti') ? 'selected' : '' ?>>Roti, Bakery & Pastry</option>
                        <option value="katering_bento" <?= (($donation['kategori'] ?? '') === 'katering_bento') ? 'selected' : '' ?>>Bento & Rice Box</option>
                        <option value="buah_sayur" <?= (($donation['kategori'] ?? '') === 'buah_sayur') ? 'selected' : '' ?>>Buah & Sayuran</option>
                        <option value="minuman" <?= (($donation['kategori'] ?? '') === 'minuman') ? 'selected' : '' ?>>Minuman</option>
                    </select>
                </div>
            </div>

            <!-- Tipe Penawaran -->
            <div class="form-group">
                <label class="form-label" for="tipe_penawaran">
                    <span>Model Penawaran <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-tag input-icon"></i>
                    <select id="tipe_penawaran" name="tipe_penawaran" class="form-control-3d" required>
                        <option value="diskon_toko" <?= (($donation['tipe_penawaran'] ?? '') === 'diskon_toko') ? 'selected' : '' ?>>Diskon Khusus Jelang Tutup (Harga Murah)</option>
                        <option value="gratis_peduli" <?= (($donation['tipe_penawaran'] ?? '') === 'gratis_peduli') ? 'selected' : '' ?>>Gratis Peduli Rantau & Homeless (Rp 0)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="price_fields_wrapper" style="background: var(--bg-surface-muted); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 20px;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-money-bill-wave" style="color: var(--primary);"></i> Penyesuaian Harga Murah & Diskon
            </div>
            
            <div class="form-row-2col">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="harga_asli">
                        <span>Harga Normal Toko (Rp) <span style="color: var(--danger);">*</span></span>
                    </label>
                    <div class="input-group-3d">
                        <i class="fa-solid fa-rupiah-sign input-icon"></i>
                        <input 
                            type="number" 
                            id="harga_asli" 
                            name="harga_asli" 
                            class="form-control-3d" 
                            value="<?= (int)($donation['harga_asli'] ?? 0) ?>" 
                            min="0" 
                            step="500"
                        >
                    </div>
                </div>

                <div class="form-group" id="harga_diskon_group" style="margin-bottom: 0;">
                    <label class="form-label" for="harga_diskon">
                        <span>Harga Tebusan Hemat (Rp) <span style="color: var(--danger);">*</span></span>
                    </label>
                    <div class="input-group-3d">
                        <i class="fa-solid fa-bolt input-icon" style="color: var(--primary);"></i>
                        <input 
                            type="number" 
                            id="harga_diskon" 
                            name="harga_diskon" 
                            class="form-control-3d" 
                            value="<?= (int)($donation['harga_diskon'] ?? 0) ?>" 
                            min="0" 
                            step="500"
                        >
                    </div>
                </div>
            </div>

            <div id="discount_preview_note" style="margin-top: 12px; font-size: 0.88rem; color: #047857; font-weight: 700; background: #ecfdf5; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid #a7f3d0;">
                <!-- Updated by JS -->
            </div>
        </div>

        <div class="form-row-2col">
            <div class="form-group">
                <label class="form-label" for="jumlah">
                    <span>Jumlah Total Porsi <span style="color: var(--danger);">*</span></span>
                    <span class="hint">Sisa saat ini: <?= (int)$donation['sisa'] ?></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-cubes-stacked input-icon"></i>
                    <input 
                        type="number" 
                        id="jumlah" 
                        name="jumlah" 
                        class="form-control-3d" 
                        value="<?= (int)$donation['jumlah'] ?>" 
                        min="1" 
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="satuan">
                    <span>Satuan Kemasan <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-box input-icon"></i>
                    <select id="satuan" name="satuan" class="form-control-3d" required>
                        <option value="porsi" <?= ($donation['satuan'] === 'porsi') ? 'selected' : '' ?>>Porsi</option>
                        <option value="kotak" <?= ($donation['satuan'] === 'kotak') ? 'selected' : '' ?>>Kotak / Bento</option>
                        <option value="paket" <?= ($donation['satuan'] === 'paket') ? 'selected' : '' ?>>Paket</option>
                        <option value="bungkus" <?= ($donation['satuan'] === 'bungkus') ? 'selected' : '' ?>>Bungkus</option>
                        <option value="potong" <?= ($donation['satuan'] === 'potong') ? 'selected' : '' ?>>Potong</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row-2col">
            <div class="form-group">
                <label class="form-label" for="jam_tutup">
                    <span>Jam Tutup Toko / Waktu Ambil <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-door-closed input-icon"></i>
                    <input 
                        type="text" 
                        id="jam_tutup" 
                        name="jam_tutup" 
                        class="form-control-3d" 
                        value="<?= e($donation['jam_tutup'] ?? '21:00 WIB') ?>" 
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="expired_at">
                    <span>Batas Akhir Penjemputan di Kasir <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-regular fa-clock input-icon"></i>
                    <input 
                        type="datetime-local" 
                        id="expired_at" 
                        name="expired_at" 
                        class="form-control-3d" 
                        value="<?= date('Y-m-d\TH:i', strtotime($donation['expired_at'])) ?>" 
                        required
                    >
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="lokasi_ambil">
                <span>Alamat Toko & Meja Kasir Pengambilan <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-location-dot input-icon"></i>
                <input 
                    type="text" 
                    id="lokasi_ambil" 
                    name="lokasi_ambil" 
                    class="form-control-3d" 
                    value="<?= e($donation['lokasi_ambil']) ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="jaminan_kualitas">
                <span>Jaminan Kualitas & Higienitas <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-shield-heart input-icon"></i>
                <input 
                    type="text" 
                    id="jaminan_kualitas" 
                    name="jaminan_kualitas" 
                    class="form-control-3d" 
                    value="<?= e($donation['jaminan_kualitas'] ?? '100% Segar & Higienis Diproduksi Hari Ini') ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="deskripsi">
                <span>Keterangan Menu & Cara Penebusan</span>
            </label>
            <textarea 
                id="deskripsi" 
                name="deskripsi" 
                class="form-control-3d"
                rows="3"
            ><?= e($donation['deskripsi']) ?></textarea>
        </div>

        <!-- Current & New Photo Upload -->
        <div class="form-group">
            <label class="form-label">
                <span>Foto Menu Makanan</span>
                <span class="hint">Biarkan kosong jika tidak ingin mengubah foto</span>
            </label>

            <?php if (!empty($donation['foto']) && file_exists(UPLOAD_DIR . $donation['foto'])): ?>
                <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 14px; padding: 10px; background: var(--bg-surface-muted); border-radius: var(--radius-md);">
                    <img src="<?= APP_URL ?>/assets/uploads/<?= e($donation['foto']) ?>" alt="Foto Sekarang" style="width: 70px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Foto saat ini tersimpan di server</span>
                </div>
            <?php endif; ?>

            <label class="upload-dropzone-3d" for="foto-input">
                <i class="fa-solid fa-cloud-arrow-up upload-icon-3d"></i>
                <div style="font-weight: 700; color: var(--text-main); font-size: 1rem;">Ganti Foto (Opsional)</div>
                <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/webp" style="display: none;">
            </label>

            <div id="foto-preview-wrap" class="file-preview-wrap">
                <img id="foto-preview-img" src="" alt="Pratinjau Foto Baru">
            </div>
        </div>

        <div class="form-actions-row" style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-3d btn-3d-secondary" style="flex: 1;">
                Batal
            </a>
            <button type="submit" class="btn-3d btn-3d-primary btn-lg" style="flex: 2;">
                <i class="fa-solid fa-check"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipe_penawaran');
    const hargaAsliInput = document.getElementById('harga_asli');
    const hargaDiskonInput = document.getElementById('harga_diskon');
    const diskonGroup = document.getElementById('harga_diskon_group');
    const previewNote = document.getElementById('discount_preview_note');

    function updateDiscountCalculation() {
        const tipe = tipeSelect.value;
        if (tipe === 'gratis_peduli') {
            diskonGroup.style.display = 'none';
            previewNote.innerHTML = '<i class="fa-solid fa-gift"></i> Menu ini disediakan <strong>100% GRATIS (Rp 0)</strong> khusus untuk tunawisma & pejuang rantau yang sangat membutuhkan.';
            return;
        }

        diskonGroup.style.display = 'block';
        const asli = parseInt(hargaAsliInput.value) || 0;
        const diskon = parseInt(hargaDiskonInput.value) || 0;

        if (asli > 0 && diskon >= 0 && diskon <= asli) {
            const hemat = asli - diskon;
            const percent = Math.round((hemat / asli) * 100);
            previewNote.innerHTML = '<i class="fa-solid fa-sparkles"></i> Pembeli hemat sebesar <strong>' + percent + '%</strong> (Rp ' + hemat.toLocaleString('id-ID') + ' / porsi)!';
        } else if (diskon > asli) {
            previewNote.innerHTML = '<span style="color: #dc2626;"><i class="fa-solid fa-circle-exclamation"></i> Harga diskon tidak boleh melebihi harga normal.</span>';
        } else {
            previewNote.innerHTML = 'Masukkan harga normal dan harga diskon.';
        }
    }

    tipeSelect.addEventListener('change', updateDiscountCalculation);
    hargaAsliInput.addEventListener('input', updateDiscountCalculation);
    hargaDiskonInput.addEventListener('input', updateDiscountCalculation);
    updateDiscountCalculation();
});
</script>
