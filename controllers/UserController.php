<?php
/**
 * Controller Profil Pengguna & Lencana Reputasi 3D - Berbagi Rasa
 */

class UserController {
    private User $userModel;
    private Donation $donationModel;
    private Review $reviewModel;

    public function __construct() {
        $this->userModel     = new User();
        $this->donationModel = new Donation();
        $this->reviewModel   = new Review();
    }

    /**
     * Halaman Pengaturan & Edit Profil Saya
     */
    public function profile(): void {
        requireLogin();
        $userId = (int)$_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        $badges = $this->userModel->getUserAchievements($userId);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_check($_POST['csrf'] ?? '')) {
                setFlash('error', 'Token keamanan tidak valid.');
                redirect(BASE_URL . '?url=user/profile');
            }

            $action = $_POST['action'] ?? 'update_profile';

            if ($action === 'update_profile') {
                $nama    = trim($_POST['nama'] ?? '');
                $telepon = trim($_POST['telepon'] ?? '');
                $alamat  = trim($_POST['alamat'] ?? '');

                if (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
                    $errors[] = 'Nama wajib diisi antara 3 hingga 100 karakter.';
                }

                if (empty($errors)) {
                    $this->userModel->updateProfile($userId, [
                        'nama'    => $nama,
                        'telepon' => $telepon,
                        'alamat'  => $alamat,
                    ]);
                    $_SESSION['nama'] = $nama;
                    setFlash('success', 'Profil Anda berhasil diperbarui!');
                    redirect(BASE_URL . '?url=user/profile');
                }
            } elseif ($action === 'change_password') {
                $passLama = $_POST['password_lama'] ?? '';
                $passBaru = $_POST['password_baru'] ?? '';
                $confirm  = $_POST['password_konfirmasi'] ?? '';

                $userWithPass = $this->userModel->findByEmail($user['email']);
                if (!password_verify($passLama, $userWithPass['password'])) {
                    $errors[] = 'Kata sandi saat ini (lama) tidak sesuai.';
                } elseif (strlen($passBaru) < 6) {
                    $errors[] = 'Kata sandi baru minimal 6 karakter.';
                } elseif ($passBaru !== $confirm) {
                    $errors[] = 'Konfirmasi kata sandi baru tidak cocok.';
                } else {
                    $this->userModel->updatePassword($userId, password_hash($passBaru, PASSWORD_BCRYPT));
                    setFlash('success', 'Kata sandi berhasil diperbarui!');
                    redirect(BASE_URL . '?url=user/profile');
                }
            }
        }

        view('user/profile', [
            'pageTitle' => 'Profil & Lencana Saya - Berbagi Rasa',
            'user'      => $user,
            'badges'    => $badges,
            'errors'    => $errors,
        ]);
    }

    /**
     * Profil Publik Pengguna / Donatur (Menampilkan Reputasi & Ulasan)
     */
    public function viewProfile($id = null): void {
        $userId = (int)$id;
        if ($userId <= 0) {
            redirect(BASE_URL . '?url=donation/index');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            setFlash('error', 'Pengguna tidak ditemukan.');
            redirect(BASE_URL . '?url=donation/index');
        }

        $badges = $this->userModel->getUserAchievements($userId);
        $reviews = ($user['role'] === 'donatur') ? $this->reviewModel->forDonatur($userId) : [];
        $ratingInfo = ($user['role'] === 'donatur') ? $this->reviewModel->getAverageRatingForDonatur($userId) : null;
        $donations = ($user['role'] === 'donatur') ? $this->donationModel->myByUser($userId) : [];

        view('user/public_profile', [
            'pageTitle'  => 'Profil ' . e($user['nama']) . ' - Berbagi Rasa',
            'user'       => $user,
            'badges'     => $badges,
            'reviews'    => $reviews,
            'ratingInfo' => $ratingInfo,
            'donations'  => $donations,
        ]);
    }
}
