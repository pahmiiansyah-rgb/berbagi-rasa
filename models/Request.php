<?php
/**
 * Model Request (Pemesanan Menu Makanan Closing Deals) - Berbagi Rasa
 */

class ClaimRequest {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::get();
    }

    /**
     * Membuat pemesanan menu baru dengan status awal 'menunggu' dan token klaim unik
     */
    public function create(array $data): int {
        $token = 'BR-KLM-' . strtoupper(bin2hex(random_bytes(3)));

        $stmt = $this->db->prepare(
            "INSERT INTO requests (donation_id, penerima_id, jumlah_minta, catatan, claim_token, status) 
             VALUES (:donation_id, :penerima_id, :jumlah_minta, :catatan, :claim_token, 'menunggu')"
        );

        $stmt->execute([
            ':donation_id'   => (int)$data['donation_id'],
            ':penerima_id'   => (int)$data['penerima_id'],
            ':jumlah_minta'  => (int)$data['jumlah_minta'],
            ':catatan'       => $data['catatan'] ?? null,
            ':claim_token'   => $token,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mencari pesanan berdasarkan ID lengkap dengan relasi donasi, toko, user, dan ulasan
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT r.*, 
                    d.judul AS judul_donasi, d.satuan, d.user_id AS donatur_id, d.lokasi_ambil, d.foto AS foto_donasi, 
                    d.sisa AS sisa_donasi, d.status AS status_donasi, d.expired_at,
                    d.harga_asli, d.harga_diskon, d.jam_tutup, d.tipe_penawaran,
                    u_donatur.nama AS nama_donatur, u_donatur.telepon AS telepon_donatur,
                    u_penerima.nama AS nama_penerima, u_penerima.telepon AS telepon_penerima, u_penerima.email AS email_penerima,
                    rv.rating AS review_rating, rv.ulasan AS review_ulasan
             FROM requests r
             JOIN donations d ON d.id = r.donation_id
             JOIN users u_donatur ON u_donatur.id = d.user_id
             JOIN users u_penerima ON u_penerima.id = r.penerima_id
             LEFT JOIN reviews rv ON rv.request_id = r.id
             WHERE r.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $req = $stmt->fetch();

        return $req ?: null;
    }

    /**
     * Memeriksa apakah pemohon sudah memiliki pesanan aktif ('menunggu' atau 'disetujui') pada donasi tertentu
     */
    public function findActiveByUserAndDonation(int $userId, int $donationId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM requests 
             WHERE penerima_id = :user_id 
               AND donation_id = :donation_id 
               AND status IN ('menunggu', 'disetujui')
             LIMIT 1"
        );
        $stmt->execute([
            ':user_id'     => $userId,
            ':donation_id' => $donationId
        ]);
        $req = $stmt->fetch();

        return $req ?: null;
    }

    /**
     * Mengambil semua pesanan masuk untuk menu milik toko tertentu
     */
    public function incomingForDonatur(int $donaturId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, 
                    d.judul AS judul_donasi, d.satuan, d.sisa AS sisa_donasi, d.lokasi_ambil, d.foto AS foto_donasi, d.status AS status_donasi,
                    d.harga_asli, d.harga_diskon, d.jam_tutup, d.tipe_penawaran,
                    u.nama AS nama_penerima, u.telepon AS telepon_penerima, u.email AS email_penerima
             FROM requests r
             JOIN donations d ON d.id = r.donation_id
             JOIN users u ON u.id = r.penerima_id
             WHERE d.user_id = :donatur_id
             ORDER BY (r.status = 'menunggu') DESC, r.created_at DESC"
        );
        $stmt->execute([':donatur_id' => $donaturId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil semua pesanan yang diajukan oleh seorang pengguna/anak rantau
     */
    public function myByPenerima(int $penerimaId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, 
                    d.judul AS judul_donasi, d.satuan, d.lokasi_ambil, d.foto AS foto_donasi, d.expired_at, d.status AS status_donasi,
                    d.harga_asli, d.harga_diskon, d.jam_tutup, d.tipe_penawaran,
                    u.nama AS nama_donatur, u.telepon AS telepon_donatur
             FROM requests r
             JOIN donations d ON d.id = r.donation_id
             JOIN users u ON u.id = d.user_id
             WHERE r.penerima_id = :penerima_id
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([':penerima_id' => $penerimaId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil semua pesanan aktif untuk menu tertentu
     */
    public function findActiveByDonation(int $donationId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM requests 
             WHERE donation_id = :donation_id 
               AND status IN ('menunggu', 'disetujui')"
        );
        $stmt->execute([':donation_id' => $donationId]);
        return $stmt->fetchAll();
    }

    /**
     * Memeriksa apakah suatu menu memiliki pesanan aktif ('menunggu' atau 'disetujui')
     */
    public function hasActiveRequests(int $donationId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM requests 
             WHERE donation_id = :donation_id 
               AND status IN ('menunggu', 'disetujui')"
        );
        $stmt->execute([':donation_id' => $donationId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Mengubah status pesanan
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE requests SET status = :status WHERE id = :id');
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }

    /**
     * Menghitung total pesanan berstatus tertentu untuk suatu menu
     */
    public function countByDonationAndStatus(int $donationId, string $status): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM requests WHERE donation_id = :donation_id AND status = :status');
        $stmt->execute([':donation_id' => $donationId, ':status' => $status]);
        return (int)$stmt->fetchColumn();
    }
}

// Alias class Request untuk kemudahan dan kompatibilitas
if (!class_exists('Request')) {
    class Request extends ClaimRequest {}
}
