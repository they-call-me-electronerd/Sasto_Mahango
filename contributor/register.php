<?php
/**
 * Contributor Registration Page
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    if (Auth::hasRole(ROLE_ADMIN)) {
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        redirect(SITE_URL . '/contributor/dashboard.php');
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($fullName) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        try {
            $userObj = new User();
            
            // Check if username or email already exists
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                // Create user
                $userId = $userObj->createUser($username, $email, $password, $fullName, ROLE_CONTRIBUTOR);
                
                if ($userId) {
                    $success = 'Account created successfully! You can now login.';
                    // Auto-login
                    $auth = new Auth();
                    if ($auth->login($username, $password)) {
                        redirect(SITE_URL . '/contributor/dashboard.php');
                    }
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}

$pageTitle = 'Contributor Registration';
$additionalCSS = ['pages/auth-admin.css']; // Load specific styles
include __DIR__ . '/../includes/header_professional.php';
?>

<main class="auth-page">
    <div class="auth-card register-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h1 class="auth-title">Join SastoMahango</h1>
            <p class="auth-subtitle">Become a contributor and help build Nepal's market intelligence</p>
        </div>
        
        <?php if ($error): ?>
            <div class="auth-alert auth-alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="auth-alert auth-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" class="auth-input" required autofocus 
                       placeholder="Choose a username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="auth-input" required
                       placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="auth-input" required
                       placeholder="Enter your full name"
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="auth-input" required placeholder="Create a password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; font-size: 1.2rem; padding: 4px;">
                        <i class="bi bi-eye" id="password-icon"></i>
                    </button>
                </div>
                <div class="password-strength" id="password-strength" style="margin-top: 0.5rem; display: none;">
                    <div style="display: flex; gap: 0.25rem; margin-bottom: 0.5rem;">
                        <div class="strength-bar" style="flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; transition: background 0.3s;"></div>
                        <div class="strength-bar" style="flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; transition: background 0.3s;"></div>
                        <div class="strength-bar" style="flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; transition: background 0.3s;"></div>
                        <div class="strength-bar" style="flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; transition: background 0.3s;"></div>
                    </div>
                    <span id="strength-text" style="font-size: 0.75rem; color: #6b7280;"></span>
                </div>
                <div class="password-requirements">
                    <i class="bi bi-shield-lock"></i> Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" class="auth-input" required placeholder="Confirm your password">
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; font-size: 1.2rem; padding: 4px;">
                        <i class="bi bi-eye" id="confirm_password-icon"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-auth">
                Create Account <i class="bi bi-arrow-right-short"></i>
            </button>
        </form>
        
        <div class="auth-links">
            <div>
                Already have an account? 
                <a href="login.php" class="auth-link-primary">Login here</a>
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

// Password strength checker
const passwordInput = document.getElementById('password');
const strengthContainer = document.getElementById('password-strength');
const strengthBars = document.querySelectorAll('.strength-bar');
const strengthText = document.getElementById('strength-text');

passwordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
        strengthContainer.style.display = 'none';
        return;
    }
    
    strengthContainer.style.display = 'block';
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    // Reset bars
    strengthBars.forEach(bar => bar.style.background = '#e5e7eb');
    
    // Fill bars based on strength
    const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e'];
    const labels = ['Weak', 'Fair', 'Good', 'Strong'];
    
    for (let i = 0; i < strength; i++) {
        strengthBars[i].style.background = colors[strength - 1];
    }
    
    strengthText.textContent = labels[strength - 1] || '';
    strengthText.style.color = colors[strength - 1] || '#6b7280';
});

// Password match validation
const confirmPasswordInput = document.getElementById('confirm_password');
confirmPasswordInput.addEventListener('input', function() {
    if (this.value && this.value !== passwordInput.value) {
        this.style.borderColor = '#ef4444';
    } else if (this.value === passwordInput.value && this.value.length > 0) {
        this.style.borderColor = '#22c55e';
    } else {
        this.style.borderColor = '';
    }
});

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
