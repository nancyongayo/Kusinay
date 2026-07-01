<?php
// ═══════════════════════════════════════════════════════════════════════════════
// DATABASE CONFIGURATION - DEPLOYMENT READY
// ═══════════════════════════════════════════════════════════════════════════════
// 
// Instructions for InfinityFree Deployment:
// 1. Set ENVIRONMENT to 'production' when deploying
// 2. Your InfinityFree database credentials are already configured below
// 3. All other files will automatically use these settings
//
// ═══════════════════════════════════════════════════════════════════════════════

// Environment setting - CHANGE TO 'production' FOR INFINITYFREE
define('ENVIRONMENT', 'production'); // 'development' or 'production'

// Development Database Configuration (localhost/XAMPP)
define('DEV_DB_HOST', 'localhost');
define('DEV_DB_USER', 'root');
define('DEV_DB_PASS', '');
define('DEV_DB_NAME', 'kusinay_db');

// Production Database Configuration (InfinityFree) - YOUR ACTUAL CREDENTIALS
define('PROD_DB_HOST', 'sql202.infinityfree.com');     // Your REAL InfinityFree DB host
define('PROD_DB_USER', 'if0_42280026');                // Your REAL InfinityFree DB username  
define('PROD_DB_PASS', 'CELCPOwsLNg');                 // Your InfinityFree DB password (check this!)
define('PROD_DB_NAME', 'if0_42280026_kusinay_db');     // Your REAL InfinityFree DB name

// Automatically set database constants based on environment
if (ENVIRONMENT === 'production') {
    define('DB_HOST', PROD_DB_HOST);
    define('DB_USER', PROD_DB_USER);
    define('DB_PASS', PROD_DB_PASS);
    define('DB_NAME', PROD_DB_NAME);
} else {
    define('DB_HOST', DEV_DB_HOST);
    define('DB_USER', DEV_DB_USER);
    define('DB_PASS', DEV_DB_PASS);
    define('DB_NAME', DEV_DB_NAME);
}

// AES-128 encryption key (16 bytes = 128 bits) — store in env in production
define('AES_KEY', 'KusiNay@2026!Sec'); // exactly 16 chars

// Security constants
define('MAX_FAILED_ATTEMPTS', 5);
define('LOCKOUT_MINUTES', 15);
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('OTP_EXPIRY_MINUTES', 10);

// Set PHP timezone to Philippine Standard Time (UTC+8)
date_default_timezone_set('Asia/Manila');

// ═══════════════════════════════════════════════════════════════════════════════
// DATABASE CONNECTION FUNCTION
// ═══════════════════════════════════════════════════════════════════════════════

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 30, // Useful for shared hosting
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Sync MySQL timezone with PHP timezone (now Asia/Manila = +08:00)
            $offset = (new DateTime())->format('P'); // +08:00
            $pdo->exec("SET time_zone = '$offset'");
            
            // Log successful connection (only in development)
            if (ENVIRONMENT === 'development') {
                error_log("✅ Database connected successfully to " . DB_HOST . "/" . DB_NAME);
            }
            
        } catch (PDOException $e) {
            // Log error details
            error_log("❌ Database connection failed: " . $e->getMessage());
            error_log("   Host: " . DB_HOST . ", Database: " . DB_NAME . ", User: " . DB_USER);
            
            // In production, show user-friendly error
            if (ENVIRONMENT === 'production') {
                die("Database connection failed. Please contact the administrator.");
            } else {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }
    return $pdo;
}

// ═══════════════════════════════════════════════════════════════════════════════
// DEPLOYMENT HELPER FUNCTIONS
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Check if we can connect to the database
 * @return bool True if connection successful, false otherwise
 */
function testDatabaseConnection(): bool {
    try {
        getDBConnection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get current environment information
 * @return array Environment details
 */
function getEnvironmentInfo(): array {
    return [
        'environment' => ENVIRONMENT,
        'db_host' => DB_HOST,
        'db_name' => DB_NAME,
        'db_user' => DB_USER,
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    ];
}