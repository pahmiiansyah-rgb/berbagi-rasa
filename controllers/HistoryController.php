<?php
/**
 * Controller Riwayat Transaksi & Audit Log - Berbagi Rasa
 */

class HistoryController {
    private StatusLog $logModel;

    public function __construct() {
        $this->logModel = new StatusLog();
    }

    /**
     * Menampilkan Buku Riwayat & Log Aktivitas Pengguna
     */
    public function index(): void {
        requireLogin();

        $userId = (int)$_SESSION['user_id'];
        $role   = $_SESSION['role'];

        $filterStatus = trim($_GET['status'] ?? 'semua');
        $page         = max(1, (int)($_GET['page'] ?? 1));
        $limit        = 10;
        $offset       = ($page - 1) * $limit;

        $totalLogs  = $this->logModel->countForUser($userId, $role, $filterStatus);
        $totalPages = (int)ceil($totalLogs / $limit);
        $logs       = $this->logModel->forUser($userId, $role, $filterStatus, $limit, $offset);

        view('history/index', [
            'pageTitle'    => 'Riwayat Aktivitas & Jejak Donasi - Berbagi Rasa',
            'logs'         => $logs,
            'filterStatus' => $filterStatus,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalLogs'    => $totalLogs,
            'role'         => $role,
        ]);
    }
}
