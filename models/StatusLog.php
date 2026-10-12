<?php
/**
 * Model StatusLog (Jejak Audit Status) - Berbagi Rasa
 */

class StatusLog {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::get();
    }

    /**
     * Mencatat jejak perubahan status (audit log)
     */
    public function log(
        int $donationId, 
        ?int $requestId, 
        ?string $fromStatus, 
        string $toStatus, 
        ?int $changedBy = null, 
        ?string $note = null
    ): int {
        $stmt = $this->db->prepare(
            "INSERT INTO status_logs (donation_id, request_id, dari_status, ke_status, diubah_oleh, catatan) 
             VALUES (:donation_id, :request_id, :dari_status, :ke_status, :diubah_oleh, :catatan)"
        );

        $stmt->execute([
            ':donation_id' => $donationId,
            ':request_id'  => $requestId,
            ':dari_status' => $fromStatus,
            ':ke_status'   => $toStatus,
            ':diubah_oleh' => $changedBy,
            ':catatan'     => $note,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mengambil riwayat log untuk donasi tertentu (untuk timeline vertikal di halaman detail)
     */
    public function forDonation(int $donationId): array {
        $stmt = $this->db->prepare(
            "SELECT sl.*, u.nama AS nama_pengubah, u.role AS role_pengubah
             FROM status_logs sl
             LEFT JOIN users u ON u.id = sl.diubah_oleh
             WHERE sl.donation_id = :donation_id
             ORDER BY sl.created_at ASC"
        );
        $stmt->execute([':donation_id' => $donationId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil riwayat transaksi untuk pengguna (berdasarkan role: donatur / penerima / admin)
     */
    public function forUser(int $userId, string $role, ?string $statusFilter = null, int $limit = 10, int $offset = 0): array {
        $params = [];

        if ($role === 'donatur') {
            $sql = "SELECT sl.*, 
                           d.judul AS judul_donasi, d.satuan, d.lokasi_ambil,
                           u_actor.nama AS nama_aktor,
                           r.jumlah_minta,
                           u_req.nama AS nama_peminta
                    FROM status_logs sl
                    JOIN donations d ON d.id = sl.donation_id
                    LEFT JOIN requests r ON r.id = sl.request_id
                    LEFT JOIN users u_req ON u_req.id = r.penerima_id
                    LEFT JOIN users u_actor ON u_actor.id = sl.diubah_oleh
                    WHERE d.user_id = :user_id";
            $params[':user_id'] = $userId;
        } elseif ($role === 'penerima') {
            $sql = "SELECT sl.*, 
                           d.judul AS judul_donasi, d.satuan, d.lokasi_ambil,
                           u_actor.nama AS nama_aktor,
                           r.jumlah_minta,
                           u_donatur.nama AS nama_donatur
                    FROM status_logs sl
                    JOIN requests r ON r.id = sl.request_id
                    JOIN donations d ON d.id = sl.donation_id
                    JOIN users u_donatur ON u_donatur.id = d.user_id
                    LEFT JOIN users u_actor ON u_actor.id = sl.diubah_oleh
                    WHERE r.penerima_id = :user_id";
            $params[':user_id'] = $userId;
        } else {
            // Admin melihat semua aktivitas log
            $sql = "SELECT sl.*, 
                           d.judul AS judul_donasi, d.satuan, d.lokasi_ambil,
                           u_actor.nama AS nama_aktor,
                           r.jumlah_minta
                    FROM status_logs sl
                    JOIN donations d ON d.id = sl.donation_id
                    LEFT JOIN requests r ON r.id = sl.request_id
                    LEFT JOIN users u_actor ON u_actor.id = sl.diubah_oleh
                    WHERE 1=1";
        }

        if (!empty($statusFilter) && $statusFilter !== 'semua') {
            $sql .= " AND sl.ke_status = :status_filter";
            $params[':status_filter'] = $statusFilter;
        }

        $sql .= " ORDER BY sl.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Menghitung total entri riwayat untuk pagination
     */
    public function countForUser(int $userId, string $role, ?string $statusFilter = null): int {
        $params = [];

        if ($role === 'donatur') {
            $sql = "SELECT COUNT(*) FROM status_logs sl
                    JOIN donations d ON d.id = sl.donation_id
                    WHERE d.user_id = :user_id";
            $params[':user_id'] = $userId;
        } elseif ($role === 'penerima') {
            $sql = "SELECT COUNT(*) FROM status_logs sl
                    JOIN requests r ON r.id = sl.request_id
                    WHERE r.penerima_id = :user_id";
            $params[':user_id'] = $userId;
        } else {
            $sql = "SELECT COUNT(*) FROM status_logs sl WHERE 1=1";
        }

        if (!empty($statusFilter) && $statusFilter !== 'semua') {
            $sql .= " AND sl.ke_status = :status_filter";
            $params[':status_filter'] = $statusFilter;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
}
