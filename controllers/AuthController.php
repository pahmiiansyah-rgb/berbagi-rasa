<?php
/**
 * Controller Otentikasi Pengguna - Berbagi Rasa
 */

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Halaman & Proses Registrasi Pengguna Baru
     */
    public function register(): void {
        if (isLoggedIn()) {
            redirect(BASE_URL . '?url=donation/index');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf'] ?? '';
            if (!csrf_check($token)) {
                $errors[] = 'Token keamanan (CSRF) tidak valid atau sesi telah kedaluwarsa.';
            }

            $nama     = trim($_POST['nama'] ?? '');
            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['password_confirmation'] ?? '';
            $role     = $_POST['role'] ?? 'penerima';
            $telepon  = trim($_POST['telepon'] ?? '');
            $alamat   = trim($_POST['alamat'] ?? '');

            // Simpan input lama agar form tidak kosong kembali
            setOld([
                'nama'    => $nama,
                'email'   => $email,
                'role'    => $role,
                'telepon' => $telepon,
                'alamat'  => $alamat
            ]);

            // Validasi Input
            if (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
                $errors[] = 'Nama lengkap wajib diisi antara 3 hingga 100 karakter.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format alamat email tidak valid.';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors[] = 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk.';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Kata sandi minimal terdiri dari 6 karakter.';
            }

            if ($password !== $confirm) {
                $errors[] = 'Konfirmasi kata sandi tidak cocok dengan kata sandi.';
            }

            if (!in_array($role, ['donatur', 'penerima'], true)) {
                $errors[] = 'Peran pengguna wajib dipilih (Donatur atau Penerima).';
            }

            if (empty($errors)) {
                $userId = $this->userModel->create([
                    'nama'     => $nama,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => $role,
                    'telepon'  => $telepon,
                    'alamat'   => $alamat,
                ]);

                clearOld();
                setFlash('success', 'Pendaftaran akun berhasil! Silakan masuk dengan akun baru Anda.');
                redirect(BASE_URL . '?url=auth/login');
            }
        }

        view('auth/register', [
            'pageTitle' => 'Daftar Akun Baru - Berbagi Rasa',
            'errors'    => $errors,
        ]);
    }

    /**
     * Halaman & Proses Masuk (Login)
     */
    public function login(): void {
        if (isLoggedIn()) {
            redirect(BASE_URL . '?url=donation/index');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf'] ?? '';
            if (!csrf_check($token)) {
                $errors[] = 'Token keamanan (CSRF) tidak valid. Silakan muat ulang halaman.';
            }

            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';

            setOld(['email' => $email]);

            if (empty($email) || empty($password)) {
                $errors[] = 'Email dan kata sandi wajib diisi.';
            } else {
                $user = $this->userModel->findByEmail($email);

                // Verifikasi password dengan BCRYPT dan pesan error generik agar aman
                if (!$user || !password_verify($password, $user['password'])) {
                    $errors[] = 'Email atau kata sandi yang Anda masukkan salah.';
                } else {
                    // Regenerasi ID Sesi untuk mencegah serangan Session Fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['nama']    = $user['nama'];
                    $_SESSION['email']   = $user['email'];
                    $_SESSION['role']    = $user['role'];

                    clearOld();
                    setFlash('success', 'Selamat datang kembali, ' . e($user['nama']) . '!');

                    if ($user['role'] === 'donatur') {
                        redirect(BASE_URL . '?url=donation/mine');
                    } else {
                        redirect(BASE_URL . '?url=donation/index');
                    }
                }
            }
        }

        view('auth/login', [
            'pageTitle' => 'Masuk - Berbagi Rasa',
            'errors'    => $errors,
        ]);
    }

    /**
     * Proses Keluar (Logout)
     */
    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        session_start();

        setFlash('info', 'Anda telah berhasil keluar dari sistem.');
        redirect(BASE_URL . '?url=donation/index');
    }
}
