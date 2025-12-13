<?php
/**
 * Contributor Dashboard
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../includes/functions.php';

// Require contributor login
Auth::requireRole(ROLE_CONTRIBUTOR, SITE_URL . '/contributor/login.php');

$pageTitle = 'Contributor Dashboard';
$metaDescription = 'Manage your contributions to Nepal\'s premier price tracking platform.';
$additionalCSS = ['pages/contributor-dashboard.css'];
$additionalJS = ['components/dashboard.js'];
$validationObj = new Validation();
$itemObj = new Item();

// Get user's submission stats
$userId = Auth::getUserId();
$username = Auth::getUsername();
$mySubmissions = $validationObj->getContributorHistory($userId, 50);

// Calculate stats
$totalSubmissions = count($mySubmissions);
$pendingCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_PENDING));
$approvedCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_APPROVED));
$rejectedCount = count(array_filter($mySubmissions, fn($s) => $s['status'] === VALIDATION_REJECTED));

// Calculate approval rate
$approvalRate = $totalSubmissions > 0 ? round(($approvedCount / $totalSubmissions) * 100) : 0;

// Get action type breakdown
$newItems = count(array_filter($mySubmissions, fn($s) => $s['action_type'] === ACTION_NEW_ITEM && $s['status'] === VALIDATION_APPROVED));
$priceUpdates = count(array_filter($mySubmissions, fn($s) => $s['action_type'] === ACTION_PRICE_UPDATE && $s['status'] === VALIDATION_APPROVED));
$itemEdits = count(array_filter($mySubmissions, fn($s) => $s['action_type'] === ACTION_ITEM_EDIT && $s['status'] === VALIDATION_APPROVED));

// Calculate streak (consecutive days with submissions)
$streak = 0;
$submissionDates = array_map(fn($s) => date('Y-m-d', strtotime($s['submitted_at'])), $mySubmissions);
$uniqueDates = array_unique($submissionDates);
rsort($uniqueDates);
$currentDate = date('Y-m-d');
foreach ($uniqueDates as $date) {
    if ($date === $currentDate || $date === date('Y-m-d', strtotime($currentDate . ' -' . $streak . ' days'))) {
        $streak++;
        $currentDate = $date;
    } else {
        break;
    }
}

// Get recent significant price changes
$priceChanges = $itemObj->getSignificantPriceChanges(10, 5);

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
        
        <!-- Dashboard Header with Notifications -->
        <div class="dashboard-header-wrapper">
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

            <!-- Notifications Panel (Left Sidebar) -->
            <div class="notifications-sidebar">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-bell-fill"></i>
                        Notifications
                        <?php if ($pendingCount > 0): ?>
                            <span class="notification-count"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </h2>
                </div>

                <div class="notifications-list">
                    <?php if ($pendingCount > 0): ?>
                        <div class="notification-item notification-warning">
                            <i class="bi bi-hourglass-split"></i>
                            <span><?php echo $pendingCount; ?> submission<?php echo $pendingCount > 1 ? 's' : ''; ?> pending review</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($approvedCount > 0): ?>
                        <div class="notification-item notification-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?php echo $approvedCount; ?> contribution<?php echo $approvedCount > 1 ? 's' : ''; ?> approved</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($totalSubmissions === 0): ?>
                        <div class="notification-item notification-info">
                            <i class="bi bi-lightbulb-fill"></i>
                            <span>Start contributing to earn rewards!</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stats-grid">
                <div class="stat-card stat-card-compact">
                    <div class="stat-icon orange">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $totalSubmissions; ?></div>
                        <h3>Submissions</h3>
                    </div>
                </div>

                <div class="stat-card stat-card-compact">
                    <div class="stat-icon blue">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $pendingCount; ?></div>
                        <h3>Pending</h3>
                    </div>
                </div>

                <div class="stat-card stat-card-compact">
                    <div class="stat-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $approvedCount; ?></div>
                        <h3>Approved</h3>
                    </div>
                </div>

                <div class="stat-card stat-card-compact">
                    <div class="stat-icon purple">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $approvalRate; ?>%</div>
                        <h3>Success Rate</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-compact">
            <div class="action-buttons">
                <a href="add_item.php" class="btn-action btn-action-primary">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Add Item</span>
                </a>
                <a href="<?php echo SITE_URL; ?>/public/products.php" class="btn-action btn-action-info">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Item</span>
                </a>
            </div>
        </div>

        <!-- Tabbed Content Area -->
        <div class="dashboard-tabs">
            <div class="tab-navigation">
                <button class="tab-btn active" data-tab="activity">
                    <i class="bi bi-clock-history"></i>
                    Activity
                </button>
                <button class="tab-btn" data-tab="breakdown">
                    <i class="bi bi-pie-chart-fill"></i>
                    Breakdown
                </button>
                <button class="tab-btn" data-tab="achievements">
                    <i class="bi bi-trophy-fill"></i>
                    Achievements
                </button>
                <button class="tab-btn" data-tab="insights">
                    <i class="bi bi-graph-up"></i>
                    Market Insights
                </button>
            </div>

            <!-- Tab Content: Activity -->
            <div class="tab-content active" id="tab-activity">
                <?php if (empty($mySubmissions)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No submissions yet</h3>
                        <p>Start contributing to build Nepal's market intelligence!</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php 
                        $recentSubmissions = array_slice($mySubmissions, 0, 15);
                        foreach ($recentSubmissions as $submission): 
                            // Get item name safely
                            $itemName = $submission['item_name'] ?? $submission['existing_item_name'] ?? 'N/A';
                            
                            // Get action type label
                            $actionLabel = $submission['action_type'] ?? 'unknown';
                            if ($actionLabel === ACTION_NEW_ITEM) {
                                $actionDisplay = 'New Item';
                                $actionIcon = 'plus-circle';
                                $actionColor = 'blue';
                            } elseif ($actionLabel === ACTION_PRICE_UPDATE) {
                                $actionDisplay = 'Price Update';
                                $actionIcon = 'currency-rupee';
                                $actionColor = 'green';
                            } elseif ($actionLabel === ACTION_ITEM_EDIT) {
                                $actionDisplay = 'Item Edit';
                                $actionIcon = 'pencil-square';
                                $actionColor = 'purple';
                            } else {
                                $actionDisplay = ucfirst(str_replace('_', ' ', $actionLabel));
                                $actionIcon = 'tag';
                                $actionColor = 'gray';
                            }
                        ?>
                            <div class="activity-item">
                                <div class="activity-header">
                                    <div class="activity-main-info">
                                        <h4 class="activity-title">
                                            <?php echo htmlspecialchars($itemName); ?>
                                        </h4>
                                        <div class="activity-meta">
                                            <span class="meta-badge meta-<?php echo $actionColor; ?>">
                                                <i class="bi bi-<?php echo $actionIcon; ?>"></i>
                                                <?php echo $actionDisplay; ?>
                                            </span>
                                            <?php if (!empty($submission['category_name'])): ?>
                                                <span class="meta-badge meta-category">
                                                    <i class="bi bi-folder"></i>
                                                    <?php echo htmlspecialchars($submission['category_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="meta-badge meta-date">
                                                <i class="bi bi-calendar3"></i>
                                                <?php echo date('M j, Y \a\t g:i A', strtotime($submission['submitted_at'])); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Additional Details -->
                                        <div class="activity-details" style="display: block !important; visibility: visible !important;">
                                            <?php 
                                            $hasDetails = false;
                                            
                                            // Price Information
                                            if ($actionLabel === ACTION_PRICE_UPDATE && !empty($submission['new_price'])): 
                                                $hasDetails = true;
                                            ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-cash"></i>
                                                    <span class="detail-label">New Price:</span>
                                                    <span class="detail-value">NPR <?php echo number_format($submission['new_price'], 2); ?></span>
                                                    <?php if (!empty($submission['old_price'])): ?>
                                                        <span class="detail-old">(was NPR <?php echo number_format($submission['old_price'], 2); ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php elseif ($actionLabel === ACTION_NEW_ITEM && !empty($submission['new_price'])): 
                                                $hasDetails = true;
                                            ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-cash"></i>
                                                    <span class="detail-label">Price:</span>
                                                    <span class="detail-value">NPR <?php echo number_format($submission['new_price'], 2); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($submission['unit'])): 
                                                $hasDetails = true;
                                            ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-box"></i>
                                                    <span class="detail-label">Unit:</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($submission['unit']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($submission['description'])): 
                                                $hasDetails = true;
                                            ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-text-left"></i>
                                                    <span class="detail-label">Description:</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars(substr($submission['description'], 0, 100)) . (strlen($submission['description']) > 100 ? '...' : ''); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (($submission['status'] ?? '') === VALIDATION_APPROVED && !empty($submission['validated_by_name'])): 
                                                $hasDetails = true;
                                            ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-person-check"></i>
                                                    <span class="detail-label">Validated by:</span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($submission['validated_by_name']); ?></span>
                                                    <?php if (!empty($submission['validated_at'])): ?>
                                                        <span class="detail-time">on <?php echo date('M j, Y', strtotime($submission['validated_at'])); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Submission ID for reference -->
                                            <div class="detail-item">
                                                <i class="bi bi-hash"></i>
                                                <span class="detail-label">Submission ID:</span>
                                                <span class="detail-value">#<?php echo $submission['validation_id'] ?? 'N/A'; ?></span>
                                            </div>
                                            
                                            <?php if (!$hasDetails): ?>
                                                <div class="detail-item">
                                                    <i class="bi bi-info-circle"></i>
                                                    <span class="detail-label">Status:</span>
                                                    <span class="detail-value">Submitted for review</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="status-badge <?php echo $submission['status'] ?? 'pending'; ?>">
                                        <?php if (($submission['status'] ?? '') === VALIDATION_PENDING): ?>
                                            <i class="bi bi-hourglass-split"></i>
                                        <?php elseif (($submission['status'] ?? '') === VALIDATION_APPROVED): ?>
                                            <i class="bi bi-check-circle-fill"></i>
                                        <?php else: ?>
                                            <i class="bi bi-x-circle-fill"></i>
                                        <?php endif; ?>
                                        <?php echo ucfirst($submission['status'] ?? 'pending'); ?>
                                    </span>
                                </div>
                                <?php if (($submission['status'] ?? '') === VALIDATION_REJECTED && !empty($submission['rejection_reason'])): ?>
                                    <div class="rejection-reason">
                                        <strong><i class="bi bi-exclamation-triangle"></i> Rejection Reason:</strong>
                                        <p><?php echo htmlspecialchars($submission['rejection_reason']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab Content: Breakdown -->
            <div class="tab-content" id="tab-breakdown">
                <div class="breakdown-grid">
                    <div class="breakdown-card">
                        <div class="breakdown-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div class="breakdown-content">
                            <h4>New Items</h4>
                            <div class="breakdown-value"><?php echo $newItems; ?></div>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: <?php echo $approvedCount > 0 ? ($newItems / $approvedCount * 100) : 0; ?>%; background: #3b82f6;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="breakdown-card">
                        <div class="breakdown-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div class="breakdown-content">
                            <h4>Price Updates</h4>
                            <div class="breakdown-value"><?php echo $priceUpdates; ?></div>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: <?php echo $approvedCount > 0 ? ($priceUpdates / $approvedCount * 100) : 0; ?>%; background: #10b981;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="breakdown-card">
                        <div class="breakdown-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="breakdown-content">
                            <h4>Item Edits</h4>
                            <div class="breakdown-value"><?php echo $itemEdits; ?></div>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: <?php echo $approvedCount > 0 ? ($itemEdits / $approvedCount * 100) : 0; ?>%; background: #8b5cf6;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="breakdown-card">
                        <div class="breakdown-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="bi bi-fire"></i>
                        </div>
                        <div class="breakdown-content">
                            <h4>Current Streak</h4>
                            <div class="breakdown-value"><?php echo $streak; ?> days</div>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: <?php echo min($streak * 10, 100); ?>%; background: #f59e0b;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Achievements -->
            <div class="tab-content" id="tab-achievements">
                <div class="achievements-grid">
                    <div class="achievement-card <?php echo $totalSubmissions >= 1 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <h4>First Step</h4>
                        <p>Submit your first contribution</p>
                        <?php if ($totalSubmissions >= 1): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge">0/1</span>
                        <?php endif; ?>
                    </div>

                    <div class="achievement-card <?php echo $approvedCount >= 10 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <h4>Rising Star</h4>
                        <p>Get 10 approved submissions</p>
                        <?php if ($approvedCount >= 10): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge"><?php echo $approvedCount; ?>/10</span>
                        <?php endif; ?>
                    </div>

                    <div class="achievement-card <?php echo $approvalRate >= 80 && $totalSubmissions >= 5 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <h4>Quality First</h4>
                        <p>Maintain 80% approval rate</p>
                        <?php if ($approvalRate >= 80 && $totalSubmissions >= 5): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge"><?php echo $approvalRate; ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="achievement-card <?php echo $streak >= 7 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h4>On Fire</h4>
                        <p>7-day contribution streak</p>
                        <?php if ($streak >= 7): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge"><?php echo $streak; ?>/7 days</span>
                        <?php endif; ?>
                    </div>

                    <div class="achievement-card <?php echo $newItems >= 5 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <h4>Item Hunter</h4>
                        <p>Add 5 new items to database</p>
                        <?php if ($newItems >= 5): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge"><?php echo $newItems; ?>/5</span>
                        <?php endif; ?>
                    </div>

                    <div class="achievement-card <?php echo $priceUpdates >= 20 ? 'unlocked' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>Market Watcher</h4>
                        <p>Submit 20 price updates</p>
                        <?php if ($priceUpdates >= 20): ?>
                            <span class="unlocked-badge"><i class="bi bi-check-circle-fill"></i> Unlocked</span>
                        <?php else: ?>
                            <span class="progress-badge"><?php echo $priceUpdates; ?>/20</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Market Insights -->
            <div class="tab-content" id="tab-insights">
                <?php if (!empty($priceChanges)): ?>
                    <div class="insights-panel">
                        <div class="price-changes-list">
                            <?php foreach ($priceChanges as $change): ?>
                                <div class="price-change-item">
                                    <div class="change-header">
                                        <h4><?php echo htmlspecialchars($change['item_name']); ?></h4>
                                        <span class="change-badge <?php echo $change['price_change_percent'] > 0 ? 'increase' : 'decrease'; ?>">
                                            <i class="bi bi-<?php echo $change['price_change_percent'] > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                            <?php echo abs($change['price_change_percent']); ?>%
                                        </span>
                                    </div>
                                    <div class="change-details">
                                        <span>NPR <?php echo number_format($change['old_price'], 2); ?></span>
                                        <i class="bi bi-arrow-right"></i>
                                        <span><strong>NPR <?php echo number_format($change['new_price'], 2); ?></strong></span>
                                    </div>
                                    <div class="change-time">
                                        <i class="bi bi-clock"></i>
                                        <?php echo date('M j, Y', strtotime($change['updated_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/public/products.php?sort=newest" class="btn-view-all">
                            <i class="bi bi-arrow-right-circle"></i>
                            View All Products
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state-small">
                        <i class="bi bi-graph-up"></i>
                        <p>No significant price changes recently</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            document.getElementById('tab-' + targetTab).classList.add('active');
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
