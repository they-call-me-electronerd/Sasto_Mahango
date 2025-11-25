<?php
/**
 * Database Configuration and Connection
 * 
 * This file establishes a secure database connection using PDO
 * with prepared statements to prevent SQL injection.
 */

require_once __DIR__ . '/env.php';

// Database configuration constants from environment
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_NAME', Env::get('DB_NAME', 'mulyasuchi_db'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', ''));
define('DB_CHARSET', Env::get('DB_CHARSET', 'utf8mb4'));

// PDO options for security and error handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true, // Connection pooling
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Set timezone for database connection
    $pdo->exec("SET time_zone = '+05:45'"); // Nepal timezone
} catch (PDOException $e) {
    // Log error securely (don't expose database details to users)
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Show generic error in production
    if (Env::isProduction()) {
        die("Service temporarily unavailable. Please try again later.");
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

?>
