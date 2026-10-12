<?php
/**
 * Otentikasi dan Otorisasi Sesi - Berbagi Rasa
 */

if (!function_exists('currentUser')) {
    /**
     * Mendapatkan data user yang sedang login dari database
     */
    function currentUser(): ?array {
        if (!isLoggedIn()) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser !== null && $cachedUser['id'] === $_SESSION['user_id']) {
            return $cachedUser;
        }

        $userModel = new User();
        $cachedUser = $userModel->findById((int)$_SESSION['user_id']);
        return $cachedUser ?: null;
    }
}

if (!function_exists('requireRole')) {
    /**
     * Membatasi akses hanya untuk role tertentu (misal: 'donatur', 'penerima', 'admin')
     */
    function requireRole(string $allowedRole): void {
        requireLogin();
        $userRole = $_SESSION['role'] ?? '';

        if ($userRole !== $allowedRole && $userRole !== 'admin') {
            setFlash('error', 'Akses ditolak. Fitur ini hanya untuk pengguna dengan peran ' . ucfirst($allowedRole) . '.');
            redirect(BASE_URL . '?url=donation/index');
        }
    }
}

if (!function_exists('isOwner')) {
    /**
     * Memeriksa apakah user yang sedang login adalah pemilik data (atau admin)
     */
    function isOwner(int $ownerId): bool {
        if (!isLoggedIn()) {
            return false;
        }
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['role'] ?? '';

        return ($userId === $ownerId || $userRole === 'admin');
    }
}
