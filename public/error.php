<?php
/**
 * Error Page
 * Displays user-friendly error messages
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';

$errorCode = isset($_GET['error']) ? (int)$_GET['error'] : 404;

$errors = [
    400 => [
        'title' => 'Bad Request',
        'message' => 'The request could not be understood by the server.',
        'icon' => 'exclamation-triangle'
    ],
    401 => [
        'title' => 'Unauthorized',
        'message' => 'You need to log in to access this page.',
        'icon' => 'lock'
    ],
    403 => [
        'title' => 'Forbidden',
        'message' => 'You don\'t have permission to access this resource.',
        'icon' => 'shield-exclamation'
    ],
    404 => [
        'title' => 'Page Not Found',
        'message' => 'The page you\'re looking for doesn\'t exist or has been moved.',
        'icon' => 'file-earmark-x'
    ],
    500 => [
        'title' => 'Server Error',
        'message' => 'Something went wrong on our end. We\'re working to fix it.',
        'icon' => 'exclamation-octagon'
    ],
    503 => [
        'title' => 'Service Unavailable',
        'message' => 'We\'re temporarily down for maintenance. Please check back soon.',
        'icon' => 'tools'
    ]
];

$error = $errors[$errorCode] ?? $errors[404];
$pageTitle = $error['title'] . ' - Error ' . $errorCode;

// Don't include navigation for error pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/variables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 800;
            background: linear-gradient(135deg, #f97316, #ea580c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin: 0 0 20px 0;
        }
        
        .error-icon {
            font-size: 80px;
            color: #f97316;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 16px 0;
        }
        
        .error-message {
            font-size: 18px;
            color: #6b7280;
            margin: 0 0 40px 0;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?php echo $errorCode; ?></div>
        <i class="bi bi-<?php echo $error['icon']; ?> error-icon"></i>
        <h1 class="error-title"><?php echo $error['title']; ?></h1>
        <p class="error-message"><?php echo $error['message']; ?></p>
        
        <div class="error-actions">
            <a href="<?php echo SITE_URL; ?>/public/index.php" class="btn btn-primary">
                <i class="bi bi-house-door"></i>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
