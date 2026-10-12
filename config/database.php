<?php
/**
 * Koneksi Database PDO Singleton - Berbagi Rasa
 * Mendukung koneksi lokal (Laragon/XAMPP) & Cloud MySQL (Vercel Serverless, TiDB Cloud, Aiven, Railway)
 */

class Database {
    private static ?PDO $instance = null;

    /**
     * Mengembalikan instance PDO tunggal (Singleton)
     */
    public static function get(): PDO {
        if (self::$instance === null) {
            // Deteksi konfigurasi database dari Environment Variables (Cloud / Vercel)
            // Mendukung DATABASE_URL / MYSQL_URL atau variabel satuan
            $dbUrl = getenv('DATABASE_URL') ?: (getenv('MYSQL_URL') ?: ($_ENV['DATABASE_URL'] ?? ($_ENV['MYSQL_URL'] ?? '')));

            if (!empty($dbUrl)) {
                $parsed = parse_url($dbUrl);
                $host = $parsed['host'] ?? '127.0.0.1';
                $port = (string)($parsed['port'] ?? '3306');
                $user = $parsed['user'] ?? 'root';
                $pass = $parsed['pass'] ?? '';
                $db   = ltrim($parsed['path'] ?? 'berbagi_rasa', '/');
            } else {
                $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? (getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? '127.0.0.1')));
                $port = (string)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? (getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? '3306'))));
                $db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? (getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? (getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? 'berbagi_rasa')))));
                $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? (getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? (getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? 'root')))));
                $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ($_ENV['DB_PASSWORD'] ?? (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : ($_ENV['MYSQLPASSWORD'] ?? '')))));
            }

            $charset = 'utf8mb4';
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Aktifkan SSL otomatis jika terhubung ke cloud database eksternal (TiDB, Aiven, Railway, Supabase, dsb)
            $isRemote = !in_array($host, ['127.0.0.1', 'localhost', '::1']);
            $sslEnv = getenv('DB_SSL') ?: ($_ENV['DB_SSL'] ?? null);
            $sslRequested = ($sslEnv === 'true' || $sslEnv === '1' || ($isRemote && $sslEnv !== 'false' && $sslEnv !== '0'));

            if ($sslRequested && defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }

            $sslCa = getenv('DB_SSL_CA') ?: ($_ENV['DB_SSL_CA'] ?? null);
            if (!empty($sslCa) && defined('PDO::MYSQL_ATTR_SSL_CA')) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
            }

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
                try {
                    self::$instance->exec("SET time_zone = '+07:00'");
                } catch (Throwable $tzEx) {
                    // Abaikan jika database cloud tertentu membatasi SET time_zone
                }
            } catch (PDOException $e) {
                // Tampilkan pesan error informatif untuk mempermudah diagnosa koneksi
                die("Koneksi database gagal: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            }
        }

        return self::$instance;
    }
}
