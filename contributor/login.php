<?php
/**
 * Contributor Login Page
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (Auth::isLoggedIn() && Auth::hasRole(ROLE_CONTRIBUTOR)) {
    redirect(SITE_URL . '/contributor/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $auth = new Auth();
            if ($auth->login($username, $password)) {
                if (Auth::hasRole(ROLE_CONTRIBUTOR)) {
                    redirect(SITE_URL . '/contributor/dashboard.php');
                } else {
                    $auth->logout();
                    $error = 'Access denied. Contributor login only.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}

$pageTitle = 'Contributor Login';
$additionalCSS = ['pages/auth-admin.css']; // Load specific styles
include __DIR__ . '/../includes/header_professional.php';
?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to manage market prices</p>
        </div>
        
        <?php if ($error): ?>
            <div class="auth-alert auth-alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="auth-input" required autofocus 
                       placeholder="Enter your username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="auth-input" required
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn-auth">
                Sign In <i class="bi bi-arrow-right-short"></i>
            </button>
        </form>
        
        <div class="auth-links">
            <div>
                Don't have an account? 
                <a href="register.php" class="auth-link-primary">Register here</a>
            </div>
            <a href="<?php echo SITE_URL; ?>/public/index.php" class="auth-link">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            
            <div class="legal-links">
                <a href="<?php echo SITE_URL; ?>/public/privacy.php">Privacy Policy</a>
                <span>•</span>
                <a href="<?php echo SITE_URL; ?>/public/terms.php">Terms of Service</a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
