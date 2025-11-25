<?php
/**
 * Security Headers
 * Sets HTTP security headers for all responses
 */

// Prevent direct access
if (!defined('MULYASUCHI_APP')) {
    die('Direct access not permitted');
}

/**
 * Set security headers
 */
function setSecurityHeaders() {
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // XSS Protection (legacy browsers)
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.socket.io",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
        "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
        "img-src 'self' data: https: blob:",
        "connect-src 'self'",
        "frame-ancestors 'none'",
        "base-uri 'self'",
        "form-action 'self'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
    
    // Permissions Policy (formerly Feature-Policy)
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    
    // HSTS (HTTP Strict Transport Security) - only in production with HTTPS
    if (Env::isProduction() && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // Remove server signature
    header_remove('X-Powered-By');
    header_remove('Server');
}

/**
 * Set cache headers for static assets
 */
function setCacheHeaders($type = 'default', $maxAge = 3600) {
    switch ($type) {
        case 'static':
            // Long cache for static assets (1 year)
            header('Cache-Control: public, max-age=31536000, immutable');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            break;
            
        case 'dynamic':
            // Short cache for dynamic content (5 minutes)
            header('Cache-Control: public, max-age=300, must-revalidate');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
            break;
            
        case 'no-cache':
            // No caching for sensitive pages
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Expires: 0');
            break;
            
        default:
            header("Cache-Control: public, max-age=$maxAge");
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
    }
}

/**
 * Get client IP address (handles proxies)
 */
function getClientIP() {
    $ip = null;
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Behind a proxy
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Validate IP address
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Apply security headers automatically
setSecurityHeaders();
?>
