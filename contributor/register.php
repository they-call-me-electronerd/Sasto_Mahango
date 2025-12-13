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
                <input type="password" id="password" name="password" class="auth-input" required placeholder="Create a password">
                <div class="password-requirements">
                    <i class="bi bi-shield-lock"></i> Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="auth-input" required placeholder="Confirm your password">
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

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
