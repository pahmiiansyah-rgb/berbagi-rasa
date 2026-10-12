<?php
/**
 * Model Donation (Penyelamatan Makanan Closing Deals) - Berbagi Rasa
 */

class Donation {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::get();
    }

    /**
     * Memperbarui status menu yang sudah melewati batas waktu menjadi 'kedaluwarsa'
     */
    public function markExpired(): int {
        $stmt = $this->db->prepare(
            "UPDATE donations 
             SET status = 'kedaluwarsa' 
             WHERE status IN ('tersedia', 'dipesan') 
               AND expired_at < NOW()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Mengambil daftar makanan yang tersedia untuk katalog publik (dengan search, filter lokasi, kategori, & filter hemat)
     */
    public function allAvailable(
        string $keyword = '',
        string $lokasi = '',
        string $kategori = '',
        string $filterHarga = '',
        int $limit = 12,
        int $offset = 0
    ): array {
        $this->markExpired();

        $sql = "SELECT d.*, u.nama AS nama_donatur, u.telepon AS telepon_donatur
                FROM donations d
                JOIN users u ON u.id = d.user_id
                WHERE d.status = 'tersedia'
                  AND d.expired_at > NOW()";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (d.judul LIKE :kw1 OR d.deskripsi LIKE :kw2 OR d.lokasi_ambil LIKE :kw3)";
            $params[':kw1'] = '%' . trim($keyword) . '%';
            $params[':kw2'] = '%' . trim($keyword) . '%';
            $params[':kw3'] = '%' . trim($keyword) . '%';
        }

        if (!empty($lokasi)) {
            $sql .= " AND d.lokasi_ambil LIKE :lokasi";
            $params[':lokasi'] = '%' . trim($lokasi) . '%';
        }

        if (!empty($kategori)) {
            $sql .= " AND d.kategori = :kategori";
            $params[':kategori'] = $kategori;
        }

        if ($filterHarga === 'gratis') {
            $sql .= " AND (d.tipe_penawaran = 'gratis_peduli' OR d.harga_diskon = 0)";
        } elseif ($filterHarga === 'under_10k') {
            $sql .= " AND d.harga_diskon > 0 AND d.harga_diskon <= 10000";
        } elseif ($filterHarga === 'diskon_tinggi') {
            $sql .= " AND d.harga_asli > 0 AND ((d.harga_asli - d.harga_diskon) / d.harga_asli) >= 0.7";
        }

        $sql .= " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Menghitung total penawaran makanan tersedia untuk pagination katalog
     */
    public function countAvailable(
        string $keyword = '',
        string $lokasi = '',
        string $kategori = '',
        string $filterHarga = ''
    ): int {
        $sql = "SELECT COUNT(*) as total
                FROM donations d
                WHERE d.status = 'tersedia'
                  AND d.expired_at > NOW()";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (d.judul LIKE :kw1 OR d.deskripsi LIKE :kw2 OR d.lokasi_ambil LIKE :kw3)";
            $params[':kw1'] = '%' . trim($keyword) . '%';
            $params[':kw2'] = '%' . trim($keyword) . '%';
            $params[':kw3'] = '%' . trim($keyword) . '%';
        }

        if (!empty($lokasi)) {
            $sql .= " AND d.lokasi_ambil LIKE :lokasi";
            $params[':lokasi'] = '%' . trim($lokasi) . '%';
        }

        if (!empty($kategori)) {
            $sql .= " AND d.kategori = :kategori";
            $params[':kategori'] = $kategori;
        }

        if ($filterHarga === 'gratis') {
            $sql .= " AND (d.tipe_penawaran = 'gratis_peduli' OR d.harga_diskon = 0)";
        } elseif ($filterHarga === 'under_10k') {
            $sql .= " AND d.harga_diskon > 0 AND d.harga_diskon <= 10000";
        } elseif ($filterHarga === 'diskon_tinggi') {
            $sql .= " AND d.harga_asli > 0 AND ((d.harga_asli - d.harga_diskon) / d.harga_asli) >= 0.7";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    /**
     * Mencari detail menu makanan berdasarkan ID beserta profil mitra toko
     */
    public function findById(int $id): ?array {
        $this->markExpired();

        $stmt = $this->db->prepare(
            "SELECT d.*, u.nama AS pemilik, u.email AS email_pemilik, u.telepon AS telepon_pemilik, u.alamat AS alamat_pemilik
             FROM donations d
             JOIN users u ON u.id = d.user_id
             WHERE d.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $donation = $stmt->fetch();

        return $donation ?: null;
    }

    /**
     * Menambahkan penawaran menu hemat baru dari toko
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO donations (
                user_id, judul, deskripsi, jumlah, sisa, satuan, harga_asli, harga_diskon,
                jam_tutup, tipe_penawaran, jaminan_kualitas, kategori, lokasi_ambil, foto, status, expired_at
            ) VALUES (
                :user_id, :judul, :deskripsi, :jumlah, :sisa, :satuan, :harga_asli, :harga_diskon,
                :jam_tutup, :tipe_penawaran, :jaminan_kualitas, :kategori, :lokasi_ambil, :foto, 'tersedia', :expired_at
            )"
        );

        $stmt->execute([
            ':user_id'          => (int)$data['user_id'],
            ':judul'            => $data['judul'],
            ':deskripsi'        => $data['deskripsi'] ?? null,
            ':jumlah'           => (int)$data['jumlah'],
            ':sisa'             => (int)$data['jumlah'],
            ':satuan'           => $data['satuan'] ?? 'porsi',
            ':harga_asli'       => (int)($data['harga_asli'] ?? 0),
            ':harga_diskon'     => (int)($data['harga_diskon'] ?? 0),
            ':jam_tutup'        => $data['jam_tutup'] ?? '21:00 WIB',
            ':tipe_penawaran'   => $data['tipe_penawaran'] ?? 'diskon_toko',
            ':jaminan_kualitas' => $data['jaminan_kualitas'] ?? '100% Segar & Higienis Diproduksi Hari Ini',
            ':kategori'         => $data['kategori'] ?? 'makanan_berat',
            ':lokasi_ambil'     => $data['lokasi_ambil'],
            ':foto'             => $data['foto'] ?? null,
            ':expired_at'       => $data['expired_at'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mengubah data penawaran makanan
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE donations 
                SET judul = :judul, 
                    deskripsi = :deskripsi, 
                    jumlah = :jumlah, 
                    sisa = :sisa, 
                    satuan = :satuan, 
                    harga_asli = :harga_asli,
                    harga_diskon = :harga_diskon,
                    jam_tutup = :jam_tutup,
                    tipe_penawaran = :tipe_penawaran,
                    jaminan_kualitas = :jaminan_kualitas,
                    kategori = :kategori,
                    lokasi_ambil = :lokasi_ambil, 
                    expired_at = :expired_at";
        
        $params = [
            ':id'               => $id,
            ':judul'            => $data['judul'],
            ':deskripsi'        => $data['deskripsi'] ?? null,
            ':jumlah'           => (int)$data['jumlah'],
            ':sisa'             => (int)$data['sisa'],
            ':satuan'           => $data['satuan'] ?? 'porsi',
            ':harga_asli'       => (int)($data['harga_asli'] ?? 0),
            ':harga_diskon'     => (int)($data['harga_diskon'] ?? 0),
            ':jam_tutup'        => $data['jam_tutup'] ?? '21:00 WIB',
            ':tipe_penawaran'   => $data['tipe_penawaran'] ?? 'diskon_toko',
            ':jaminan_kualitas' => $data['jaminan_kualitas'] ?? '100% Segar & Higienis Diproduksi Hari Ini',
            ':kategori'         => $data['kategori'] ?? 'makanan_berat',
            ':lokasi_ambil'     => $data['lokasi_ambil'],
            ':expired_at'       => $data['expired_at'],
        ];

        if (isset($data['foto']) && $data['foto'] !== null) {
            $sql .= ", foto = :foto";
            $params[':foto'] = $data['foto'];
        }

        if (isset($data['status'])) {
            $sql .= ", status = :status";
            $params[':status'] = $data['status'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Mengambil semua menu makanan untuk Moderasi Admin
     */
    public function allForAdmin(): array {
        $this->markExpired();
        $stmt = $this->db->query(
            "SELECT d.*, u.nama AS nama_donatur, u.email AS email_donatur,
                    (SELECT COUNT(*) FROM requests r WHERE r.donation_id = d.id) AS total_requests
             FROM donations d
             JOIN users u ON u.id = d.user_id
             ORDER BY d.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Menghapus penawaran makanan
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM donations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Mengambil semua penawaran milik toko tertentu
     */
    public function myByUser(int $userId): array {
        $this->markExpired();

        $stmt = $this->db->prepare(
            "SELECT d.*, 
                    (SELECT COUNT(*) FROM requests r WHERE r.donation_id = d.id AND r.status = 'menunggu') AS pending_requests_count,
                    (SELECT COUNT(*) FROM requests r WHERE r.donation_id = d.id) AS total_requests_count
             FROM donations d
             WHERE d.user_id = :user_id
             ORDER BY d.created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengurangi sisa porsi penawaran
     */
    public function decSisa(int $id, int $amount): bool {
        $stmt = $this->db->prepare('UPDATE donations SET sisa = sisa - :amount WHERE id = :id AND sisa >= :amount');
        return $stmt->execute([':id' => $id, ':amount' => $amount]);
    }

    /**
     * Menambah kembali sisa porsi penawaran (jika request ditolak/dibatalkan)
     */
    public function incSisa(int $id, int $amount): bool {
        $stmt = $this->db->prepare('UPDATE donations SET sisa = LEAST(jumlah, sisa + :amount) WHERE id = :id');
        return $stmt->execute([':id' => $id, ':amount' => $amount]);
    }

    /**
     * Mengubah status penawaran makanan secara langsung
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE donations SET status = :status WHERE id = :id');
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }

    /**
     * Mengambil daftar lokasi unik untuk filter dropdown
     */
    public function distinctLocations(): array {
        $stmt = $this->db->query(
            "SELECT DISTINCT lokasi_ambil 
             FROM donations 
             WHERE status = 'tersedia' AND expired_at > NOW() 
             ORDER BY lokasi_ambil ASC 
             LIMIT 20"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Menghitung statistik dampak nyata untuk visualisasi metrik 3D
     */
    public function getStats(): array {
        $stmt = $this->db->query(
            "SELECT 
                COALESCE(SUM(jumlah), 0) AS total_makanan_didonasikan,
                COALESCE(SUM(CASE WHEN status = 'selesai' THEN jumlah ELSE (jumlah - sisa) END), 0) AS total_makanan_terselamatkan,
                COALESCE(SUM(CASE WHEN (jumlah - sisa) > 0 THEN (jumlah - sisa) * (harga_asli - harga_diskon) ELSE 0 END), 0) AS total_rupiah_dihemat,
                COUNT(CASE WHEN status = 'tersedia' AND expired_at > NOW() THEN 1 END) AS total_tersedia,
                (SELECT COUNT(*) FROM users WHERE role = 'donatur') AS total_donatur,
                (SELECT COUNT(*) FROM users WHERE role = 'penerima') AS total_penerima,
                (SELECT COUNT(*) FROM requests WHERE status = 'diambil') AS total_terdistribusi
             FROM donations"
        );
        return $stmt->fetch() ?: [
            'total_makanan_didonasikan'   => 0,
            'total_makanan_terselamatkan' => 0,
            'total_rupiah_dihemat'        => 0,
            'total_tersedia'              => 0,
            'total_donatur'               => 0,
            'total_penerima'              => 0,
            'total_terdistribusi'         => 0,
        ];
    }
}
