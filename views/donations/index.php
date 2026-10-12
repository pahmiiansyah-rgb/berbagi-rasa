<?php
/**
 * View Landing Page Pengenalan Gerakan - Berbagi Rasa
 * Humanized AI Copywriting & Authentic Storytelling
 */
?>

<!-- 1. Hero Section (Shopify Dark Editorial Composition) -->
<section id="home" class="hero-shopify-section">
    <!-- Background Looping Video with Poster Fallback -->
    <video class="hero-video-bg" autoplay muted loop playsinline poster="<?= ASSET_URL ?>/assets/images/hero-cinematic.jpg">
        <source src="<?= ASSET_URL ?>/assets/videos/hero-bg.mp4" type="video/mp4">
    </video>

    <div class="hero-video-overlay"></div>

    <div class="hero-editorial-container">
        <div class="hero-editorial-content">
            <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 750; color: #34d399; background: rgba(16, 185, 129, 0.15); padding: 6px 14px; border-radius: 999px; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 16px;">
                <i class="fa-solid fa-leaf"></i> Gerakan Mengurangi Pangan Terbuang
            </div>

            <h1 class="hero-editorial-title">
                Makanan enak hari ini<br>
                tak perlu berakhir di tempat sampah.
            </h1>

            <p class="hero-editorial-subtitle">
                Setiap malam, banyak resto, kafe, dan toko roti menyisakan makanan yang baru dimasak tapi belum sempat terbeli. Berbagi Rasa menghubungkan pemilik usaha kuliner dengan siapa saja yang ingin makan enak dengan harga ramah kantong—atau diambil gratis bagi teman rantau yang sedang kesulitan.
            </p>

            <div class="hero-editorial-actions">
                <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-shopify-white">
                    <i class="fa-solid fa-bowl-food"></i> Lihat Menu Makanan Malam Ini
                </a>
                <a href="#cara-kerja" class="btn-shopify-pill-ghost">
                    <span class="play-icon-circle"><i class="fa-solid fa-circle-question"></i></span>
                    <span>Bagaimana Cara Kerjanya?</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 2. Ringkasan Dampak Nyata (Metrics Ribbon) -->
<section class="metrics-ribbon-section">
    <div class="container">
        <div class="metrics-grid-cinematic">
            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap emerald">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= formatRupiah($stats['total_rupiah_dihemat'] ?? 0) ?></span>
                    <span class="metric-desc">Biaya Makan yang Berhasil Dihemat</span>
                </div>
            </div>

            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap amber">
                    <i class="fa-solid fa-bowl-food"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= number_format($stats['total_makanan_terselamatkan'] ?? 0) ?> Porsi</span>
                    <span class="metric-desc">Makanan Segar Terselamatkan</span>
                </div>
            </div>

            <div class="metric-card-cinematic">
                <div class="metric-icon-wrap blue">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div class="metric-data">
                    <span class="metric-value"><?= number_format($stats['total_donatur'] ?? 0) ?> Tempat</span>
                    <span class="metric-desc">Resto, Warung, &amp; Bakery Bergabung</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Kenapa Berbagi Rasa Ada? (#tentang) -->
<section id="tentang" style="padding-top: 64px; padding-bottom: 56px;">
    <div class="container">
        <div style="text-align: center; max-width: 720px; margin: 0 auto 48px auto;">
            <span class="mission-badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                <i class="fa-solid fa-heart"></i> Cerita di Balik Berbagi Rasa
            </span>
            <h2 style="font-size: 2.1rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; margin: 14px 0 14px 0; line-height: 1.3;">
                Dua Masalah Nyata yang Bisa Kita Selesaikan Bersama
            </h2>
            <p style="color: #94a3b8; font-size: 1.02rem; line-height: 1.65; margin: 0;">
                Bukan karena makanannya tidak enak. Setiap hari menjelang tutup, pemilik usaha makanan di sekitar kita sering menghadapi surplus porsi. Di waktu yang sama, banyak teman kita yang harus berhitung ketat untuk sekadar makan malam layak.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 32px;">
            <!-- Sisi Pemilik Usaha -->
            <div style="background: #0f1420; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px;">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 750; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em;">Untuk Pemilik Resto &amp; Bakery</span>
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 8px 0 12px 0;">
                        Tutup toko dengan tenang, tanpa rasa bersalah membuang makanan.
                    </h3>
                    <p style="color: #94a3b8; font-size: 0.94rem; line-height: 1.65; margin: 0;">
                        Daripada rugi biaya bahan baku dan menumpuk limbah makanan, cukup buka website, masukkan jumlah porsi yang belum terjual, dan beri harga promo spesial. Dapurmu tetap bersih, modal tertutup, dan makananmu dinikmati orang yang tepat.
                    </p>
                </div>
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                    <a href="<?= BASE_URL ?>?url=auth/register" style="font-size: 0.9rem; font-weight: 700; color: #f59e0b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Daftarkan Usaha Anda Gratis <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Sisi Pembeli & Anak Rantau -->
            <div style="background: #0f1420; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 32px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px;">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 750; color: #10b981; text-transform: uppercase; letter-spacing: 0.05em;">Untuk Anak Kos &amp; Pekerja Harian</span>
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 8px 0 12px 0;">
                        Makan malam enak dan bergizi, tanpa bikin kantong kempes.
                    </h3>
                    <p style="color: #94a3b8; font-size: 0.94rem; line-height: 1.65; margin: 0;">
                        Kamu bisa beli makanan berkualitas dari resto dan toko roti favoritmu dengan potongan harga hingga 80%—bahkan gratis untuk porsi peduli pangan. Tinggal pesan lewat web, lalu ambil sendiri ke kasir toko sebelum mereka tutup.
                    </p>
                </div>
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                    <a href="<?= BASE_URL ?>?url=donation/catalog" style="font-size: 0.9rem; font-weight: 700; color: #10b981; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Buka Katalog Menu Tersedia <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Fitur Penting Platform (#fitur) -->
<section id="fitur" style="padding-top: 56px; padding-bottom: 64px; background: rgba(255, 255, 255, 0.015); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 48px auto;">
            <span class="mission-badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                <i class="fa-solid fa-sparkles"></i> Kemudahan untuk Semua
            </span>
            <h2 style="font-size: 2.1rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; margin: 14px 0 12px 0;">
                Fitur Penting di Berbagi Rasa
            </h2>
            <p style="color: #94a3b8; font-size: 1rem; line-height: 1.6; margin: 0;">
                Dirancang sederhana agar siapa saja bisa langsung menggunakan tanpa ribet, baik untuk pembeli maupun pemilik usaha.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <!-- Fitur 1: Closing Deals -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Diskon Closing Deals 50% – 80%</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Beli menu lezat dari bakery, kafe, dan resto langganan dengan harga miring menjelang jam operasional toko berakhir.
                </p>
            </div>

            <!-- Fitur 2: Gratis Peduli Rantau -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Porsi Gratis Peduli (Rp 0)</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Khusus teman perantau, anak kos berbekal pas-pasan, atau siapa pun yang sedang kesulitan makan malam hari ini.
                </p>
            </div>

            <!-- Fitur 3: Kode Ambil Cepat -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Kode Ambil Kasir Instan</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Reservasi langsung lewat website dan bawa kode ambil ke kasir toko. Tanpa perantara pihak ketiga dan bebas biaya admin.
                </p>
            </div>

            <!-- Fitur 4: Jaminan Batch Segar -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(168, 85, 247, 0.15); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Jaminan Porsi Segar Etalase</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Bukan makanan sisa piring orang lain. Semua menu adalah batch produksi segar hari yang sama dengan standar kebersihan terjaga.
                </p>
            </div>

            <!-- Fitur 5: Dashboard Mitra Usaha -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(236, 72, 153, 0.15); color: #f472b6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Panel Kelola untuk Pemilik Usaha</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Pasang sisa porsi hanya dalam 1 menit lewat handphone, setujui pesanan masuk, dan pantau biaya modal yang terselamatkan.
                </p>
            </div>

            <!-- Fitur 6: Penjemputan Terdekat -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 165, 233, 0.15); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h4 style="font-size: 1.12rem; font-weight: 800; color: #ffffff; margin: 0;">Saring Menu Berdasarkan Wilayah</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Cari toko atau warung terdekat dari tempat tinggal atau kostmu agar penjemputan sebelum toko tutup semakin praktis.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 5. Cara Kerja Sederhana (#cara-kerja) -->
<section id="cara-kerja" style="padding-top: 56px; padding-bottom: 64px;">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 48px auto;">
            <span class="mission-badge" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25);">
                <i class="fa-solid fa-list-check"></i> Alur Pemesanan
            </span>
            <h2 style="font-size: 2.1rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; margin: 14px 0 12px 0;">
                Tiga Langkah Praktis Sebelum Toko Tutup
            </h2>
            <p style="color: #94a3b8; font-size: 1rem; line-height: 1.6; margin: 0;">
                Tanpa perantara, tanpa antrean panjang. Semua proses dilakukan langsung antara kamu dan kasir toko.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <!-- Langkah 1 -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 28px; position: relative;">
                <div style="font-size: 2.4rem; font-weight: 900; color: rgba(255, 255, 255, 0.08); position: absolute; top: 20px; right: 24px;">01</div>
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; margin-bottom: 18px;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Toko Pasang Menu Jelang Tutup</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Menjelang sore atau malam hari (sekitar pukul 17.00 - 21.00 WIB), toko memasukkan sisa porsi segar di etalase yang belum terjual dengan potongan harga besar atau gratis.
                </p>
            </div>

            <!-- Langkah 2 -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 28px; position: relative;">
                <div style="font-size: 2.4rem; font-weight: 900; color: rgba(255, 255, 255, 0.08); position: absolute; top: 20px; right: 24px;">02</div>
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; margin-bottom: 18px;">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Kamu Pesan Lewat Web</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Buka katalog, pilih makanan di lokasi terdekatmu, dan tentukan berapa porsi yang ingin ditebus. Setelah toko mengonfirmasi, kamu akan menerima kode ambil kasir.
                </p>
            </div>

            <!-- Langkah 3 -->
            <div style="background: #0d121c; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 28px; position: relative;">
                <div style="font-size: 2.4rem; font-weight: 900; color: rgba(255, 255, 255, 0.08); position: absolute; top: 20px; right: 24px;">03</div>
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; margin-bottom: 18px;">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Tebus Langsung di Kasir</h4>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Datang ke toko sebelum jam operasional tutup. Sebutkan kode ambil ke kasir, bayar sesuai nominal hemat (atau Rp 0 untuk menu peduli), dan nikmati makanan hangatmu.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 5. Jaminan Kelayakan & Kualitas (#kualitas) -->
<section id="kualitas" style="padding-top: 64px; padding-bottom: 56px;">
    <div class="container">
        <div style="background: linear-gradient(135deg, #0e1523 0%, #0a0e17 100%); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 40px; box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 36px; align-items: center;">
                <div>
                    <span class="mission-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 12px;">
                        <i class="fa-solid fa-shield-halved"></i> Jaminan Mutu
                    </span>
                    <h2 style="font-size: 1.95rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; line-height: 1.3; margin: 10px 0 14px 0;">
                        Bukan Makanan Sisa.<br>Ini Porsi Segar Produksi Hari Ini.
                    </h2>
                    <p style="color: #94a3b8; font-size: 0.98rem; line-height: 1.65; margin: 0 0 20px 0;">
                        Masih banyak yang salah paham mengira makanan closing deals adalah sisa santapan di meja. Di Berbagi Rasa, standarnya sangat jelas:
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 1.1rem; margin-top: 3px;"></i>
                            <div>
                                <strong style="color: #ffffff; font-size: 0.95rem; display: block;">100% Porsi Utuh dari Etalase</strong>
                                <span style="color: #94a3b8; font-size: 0.88rem;">Makanan dimasak atau dipanggang pada hari yang sama, bukan makanan bekas orang lain.</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 1.1rem; margin-top: 3px;"></i>
                            <div>
                                <strong style="color: #ffffff; font-size: 0.95rem; display: block;">Kemasan Bersih &amp; Higienis</strong>
                                <span style="color: #94a3b8; font-size: 0.88rem;">Dikemas layak dengan standar kebersihan masing-masing toko mitra.</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 1.1rem; margin-top: 3px;"></i>
                            <div>
                                <strong style="color: #ffffff; font-size: 0.95rem; display: block;">Bermartabat Tanpa Rasa Canggung</strong>
                                <span style="color: #94a3b8; font-size: 0.88rem;">Kamu adalah penyelamat pangan. Mengambil makanan hemat adalah tindakan bijak dan patut dibanggakan.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Box Ajak Mulai -->
                <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 28px; text-align: center;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); color: #10b981; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 16px;">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">
                        Ingin Tahu Menu yang Tersedia Malam Ini?
                    </h3>
                    <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 22px;">
                        Buka katalog sekarang untuk melihat toko mana saja di sekitarmu yang sedang membuka tebusan promo jelang tutup.
                    </p>
                    <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-shopify-white" style="width: 100%; justify-content: center; padding: 14px 20px;">
                        Buka Katalog Makanan Siap Tebus
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. Pertanyaan yang Sering Muncul (FAQ Singkat & Jujur) -->
<section style="padding-top: 48px; padding-bottom: 64px;">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto 36px auto;">
            <h2 style="font-size: 1.85rem; font-weight: 850; color: #ffffff; letter-spacing: -0.02em;">
                Pertanyaan yang Sering Ditanyakan
            </h2>
            <p style="color: #94a3b8; font-size: 0.96rem; margin-top: 8px;">
                Semua yang perlu kamu ketahui sebelum memesan atau bergabung.
            </p>
        </div>

        <div style="max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
            <div style="background: #0f1420; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 22px 24px;">
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">
                    Kapan waktu terbaik untuk mengecek menu di Berbagi Rasa?
                </h4>
                <p style="color: #94a3b8; font-size: 0.92rem; line-height: 1.65; margin: 0;">
                    Umumnya antara pukul 17.00 hingga 21.30 WIB. Di jam-jam inilah sebagian besar resto, bakery, dan kafe mulai menghitung sisa porsi yang belum terjual dan memasang penawaran closing deals di website.
                </p>
            </div>

            <div style="background: #0f1420; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 22px 24px;">
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">
                    Siapa saja yang boleh memesan menu di sini?
                </h4>
                <p style="color: #94a3b8; font-size: 0.92rem; line-height: 1.65; margin: 0;">
                    Siapa saja boleh memesan menu promo diskon toko. Semakin banyak makanan yang dibeli, semakin sedikit makanan yang terbuang. Khusus menu gratis peduli (Rp 0), kami memohon kebijaksanaan pengguna agar diprioritaskan bagi teman perantau, mahasiswa berbekal pas-pasan, atau saudara kita yang benar-benar membutuhkan makan malam hari ini.
                </p>
            </div>

            <div style="background: #0f1420; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 22px 24px;">
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">
                    Apakah ada biaya admin atau biaya langganan?
                </h4>
                <p style="color: #94a3b8; font-size: 0.92rem; line-height: 1.65; margin: 0;">
                    Tidak ada sama sekali. Penggunaan platform Berbagi Rasa 100% gratis, baik untuk pembeli maupun untuk pemilik toko yang ingin mendaftar.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 7. Call To Action Mitra Toko (#mitra) -->
<section id="mitra" style="padding-top: 32px; padding-bottom: 72px;">
    <div class="container">
        <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 24px; padding: 48px 32px; text-align: center;">
            <div style="max-width: 620px; margin: 0 auto;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h2 style="font-size: 2rem; font-weight: 850; color: #ffffff; letter-spacing: -0.025em; line-height: 1.3; margin-bottom: 12px;">
                    Punya Usaha Makanan atau Toko Roti?
                </h2>
                <p style="color: #94a3b8; font-size: 1rem; line-height: 1.65; margin-bottom: 28px;">
                    Mari berkolaborasi. Daftarkan usaha kulinermu sekarang dan mulai pasang porsi surplus sebelum toko tutup. Kurangi kerugian, jaga lingkungan, dan hadirkan makan malam bermakna bagi warga sekitar.
                </p>

                <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>?url=auth/register" class="btn-shopify-white" style="padding: 13px 26px;">
                            <i class="fa-solid fa-circle-plus"></i> Daftarkan Toko Anda Gratis
                        </a>
                        <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-shopify-pill-ghost" style="padding: 13px 24px;">
                            Buka Katalog Menu
                        </a>
                    <?php elseif (($_SESSION['role'] ?? '') === 'donatur'): ?>
                        <a href="<?= BASE_URL ?>?url=donation/create" class="btn-shopify-white" style="padding: 13px 26px;">
                            <i class="fa-solid fa-plus"></i> + Pasang Menu Promo Malam Ini
                        </a>
                        <a href="<?= BASE_URL ?>?url=donation/mine" class="btn-shopify-pill-ghost" style="padding: 13px 24px;">
                            Buka Etalase Toko
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>?url=donation/catalog" class="btn-shopify-white" style="padding: 13px 26px;">
                            <i class="fa-solid fa-bowl-food"></i> Buka Katalog Makanan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
