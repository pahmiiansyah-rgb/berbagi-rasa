        <?php if (!str_starts_with($currentUrl, 'donation/index')): ?>
            </div> <!-- /container -->
        <?php endif; ?>
    </main>

    <!-- Footer Cinematic Dark -->
    <footer class="footer-cinematic">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Info -->
                <div class="footer-brand">
                    <div class="brand-logo" style="margin-bottom: 14px;">
                        <div class="brand-icon" style="width: 32px; height: 32px; font-size: 1rem;">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <span class="brand-title" style="color: #ffffff; font-size: 1.25rem;">Berbagi Rasa<span class="brand-dot" style="color: #10b981;">.</span></span>
                    </div>
                    <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; max-width: 380px;">
                        Platform penyelamat makanan berkualitas prima menjelang tutup toko. Menghubungkan restoran dan bakery dengan anak kos dan pejuang nafkah untuk pangan lezat bergizi dengan harga murah.
                    </p>
                </div>

                <!-- Navigation Quick Links -->
                <div>
                    <h4 class="footer-heading">Eksplorasi</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>?url=donation/index">Katalog Menu Hemat</a></li>
                        <li><a href="<?= BASE_URL ?>?url=auth/login">Masuk Akun</a></li>
                        <li><a href="<?= BASE_URL ?>?url=auth/register">Daftar Mitra / Pengguna</a></li>
                    </ul>
                </div>

                <!-- Komunitas & Nilai -->
                <div>
                    <h4 class="footer-heading">Dukungan Rantau</h4>
                    <ul class="footer-links">
                        <li><a href="#">Untuk Mahasiswa Kos</a></li>
                        <li><a href="#">Subsidi Peduli Tunawisma</a></li>
                        <li><a href="#">Standar Higienitas Toko</a></li>
                    </ul>
                </div>

                <!-- Standar Kualitas -->
                <div>
                    <h4 class="footer-heading">100% Layak & Bermartabat</h4>
                    <div class="footer-disclaimer-box">
                        <i class="fa-solid fa-circle-check" style="color: #10b981; margin-right: 6px;"></i>
                        <strong>Bukan Sisa Piring:</strong> Makanan yang diposting adalah batch segar produksi hari ini yang belum habis saat jam tutup toko. Higienis, terjaga rasa & mutunya, ditebus dengan harga hemat secara bermartabat.
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Status -->
            <div class="footer-bottom">
                <div style="color: #64748b; font-size: 0.85rem;">
                    &copy; <?= date('Y') ?> <strong>Berbagi Rasa</strong>. Desain Sinematik & Standar Tinggi PHP MVC Native.
                </div>
                <div style="display: flex; gap: 16px; align-items: center; font-size: 0.85rem; color: #64748b;">
                    <span><i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> 100% Prepared Statements & CSRF Protected</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- App JavaScript (Root-Relative with Cache-Buster) -->
    <script src="<?= ASSET_URL ?>/assets/js/app.js?v=<?= filemtime(ROOT_DIR . '/assets/js/app.js') ?>"></script>
</body>
</html>
