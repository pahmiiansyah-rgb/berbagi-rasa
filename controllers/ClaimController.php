<?php
/**
 * Controller Kupon / Tiket Digital Klaim Pengambilan 3D - Berbagi Rasa
 */

class ClaimController {
    private ClaimRequest $requestModel;

    public function __construct() {
        $this->requestModel = new ClaimRequest();
    }

    /**
     * Menampilkan Tiket Kupon Digital 3D Pengambilan Makanan
     */
    public function pass($id = null): void {
        requireLogin();
        $requestId = (int)$id;

        $request = $this->requestModel->findById($requestId);
        if (!$request) {
            setFlash('error', 'Kupon klaim tidak ditemukan.');
            redirect(BASE_URL . '?url=request/mine');
        }

        $userId = (int)$_SESSION['user_id'];
        $isOwner = ((int)$request['penerima_id'] === $userId || (int)$request['donatur_id'] === $userId || $_SESSION['role'] === 'admin');

        if (!$isOwner) {
            setFlash('error', 'Akses ditolak.');
            redirect(BASE_URL . '?url=donation/index');
        }

        view('requests/pass', [
            'pageTitle' => 'Kupon Klaim #' . e($request['claim_token']) . ' - Berbagi Rasa',
            'request'   => $request,
        ]);
    }
}
