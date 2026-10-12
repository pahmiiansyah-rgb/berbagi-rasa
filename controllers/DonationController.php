<?php
/**
 * Controller Menu Makanan Closing Deals (Penyelamatan Makanan Murah Jelang Tutup Toko) - Berbagi Rasa
 */

class DonationController {
    private Donation $donationModel;
    private ClaimRequest $requestModel;
    private StatusLog $logModel;

    public function __construct() {
        $this->donationModel = new Donation();
        $this->requestModel  = new ClaimRequest();
        $this->logModel      = new StatusLog();
    }

    /**
     * Halaman Utama / Landing Page Pengenalan Gerakan Berbagi Rasa
     */
    public function index(): void {
        // Jika ada filter atau pencarian dari luar, arahkan ke katalog
        if (!empty($_GET['q']) || !empty($_GET['lokasi']) || !empty($_GET['harga']) || !empty($_GET['kategori'])) {
            $this->catalog();
            return;
        }

        $stats = $this->donationModel->getStats();

        view('donations/index', [
            'pageTitle' => 'Berbagi Rasa — Makanan Enak Ramah Kantong Sebelum Toko Tutup',
            'stats'     => $stats,
        ]);
    }

    /**
     * Halaman Katalog Makanan Siap Tebus
     */
    public function catalog(): void {
        $keyword     = trim($_GET['q'] ?? '');
        $lokasi      = trim($_GET['lokasi'] ?? '');
        $kategori    = trim($_GET['kategori'] ?? '');
        $filterHarga = trim($_GET['harga'] ?? '');
        $page        = max(1, (int)($_GET['page'] ?? 1));
        $limit       = 12;
        $offset      = ($page - 1) * $limit;

        $totalItems = $this->donationModel->countAvailable($keyword, $lokasi, $kategori, $filterHarga);
        $totalPages = (int)ceil($totalItems / $limit);
        $donations  = $this->donationModel->allAvailable($keyword, $lokasi, $kategori, $filterHarga, $limit, $offset);
        $locations  = $this->donationModel->distinctLocations();
        $stats      = $this->donationModel->getStats();

        view('donations/catalog', [
            'pageTitle'   => 'Katalog Menu Siap Tebus Jelang Tutup Toko - Berbagi Rasa',
            'donations'   => $donations,
            'keyword'     => $keyword,
            'lokasi'      => $lokasi,
            'kategori'    => $kategori,
            'filterHarga' => $filterHarga,
            'locations'   => $locations,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalItems'  => $totalItems,
            'stats'       => $stats,
        ]);
    }

    /**
     * Halaman Detail Makanan & Formulir Reservasi/Penebusan
     */
    public function show($id = null): void {
        $donationId = (int)$id;
        if ($donationId <= 0) {
            redirect(BASE_URL . '?url=donation/index');
        }

        $donation = $this->donationModel->findById($donationId);
        if (!$donation) {
            setFlash('error', 'Menu makanan yang Anda cari tidak ditemukan atau telah ditutup.');
            redirect(BASE_URL . '?url=donation/index');
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $isMyDonation = ($userId > 0 && ((int)$donation['user_id'] === $userId || $isAdmin));
        
        $myActiveRequest = null;
        if ($userId > 0 && !$isMyDonation) {
            $myActiveRequest = $this->requestModel->findActiveByUserAndDonation($userId, $donationId);
        }

        $logs = $this->logModel->forDonation($donationId);

        view('donations/show', [
            'pageTitle'       => e($donation['judul']) . ' - Berbagi Rasa',
            'donation'        => $donation,
            'isMyDonation'    => $isMyDonation,
            'isAdmin'         => $isAdmin,
            'myActiveRequest' => $myActiveRequest,
            'logs'            => $logs,
        ]);
    }

    /**
     * Form Tambah Menu Hemat Jelang Tutup (Hanya untuk Mitra Toko/Donatur)
     */
    public function create(): void {
        requireRole('donatur');

        view('donations/create', [
            'pageTitle' => 'Pasang Menu Murah Jelang Tutup Toko - Berbagi Rasa',
            'errors'    => [],
        ]);
    }

    /**
     * Menyimpan Penawaran Makanan Baru + Upload Foto Aman
     */
    public function store(): void {
        requireRole('donatur');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?url=donation/create');
        }

        if (!csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Token keamanan CSRF tidak valid. Silakan ulangi.');
            redirect(BASE_URL . '?url=donation/create');
        }

        $judul            = trim($_POST['judul'] ?? '');
        $deskripsi        = trim($_POST['deskripsi'] ?? '');
        $jumlah           = (int)($_POST['jumlah'] ?? 0);
        $satuan           = trim($_POST['satuan'] ?? 'porsi');
        $kategori         = trim($_POST['kategori'] ?? 'makanan_berat');
        $tipe_penawaran   = trim($_POST['tipe_penawaran'] ?? 'diskon_toko');
        $harga_asli       = max(0, (int)($_POST['harga_asli'] ?? 0));
        $harga_diskon     = ($tipe_penawaran === 'gratis_peduli') ? 0 : max(0, (int)($_POST['harga_diskon'] ?? 0));
        $jam_tutup        = trim($_POST['jam_tutup'] ?? '21:00 WIB');
        $jaminan_kualitas = trim($_POST['jaminan_kualitas'] ?? '100% Segar & Higienis Diproduksi Hari Ini');
        $lokasi_ambil     = trim($_POST['lokasi_ambil'] ?? '');
        $expired_at       = trim($_POST['expired_at'] ?? '');

        setOld([
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'jumlah'           => $jumlah,
            'satuan'           => $satuan,
            'kategori'         => $kategori,
            'tipe_penawaran'   => $tipe_penawaran,
            'harga_asli'       => $harga_asli,
            'harga_diskon'     => $harga_diskon,
            'jam_tutup'        => $jam_tutup,
            'jaminan_kualitas' => $jaminan_kualitas,
            'lokasi_ambil'     => $lokasi_ambil,
            'expired_at'       => $expired_at,
        ]);

        $errors = [];

        // Validasi Field
        if (mb_strlen($judul) < 5 || mb_strlen($judul) > 150) {
            $errors[] = 'Nama/judul menu wajib diisi antara 5 hingga 150 karakter.';
        }

        if ($jumlah <= 0) {
            $errors[] = 'Jumlah porsi makanan harus berupa angka lebih dari 0.';
        }

        if (!in_array($satuan, ['porsi', 'kotak', 'paket', 'bungkus', 'potong'], true)) {
            $errors[] = 'Pilihan satuan makanan tidak valid.';
        }

        if ($tipe_penawaran === 'diskon_toko') {
            if ($harga_asli <= 0) {
                $errors[] = 'Harga normal toko wajib diisi lebih dari Rp 0 untuk promo diskon.';
            }
            if ($harga_diskon > $harga_asli) {
                $errors[] = 'Harga murah/diskon tidak boleh melebihi harga normal toko.';
            }
        }

        if (empty($jam_tutup)) {
            $errors[] = 'Jam operasional tutup toko & jendela waktu penjemputan wajib diisi.';
        }

        if (empty($jaminan_kualitas)) {
            $errors[] = 'Keterangan jaminan kualitas & higienitas makanan wajib diisi.';
        }

        if (empty($lokasi_ambil)) {
            $errors[] = 'Lokasi penjemputan makanan (alamat toko & meja kasir) wajib diisi dengan jelas.';
        }

        if (empty($expired_at)) {
            $errors[] = 'Batas akhir waktu penjemputan malam ini wajib ditentukan.';
        } else {
            $expiredTimestamp = strtotime($expired_at);
            if (!$expiredTimestamp || $expiredTimestamp <= time()) {
                $errors[] = 'Batas waktu penjemputan harus berada di waktu yang akan datang (malam ini atau besok).';
            }
        }

        // Upload Foto
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Foto makanan wajib diunggah agar calon pembeli hemat/perantau dapat melihat kualitas makanan.';
        }

        if (!empty($errors)) {
            view('donations/create', [
                'pageTitle' => 'Pasang Menu Murah Jelang Tutup Toko - Berbagi Rasa',
                'errors'    => $errors,
            ]);
            return;
        }

        // Upload Foto dengan 7 lapis keamanan
        $uploadResult = handleUpload($_FILES['foto']);
        if (!$uploadResult['ok']) {
            $errors[] = $uploadResult['error'];
            view('donations/create', [
                'pageTitle' => 'Pasang Menu Murah Jelang Tutup Toko - Berbagi Rasa',
                'errors'    => $errors,
            ]);
            return;
        }

        $userId = (int)$_SESSION['user_id'];

        $donationId = $this->donationModel->create([
            'user_id'          => $userId,
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'jumlah'           => $jumlah,
            'satuan'           => $satuan,
            'kategori'         => $kategori,
            'harga_asli'       => $harga_asli,
            'harga_diskon'     => $harga_diskon,
            'jam_tutup'        => $jam_tutup,
            'tipe_penawaran'   => $tipe_penawaran,
            'jaminan_kualitas' => $jaminan_kualitas,
            'lokasi_ambil'     => $lokasi_ambil,
            'foto'             => $uploadResult['filename'],
            'expired_at'       => date('Y-m-d H:i:s', strtotime($expired_at)),
        ]);

        $hematPersen = ($harga_asli > 0) ? round((($harga_asli - $harga_diskon) / $harga_asli) * 100) : 0;
        $infoHarga = ($tipe_penawaran === 'gratis_peduli' || $harga_diskon === 0) 
            ? 'Gratis Peduli Rantau/Homeless' 
            : "Rp " . number_format($harga_diskon, 0, ',', '.') . " (Hemat {$hematPersen}%)";

        // Catat jejak awal di status_logs
        $this->logModel->log(
            $donationId,
            null,
            null,
            'tersedia',
            $userId,
            "Menu hemat jelang tutup toko diterbitkan: {$jumlah} {$satuan} @ {$infoHarga}."
        );

        clearOld();
        setFlash('success', 'Menu makanan hemat jelang tutup toko berhasil dipublikasikan ke katalog!');
        redirect(BASE_URL . '?url=donation/mine');
    }

    /**
     * Form Edit Menu Makanan
     */
    public function edit($id = null): void {
        requireLogin();
        $donationId = (int)$id;
        $donation = $this->donationModel->findById($donationId);

        if (!$donation) {
            setFlash('error', 'Menu makanan tidak ditemukan.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        if (!isOwner((int)$donation['user_id']) && !$isAdmin) {
            setFlash('error', 'Akses ditolak. Anda bukan pemilik menu ini.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        if (!$isAdmin && ($donation['status'] === 'selesai' || $donation['status'] === 'kedaluwarsa')) {
            setFlash('warning', 'Menu yang sudah selesai atau kedaluwarsa tidak dapat diubah.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        view('donations/edit', [
            'pageTitle' => 'Ubah Menu Makanan Hemat - Berbagi Rasa',
            'donation'  => $donation,
            'errors'    => [],
        ]);
    }

    /**
     * Update Data Menu Makanan
     */
    public function update($id = null): void {
        requireLogin();
        $donationId = (int)$id;
        $donation = $this->donationModel->findById($donationId);
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');

        if (!$donation || (!isOwner((int)$donation['user_id']) && !$isAdmin)) {
            setFlash('error', 'Menu makanan tidak ditemukan atau akses ditolak.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?url=donation/edit/' . $donationId);
        }

        if (!csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Token CSRF tidak valid.');
            redirect(BASE_URL . '?url=donation/edit/' . $donationId);
        }

        $judul            = trim($_POST['judul'] ?? '');
        $deskripsi        = trim($_POST['deskripsi'] ?? '');
        $jumlah           = (int)($_POST['jumlah'] ?? $donation['jumlah']);
        $satuan           = trim($_POST['satuan'] ?? $donation['satuan']);
        $kategori         = trim($_POST['kategori'] ?? ($donation['kategori'] ?? 'makanan_berat'));
        $tipe_penawaran   = trim($_POST['tipe_penawaran'] ?? ($donation['tipe_penawaran'] ?? 'diskon_toko'));
        $harga_asli       = max(0, (int)($_POST['harga_asli'] ?? ($donation['harga_asli'] ?? 0)));
        $harga_diskon     = ($tipe_penawaran === 'gratis_peduli') ? 0 : max(0, (int)($_POST['harga_diskon'] ?? ($donation['harga_diskon'] ?? 0)));
        $jam_tutup        = trim($_POST['jam_tutup'] ?? ($donation['jam_tutup'] ?? '21:00 WIB'));
        $jaminan_kualitas = trim($_POST['jaminan_kualitas'] ?? ($donation['jaminan_kualitas'] ?? '100% Segar & Higienis'));
        $lokasi_ambil     = trim($_POST['lokasi_ambil'] ?? '');
        $expired_at       = trim($_POST['expired_at'] ?? '');

        $errors = [];

        if (mb_strlen($judul) < 5 || mb_strlen($judul) > 150) {
            $errors[] = 'Judul menu wajib diisi antara 5 hingga 150 karakter.';
        }

        $alreadyClaimed = (int)$donation['jumlah'] - (int)$donation['sisa'];
        if ($jumlah < $alreadyClaimed) {
            $errors[] = "Jumlah total tidak boleh kurang dari porsi yang sudah disetujui ({$alreadyClaimed} {$satuan}).";
        }

        if ($tipe_penawaran === 'diskon_toko') {
            if ($harga_asli <= 0) {
                $errors[] = 'Harga normal toko wajib diisi lebih dari Rp 0.';
            }
            if ($harga_diskon > $harga_asli) {
                $errors[] = 'Harga diskon tidak boleh melebihi harga normal.';
            }
        }

        if (empty($lokasi_ambil)) {
            $errors[] = 'Lokasi penjemputan wajib diisi.';
        }

        $expiredTimestamp = strtotime($expired_at);
        if (!$expiredTimestamp || $expiredTimestamp <= time()) {
            $errors[] = 'Batas waktu penjemputan harus berada di masa depan.';
        }

        $newPhotoFilename = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = handleUpload($_FILES['foto']);
            if (!$uploadResult['ok']) {
                $errors[] = $uploadResult['error'];
            } else {
                $newPhotoFilename = $uploadResult['filename'];
            }
        }

        if (!empty($errors)) {
            view('donations/edit', [
                'pageTitle' => 'Ubah Menu Makanan Hemat - Berbagi Rasa',
                'donation'  => array_merge($donation, [
                    'judul'            => $judul,
                    'deskripsi'        => $deskripsi,
                    'jumlah'           => $jumlah,
                    'satuan'           => $satuan,
                    'kategori'         => $kategori,
                    'tipe_penawaran'   => $tipe_penawaran,
                    'harga_asli'       => $harga_asli,
                    'harga_diskon'     => $harga_diskon,
                    'jam_tutup'        => $jam_tutup,
                    'jaminan_kualitas' => $jaminan_kualitas,
                    'lokasi_ambil'     => $lokasi_ambil,
                    'expired_at'       => $expired_at,
                ]),
                'errors'    => $errors,
            ]);
            return;
        }

        $selisih = $jumlah - (int)$donation['jumlah'];
        $newSisa = max(0, (int)$donation['sisa'] + $selisih);

        $updateData = [
            'judul'            => $judul,
            'deskripsi'        => $deskripsi,
            'jumlah'           => $jumlah,
            'sisa'             => $newSisa,
            'satuan'           => $satuan,
            'kategori'         => $kategori,
            'tipe_penawaran'   => $tipe_penawaran,
            'harga_asli'       => $harga_asli,
            'harga_diskon'     => $harga_diskon,
            'jam_tutup'        => $jam_tutup,
            'jaminan_kualitas' => $jaminan_kualitas,
            'lokasi_ambil'     => $lokasi_ambil,
            'expired_at'       => date('Y-m-d H:i:s', $expiredTimestamp),
        ];

        if ($newPhotoFilename !== null) {
            $updateData['foto'] = $newPhotoFilename;
            if (!empty($donation['foto'])) {
                $oldPath = UPLOAD_DIR . $donation['foto'];
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $this->donationModel->update($donationId, $updateData);

        $this->logModel->log(
            $donationId,
            null,
            $donation['status'],
            $donation['status'],
            (int)$_SESSION['user_id'],
            "Data menu hemat diperbarui oleh toko."
        );

        setFlash('success', 'Data menu makanan hemat berhasil diperbarui.');
        redirect(BASE_URL . '?url=donation/mine');
    }

    /**
     * Hapus Menu Makanan
     */
    public function destroy($id = null): void {
        requireLogin();
        $donationId = (int)$id;
        $donation = $this->donationModel->findById($donationId);

        if (!$donation) {
            setFlash('error', 'Menu makanan tidak ditemukan.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        if (!isOwner((int)$donation['user_id']) && !$isAdmin) {
            setFlash('error', 'Akses ditolak.');
            redirect(BASE_URL . '?url=donation/mine');
        }

        $activeRequests = $this->requestModel->findActiveByDonation($donationId);
        if (!empty($activeRequests)) {
            if ($isAdmin) {
                // Admin berhak penuh: batalkan pesanan aktif secara otomatis dengan catatan audit
                foreach ($activeRequests as $req) {
                    $this->requestModel->updateStatus((int)$req['id'], 'dibatalkan');
                    $this->logModel->log(
                        $donationId,
                        (int)$req['id'],
                        $req['status'],
                        'dibatalkan',
                        (int)$_SESSION['user_id'],
                        "Pesanan dibatalkan otomatis karena menu dihapus oleh Administrator."
                    );
                }
            } else {
                setFlash('error', 'Menu ini tidak dapat dihapus karena masih ada pesanan aktif (menunggu atau disetujui). Hubungi Administrator jika perlu pembatalan khusus.');
                redirect(BASE_URL . '?url=donation/mine');
            }
        }

        if (!empty($donation['foto'])) {
            $fotoPath = UPLOAD_DIR . $donation['foto'];
            if (file_exists($fotoPath) && is_file($fotoPath)) {
                @unlink($fotoPath);
            }
        }

        $this->donationModel->delete($donationId);
        $roleLabel = $isAdmin ? 'Administrator' : 'pemilik toko';
        setFlash('success', "Menu makanan '{$donation['judul']}' berhasil dihapus dari sistem oleh {$roleLabel}.");
        redirect(BASE_URL . '?url=donation/mine');
    }

    /**
     * Halaman Etalase Toko Saya / Kelola Semua Menu (Admin)
     */
    public function mine(): void {
        requireRole('donatur');
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $userId  = (int)$_SESSION['user_id'];

        if ($isAdmin) {
            $donations = $this->donationModel->allForAdmin();
            $pageTitle = 'Kelola Semua Menu Toko (Admin) - Berbagi Rasa';
        } else {
            $donations = $this->donationModel->myByUser($userId);
            $pageTitle = 'Etalase Toko Saya - Berbagi Rasa';
        }

        view('donations/mine', [
            'pageTitle' => $pageTitle,
            'donations' => $donations,
            'isAdmin'   => $isAdmin,
        ]);
    }
}
