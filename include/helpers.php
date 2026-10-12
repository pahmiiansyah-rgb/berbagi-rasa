<?php
/**
 * Helper Baku Sistem - Berbagi Rasa
 */

if (!function_exists('e')) {
    /**
     * Escape output untuk mencegah serangan XSS
     */
    function e(?string $string): string {
        return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect aman ke URL tertentu
     */
    function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('view')) {
    /**
     * Render view template dengan layout
     */
    function view(string $path, array $data = []): void {
        extract($data);
        $viewFile = ROOT_DIR . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path) . '.php';
        
        if (!file_exists($viewFile)) {
            die("View template tidak ditemukan: " . e($path));
        }

        require_once ROOT_DIR . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
        require $viewFile;
        require_once ROOT_DIR . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';
    }
}

if (!function_exists('isLoggedIn')) {
    /**
     * Cek apakah user sedang login
     */
    function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('requireLogin')) {
    /**
     * Memastikan user sudah login, redirect jika belum
     */
    function requireLogin(): void {
        if (!isLoggedIn()) {
            setFlash('error', 'Silakan masuk (login) terlebih dahulu untuk melanjutkan.');
            redirect(BASE_URL . '?url=auth/login');
        }
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Membuat atau mengambil token CSRF dari sesi
     */
    function csrf_token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_check')) {
    /**
     * Verifikasi token CSRF dari form POST
     */
    function csrf_check(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('setFlash')) {
    /**
     * Menyimpan pesan notifikasi flash ke sesi
     */
    function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type' => $type, // 'success', 'error', 'warning', 'info'
            'message' => $message
        ];
    }
}

if (!function_exists('flash')) {
    /**
     * Mengambil dan membersihkan pesan flash
     */
    function flash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('setOld')) {
    /**
     * Menyimpan data form input sebelumnya untuk repopulation
     */
    function setOld(array $data): void {
        $_SESSION['old_input'] = $data;
    }
}

if (!function_exists('old')) {
    /**
     * Mengambil nilai input lama dari sesi
     */
    function old(string $key, $default = '') {
        $val = $_SESSION['old_input'][$key] ?? $default;
        return $val;
    }
}

if (!function_exists('clearOld')) {
    /**
     * Membersihkan input lama
     */
    function clearOld(): void {
        unset($_SESSION['old_input']);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format tanggal & waktu ke bahasa Indonesia
     */
    function formatDate(?string $datetime): string {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        if (!$timestamp) return '-';

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $day = date('d', $timestamp);
        $monthNum = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);
        $time = date('H:i', $timestamp);

        return "{$day} " . ($months[$monthNum] ?? '') . " {$year}, {$time} WIB";
    }
}

if (!function_exists('timeRemaining')) {
    /**
     * Menghitung sisa waktu expired dalam format ramah manusia
     */
    function timeRemaining(?string $datetime): string {
        if (!$datetime) return 'Waktu tidak ditentukan';
        $target = strtotime($datetime);
        $now = time();
        $diff = $target - $now;

        if ($diff <= 0) {
            return 'Sudah lewat batas';
        }

        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);

        if ($hours > 24) {
            $days = floor($hours / 24);
            $remHours = $hours % 24;
            return "Sisa {$days} hari {$remHours} jam";
        }

        if ($hours > 0) {
            return "Sisa {$hours} jam {$minutes} mnt";
        }

        return "Sisa {$minutes} menit";
    }
}

if (!function_exists('statusBadge')) {
    /**
     * Badge status donasi dengan gaya 3D
     */
    function statusBadge(string $status): string {
        $map = [
            'tersedia'    => ['label' => 'Tersedia', 'class' => 'badge-tersedia', 'icon' => 'fa-check-circle'],
            'dipesan'     => ['label' => 'Dipesan', 'class' => 'badge-dipesan', 'icon' => 'fa-clock'],
            'selesai'     => ['label' => 'Selesai', 'class' => 'badge-selesai', 'icon' => 'fa-box-open'],
            'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'class' => 'badge-kedaluwarsa', 'icon' => 'fa-ban'],
            'dibatalkan'  => ['label' => 'Dibatalkan', 'class' => 'badge-dibatalkan', 'icon' => 'fa-times-circle'],
        ];

        $info = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-default', 'icon' => 'fa-circle'];
        return '<span class="badge 3d-badge ' . e($info['class']) . '"><i class="' . e($info['icon']) . '"></i> ' . e($info['label']) . '</span>';
    }
}

if (!function_exists('formatRupiah')) {
    /**
     * Format angka ke format mata uang Rupiah
     */
    function formatRupiah(?int $angka): string {
        if ($angka === null || $angka <= 0) {
            return 'Rp 0 (Gratis)';
        }
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('discountPercent')) {
    /**
     * Hitung persentase diskon hemat dari harga asli dan harga diskon
     */
    function discountPercent(int $hargaAsli, int $hargaDiskon): int {
        if ($hargaAsli <= 0) return 0;
        if ($hargaDiskon <= 0) return 100;
        $diff = $hargaAsli - $hargaDiskon;
        if ($diff <= 0) return 0;
        $percent = round(($diff / $hargaAsli) * 100);
        return max(0, min(100, (int)$percent));
    }
}

if (!function_exists('discountBadge')) {
    /**
     * Badge 3D diskon dan penghematan harga makanan jelang tutup
     */
    function discountBadge(int $hargaAsli, int $hargaDiskon, string $tipePenawaran = 'diskon_toko'): string {
        if ($tipePenawaran === 'gratis_peduli' || $hargaDiskon <= 0) {
            return '<span class="badge-discount-3d badge-free"><i class="fa-solid fa-gift"></i> GRATIS PEDULI</span>';
        }
        $percent = discountPercent($hargaAsli, $hargaDiskon);
        return '<span class="badge-discount-3d badge-percent"><i class="fa-solid fa-bolt"></i> HEMAT ' . $percent . '%</span>';
    }
}

if (!function_exists('requestBadge')) {
    /**
     * Badge status request dengan gaya 3D
     */
    function requestBadge(string $status): string {
        $map = [
            'menunggu'   => ['label' => 'Menunggu', 'class' => 'badge-menunggu', 'icon' => 'fa-hourglass-half'],
            'disetujui'  => ['label' => 'Disetujui', 'class' => 'badge-disetujui', 'icon' => 'fa-check-double'],
            'ditolak'    => ['label' => 'Ditolak', 'class' => 'badge-ditolak', 'icon' => 'fa-times'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'badge-dibatalkan', 'icon' => 'fa-minus-circle'],
            'diambil'    => ['label' => 'Sudah Diambil', 'class' => 'badge-diambil', 'icon' => 'fa-hand-holding-heart'],
        ];

        $info = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-default', 'icon' => 'fa-circle'];
        return '<span class="badge 3d-badge ' . e($info['class']) . '"><i class="' . e($info['icon']) . '"></i> ' . e($info['label']) . '</span>';
    }
}

