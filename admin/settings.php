<?php
/**
 * Admin Settings
 * Profile and System Configuration
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin login
Auth::requireRole(ROLE_ADMIN, SITE_URL . '/admin/login.php');

$pageTitle = 'Settings - Mulyasuchi Admin';
$userObj = new User();
$currentUser = $userObj->getUserById(Auth::getUserId());

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('Invalid security token', 'error');
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_profile') {
            $data = [
                'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
                'email' => sanitizeInput($_POST['email'] ?? ''),
                'phone' => sanitizeInput($_POST['phone'] ?? '')
            ];
            
            // Only update password if provided
            if (!empty($_POST['new_password'])) {
                if (strlen($_POST['new_password']) < 8) {
                    setFlashMessage('Password must be at least 8 characters', 'error');
                } else {
                    $data['password'] = $_POST['new_password'];
                }
            }
            
            if (!hasFlashMessage()) {
                if ($userObj->update(Auth::getUserId(), $data)) {
                    setFlashMessage('Profile updated successfully', 'success');
                    // Refresh user data
                    $currentUser = $userObj->getUserById(Auth::getUserId());
                } else {
                    setFlashMessage('Failed to update profile', 'error');
                }
            }
        }
    }
}

$additionalCSS = ['pages/auth-admin.css'];
include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Main Content -->
<main class="dashboard-layout" style="padding-top: 100px;">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Settings</h1>
                <p class="dashboard-subtitle">Manage your profile and system preferences</p>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlashMessage()): ?>
            <?php $flash = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?>"></i>
                <span><?php echo htmlspecialchars($flash['message']); ?></span>
            </div>
        <?php endif; ?>

        <div class="settings-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            
            <!-- Profile Settings -->
            <div class="create-user-section" style="margin-top: 0;">
                <h2>
                    <i class="bi bi-person-circle"></i>
                    Profile Settings
                </h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($currentUser['username']); ?>" disabled style="background: var(--bg-secondary); cursor: not-allowed;">
                            <small style="color: var(--dash-text-secondary);">Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($currentUser['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" placeholder="Optional">
                        </div>
                        
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="new_password" minlength="8" placeholder="Enter new password to change">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- System Info -->
            <div class="create-user-section" style="margin-top: 0;">
                <h2>
                    <i class="bi bi-info-circle"></i>
                    System Information
                </h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">PHP Version</div>
                        <div class="detail-value"><?php echo phpversion(); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Server Software</div>
                        <div class="detail-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Database</div>
                        <div class="detail-value">MySQL</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Application Version</div>
                        <div class="detail-value">1.0.0</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Environment</div>
                        <div class="detail-value">Production</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Timezone</div>
                        <div class="detail-value"><?php echo date_default_timezone_get(); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Page Scripts -->
<script>
// Auto-dismiss alerts
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

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
