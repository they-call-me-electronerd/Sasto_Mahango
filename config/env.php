<?php
/**
 * Environment Variable Loader
 * Loads configuration from .env file
 */

class Env {
    private static $loaded = false;
    private static $variables = [];
    
    /**
     * Load environment variables from .env file
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }
        
        if (!file_exists($path)) {
            // Try .env.example as fallback in development
            $examplePath = __DIR__ . '/../.env.example';
            if (file_exists($examplePath) && getenv('APP_ENV') !== 'production') {
                $path = $examplePath;
            } else {
                throw new Exception('.env file not found. Copy .env.example to .env and configure.');
            }
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                self::$variables[$key] = $value;
                
                // Also set as environment variable
                if (!getenv($key)) {
                    putenv("$key=$value");
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$variables[$key] ?? getenv($key) ?: $default;
    }
    
    /**
     * Check if running in production
     */
    public static function isProduction() {
        return self::get('APP_ENV') === 'production';
    }
    
    /**
     * Check if debug mode is enabled
     */
    public static function isDebug() {
        return filter_var(self::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
    }
}

// Auto-load on include
Env::load();
?>
