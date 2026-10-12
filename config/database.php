<?php
/**
 * Koneksi Database PDO Singleton - Berbagi Rasa
 * Mendukung koneksi lokal (Laragon/XAMPP), Cloud MySQL (TiDB Cloud, Aiven, Railway),
 * dan Auto-Fallback SQLite Mandiri untuk Vercel Serverless tanpa konfigurasi tambahan.
 */

class Database {
    private static ?PDO $instance = null;

    /**
     * Mengembalikan instance PDO tunggal (Singleton)
     */
    public static function get(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // 1. Deteksi apakah konfigurasi MySQL Cloud/Lokal tersedia
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

        // Coba koneksi ke MySQL terlebih dahulu
        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
            try {
                self::$instance->exec("SET time_zone = '+07:00'");
            } catch (Throwable $tzEx) {
                // Abaikan jika database cloud tertentu membatasi SET time_zone
            }
            return self::$instance;
        } catch (PDOException $e) {
            // 2. Jika MySQL tidak dapat dihubungi (misal di Vercel tanpa DB_HOST atau Connection refused),
            // aktifkan fallback otomatis ke database SQLite mandiri yang telah dipaketkan
            $sqliteInstance = self::trySqliteFallback();
            if ($sqliteInstance !== null) {
                self::$instance = $sqliteInstance;
                return self::$instance;
            }

            // 3. Jika fallback SQLite juga tidak tersedia, tampilkan pesan error
            http_response_code(500);
            $rawError = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            die("Koneksi database gagal: " . $rawError);
        }
    }

    /**
     * Fallback otomatis ke database SQLite mandiri saat berjalan di Vercel / Serverless
     * Memungkinkan website langsung aktif tanpa perlu setup database eksternal
     */
    private static function trySqliteFallback(): ?PDO {
        try {
            $bundledSqlite = realpath(__DIR__ . '/../data/database.sqlite');
            if (!$bundledSqlite || !file_exists($bundledSqlite)) {
                return null;
            }

            // Di Vercel, file bundle aplikasi bersifat read-only.
            // Kita salin database ke direktori sementara /tmp agar aplikasi dapat membaca & menulis data transaksi
            $tmpDir = sys_get_temp_dir();
            $targetSqlite = $tmpDir . DIRECTORY_SEPARATOR . 'berbagi_rasa.sqlite';

            if (!file_exists($targetSqlite) || (filemtime($bundledSqlite) > filemtime($targetSqlite))) {
                @copy($bundledSqlite, $targetSqlite);
            }

            $activePath = file_exists($targetSqlite) ? $targetSqlite : $bundledSqlite;

            $pdo = new PDO('sqlite:' . $activePath, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Daftarkan fungsi kompatibilitas MySQL untuk SQLite
            $pdo->sqliteCreateFunction('now', fn() => date('Y-m-d H:i:s'));
            $pdo->sqliteCreateFunction('date_add', function($date, $interval) {
                return date('Y-m-d H:i:s', strtotime($interval, strtotime($date)));
            });
            $pdo->sqliteCreateFunction('date_sub', function($date, $interval) {
                return date('Y-m-d H:i:s', strtotime('-' . ltrim($interval, '-'), strtotime($date)));
            });

            @$pdo->exec('PRAGMA foreign_keys = ON;');
            @$pdo->exec('PRAGMA journal_mode = WAL;');

            return $pdo;
        } catch (Throwable $t) {
            return null;
        }
    }
}
