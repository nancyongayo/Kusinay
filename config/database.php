<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kusinay_db');

// AES-128 encryption key (16 bytes = 128 bits) — store in env in production
define('AES_KEY', 'KusiNay@2026!Sec'); // exactly 16 chars

// Set PHP timezone to Philippine Standard Time (UTC+8)
date_default_timezone_set('Asia/Manila');

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        // Sync MySQL timezone with PHP timezone (now Asia/Manila = +08:00)
        $offset = (new DateTime())->format('P'); // +08:00
        $pdo->exec("SET time_zone = '$offset'");
    }
    return $pdo;
}
