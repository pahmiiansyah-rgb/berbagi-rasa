<?php
/**
 * View Masuk (Login) - Berbagi Rasa
 */
?>
<div class="form-card-3d" style="max-width: 480px;">
    <div style="display: flex; justify-content: center; margin-bottom: 12px;">
        <span style="font-size: 0.76rem; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); padding: 4px 14px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.02em;">
            <i class="fa-solid fa-shield-halved"></i> Akses Terproteksi &amp; Terenkripsi
        </span>
    </div>

    <div class="form-header">
        <div class="brand-icon-3d" style="margin: 0 auto 16px; width: 58px; height: 58px; font-size: 1.6rem;">
            <i class="fa-solid fa-right-to-bracket"></i>
        </div>
        <h1>Selamat Datang</h1>
        <p>Silakan masuk ke akun Berbagi Rasa Anda untuk melanjutkan</p>
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

    <form action="<?= BASE_URL ?>?url=auth/login" method="POST">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label class="form-label" for="email">
                <span>Alamat Email</span>
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
                    autofocus
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">
                <span>Kata Sandi</span>
                <span class="hint">Min. 6 karakter</span>
            </label>
            <div class="input-group-3d has-action">
                <i class="fa-solid fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control-3d" 
                    placeholder="Masukkan kata sandi..." 
                    required
                >
                <button type="button" class="btn-toggle-password" id="btnToggleLoginPass" title="Lihat/Sembunyikan Kata Sandi" aria-label="Toggle password visibility">
                    <i class="fa-solid fa-eye" id="eyeIconLogin"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-3d btn-3d-primary" style="margin-top: 10px;">
            <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
        </button>
    </form>

    <!-- Quick Demo Testing Autofill Buttons -->
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center;">
        <span style="font-size: 0.78rem; font-weight: 750; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 12px;">
            Demo Cepat (Klik untuk Mengisi Akun Pengujian):
        </span>
        <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="btn-demo-chip btn-demo-fill" data-email="bakery@berbagi.id" data-pass="demo123">
                <i class="fa-solid fa-store" style="color: #10b981;"></i> Mitra Bakery
            </button>
            <button type="button" class="btn-demo-chip btn-demo-fill" data-email="padang@berkah.id" data-pass="demo123">
                <i class="fa-solid fa-utensils" style="color: #f59e0b;"></i> Mitra Resto
            </button>
            <button type="button" class="btn-demo-chip btn-demo-fill" data-email="andi@mail.id" data-pass="demo123">
                <i class="fa-solid fa-user-graduate" style="color: #3b82f6;"></i> Anak Rantau
            </button>
            <button type="button" class="btn-demo-chip btn-demo-fill" data-email="admin@berbagirasa.id" data-pass="admin123">
                <i class="fa-solid fa-shield-halved" style="color: #a855f7;"></i> Admin
            </button>
        </div>
    </div>

    <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: #94a3b8;">
        Belum memiliki akun? <a href="<?= BASE_URL ?>?url=auth/register" style="color: #10b981; font-weight: 700; text-decoration: none;">Daftar Akun Baru &rarr;</a>
    </div>
</div>
