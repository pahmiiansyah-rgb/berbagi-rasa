<?php
/**
 * Pintu Masuk Tunggal & Router Aplikasi - Berbagi Rasa
 */

// Memulai sesi PHP jika belum aktif (ramah lingkungan serverless seperti Vercel)
if (session_status() === PHP_SESSION_NONE) {
    if (getenv('VERCEL') || !is_writable(session_save_path())) {
        $tmpSession = sys_get_temp_dir();
        if (is_writable($tmpSession)) {
            @session_save_path($tmpSession);
        }
    }
    @session_start();
}

// Muat Konfigurasi & Utilitas Sistem
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/include/helpers.php';
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/include/upload.php';

// Muat Model Data
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Donation.php';
require_once __DIR__ . '/models/Request.php';
require_once __DIR__ . '/models/StatusLog.php';

// Muat Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DonationController.php';
require_once __DIR__ . '/controllers/RequestController.php';
require_once __DIR__ . '/controllers/HistoryController.php';

// Parsing URL parameter (format: ?url=controller/method/param1/param2... atau via request path)
$rawUrl = $_GET['url'] ?? '';
if (empty($rawUrl)) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $uriPath = trim($uriPath, '/');
    $uriPath = preg_replace('#^(api/)?index\.php/?#', '', $uriPath);
    if (!empty($basePath)) {
        $cleanBase = trim($basePath, '/');
        $uriPath = preg_replace('#^' . preg_quote($cleanBase, '#') . '/?#', '', $uriPath);
    }
    $rawUrl = !empty($uriPath) ? $uriPath : 'donation/index';
}

$rawUrl = trim($rawUrl, '/');
$segments = explode('/', $rawUrl);

$controllerName = strtolower($segments[0] ?? 'donation');
$methodName     = $segments[1] ?? 'index';
$params         = array_slice($segments, 2);

// Peta Controller yang diizinkan
$controllerMap = [
    'donation' => 'DonationController',
    'auth'     => 'AuthController',
    'request'  => 'RequestController',
    'history'  => 'HistoryController',
];

if (!array_key_exists($controllerName, $controllerMap)) {
    setFlash('error', 'Halaman yang Anda tuju tidak ditemukan.');
    redirect(BASE_URL . '?url=donation/index');
}

$className = $controllerMap[$controllerName];
$controller = new $className();

if (!method_exists($controller, $methodName)) {
    setFlash('error', 'Layanan atau aksi yang Anda minta tidak tersedia.');
    redirect(BASE_URL . '?url=donation/index');
}

// Eksekusi method controller dengan parameter
try {
    call_user_func_array([$controller, $methodName], $params);
} catch (Throwable $e) {
    // Log error di level server tanpa membocorkan ke pengguna
    error_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    setFlash('error', 'Terjadi gangguan sistem sementara. Silakan coba beberapa saat lagi.');
    redirect(BASE_URL . '?url=donation/index');
}
