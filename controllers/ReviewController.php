<?php
/**
 * Controller Penilaian & Ulasan Makanan 3D - Berbagi Rasa
 */

class ReviewController {
    private Review $reviewModel;
    private ClaimRequest $requestModel;

    public function __construct() {
        $this->reviewModel   = new Review();
        $this->requestModel  = new ClaimRequest();
    }

    /**
     * Menyimpan Ulasan dan Rating Makanan
     */
    public function store(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
            setFlash('error', 'Permintaan penilaian tidak valid.');
            redirect(BASE_URL . '?url=request/mine');
        }

        $userId    = (int)$_SESSION['user_id'];
        $requestId = (int)($_POST['request_id'] ?? 0);
        $rating    = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $ulasan    = trim($_POST['ulasan'] ?? '');

        $request = $this->requestModel->findById($requestId);
        if (!$request || (int)$request['penerima_id'] !== $userId) {
            setFlash('error', 'Akses ditolak atau klaim tidak ditemukan.');
            redirect(BASE_URL . '?url=request/mine');
        }

        if ($request['status'] !== 'diambil') {
            setFlash('warning', 'Penilaian hanya dapat diberikan setelah makanan selesai diambil.');
            redirect(BASE_URL . '?url=request/mine');
        }

        // Cek apakah sudah pernah dinilai
        $existing = $this->reviewModel->findByRequest($requestId);
        if ($existing) {
            setFlash('info', 'Anda sudah memberikan ulasan untuk makanan ini sebelumnya.');
            redirect(BASE_URL . '?url=request/mine');
        }

        $this->reviewModel->create([
            'request_id'  => $requestId,
            'donation_id' => (int)$request['donation_id'],
            'penerima_id' => $userId,
            'rating'      => $rating,
            'ulasan'      => $ulasan,
        ]);

        setFlash('success', 'Terima kasih! Ulasan & rating Anda telah tersimpan dan sangat berarti bagi donatur.');
        redirect(BASE_URL . '?url=request/mine');
    }
}
