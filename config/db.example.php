<?php
/**
 * Database connection (PDO) — EXAMPLE / TEMPLATE.
 *
 * Copy this file to  config/db.php  and set the credentials for THIS
 * environment. config/db.php is git-ignored, so local and server each keep
 * their own credentials and never overwrite each other.
 */

// --- Local (XAMPP) ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'glasswall');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// --- Server (fill in your host's values, then comment out the local block above) ---
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'your_db_name');
// define('DB_USER', 'your_db_user');
// define('DB_PASS', 'your_db_password');
// define('DB_CHARSET', 'utf8mb4');

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Database connection failed: ' . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}

/** Escape helper for safe HTML output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
