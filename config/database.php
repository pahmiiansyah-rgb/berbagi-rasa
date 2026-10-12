<?php
/**
 * Koneksi Database PDO Singleton - Berbagi Rasa
 */

class Database {
    private static ?PDO $instance = null;

    private static string $host = '127.0.0.1';
    private static string $db   = 'berbagi_rasa';
    private static string $user = 'root';
    private static string $pass = '';
    private static string $charset = 'utf8mb4';

    /**
     * Mengembalikan instance PDO tunggal (Singleton)
     */
    public static function get(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, $options);
                self::$instance->exec("SET time_zone = '+07:00'");
            } catch (PDOException $e) {
                // Di level produksi tampilkan pesan ramah tanpa membocorkan kredensial
                die("Koneksi database gagal: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            }
        }

        return self::$instance;
    }
}
