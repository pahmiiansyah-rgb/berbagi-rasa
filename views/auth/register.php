<?php
/**
 * View Registrasi Pengguna Baru - Berbagi Rasa
 */
?>
<div class="form-card-3d" style="max-width: 580px;">
    <div style="display: flex; justify-content: center; margin-bottom: 12px;">
        <span style="font-size: 0.76rem; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); padding: 4px 14px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.02em;">
            <i class="fa-solid fa-shield-halved"></i> Registrasi Terverifikasi &amp; Aman
        </span>
    </div>

    <div class="form-header">
        <div class="brand-icon-3d" style="margin: 0 auto 16px; width: 58px; height: 58px; font-size: 1.6rem;">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1>Daftar Akun Baru</h1>
        <p>Bergabung bersama gerakan penyelamatan makanan layak konsumsi</p>
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

    <form action="<?= BASE_URL ?>?url=auth/register" method="POST">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <!-- Pilih Peran Akun (Modern Interactive Radio Cards) -->
        <div class="form-group">
            <label class="form-label">
                <span>Pilih Peran Akun Anda <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="role-picker-grid">
                <label class="role-radio-card">
                    <input type="radio" name="role" value="penerima" <?= (old('role', 'penerima') === 'penerima') ? 'checked' : '' ?>>
                    <div class="role-card-label">
                        <i class="fa-solid fa-bag-shopping role-icon-3d"></i>
                        <div>
                            <strong style="display: block; font-size: 0.96rem; color: #ffffff; margin-bottom: 2px;">Penyelamat Makanan</strong>
                            <span style="font-size: 0.78rem; color: #94a3b8; line-height: 1.4; display: block;">Anak kos, mahasiswa rantau, pejuang nafkah berhemat</span>
                        </div>
                    </div>
                </label>

                <label class="role-radio-card">
                    <input type="radio" name="role" value="donatur" <?= (old('role') === 'donatur') ? 'checked' : '' ?>>
                    <div class="role-card-label">
                        <i class="fa-solid fa-store role-icon-3d"></i>
                        <div>
                            <strong style="display: block; font-size: 0.96rem; color: #ffffff; margin-bottom: 2px;">Mitra Toko &amp; Restoran</strong>
                            <span style="font-size: 0.78rem; color: #94a3b8; line-height: 1.4; display: block;">Bakery, cafe, resto yang menjual promo surplus closing</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="nama">
                <span>Nama Lengkap / Nama Tempat Usaha <span style="color: var(--danger);">*</span></span>
                <span class="hint">Min. 3 karakter</span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-user input-icon"></i>
                <input 
                    type="text" 
                    id="nama" 
                    name="nama" 
                    class="form-control-3d" 
                    placeholder="Contoh: Andi Pratama atau Toko Roti Berkah" 
                    value="<?= e(old('nama')) ?>" 
                    required 
                    minlength="3" 
                    maxlength="100"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">
                <span>Alamat Email Aktif <span style="color: var(--danger);">*</span></span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control-3d" 
                    placeholder="nama@email.com" 
                    value="<?= e(old('email')) ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-row-2col">
            <div class="form-group">
                <label class="form-label" for="password">
                    <span>Kata Sandi <span style="color: var(--danger);">*</span></span>
                    <span class="hint">Min. 6 digit</span>
                </label>
                <div class="input-group-3d has-action">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control-3d" 
                        placeholder="Minimal 6 karakter" 
                        required 
                        minlength="6"
                    >
                    <button type="button" class="btn-toggle-password" id="btnToggleRegPass" title="Lihat/Sembunyikan Kata Sandi">
                        <i class="fa-solid fa-eye" id="eyeIconReg"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">
                    <span>Ulangi Sandi <span style="color: var(--danger);">*</span></span>
                </label>
                <div class="input-group-3d has-action">
                    <i class="fa-solid fa-check-double input-icon"></i>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-control-3d" 
                        placeholder="Ketik ulang kata sandi" 
                        required 
                        minlength="6"
                    >
                    <button type="button" class="btn-toggle-password" id="btnToggleRegConfirmPass" title="Lihat/Sembunyikan Kata Sandi">
                        <i class="fa-solid fa-eye" id="eyeIconRegConfirm"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="telepon">
                <span>Nomor WhatsApp / Telepon</span>
                <span class="hint">Untuk koordinasi penjemputan</span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-phone input-icon"></i>
                <input 
                    type="text" 
                    id="telepon" 
                    name="telepon" 
                    class="form-control-3d" 
                    placeholder="081234567890" 
                    value="<?= e(old('telepon')) ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="alamat">
                <span>Alamat / Lokasi Stand</span>
            </label>
            <div class="input-group-3d">
                <i class="fa-solid fa-location-dot input-icon"></i>
                <input 
                    type="text" 
                    id="alamat" 
                    name="alamat" 
                    class="form-control-3d" 
                    placeholder="Gedung A Lantai 1, Depan Lift Kampus" 
                    value="<?= e(old('alamat')) ?>"
                >
            </div>
        </div>

        <button type="submit" class="btn-3d btn-3d-primary btn-block btn-lg" style="margin-top: 16px;">
            <i class="fa-solid fa-check"></i> Selesaikan Pendaftaran
        </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: var(--text-muted);">
        Sudah memiliki akun? <a href="<?= BASE_URL ?>?url=auth/login" style="color: var(--primary-dark); font-weight: 700; text-decoration: none;">Masuk ke Akun Anda</a>
    </div>
</div>
