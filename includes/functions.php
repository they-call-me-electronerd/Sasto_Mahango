<?php
/**
 * Helper Functions
 * 
 * Reusable utility functions for the application
 */

/**
 * Sanitize input to prevent XSS
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Upload image securely with enhanced validation
 */
function uploadImage($file, $oldImagePath = null) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        return ['success' => false, 'error' => $errors[$file['error']] ?? 'Upload error'];
    }
    
    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds ' . (MAX_FILE_SIZE / 1048576) . 'MB limit'];
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = unserialize(ALLOWED_EXTENSIONS);
    
    // Validate extension
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)];
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedTypes = unserialize(ALLOWED_IMAGE_TYPES);
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file MIME type'];
    }
    
    // Verify it's actually an image
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'error' => 'File is not a valid image'];
    }
    
    // Prevent double extension attacks
    $fullName = strtolower($file['name']);
    if (preg_match('/\\.(php|phtml|php3|php4|php5|phps|phar)/', $fullName)) {
        return ['success' => false, 'error' => 'Suspicious file detected'];
    }
    
    // Generate unique, safe filename
    $filename = uniqid('item_', true) . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    // Ensure upload directory exists
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create upload directory'];
        }
    }
    
    // Process and optimize image (if GD extension is available)
    if (extension_loaded('gd')) {
        try {
            $image = null;
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file['tmp_name']);
                    break;
                case IMAGETYPE_WEBP:
                    $image = imagecreatefromwebp($file['tmp_name']);
                    break;
                default:
                    return ['success' => false, 'error' => 'Unsupported image format'];
            }
            
            if ($image === false) {
                return ['success' => false, 'error' => 'Failed to process image'];
            }
            
            // Get original dimensions
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);
        
            // Resize if too large (max 1200px width)
            $maxWidth = 1200;
            if ($origWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = ($origHeight * $maxWidth) / $origWidth;
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG
                if ($imageInfo[2] === IMAGETYPE_PNG) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }
                
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagedestroy($image);
                $image = $resized;
            }
            
            // Save optimized image (strips EXIF data)
            $saved = false;
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $saved = imagejpeg($image, $destination, 85); // 85% quality
            } elseif ($extension === 'png') {
                $saved = imagepng($image, $destination, 8); // Compression level 8
            } elseif ($extension === 'webp') {
                $saved = imagewebp($image, $destination, 85);
            }
            
            imagedestroy($image);
            
            if (!$saved) {
                return ['success' => false, 'error' => 'Failed to save image'];
            }
            
            // Set file permissions
            chmod($destination, 0644);
            
        } catch (Exception $e) {
            error_log('Image processing error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Image processing failed'];
        }
    } else {
        // GD not available, just move the file without processing
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Failed to save image'];
        }
        chmod($destination, 0644);
    }
    
    // Delete old image if exists
    if ($oldImagePath && file_exists(UPLOAD_DIR . $oldImagePath)) {
        @unlink(UPLOAD_DIR . $oldImagePath);
    }
    
    return ['success' => true, 'filename' => $filename];
}

/**
 * Normalize stored image paths to filenames for consistent handling
 */
function normalizeItemImagePath($imagePath) {
    if (empty($imagePath)) {
        return null;
    }
    $sanitized = trim(str_replace('\\', '/', $imagePath));
    $basename = basename($sanitized);
    return $basename ?: null;
}

/**
 * Build the absolute path for an item image if available
 */
function getItemImageFilePath($imagePath) {
    $filename = normalizeItemImagePath($imagePath);
    if (!$filename) {
        return null;
    }
    return rtrim(UPLOAD_DIR, "/\\") . DIRECTORY_SEPARATOR . $filename;
}

/**
 * Build the public URL for an item image
 */
function getItemImageUrl($imagePath) {
    $filename = normalizeItemImagePath($imagePath);
    if (!$filename) {
        return null;
    }
    return rtrim(UPLOAD_URL, '/') . '/' . rawurlencode($filename);
}

/**
 * Determine if an item image exists on disk
 */
function itemHasImage($imagePath) {
    $filePath = getItemImageFilePath($imagePath);
    return $filePath ? file_exists($filePath) : false;
}

/**
 * Set flash message
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Check if flash message exists
 */
function hasFlashMessage() {
    return isset($_SESSION['flash_message']);
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        
        return ['type' => $type, 'message' => $message];
    }
    
    return null;
}

/**
 * Format price in Nepali currency
 */
function formatPrice($price) {
    return 'Rs. ' . number_format($price, 2);
}

/**
 * Get price change indicator HTML
 */
function getPriceChangeIndicator($changePercent) {
    if ($changePercent > 0) {
        return '<span class="price-up">▲ ' . abs($changePercent) . '%</span>';
    } elseif ($changePercent < 0) {
        return '<span class="price-down">▼ ' . abs($changePercent) . '%</span>';
    } else {
        return '<span class="price-neutral">━ 0%</span>';
    }
}

/**
 * Time ago helper
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

/**
 * Generate pagination HTML
 */
function generatePagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="prev">« Previous</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="current">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="next">Next »</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Redirect helper
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

?>
