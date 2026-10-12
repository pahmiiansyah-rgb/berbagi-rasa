<?php
/**
 * View Tambah Menu Makanan Hemat Jelang Tutup Toko - Berbagi Rasa
 */
?>
<div class="form-card-3d">
    <div class="form-header">
        <div class="brand-icon-3d" style="margin: 0 auto 16px; width: 56px; height: 56px; font-size: 1.6rem;">
            <i class="fa-solid fa-store"></i>
        </div>
        <h1>Pasang Menu Hemat Jelang Tutup Toko</h1>
        <p>Jual makanan berkualitas prima dengan harga miring sebelum toko tutup. Bantu anak rantau & cegah pemborosan makanan!</p>
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

    <form action="<?= BASE_URL ?>?url=donation/store" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <!-- Nama Menu -->
        <div class="form-group">
            <label class="form-label" for="judul">
                <span>Nama / Menu Makanan <span style="color: var(--danger);">*</span></span>
                <span class="hint">5–150 huruf</span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-utensils input-icon"></i>
                <input 
                    type="text" 
                    id="judul" 
                    name="judul" 
                    class="form-control-3d" 
                    placeholder="Contoh: Paket Nasi Rendang Komplit & Sayur Kapau" 
                    value="<?= e(old('judul')) ?>" 
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
                        <option value="makanan_berat" <?= (old('kategori') === 'makanan_berat') ? 'selected' : '' ?>>Makanan Berat / Nasi</option>
                        <option value="snack_roti" <?= (old('kategori') === 'snack_roti') ? 'selected' : '' ?>>Roti, Bakery & Pastry</option>
                        <option value="katering_bento" <?= (old('kategori') === 'katering_bento') ? 'selected' : '' ?>>Bento & Rice Box</option>
                        <option value="buah_sayur" <?= (old('kategori') === 'buah_sayur') ? 'selected' : '' ?>>Buah & Sayuran</option>
                        <option value="minuman" <?= (old('kategori') === 'minuman') ? 'selected' : '' ?>>Minuman</option>
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
                        <option value="diskon_toko" <?= (old('tipe_penawaran') === 'diskon_toko') ? 'selected' : '' ?>>Diskon Khusus Jelang Tutup (Harga Murah)</option>
                        <option value="gratis_peduli" <?= (old('tipe_penawaran') === 'gratis_peduli') ? 'selected' : '' ?>>Gratis Peduli Rantau & Homeless (Rp 0)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="price_fields_wrapper" style="background: var(--bg-surface-muted); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 20px;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-money-bill-wave" style="color: var(--primary);"></i> Penentuan Harga Murah & Diskon
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
                            placeholder="Contoh: 26000" 
                            value="<?= e(old('harga_asli', '25000')) ?>" 
                            min="0" 
                            step="500"
                        >
                    </div>
                </div>

                <div class="form-group" id="harga_diskon_group" style="margin-bottom: 0;">
                    <label class="form-label" for="harga_diskon">
                        <span>Harga Tebusan Hemat (Rp) <span style="color: var(--danger);">*</span></span>
                        <span class="hint">Harga murah di kasir</span>
                    </label>
                    <div class="input-group-3d">
                        <i class="fa-solid fa-bolt input-icon" style="color: var(--primary);"></i>
                        <input 
                            type="number" 
                            id="harga_diskon" 
                            name="harga_diskon" 
                            class="form-control-3d" 
                            placeholder="Contoh: 6000" 
                            value="<?= e(old('harga_diskon', '6000')) ?>" 
                            min="0" 
                            step="500"
                        >
                    </div>
                </div>
            </div>

            <!-- Discount Live Summary -->
            <div id="discount_preview_note" style="margin-top: 12px; font-size: 0.88rem; color: #047857; font-weight: 700; background: #ecfdf5; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid #a7f3d0;">
                <i class="fa-solid fa-sparkles"></i> Pembeli hemat sebesar <strong>76%</strong> (Rp 19.000 / porsi)!
            </div>
        </div>

        <div class="form-row-2col">
            <div class="form-group">
                <label class="form-label" for="jumlah">
                    <span>Jumlah Porsi Tersedia <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d">
                    <i class="fa-solid fa-cubes-stacked input-icon"></i>
                    <input 
                        type="number" 
                        id="jumlah" 
                        name="jumlah" 
                        class="form-control-3d" 
                        placeholder="Contoh: 15" 
                        value="<?= e(old('jumlah', '10')) ?>" 
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
                        <option value="porsi" <?= (old('satuan') === 'porsi') ? 'selected' : '' ?>>Porsi</option>
                        <option value="kotak" <?= (old('satuan') === 'kotak') ? 'selected' : '' ?>>Kotak / Bento</option>
                        <option value="paket" <?= (old('satuan') === 'paket') ? 'selected' : '' ?>>Paket</option>
                        <option value="bungkus" <?= (old('satuan') === 'bungkus') ? 'selected' : '' ?>>Bungkus</option>
                        <option value="potong" <?= (old('satuan') === 'potong') ? 'selected' : '' ?>>Potong</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row-2col">
            <!-- Jam Tutup Toko -->
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
                        placeholder="Contoh: 21:00 WIB (Ambil 20:00 - 21:00)" 
                        value="<?= e(old('jam_tutup', '21:00 WIB')) ?>" 
                        required
                    >
                </div>
            </div>

            <!-- Batas Waktu Kadaluarsa / Sistem Expiry -->
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
                        value="<?= e(old('expired_at', date('Y-m-d\TH:i', strtotime('+4 hours')))) ?>" 
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
                    placeholder="Contoh: RM Minang Saiyo, Jl. Raya Kampus No. 12 (Meja Kasir Utama)" 
                    value="<?= e(old('lokasi_ambil')) ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="jaminan_kualitas">
                <span>Jaminan Kualitas & Higienitas Makanan <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-shield-heart input-icon"></i>
                <input 
                    type="text" 
                    id="jaminan_kualitas" 
                    name="jaminan_kualitas" 
                    class="form-control-3d" 
                    placeholder="Contoh: 100% Segar & Higienis, dimasak fresh siang hari dalam kemasan tertutup" 
                    value="<?= e(old('jaminan_kualitas', '100% Segar & Higienis Diproduksi Hari Ini')) ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="deskripsi">
                <span>Keterangan Menu & Cara Penebusan</span>
                <span class="hint">Opsional</span>
            </label>
            <textarea 
                id="deskripsi" 
                name="deskripsi" 
                class="form-control-3d" 
                rows="3"
                placeholder="Contoh: Menu baru dimasak siang tadi. Harap sebutkan kode klaim pemesanan di kasir sebelum jam tutup toko..."
            ><?= e(old('deskripsi')) ?></textarea>
        </div>

        <!-- 3D Upload Dropzone & Live Preview -->
        <div class="form-group">
            <label class="form-label">
                <span>Foto Menu Makanan <span style="color: var(--danger);">*</span></span>
                <span class="hint">JPG, PNG, WebP (Maks. 2MB)</span>
            </label>
            
            <label class="upload-dropzone-3d" for="foto-input">
                <i class="fa-solid fa-cloud-arrow-up upload-icon-3d"></i>
                <div style="font-weight: 700; color: var(--text-main); font-size: 1rem;">Klik untuk Memilih Foto Menu</div>
                <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">Tampilkan foto yang jelas agar pembeli hemat yakin dengan kualitasnya</div>
                <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/webp" style="display: none;" required>
            </label>

            <!-- Preview Image Wrap -->
            <div id="foto-preview-wrap" class="file-preview-wrap">
                <img id="foto-preview-img" src="" alt="Pratinjau Foto">
            </div>
        </div>

        <div class="form-actions-row" style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-3d btn-3d-secondary" style="flex: 1;">
                Batal
            </a>
            <button type="submit" class="btn-3d btn-3d-primary btn-lg" style="flex: 2;">
                <i class="fa-solid fa-store"></i> Terbitkan Menu Promo
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
