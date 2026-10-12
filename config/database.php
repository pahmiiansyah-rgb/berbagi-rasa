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
                http_response_code(500);
                $isVercel = !empty(getenv('VERCEL')) || !empty($_ENV['VERCEL']) || strpos($_SERVER['HTTP_HOST'] ?? '', 'vercel.app') !== false;
                $maskedPass = !empty($pass) ? (substr($pass, 0, 2) . str_repeat('•', max(4, strlen($pass) - 2))) : '(kosong)';
                $rawError = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                
                // Jika koneksi gagal, berikan panduan visual langsung di browser
                ?>
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Koneksi Database Cloud Diperlukan — Berbagi Rasa</title>
                    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
                    <style>
                        * { box-sizing: border-box; margin: 0; padding: 0; }
                        body {
                            background: #0b0f19;
                            color: #f3f4f6;
                            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            min-height: 100vh;
                            padding: 24px;
                        }
                        .diag-card {
                            background: #111827;
                            border: 1px solid rgba(255, 255, 255, 0.1);
                            border-radius: 16px;
                            max-width: 680px;
                            width: 100%;
                            padding: 36px;
                            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
                        }
                        .badge {
                            display: inline-block;
                            padding: 6px 14px;
                            border-radius: 9999px;
                            font-size: 12px;
                            font-weight: 700;
                            letter-spacing: 0.5px;
                            text-transform: uppercase;
                            background: rgba(245, 158, 11, 0.15);
                            color: #fbbf24;
                            border: 1px solid rgba(245, 158, 11, 0.3);
                            margin-bottom: 16px;
                        }
                        h1 {
                            font-size: 24px;
                            font-weight: 800;
                            color: #ffffff;
                            margin-bottom: 12px;
                            line-height: 1.3;
                        }
                        p.lead {
                            color: #9ca3af;
                            font-size: 14px;
                            line-height: 1.6;
                            margin-bottom: 24px;
                        }
                        .error-box {
                            background: #1f1415;
                            border-left: 4px solid #ef4444;
                            padding: 14px 16px;
                            border-radius: 8px;
                            margin-bottom: 24px;
                        }
                        .error-box p {
                            font-size: 12px;
                            color: #fca5a5;
                            font-family: monospace;
                            word-break: break-all;
                        }
                        .env-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 24px;
                            font-size: 13px;
                            background: #0f172a;
                            border-radius: 8px;
                            overflow: hidden;
                        }
                        .env-table th, .env-table td {
                            padding: 10px 14px;
                            text-align: left;
                            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                        }
                        .env-table th {
                            background: rgba(255, 255, 255, 0.03);
                            color: #94a3b8;
                            font-weight: 600;
                        }
                        .env-table td code {
                            background: rgba(255, 255, 255, 0.08);
                            padding: 2px 6px;
                            border-radius: 4px;
                            color: #38bdf8;
                            font-family: monospace;
                        }
                        .steps {
                            margin-bottom: 24px;
                        }
                        .step-item {
                            display: flex;
                            gap: 12px;
                            margin-bottom: 14px;
                            font-size: 13px;
                            color: #d1d5db;
                            line-height: 1.5;
                        }
                        .step-num {
                            background: #10b981;
                            color: #064e3b;
                            font-weight: 800;
                            width: 22px;
                            height: 22px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 12px;
                            flex-shrink: 0;
                        }
                        .step-item a {
                            color: #38bdf8;
                            text-decoration: underline;
                        }
                        .btn-action {
                            display: inline-block;
                            background: #10b981;
                            color: #ffffff;
                            font-weight: 700;
                            padding: 12px 24px;
                            border-radius: 8px;
                            text-decoration: none;
                            font-size: 14px;
                            text-align: center;
                        }
                        .btn-action:hover {
                            background: #059669;
                        }
                    </style>
                </head>
                <body>
                    <div class="diag-card">
                        <span class="badge">Langkah Terakhir Deployment</span>
                        <h1>Hubungkan Database Cloud ke Vercel</h1>
                        <p class="lead">
                            Aplikasi PHP Berbagi Rasa telah <strong>berhasil berjalan di Vercel</strong>, namun belum dapat terhubung ke server database MySQL.
                            Karena Vercel adalah platform <em>Serverless Cloud</em>, sistem tidak memiliki server MySQL lokal (<code>127.0.0.1</code>).
                        </p>

                        <div class="error-box">
                            <p><strong>Pesan Sistem:</strong> <?= $rawError ?></p>
                        </div>

                        <h3 style="font-size: 14px; color: #e2e8f0; margin-bottom: 10px;">Status Konfigurasi Saat Ini:</h3>
                        <table class="env-table">
                            <tr><th>Variabel</th><th>Nilai Terdeteksi</th><th>Status</th></tr>
                            <tr><td><code>DB_HOST</code></td><td><code><?= htmlspecialchars($host, ENT_QUOTES) ?></code></td><td><?= ($host === '127.0.0.1' || $host === 'localhost') ? '<span style="color:#f87171;">⚠️ Belum diatur (masih localhost)</span>' : '<span style="color:#4ade80;">✓ Terisi</span>' ?></td></tr>
                            <tr><td><code>DB_PORT</code></td><td><code><?= htmlspecialchars($port, ENT_QUOTES) ?></code></td><td>Default 3306</td></tr>
                            <tr><td><code>DB_NAME</code></td><td><code><?= htmlspecialchars($db, ENT_QUOTES) ?></code></td><td>-</td></tr>
                            <tr><td><code>DB_USER</code></td><td><code><?= htmlspecialchars($user, ENT_QUOTES) ?></code></td><td>-</td></tr>
                            <tr><td><code>DB_PASS</code></td><td><code><?= htmlspecialchars($maskedPass, ENT_QUOTES) ?></code></td><td>-</td></tr>
                        </table>

                        <h3 style="font-size: 14px; color: #e2e8f0; margin-bottom: 12px;">Cara Mengatasinya (Hanya butuh 2-3 menit):</h3>
                        <div class="steps">
                            <div class="step-item">
                                <span class="step-num">1</span>
                                <div>Buat database MySQL gratis di <a href="https://tidbcloud.com" target="_blank" rel="noopener">TiDB Cloud</a> (pilih <strong>Serverless Free</strong>) atau <a href="https://aiven.io" target="_blank" rel="noopener">Aiven for MySQL</a>.</div>
                            </div>
                            <div class="step-item">
                                <span class="step-num">2</span>
                                <div>Di TiDB Cloud / Aiven, buka <strong>SQL Editor</strong>, tempel isi file <a href="https://github.com/pahmiiansyah-rgb/berbagi-rasa/blob/main/database.sql" target="_blank" rel="noopener"><code>database.sql</code></a> dari repositori Anda, lalu klik <strong>Run</strong> untuk membuat tabel & akun demo.</div>
                            </div>
                            <div class="step-item">
                                <span class="step-num">3</span>
                                <div>Buka <strong>Vercel Dashboard > Project berbagi-rasa > Settings > Environment Variables</strong>, lalu masukkan <code>DB_HOST</code>, <code>DB_PORT</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code>, dan <code>DB_SSL=true</code>.</div>
                            </div>
                            <div class="step-item">
                                <span class="step-num">4</span>
                                <div>Buka menu <strong>Deployments</strong> di Vercel, klik tombol titik tiga (<code>...</code>) di deployment terbaru, lalu pilih <strong>Redeploy</strong> agar variabel baru aktif.</div>
                            </div>
                        </div>

                        <a href="https://vercel.com/dashboard" target="_blank" rel="noopener" class="btn-action">Buka Dashboard Vercel &rarr;</a>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }
        }

        return self::$instance;
    }
}
