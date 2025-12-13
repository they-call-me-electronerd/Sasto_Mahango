<?php
/**
 * Test Upload Diagnostic Tool
 * Use this to test if file uploads are working properly
 */

// Display PHP upload settings
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Diagnostic Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #f97316;
            margin-top: 0;
        }
        .info-row {
            display: flex;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            width: 250px;
        }
        .value {
            color: #666;
        }
        .success {
            color: #22c55e;
            font-weight: bold;
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        .warning {
            color: #f59e0b;
            font-weight: bold;
        }
        form {
            margin-top: 20px;
        }
        input[type="file"] {
            padding: 10px;
            border: 2px solid #f97316;
            border-radius: 4px;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            background: #f97316;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #ea580c;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>📊 PHP Upload Configuration</h2>
        <div class="info-row">
            <span class="label">File Uploads Enabled:</span>
            <span class="value <?php echo ini_get('file_uploads') ? 'success' : 'error'; ?>">
                <?php echo ini_get('file_uploads') ? 'YES ✓' : 'NO ✗'; ?>
            </span>
        </div>
        <div class="info-row">
            <span class="label">Upload Max Filesize:</span>
            <span class="value"><?php echo ini_get('upload_max_filesize'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Post Max Size:</span>
            <span class="value"><?php echo ini_get('post_max_size'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Max File Uploads:</span>
            <span class="value"><?php echo ini_get('max_file_uploads'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Upload Temp Dir:</span>
            <span class="value"><?php echo ini_get('upload_tmp_dir') ?: 'System Default'; ?></span>
        </div>
        <div class="info-row">
            <span class="label">Max Execution Time:</span>
            <span class="value"><?php echo ini_get('max_execution_time'); ?>s</span>
        </div>
        <div class="info-row">
            <span class="label">Memory Limit:</span>
            <span class="value"><?php echo ini_get('memory_limit'); ?></span>
        </div>
    </div>

    <div class="card">
        <h2>🧪 Test File Upload</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="test_file" accept="image/*" required>
            <button type="submit">Upload Test File</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<h3>Upload Test Results:</h3>';
            
            echo '<pre>';
            echo "POST Data Received: " . (empty($_POST) ? 'NO' : 'YES') . "\n";
            echo "FILES Data Received: " . (empty($_FILES) ? 'NO' : 'YES') . "\n\n";
            
            if (!empty($_FILES)) {
                echo "=== \$_FILES Array ===\n";
                print_r($_FILES);
                
                if (isset($_FILES['test_file'])) {
                    $file = $_FILES['test_file'];
                    
                    echo "\n=== File Analysis ===\n";
                    echo "Original Name: " . $file['name'] . "\n";
                    echo "MIME Type (browser): " . $file['type'] . "\n";
                    echo "Size: " . number_format($file['size']) . " bytes (" . number_format($file['size']/1024, 2) . " KB)\n";
                    echo "Temp Location: " . $file['tmp_name'] . "\n";
                    echo "Error Code: " . $file['error'] . "\n";
                    
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        echo "\n✓ File uploaded successfully to temp location!\n";
                        
                        if (file_exists($file['tmp_name'])) {
                            echo "✓ Temp file exists\n";
                            
                            $detectedMime = mime_content_type($file['tmp_name']);
                            echo "MIME Type (detected): " . $detectedMime . "\n";
                            
                            $imageInfo = @getimagesize($file['tmp_name']);
                            if ($imageInfo) {
                                echo "✓ Valid image file\n";
                                echo "Dimensions: " . $imageInfo[0] . "x" . $imageInfo[1] . "\n";
                                echo "Image Type: " . image_type_to_mime_type($imageInfo[2]) . "\n";
                            } else {
                                echo "✗ Not a valid image file\n";
                            }
                            
                            // Test write permissions
                            $testDir = __DIR__ . '/../assets/uploads/items/';
                            if (!is_dir($testDir)) {
                                echo "\n⚠ Upload directory doesn't exist: $testDir\n";
                                if (mkdir($testDir, 0755, true)) {
                                    echo "✓ Created upload directory\n";
                                } else {
                                    echo "✗ Failed to create upload directory\n";
                                }
                            } else {
                                echo "\n✓ Upload directory exists\n";
                            }
                            
                            if (is_writable($testDir)) {
                                echo "✓ Upload directory is writable\n";
                            } else {
                                echo "✗ Upload directory is NOT writable\n";
                            }
                            
                        } else {
                            echo "✗ Temp file doesn't exist (PHP might have moved/deleted it)\n";
                        }
                    } else {
                        echo "\n✗ Upload Error: ";
                        switch ($file['error']) {
                            case UPLOAD_ERR_INI_SIZE:
                                echo "File exceeds upload_max_filesize\n";
                                break;
                            case UPLOAD_ERR_FORM_SIZE:
                                echo "File exceeds MAX_FILE_SIZE\n";
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                echo "File was only partially uploaded\n";
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                echo "No file was uploaded\n";
                                break;
                            case UPLOAD_ERR_NO_TMP_DIR:
                                echo "Missing temporary folder\n";
                                break;
                            case UPLOAD_ERR_CANT_WRITE:
                                echo "Failed to write file to disk\n";
                                break;
                            case UPLOAD_ERR_EXTENSION:
                                echo "Upload stopped by PHP extension\n";
                                break;
                            default:
                                echo "Unknown error\n";
                        }
                    }
                }
            } else {
                echo "✗ No files received in \$_FILES array\n";
                echo "This could indicate:\n";
                echo "  - Form missing enctype='multipart/form-data'\n";
                echo "  - File size exceeds post_max_size\n";
                echo "  - PHP configuration issue\n";
            }
            echo '</pre>';
        }
        ?>
    </div>

    <div class="card">
        <h2>📝 Notes</h2>
        <ul>
            <li>For the main site to work, <strong>File Uploads Enabled</strong> must be YES</li>
            <li><strong>Upload Max Filesize</strong> should be at least 5M (for your 5MB limit)</li>
            <li><strong>Post Max Size</strong> should be larger than Upload Max Filesize</li>
            <li>The upload directory must be writable by the web server</li>
            <li>After testing, you can delete this file for security</li>
        </ul>
    </div>
</body>
</html>
