<?php
/**
 * Model User - Berbagi Rasa
 */

class User {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::get();
    }

    /**
     * Mendaftarkan pengguna baru dengan kata sandi ter-hash BCRYPT
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (nama, email, password, role, telepon, alamat) 
             VALUES (:nama, :email, :password, :role, :telepon, :alamat)'
        );

        $stmt->execute([
            ':nama'     => $data['nama'],
            ':email'    => strtolower(trim($data['email'])),
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':role'     => $data['role'] ?? 'penerima',
            ':telepon'  => $data['telepon'] ?? null,
            ':alamat'   => $data['alamat'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mencari pengguna berdasarkan email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Mencari pengguna berdasarkan ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT id, nama, email, role, telepon, alamat, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Memperbarui informasi profil pengguna
     */
    public function updateProfile(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE users 
             SET nama = :nama, 
                 telepon = :telepon, 
                 alamat = :alamat 
             WHERE id = :id'
        );
        return $stmt->execute([
            ':nama'    => $data['nama'],
            ':telepon' => $data['telepon'] ?? null,
            ':alamat'  => $data['alamat'] ?? null,
            ':id'      => $id,
        ]);
    }

    /**
     * Memperbarui kata sandi pengguna
     */
    public function updatePassword(int $id, string $newPasswordHash): bool {
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute([
            ':password' => $newPasswordHash,
            ':id'       => $id,
        ]);
    }

    /**
     * Menghitung Lencana Prestasi & Reputasi 3D untuk Pengguna
     */
    public function getUserAchievements(int $userId): array {
        $user = $this->findById($userId);
        if (!$user) return [];

        $role = $user['role'];
        $badges = [];

        if ($role === 'donatur') {
            // Hitung total makanan yang telah didonasikan
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah), 0) AS total_porsi, COUNT(*) AS total_donasi FROM donations WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $stats = $stmt->fetch();

            $porsi = (int)$stats['total_porsi'];
            $donasi = (int)$stats['total_donasi'];

            if ($donasi >= 1) {
                $badges[] = ['title' => 'Mitra Perintis', 'desc' => 'Telah mempublikasikan donasi makanan pertama', 'icon' => 'fa-seedling', 'color' => '#10b981'];
            }
            if ($porsi >= 20) {
                $badges[] = ['title' => 'Pahlawan Pangan', 'desc' => 'Telah menyelamatkan 20+ porsi makanan', 'icon' => 'fa-shield-heart', 'color' => '#3b82f6'];
            }
            if ($porsi >= 50) {
                $badges[] = ['title' => 'Donatur Teladan', 'desc' => 'Telah menyalurkan 50+ porsi makanan bergizi', 'icon' => 'fa-crown', 'color' => '#f59e0b'];
            }
        } elseif ($role === 'penerima') {
            // Hitung total makanan yang telah diambil
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah_minta), 0) AS total_klaim, COUNT(*) AS total_req FROM requests WHERE penerima_id = :uid AND status = 'diambil'");
            $stmt->execute([':uid' => $userId]);
            $stats = $stmt->fetch();

            $klaim = (int)$stats['total_klaim'];

            if ($klaim >= 1) {
                $badges[] = ['title' => 'Penyelamat Makanan', 'desc' => 'Mencegah makanan terbuang ke tempat sampah', 'icon' => 'fa-hand-holding-heart', 'color' => '#10b981'];
            }
            if ($klaim >= 10) {
                $badges[] = ['title' => 'Relawan Aktif', 'desc' => 'Telah menyalurkan 10+ porsi konsumsi', 'icon' => 'fa-medal', 'color' => '#f59e0b'];
            }
        } else {
            $badges[] = ['title' => 'Penjaga Ekosistem', 'desc' => 'Administrator pengawas platform Berbagi Rasa', 'icon' => 'fa-shield-halved', 'color' => '#8b5cf6'];
        }

        return $badges;
    }

    /**
     * Mengambil semua pengguna (untuk Admin)
     */
    public function allUsers(): array {
        $stmt = $this->db->query('SELECT id, nama, email, role, telepon, alamat, created_at FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }
}
