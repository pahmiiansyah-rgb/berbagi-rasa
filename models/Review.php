<?php
/**
 * Model Review (Ulasan & Penilaian Kualitas Makanan 3D) - Berbagi Rasa
 */

class Review {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::get();
    }

    /**
     * Menyimpan ulasan & rating bintang dari penerima
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO reviews (request_id, donation_id, penerima_id, rating, ulasan) 
             VALUES (:request_id, :donation_id, :penerima_id, :rating, :ulasan)"
        );

        $stmt->execute([
            ':request_id'  => (int)$data['request_id'],
            ':donation_id' => (int)$data['donation_id'],
            ':penerima_id' => (int)$data['penerima_id'],
            ':rating'      => max(1, min(5, (int)$data['rating'])),
            ':ulasan'      => trim($data['ulasan'] ?? ''),
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mencari ulasan berdasarkan ID permohonan klaim
     */
    public function findByRequest(int $requestId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM reviews WHERE request_id = :req_id LIMIT 1');
        $stmt->execute([':req_id' => $requestId]);
        $rev = $stmt->fetch();
        return $rev ?: null;
    }

    /**
     * Mengambil ulasan untuk suatu donasi makanan tertentu
     */
    public function forDonation(int $donationId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.nama AS nama_pengulas
             FROM reviews r
             JOIN users u ON u.id = r.penerima_id
             WHERE r.donation_id = :donation_id
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([':donation_id' => $donationId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil ulasan yang ditujukan untuk semua donasi milik donatur tertentu
     */
    public function forDonatur(int $donaturId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, d.judul AS judul_donasi, u.nama AS nama_pengulas
             FROM reviews r
             JOIN donations d ON d.id = r.donation_id
             JOIN users u ON u.id = r.penerima_id
             WHERE d.user_id = :donatur_id
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([':donatur_id' => $donaturId]);
        return $stmt->fetchAll();
    }

    /**
     * Menghitung rata-rata rating untuk donatur
     */
    public function getAverageRatingForDonatur(int $donaturId): array {
        $stmt = $this->db->prepare(
            "SELECT AVG(r.rating) AS avg_rating, COUNT(r.id) AS total_reviews
             FROM reviews r
             JOIN donations d ON d.id = r.donation_id
             WHERE d.user_id = :donatur_id"
        );
        $stmt->execute([':donatur_id' => $donaturId]);
        $res = $stmt->fetch();

        return [
            'avg_rating'    => round((float)($res['avg_rating'] ?? 0), 1),
            'total_reviews' => (int)($res['total_reviews'] ?? 0),
        ];
    }
}
