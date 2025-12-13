<?php
/**
 * Admin Login Page
 * Modern & Professional Design matching SastoMahango Brand
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (Auth::isLoggedIn() && Auth::hasRole(ROLE_ADMIN)) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $auth = new Auth();
            $result = $auth->login($username, $password);
            
            if (is_array($result) && isset($result['success'])) {
                if ($result['success']) {
                    // Check if admin role
                    if (Auth::hasRole(ROLE_ADMIN)) {
                        redirect(SITE_URL . '/admin/dashboard.php');
                    } else {
                        $auth->logout();
                        $error = 'Access denied. Admin login only.';
                    }
                } else {
                    $error = $result['message'] ?? 'Invalid username or password.';
                }
            } else {
                // Backward compatibility with old boolean return
                if ($result) {
                    if (Auth::hasRole(ROLE_ADMIN)) {
                        redirect(SITE_URL . '/admin/dashboard.php');
                    } else {
                        $auth->logout();
                        $error = 'Access denied. Admin login only.';
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
            }
        }
    }
}

$pageTitle = 'Admin Login - SastoMahango';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Core Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/variables.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/utilities.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/pages/auth-admin.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/themes/dark-mode.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/theme-toggle.css">
</head>
<body>

<!-- Theme Toggle Button (Floating) -->
<button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle theme">
    <i class="bi bi-sun-fill theme-icon-light"></i>
    <i class="bi bi-moon-fill theme-icon-dark"></i>
</button>

<!-- Admin Login Page -->
<main class="auth-page">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="auth-title">Admin Login</h1>
            <p class="auth-subtitle">Secure access to the admin dashboard</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label for="username">
                    <i class="bi bi-person-fill"></i> Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="auth-input"
                    placeholder="Enter your username"
                    required 
                    autofocus 
                    autocomplete="username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="bi bi-lock-fill"></i> Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="auth-input"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-box-arrow-in-right"></i> Login to Dashboard
            </button>
        </form>

        <!-- Info Box (Development Only) -->
        <div class="info-box">
            <div class="info-header">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Development Credentials</strong>
            </div>
            <div class="info-content">
                <div class="credential-item">
                    <span class="credential-label">Username:</span>
                    <code>admin</code>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <code>Admin@123</code>
                </div>
                <div class="warning-text">
                    <i class="bi bi-shield-exclamation"></i>
                    Change these credentials immediately in production!
                </div>
            </div>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <a href="<?php echo SITE_URL; ?>/public/index.php" class="auth-link">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</main>

<!-- Theme Manager Script -->
<script src="<?php echo SITE_URL; ?>/assets/js/core/theme-manager.js"></script>

<!-- Auto-dismiss alerts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideOutUp 0.5s ease forwards';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>

</body>
</html>
