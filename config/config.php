<?php
/**
 * Site Configuration
 * 
 * Global configuration settings for the Mulyasuchi platform
 */

require_once __DIR__ . '/env.php';

// Load security functions
require_once __DIR__ . '/security.php';

// Prevent direct access
if (!defined('MULYASUCHI_APP')) {
    die('Direct access not permitted');
}

// Site Information
define('SITE_NAME', Env::get('SITE_NAME', 'Mulyasuchi'));
define('SITE_TAGLINE', 'Your Trusted Market Intelligence Platform');
define('SITE_URL', Env::get('SITE_URL', 'http://localhost/MulyaSuchi'));
define('SITE_EMAIL', Env::get('SITE_EMAIL', 'contact@mulyasuchi.com'));

// File Upload Configuration
define('UPLOAD_DIR', Env::get('UPLOAD_DIR', __DIR__ . '/../assets/uploads/items/'));
define('UPLOAD_URL', SITE_URL . '/assets/uploads/items/');
define('MAX_FILE_SIZE', (int)Env::get('MAX_FILE_SIZE', 5242880)); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', serialize(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']));
define('ALLOWED_EXTENSIONS', serialize(['jpg', 'jpeg', 'png', 'webp']));

// Pagination
define('ITEMS_PER_PAGE', 20);

// Session Configuration
define('SESSION_LIFETIME', (int)Env::get('SESSION_LIFETIME', 3600)); // 1 hour in seconds
define('SESSION_NAME', Env::get('SESSION_NAME', 'MULYASUCHI_SESSION'));

// Security
define('CSRF_TOKEN_NAME', Env::get('CSRF_TOKEN_NAME', 'csrf_token'));
define('PASSWORD_MIN_LENGTH', (int)Env::get('PASSWORD_MIN_LENGTH', 8));

// Timezone
date_default_timezone_set('Asia/Kathmandu');

// Error Reporting (production-safe)
if (Env::isProduction()) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('log_errors', 1);
    ini_set('error_log', Env::get('LOG_FILE', __DIR__ . '/../logs/error.log'));
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    
    $isSecure = Env::isProduction() || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly' => true,
        'cookie_secure' => $isSecure, // HTTPS only in production
        'cookie_samesite' => 'Strict', // CSRF protection
        'use_strict_mode' => true,
        'use_only_cookies' => true,
        'sid_length' => 48,
        'sid_bits_per_character' => 6
    ]);
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerate every 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

?>
