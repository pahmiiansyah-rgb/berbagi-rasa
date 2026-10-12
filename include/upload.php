<?php
/**
 * Handler Upload Foto Aman (7 Lapis Proteksi) - Berbagi Rasa
 */

if (!function_exists('handleUpload')) {
    /**
     * Memproses upload file foto donasi dengan 7 lapis validasi ketat
     *
     * @param array $file Array $_FILES['foto']
     * @return array ['ok' => bool, 'filename' => string, 'error' => string]
     */
    function handleUpload(array $file): array {
        // 1. Cek apakah ada error saat upload
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['ok' => false, 'filename' => '', 'error' => 'Parameter upload file tidak valid.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return ['ok' => false, 'filename' => '', 'error' => 'Ukuran file melebihi batas maksimal server (2MB).'];
                case UPLOAD_ERR_PARTIAL:
                    return ['ok' => false, 'filename' => '', 'error' => 'File hanya terunggah sebagian. Silakan ulangi.'];
                case UPLOAD_ERR_NO_FILE:
                    return ['ok' => false, 'filename' => '', 'error' => 'Tidak ada file foto yang dipilih.'];
                default:
                    return ['ok' => false, 'filename' => '', 'error' => 'Terjadi kesalahan sistem saat mengunggah foto.'];
            }
        }

        // 2. Cek ukuran file (Maksimal 2 MB = 2.097.152 bytes)
        if ($file['size'] > MAX_FILE_SIZE) {
            $uploadedMb = round($file['size'] / (1024 * 1024), 2);
            return ['ok' => false, 'filename' => '', 'error' => "Ukuran foto maksimal 2MB (file Anda: {$uploadedMb}MB)."];
        }

        // 3. Cek MIME type riil dari isi file menggunakan mime_content_type
        $finfoMime = mime_content_type($file['tmp_name']);
        if (!in_array($finfoMime, ALLOWED_MIME, true)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Format foto tidak valid. Hanya JPG, PNG, dan WebP yang diizinkan.'];
        }

        // 4. Cek struktur gambar dengan getimagesize() untuk mencegah file biner palsu
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['ok' => false, 'filename' => '', 'error' => 'File yang diunggah bukan file gambar valid.'];
        }

        // 5. Cek ekstensi file
        $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($originalExt, ALLOWED_EXT, true)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Ekstensi file tidak didukung. Gunakan .jpg, .jpeg, .png, atau .webp.'];
        }

        // 6. Normalisasi ekstensi dan buat nama file acak unik
        $finalExt = ($originalExt === 'jpeg') ? 'jpg' : $originalExt;
        $uniqueName = uniqid('mkn_', true) . '.' . $finalExt;

        // 7. Pastikan direktori tujuan tersedia dan pindahkan file
        $targetDir = UPLOAD_DIR;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Pastikan ada file index.html pencegah directory listing
        $indexHtml = $targetDir . 'index.html';
        if (!file_exists($indexHtml)) {
            file_put_contents($indexHtml, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>');
        }

        $targetPath = $targetDir . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Gagal memindahkan file ke direktori penyimpanan server.'];
        }

        return [
            'ok' => true,
            'filename' => $uniqueName,
            'error' => ''
        ];
    }
}
