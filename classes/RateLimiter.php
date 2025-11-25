<?php
/**
 * Rate Limiting Class
 * Prevents brute force attacks and API abuse
 */

class RateLimiter {
    private $enabled;
    private $maxAttempts;
    private $decayMinutes;
    private $storageFile;
    
    public function __construct() {
        $this->enabled = filter_var(Env::get('RATE_LIMIT_ENABLED', 'true'), FILTER_VALIDATE_BOOLEAN);
        $this->maxAttempts = (int)Env::get('RATE_LIMIT_MAX_ATTEMPTS', 5);
        $this->decayMinutes = (int)Env::get('RATE_LIMIT_DECAY_MINUTES', 15);
        $this->storageFile = __DIR__ . '/../logs/rate_limits.json';
        
        // Create logs directory if it doesn't exist
        $logsDir = dirname($this->storageFile);
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
    }
    
    /**
     * Check if request is allowed
     */
    public function attempt($key, $maxAttempts = null, $decayMinutes = null) {
        if (!$this->enabled) {
            return true;
        }
        
        $maxAttempts = $maxAttempts ?? $this->maxAttempts;
        $decayMinutes = $decayMinutes ?? $this->decayMinutes;
        
        $limits = $this->getLimits();
        $now = time();
        $key = $this->makeKey($key);
        
        // Clean old entries
        $this->cleanOldEntries($limits, $now);
        
        // Check if key exists and is still valid
        if (isset($limits[$key])) {
            $data = $limits[$key];
            
            // Check if decay period has passed
            if ($now - $data['time'] > ($decayMinutes * 60)) {
                unset($limits[$key]);
            } else {
                // Check if max attempts exceeded
                if ($data['attempts'] >= $maxAttempts) {
                    $this->saveLimits($limits);
                    return false;
                }
                
                // Increment attempts
                $limits[$key]['attempts']++;
                $this->saveLimits($limits);
                return true;
            }
        }
        
        // First attempt
        $limits[$key] = [
            'attempts' => 1,
            'time' => $now
        ];
        
        $this->saveLimits($limits);
        return true;
    }
    
    /**
     * Check if too many attempts
     */
    public function tooManyAttempts($key, $maxAttempts = null) {
        if (!$this->enabled) {
            return false;
        }
        
        $maxAttempts = $maxAttempts ?? $this->maxAttempts;
        $limits = $this->getLimits();
        $key = $this->makeKey($key);
        
        if (!isset($limits[$key])) {
            return false;
        }
        
        return $limits[$key]['attempts'] >= $maxAttempts;
    }
    
    /**
     * Get remaining attempts
     */
    public function retriesLeft($key, $maxAttempts = null) {
        if (!$this->enabled) {
            return $maxAttempts ?? $this->maxAttempts;
        }
        
        $maxAttempts = $maxAttempts ?? $this->maxAttempts;
        $limits = $this->getLimits();
        $key = $this->makeKey($key);
        
        if (!isset($limits[$key])) {
            return $maxAttempts;
        }
        
        return max(0, $maxAttempts - $limits[$key]['attempts']);
    }
    
    /**
     * Get available in seconds
     */
    public function availableIn($key) {
        if (!$this->enabled) {
            return 0;
        }
        
        $limits = $this->getLimits();
        $key = $this->makeKey($key);
        
        if (!isset($limits[$key])) {
            return 0;
        }
        
        $elapsed = time() - $limits[$key]['time'];
        $decaySeconds = $this->decayMinutes * 60;
        
        return max(0, $decaySeconds - $elapsed);
    }
    
    /**
     * Clear rate limit for a key
     */
    public function clear($key) {
        $limits = $this->getLimits();
        $key = $this->makeKey($key);
        
        if (isset($limits[$key])) {
            unset($limits[$key]);
            $this->saveLimits($limits);
        }
    }
    
    /**
     * Make a unique key
     */
    private function makeKey($key) {
        return sha1($key);
    }
    
    /**
     * Get all rate limits
     */
    private function getLimits() {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        
        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?? [];
    }
    
    /**
     * Save rate limits
     */
    private function saveLimits($limits) {
        file_put_contents($this->storageFile, json_encode($limits), LOCK_EX);
    }
    
    /**
     * Clean old entries
     */
    private function cleanOldEntries(&$limits, $now) {
        $decaySeconds = $this->decayMinutes * 60;
        
        foreach ($limits as $key => $data) {
            if ($now - $data['time'] > $decaySeconds) {
                unset($limits[$key]);
            }
        }
    }
    
    /**
     * Get rate limit key for login
     */
    public static function loginKey($username, $ip) {
        return 'login:' . $username . ':' . $ip;
    }
    
    /**
     * Get rate limit key for IP
     */
    public static function ipKey($ip, $action = 'general') {
        return $action . ':' . $ip;
    }
}
?>
