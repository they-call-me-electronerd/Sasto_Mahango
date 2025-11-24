<?php
/**
 * Contributor Dashboard
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../includes/functions.php';

// Require contributor login
Auth::requireRole(ROLE_CONTRIBUTOR, SITE_URL . '/contributor/login.php');

$pageTitle = 'Contributor Dashboard';
$metaDescription = 'Manage your contributions to Nepal\'s premier price tracking platform.';
$additionalCSS = ['pages/contributor-dashboard.css'];
$additionalJS = ['components/dashboard.js'];
$validationObj = new Validation();

// Get user's submission stats
$userId = Auth::getUserId();
$username = Auth::getUsername();
$mySubmissions = $validationObj->getContributorHistory($userId, 10);

// Calculate stats
$totalSubmissions = count($mySubmissions);
$pendingCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_PENDING));
$approvedCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_APPROVED));
$rejectedCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_REJECTED));

// Calculate approval rate
$approvalRate = $totalSubmissions > 0 ? round(($approvedCount / $totalSubmissions) * 100) : 0;

// Get flash message
$flashMessage = getFlashMessage();

// Set current page for nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

include __DIR__ . '/../includes/header_professional.php';
?>

<main class="contributor-dashboard">
    <div class="dashboard-container">
        <?php if ($flashMessage): ?>
            <div class="flash-message flash-<?php echo $flashMessage['type']; ?>" style="margin-bottom: var(--spacing-xl); padding: var(--spacing-lg); border-radius: var(--radius-xl); background: <?php echo $flashMessage['type'] === 'success' ? 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)' : 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)'; ?>; border: 2px solid <?php echo $flashMessage['type'] === 'success' ? '#10b981' : '#ef4444'; ?>; color: <?php echo $flashMessage['type'] === 'success' ? '#065f46' : '#991b1b'; ?>; font-weight: 600; display: flex; align-items: center; gap: var(--spacing-md); animation: slideDown 0.5s ease;">
                <i class="bi bi-<?php echo $flashMessage['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'; ?>" style="font-size: 1.5rem;"></i>
                <span><?php echo htmlspecialchars($flashMessage['message']); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="dashboard-greeting">
                <div class="greeting-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="greeting-content">
                    <h1>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                    <p>Ready to contribute to Nepal's market intelligence platform</p>
                </div>
            </div>
            <div class="dashboard-breadcrumb">
                <a href="<?php echo SITE_URL; ?>/public/index.php" style="color: var(--text-secondary); text-decoration: none;">Home</a>
                <i class="bi bi-chevron-right"></i>
                <span style="color: var(--brand-primary); font-weight: 600;">Dashboard</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span>Active</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3>Total Submissions</h3>
                    <div class="stat-value"><?php echo $totalSubmissions; ?></div>
                    <p class="stat-description">All-time contributions</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-hourglass-split"></i>
                        <span>Review</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3>Pending Review</h3>
                    <div class="stat-value"><?php echo $pendingCount; ?></div>
                    <p class="stat-description">Awaiting validation</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span><?php echo $approvalRate; ?>%</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3>Approved</h3>
                    <div class="stat-value"><?php echo $approvedCount; ?></div>
                    <p class="stat-description">Successfully validated</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="stat-trend up">
                        <i class="bi bi-trophy"></i>
                        <span>Level 1</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3>Contributor Points</h3>
                    <div class="stat-value"><?php echo $approvedCount * 10; ?></div>
                    <p class="stat-description">Reputation score</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Quick Actions
                </h2>
            </div>
            <div class="action-grid">
                <a href="add_item.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <h3>Add New Item</h3>
                    </div>
                    <p>Add a new product or commodity to the marketplace database</p>
                </a>

                <a href="update_price.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                        <h3>Update Price</h3>
                    </div>
                    <p>Submit current market prices for existing items</p>
                </a>

                <a href="#activity-section" class="action-card" onclick="smoothScroll(event, 'activity-section')">
                    <div class="action-card-header">
                        <div class="action-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3>View Analytics</h3>
                    </div>
                    <p>Track your contribution statistics and performance</p>
                </a>
            </div>
        </div>

        <!-- Dashboard Grid: Recent Activity & Notifications -->
        <div class="dashboard-grid" id="activity-section">
            <!-- Recent Submissions -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-clock-history"></i>
                        Recent Activity
                    </h2>
                </div>

                <?php if (empty($mySubmissions)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No submissions yet</h3>
                        <p>Start contributing to build Nepal's market intelligence!</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($mySubmissions as $submission): ?>
                            <div class="activity-item">
                                <div class="activity-header">
                                    <div>
                                        <h4 class="activity-title">
                                            <?php echo htmlspecialchars($submission['item_name'] ?? $submission['existing_item_name'] ?? 'Item'); ?>
                                        </h4>
                                        <div class="activity-meta">
                                            <span>
                                                <i class="bi bi-tag"></i>
                                                <?php echo ucfirst($submission['action_type']); ?>
                                            </span>
                                            <span>
                                                <i class="bi bi-calendar3"></i>
                                                <?php echo date('M j, Y', strtotime($submission['submitted_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="status-badge <?php echo $submission['status']; ?>">
                                        <?php if ($submission['status'] === VALIDATION_PENDING): ?>
                                            <i class="bi bi-hourglass-split"></i>
                                        <?php elseif ($submission['status'] === VALIDATION_APPROVED): ?>
                                            <i class="bi bi-check-circle-fill"></i>
                                        <?php else: ?>
                                            <i class="bi bi-x-circle-fill"></i>
                                        <?php endif; ?>
                                        <?php echo ucfirst($submission['status']); ?>
                                    </span>
                                </div>
                                <?php if ($submission['status'] === VALIDATION_REJECTED && !empty($submission['rejection_reason'])): ?>
                                    <div class="rejection-reason">
                                        <strong><i class="bi bi-exclamation-triangle"></i> Reason:</strong>
                                        <?php echo htmlspecialchars($submission['rejection_reason']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notifications Panel -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-bell-fill"></i>
                        Notifications
                    </h2>
                </div>

                <div class="notifications-panel">
                    <?php if ($pendingCount > 0): ?>
                        <div class="notification-item unread">
                            <div class="notification-header">
                                <div class="notification-icon info">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="notification-content">
                                    <h4>Submissions Under Review</h4>
                                    <p>You have <?php echo $pendingCount; ?> submission<?php echo $pendingCount > 1 ? 's' : ''; ?> awaiting validation</p>
                                </div>
                            </div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i>
                                Just now
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($approvedCount > 0): ?>
                        <div class="notification-item">
                            <div class="notification-header">
                                <div class="notification-icon success">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="notification-content">
                                    <h4>Contributions Approved</h4>
                                    <p><?php echo $approvedCount; ?> of your submissions have been approved!</p>
                                </div>
                            </div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i>
                                Recent
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="notification-item">
                        <div class="notification-header">
                            <div class="notification-icon info">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <div class="notification-content">
                                <h4>Welcome to Mulyasuchi</h4>
                                <p>Thank you for joining our contributor community!</p>
                            </div>
                        </div>
                        <div class="notification-time">
                            <i class="bi bi-clock"></i>
                            <?php echo date('M j, Y'); ?>
                        </div>
                    </div>

                    <?php if ($totalSubmissions === 0): ?>
                        <div class="notification-item">
                            <div class="notification-header">
                                <div class="notification-icon warning">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </div>
                                <div class="notification-content">
                                    <h4>Start Contributing</h4>
                                    <p>Add your first item or price update to get started!</p>
                                </div>
                            </div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i>
                                Tip
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
