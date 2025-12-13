<?php
/**
 * Contributor Login Page
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
            $result = $auth->login($username, $password);
            
            if (is_array($result) && isset($result['success'])) {
                if ($result['success']) {
                    if (Auth::hasRole(ROLE_CONTRIBUTOR)) {
                        redirect(SITE_URL . '/contributor/dashboard.php');
                    } else {
                        $auth->logout();
                        $error = 'Access denied. Contributor login only.';
                    }
                } else {
                    $error = $result['message'] ?? 'Invalid username or password.';
                }
            } else {
                // Backward compatibility
                if ($result) {
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
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="auth-input" required
                           placeholder="Enter your password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; font-size: 1.2rem; padding: 4px;">
                        <i class="bi bi-eye" id="password-icon"></i>
                    </button>
                </div>
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
                <a href="<?php echo SITE_URL; ?>/public/privacy-policy.php">Privacy Policy</a>
                <span>•</span>
                <a href="<?php echo SITE_URL; ?>/public/terms-of-service.php">Terms of Service</a>
            </div>
        </div>
    </div>
</main>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Add floating particles
function createParticles() {
    const authPage = document.querySelector('.auth-page');
    if (!authPage) return;
    
    for (let i = 0; i < 15; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 10 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        authPage.appendChild(particle);
    }
}

createParticles();

// Input focus animations
document.querySelectorAll('.auth-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
