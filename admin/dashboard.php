<?php
/**
 * Admin Dashboard
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin login
Auth::requireRole(ROLE_ADMIN, SITE_URL . '/admin/login.php');

$pageTitle = 'Admin Dashboard';
$additionalCSS = ['pages/auth-admin.css']; // Load specific styles
include __DIR__ . '/../includes/header_professional.php';

$validationObj = new Validation();
$itemObj = new Item();
$userObj = new User();

$pendingCount = $validationObj->countPendingValidations();
$totalItems = $itemObj->countItems();
$totalUsers = $userObj->countUsers();
?>

<main class="dashboard-layout" style="padding-top: 100px;">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">Welcome back, <?php echo htmlspecialchars(Auth::getUsername()); ?></p>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                <i class="bi bi-calendar3"></i> <?php echo date('l, F j, Y'); ?>
            </div>
        </div>
        
        <?php if ($pendingCount > 0): ?>
        <div class="pending-alert">
            <div class="pending-content">
                <div class="pending-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="pending-text">
                    You have <strong><?php echo $pendingCount; ?></strong> submission<?php echo $pendingCount != 1 ? 's' : ''; ?> pending validation.
                </div>
            </div>
            <a href="validation_queue.php" class="btn-action-sm">
                Review Queue <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-value"><?php echo $pendingCount; ?></div>
                <div class="stat-label">Pending Reviews</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-value"><?php echo $totalItems; ?></div>
                <div class="stat-label">Active Items</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Contributors</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-activity"></i>
                </div>
                <div class="stat-value">100%</div>
                <div class="stat-label">System Status</div>
            </div>
        </div>
        
        <div class="actions-section">
            <h2 class="section-title">
                <i class="bi bi-grid-fill" style="color: var(--brand-primary);"></i> Quick Actions
            </h2>
            
            <div class="actions-grid">
                <a href="validation_queue.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--brand-primary);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="action-card-title">Validation Queue</h3>
                    <p class="action-card-desc">Review and approve price submissions</p>
                </a>
                
                <a href="user_management.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--brand-secondary);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="action-card-title">Manage Users</h3>
                    <p class="action-card-desc">Create, edit, or remove users</p>
                </a>
                
                <a href="<?php echo SITE_URL; ?>/public/products.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--brand-accent);">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                    <h3 class="action-card-title">View Items</h3>
                    <p class="action-card-desc">Browse all published items</p>
                </a>
                
                <a href="<?php echo SITE_URL; ?>/public/index.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--text-secondary);">
                        <i class="bi bi-globe"></i>
                    </div>
                    <h3 class="action-card-title">Public Site</h3>
                    <p class="action-card-desc">View the live website</p>
                </a>
                
                <a href="system_logs.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--text-tertiary);">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="action-card-title">System Logs</h3>
                    <p class="action-card-desc">Audit trail of activities</p>
                </a>
                
                <a href="settings.php" class="action-card">
                    <div class="action-card-icon" style="color: var(--text-tertiary);">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <h3 class="action-card-title">Settings</h3>
                    <p class="action-card-desc">Profile & System Config</p>
                </a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
