<?php
/**
 * Controller Permintaan Donasi (Claim Request & Status Flow) - Berbagi Rasa
 */

class RequestController {
    private ClaimRequest $requestModel;
    private Donation $donationModel;
    private StatusLog $logModel;
    private PDO $db;

    public function __construct() {
        $this->requestModel  = new ClaimRequest();
        $this->donationModel = new Donation();
        $this->logModel      = new StatusLog();
        $this->db            = Database::get();
    }

    /**
     * Mengajukan Permintaan Donasi Makanan Baru
     */
    public function store(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?url=donation/index');
        }

        if (!csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Token keamanan tidak valid. Silakan coba kembali.');
            redirect(BASE_URL . '?url=donation/index');
        }

        $userId       = (int)$_SESSION['user_id'];
        $donationId   = (int)($_POST['donation_id'] ?? 0);
        $jumlahMinta  = (int)($_POST['jumlah_minta'] ?? 0);
        $catatan      = trim($_POST['catatan'] ?? '');

        $donation = $this->donationModel->findById($donationId);

        if (!$donation) {
            setFlash('error', 'Donasi yang Anda minta tidak ditemukan.');
            redirect(BASE_URL . '?url=donation/index');
        }

        // 1. Penolakan: Tidak bisa meminta donasi milik sendiri
        if ((int)$donation['user_id'] === $userId) {
            setFlash('error', 'Anda tidak dapat mengajukan permintaan untuk donasi milik Anda sendiri.');
            redirect(BASE_URL . '?url=donation/show/' . $donationId);
        }

        // 2. Penolakan: Donasi tidak tersedia atau sudah kedaluwarsa
        if ($donation['status'] !== 'tersedia' || strtotime($donation['expired_at']) <= time()) {
            setFlash('error', 'Maaf, donasi makanan ini sudah tidak tersedia atau telah melewati batas waktu.');
            redirect(BASE_URL . '?url=donation/show/' . $donationId);
        }

        // 3. Penolakan: Jumlah minta di luar batas sisa
        if ($jumlahMinta <= 0) {
            setFlash('error', 'Jumlah porsi yang diminta harus lebih dari 0.');
            redirect(BASE_URL . '?url=donation/show/' . $donationId);
        }

        if ($jumlahMinta > (int)$donation['sisa']) {
            setFlash('error', "Jumlah yang diminta ({$jumlahMinta} {$donation['satuan']}) melebihi sisa yang tersedia ({$donation['sisa']} {$donation['satuan']}).");
            redirect(BASE_URL . '?url=donation/show/' . $donationId);
        }

        // 4. Penolakan: Duplikasi permintaan aktif (menunggu atau disetujui)
        $existing = $this->requestModel->findActiveByUserAndDonation($userId, $donationId);
        if ($existing) {
            setFlash('warning', 'Anda sudah memiliki permintaan yang sedang aktif (menunggu/disetujui) pada donasi ini.');
            redirect(BASE_URL . '?url=donation/show/' . $donationId);
        }

        // Simpan Permintaan Baru (Status: 'menunggu')
        $requestId = $this->requestModel->create([
            'donation_id'  => $donationId,
            'penerima_id'  => $userId,
            'jumlah_minta' => $jumlahMinta,
            'catatan'      => $catatan,
        ]);

        $hargaPerPorsi = (int)($donation['harga_diskon'] ?? 0);
        $totalTebus    = $hargaPerPorsi * $jumlahMinta;
        $infoTebus     = ($totalTebus <= 0) ? 'Gratis Peduli' : 'Total Tebus Kasir: Rp ' . number_format($totalTebus, 0, ',', '.');

        // Catat di Status Log
        $this->logModel->log(
            $donationId,
            $requestId,
            null,
            'menunggu',
            $userId,
            "Pemesanan diajukan oleh {$_SESSION['nama']} sebanyak {$jumlahMinta} {$donation['satuan']} ({$infoTebus})."
        );

        setFlash('success', "Pesanan menu hemat berhasil dikirim ({$infoTebus})! Mohon tunggu konfirmasi dari mitra toko.");
        redirect(BASE_URL . '?url=request/mine');
    }

    /**
     * Menyetujui Permintaan Donasi (Transaksional ACID dengan Row Locking)
     */
    public function approve($id = null): void {
        requireLogin();
        $requestId = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Aksi tidak valid.');
            redirect(BASE_URL . '?url=request/incoming');
        }

        $request = $this->requestModel->findById($requestId);
        if (!$request) {
            setFlash('error', 'Data permintaan tidak ditemukan.');
            redirect(BASE_URL . '?url=request/incoming');
        }

        // Otorisasi: hanya pemilik donasi atau admin yang boleh menyetujui
        if (!isOwner((int)$request['donatur_id'])) {
            setFlash('error', 'Akses ditolak. Anda bukan pemilik donasi ini.');
            redirect(BASE_URL . '?url=request/incoming');
        }

        if ($request['status'] !== 'menunggu') {
            setFlash('warning', 'Permintaan ini sudah diproses sebelumnya (Status: ' . ucfirst($request['status']) . ').');
            redirect(BASE_URL . '?url=request/incoming');
        }

        // Mulai Transaksi Database ACID dengan Row-Locking (SELECT ... FOR UPDATE)
        try {
            $this->db->beginTransaction();

            // Kunci baris donasi agar tidak terjadi race condition / minus stock
            $lockSql = 'SELECT id, sisa, status, expired_at, satuan FROM donations WHERE id = :id';
            if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
                $lockSql .= ' FOR UPDATE';
            }
            $lockStmt = $this->db->prepare($lockSql);
            $lockStmt->execute([':id' => $request['donation_id']]);
            $lockedDonation = $lockStmt->fetch();

            if (!$lockedDonation) {
                throw new Exception('Donasi terkait tidak ditemukan dalam database.');
            }

            if (strtotime($lockedDonation['expired_at']) <= time()) {
                throw new Exception('Donasi ini sudah melewati batas waktu (kedaluwarsa).');
            }

            if ((int)$lockedDonation['sisa'] < (int)$request['jumlah_minta']) {
                throw new Exception("Sisa porsi ({$lockedDonation['sisa']} {$lockedDonation['satuan']}) tidak mencukupi untuk permintaan ({$request['jumlah_minta']} {$lockedDonation['satuan']}).");
            }

            // Kurangi sisa stok
            $newSisa = (int)$lockedDonation['sisa'] - (int)$request['jumlah_minta'];
            $newDonationStatus = ($newSisa === 0) ? 'dipesan' : 'dipesan';

            $updateDonation = $this->db->prepare('UPDATE donations SET sisa = :sisa, status = :status WHERE id = :id');
            $updateDonation->execute([
                ':sisa'   => $newSisa,
                ':status' => $newDonationStatus,
                ':id'     => $request['donation_id']
            ]);

            // Ubah status request menjadi 'disetujui'
            $updateReq = $this->db->prepare("UPDATE requests SET status = 'disetujui' WHERE id = :id");
            $updateReq->execute([':id' => $requestId]);

            // Catat log
            $this->logModel->log(
                (int)$request['donation_id'],
                $requestId,
                'menunggu',
                'disetujui',
                (int)$_SESSION['user_id'],
                "Disetujui oleh {$_SESSION['nama']}. Sisa stok kini: {$newSisa} {$lockedDonation['satuan']}."
            );

            $this->db->commit();
            setFlash('success', "Permintaan dari {$request['nama_penerima']} berhasil disetujui!");
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Gagal menyetujui permintaan: ' . $e->getMessage());
        }

        redirect(BASE_URL . '?url=request/incoming');
    }

    /**
     * Menolak Permintaan Donasi
     */
    public function reject($id = null): void {
        requireLogin();
        $requestId = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Aksi tolak tidak valid.');
            redirect(BASE_URL . '?url=request/incoming');
        }

        $request = $this->requestModel->findById($requestId);
        if (!$request || !isOwner((int)$request['donatur_id'])) {
            setFlash('error', 'Akses ditolak atau permintaan tidak ditemukan.');
            redirect(BASE_URL . '?url=request/incoming');
        }

        $alasan = trim($_POST['alasan'] ?? 'Permintaan ditolak oleh donatur.');

        try {
            $this->db->beginTransaction();

            $prevStatus = $request['status'];

            // Jika sebelumnya sudah disetujui, kembalikan sisa stok ke donasi
            if ($prevStatus === 'disetujui') {
                $this->donationModel->incSisa((int)$request['donation_id'], (int)$request['jumlah_minta']);
                
                // Cek apakah donasi masih aktif untuk mengembalikan status 'tersedia'
                $donation = $this->donationModel->findById((int)$request['donation_id']);
                if ($donation && $donation['sisa'] > 0 && strtotime($donation['expired_at']) > time()) {
                    $this->donationModel->updateStatus((int)$donation['id'], 'tersedia');
                }
            }

            $this->requestModel->updateStatus($requestId, 'ditolak');

            $this->logModel->log(
                (int)$request['donation_id'],
                $requestId,
                $prevStatus,
                'ditolak',
                (int)$_SESSION['user_id'],
                $alasan
            );

            $this->db->commit();
            setFlash('info', 'Permintaan donasi telah ditolak.');
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Terjadi kesalahan saat memproses penolakan.');
        }

        redirect(BASE_URL . '?url=request/incoming');
    }

    /**
     * Membatalkan Permintaan Donasi (Oleh Penerima)
     */
    public function cancel($id = null): void {
        requireLogin();
        $requestId = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Aksi pembatalan tidak valid.');
            redirect(BASE_URL . '?url=request/mine');
        }

        $request = $this->requestModel->findById($requestId);
        if (!$request || (int)$request['penerima_id'] !== (int)$_SESSION['user_id']) {
            setFlash('error', 'Akses ditolak atau data permintaan tidak ditemukan.');
            redirect(BASE_URL . '?url=request/mine');
        }

        if (in_array($request['status'], ['ditolak', 'dibatalkan', 'diambil'], true)) {
            setFlash('warning', 'Permintaan ini tidak dapat dibatalkan.');
            redirect(BASE_URL . '?url=request/mine');
        }

        try {
            $this->db->beginTransaction();

            $prevStatus = $request['status'];

            // Jika status sebelumnya disetujui, kembalikan stok donasi
            if ($prevStatus === 'disetujui') {
                $this->donationModel->incSisa((int)$request['donation_id'], (int)$request['jumlah_minta']);
                
                $donation = $this->donationModel->findById((int)$request['donation_id']);
                if ($donation && $donation['sisa'] > 0 && strtotime($donation['expired_at']) > time()) {
                    $this->donationModel->updateStatus((int)$donation['id'], 'tersedia');
                }
            }

            $this->requestModel->updateStatus($requestId, 'dibatalkan');

            $this->logModel->log(
                (int)$request['donation_id'],
                $requestId,
                $prevStatus,
                'dibatalkan',
                (int)$_SESSION['user_id'],
                "Dibatalkan oleh pemohon ({$_SESSION['nama']})."
            );

            $this->db->commit();
            setFlash('info', 'Permintaan donasi Anda telah dibatalkan.');
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Gagal membatalkan permintaan.');
        }

        redirect(BASE_URL . '?url=request/mine');
    }

    /**
     * Konfirmasi Pengambilan Makanan ('diambil' / 'selesai')
     */
    public function complete($id = null): void {
        requireLogin();
        $requestId = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Aksi konfirmasi tidak valid.');
            redirect(BASE_URL . '?url=request/mine');
        }

        $request = $this->requestModel->findById($requestId);
        if (!$request) {
            setFlash('error', 'Data permintaan tidak ditemukan.');
            redirect(BASE_URL . '?url=donation/index');
        }

        $userId = (int)$_SESSION['user_id'];
        $isDonatur = ((int)$request['donatur_id'] === $userId);
        $isPenerima = ((int)$request['penerima_id'] === $userId);
        $isAdmin = ($_SESSION['role'] === 'admin');

        if (!$isDonatur && !$isPenerima && !$isAdmin) {
            setFlash('error', 'Akses ditolak.');
            redirect(BASE_URL . '?url=donation/index');
        }

        try {
            $this->db->beginTransaction();

            $prevStatus = $request['status'];

            // Update status permintaan menjadi 'diambil'
            $this->requestModel->updateStatus($requestId, 'diambil');

            $this->logModel->log(
                (int)$request['donation_id'],
                $requestId,
                $prevStatus,
                'diambil',
                $userId,
                "Makanan telah berhasil diambil & dikonfirmasi oleh {$_SESSION['nama']}."
            );

            // Periksa apakah semua request untuk donasi ini sudah diambil dan sisa 0
            $donation = $this->donationModel->findById((int)$request['donation_id']);
            if ($donation) {
                $pendingCount = $this->requestModel->countByDonationAndStatus((int)$donation['id'], 'menunggu');
                $approvedCount = $this->requestModel->countByDonationAndStatus((int)$donation['id'], 'disetujui');

                if ((int)$donation['sisa'] === 0 && $pendingCount === 0 && $approvedCount === 0) {
                    $this->donationModel->updateStatus((int)$donation['id'], 'selesai');
                    $this->logModel->log(
                        (int)$donation['id'],
                        null,
                        $donation['status'],
                        'selesai',
                        $userId,
                        "Seluruh porsi makanan telah selesai tersalurkan secara tuntas."
                    );
                }
            }

            $this->db->commit();
            setFlash('success', 'Konfirmasi berhasil! Terima kasih telah berbagi rasa dan kebaikan.');
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Terjadi kesalahan saat memproses konfirmasi.');
        }

        if ($isDonatur) {
            redirect(BASE_URL . '?url=request/incoming');
        } else {
            redirect(BASE_URL . '?url=request/mine');
        }
    }

    /**
     * Layar Permintaan Masuk (Untuk Donatur & Admin)
     */
    public function incoming(): void {
        requireLogin();
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $userId  = (int)$_SESSION['user_id'];

        if ($isAdmin) {
            $requests  = $this->requestModel->allForAdmin();
            $pageTitle = 'Semua Pesanan Masuk (Admin) - Berbagi Rasa';
        } else {
            $requests  = $this->requestModel->incomingForDonatur($userId);
            $pageTitle = 'Pesanan Masuk Toko - Berbagi Rasa';
        }

        view('requests/incoming', [
            'pageTitle' => $pageTitle,
            'requests'  => $requests,
            'isAdmin'   => $isAdmin,
        ]);
    }

    /**
     * Layar Permintaan Saya (Untuk Penerima)
     */
    public function mine(): void {
        requireLogin();
        $userId = (int)$_SESSION['user_id'];
        $requests = $this->requestModel->myByPenerima($userId);

        view('requests/mine', [
            'pageTitle' => 'Permintaan Makanan Saya - Berbagi Rasa',
            'requests'  => $requests,
        ]);
    }
}
