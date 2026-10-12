<?php
/**
 * Konfigurasi Utama Sistem - Berbagi Rasa
 */

// Set Zona Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// Menentukan protokol (http / https) - mendukung reverse proxy Vercel & HTTPS cloud
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

$protocol = $isHttps ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Deteksi direktori base path, menetralkan awalan '/api' jika di Vercel Serverless
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.' || $scriptDir === '/api') {
    $basePath = '';
} else {
    $basePath = rtrim($scriptDir, '/');
}

// Base URL fleksibel mengikuti domain/vhost, Vercel Serverless, atau subfolder Laragon
defined('BASE_URL') or define('BASE_URL', $protocol . $host . $basePath . '/index.php');
defined('APP_URL') or define('APP_URL', $protocol . $host . $basePath);
defined('ASSET_URL') or define('ASSET_URL', $basePath);
defined('ROOT_DIR') or define('ROOT_DIR', realpath(__DIR__ . '/..'));
defined('UPLOAD_DIR') or define('UPLOAD_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
defined('MAX_FILE_SIZE') or define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB
defined('ALLOWED_MIME') or define('ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp']);
defined('ALLOWED_EXT') or define('ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp']);
