<?php
/**
 * Admin Validation Queue
 * Modern, Professional Review System for Contributor Submissions
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

// Require admin login
Auth::requireRole(ROLE_ADMIN, SITE_URL . '/admin/login.php');

$pageTitle = 'Validation Queue - Mulyasuchi Admin';
$validationObj = new Validation();

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('Invalid security token', 'error');
    } else {
        $queueId = (int)($_POST['queue_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        
        if ($action === 'approve') {
            $result = $validationObj->approveValidation($queueId);
            if ($result) {
                setFlashMessage('Submission approved successfully!', 'success');
                Logger::log('validation_approved', 'Validation queue #' . $queueId . ' approved');
            } else {
                setFlashMessage('Failed to approve submission', 'error');
            }
        } elseif ($action === 'reject') {
            $reason = sanitizeInput($_POST['rejection_reason'] ?? '');
            if (empty($reason)) {
                setFlashMessage('Please provide a rejection reason', 'error');
            } else {
                $result = $validationObj->rejectValidation($queueId, $reason);
                if ($result) {
                    setFlashMessage('Submission rejected', 'success');
                    Logger::log('validation_rejected', 'Validation queue #' . $queueId . ' rejected');
                } else {
                    setFlashMessage('Failed to reject submission', 'error');
                }
            }
        }
        redirect(SITE_URL . '/admin/validation_queue.php');
    }
}

// Filter and search parameters
$searchTerm = sanitizeInput($_GET['search'] ?? '');

// Get pending submissions
$allSubmissions = $validationObj->getPendingValidations();
$pendingSubmissions = $allSubmissions;

// Calculate stats (before filtering for accurate counts)
$totalPending = count($allSubmissions);
$newItems = count(array_filter($allSubmissions, fn($s) => $s['action_type'] === ACTION_NEW_ITEM));
$priceUpdates = count(array_filter($allSubmissions, fn($s) => $s['action_type'] === ACTION_PRICE_UPDATE));
$itemEdits = count(array_filter($allSubmissions, fn($s) => $s['action_type'] === ACTION_ITEM_EDIT));

// Apply search filter
if (!empty($searchTerm)) {
    $pendingSubmissions = array_filter($pendingSubmissions, function($sub) use ($searchTerm) {
        return stripos($sub['item_name'] ?? $sub['existing_item_name'] ?? '', $searchTerm) !== false ||
               stripos($sub['full_name'] ?? '', $searchTerm) !== false;
    });
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
                <h1 class="dashboard-title">Validation Queue</h1>
                <p class="dashboard-subtitle">Review and approve contributor submissions</p>
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

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value"><?php echo $totalPending; ?></div>
                    <div class="stat-trend">Requires attention</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="bi bi-file-earmark-plus"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">New Items</div>
                    <div class="stat-value"><?php echo $newItems; ?></div>
                    <div class="stat-trend">Product additions</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-tag"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Price Updates</div>
                    <div class="stat-value"><?php echo $priceUpdates; ?></div>
                    <div class="stat-trend">Market changes</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Item Edits</div>
                    <div class="stat-value"><?php echo $itemEdits; ?></div>
                    <div class="stat-trend">Edit requests</div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="admin-controls">
            <form method="GET" class="search-form" id="searchForm">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by item name or contributor..." 
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="search-input">
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($searchTerm): ?>
                    <a href="validation_queue.php" class="btn-clear">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Submissions List -->
        <?php if (empty($pendingSubmissions)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2>All Caught Up!</h2>
                <p>No pending submissions at the moment. Great job!</p>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn-primary">
                    <i class="bi bi-speedometer2"></i> Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <div class="submissions-list">
                <?php foreach ($pendingSubmissions as $submission): ?>
                    <div class="submission-card" data-submission-id="<?php echo $submission['queue_id']; ?>">
                        
                        <!-- Card Header -->
                        <div class="submission-header">
                            <div class="submission-meta">
                                <?php
                                $badgeClass = 'update';
                                $badgeIcon = 'tag';
                                $badgeLabel = 'Price Update';
                                
                                if ($submission['action_type'] === ACTION_NEW_ITEM) {
                                    $badgeClass = 'new';
                                    $badgeIcon = 'file-earmark-plus';
                                    $badgeLabel = 'New Item';
                                } elseif ($submission['action_type'] === ACTION_ITEM_EDIT) {
                                    $badgeClass = 'edit';
                                    $badgeIcon = 'pencil-square';
                                    $badgeLabel = 'Item Edit';
                                }
                                ?>
                                <span class="submission-badge badge-<?php echo $badgeClass; ?>">
                                    <i class="bi bi-<?php echo $badgeIcon; ?>"></i>
                                    <?php echo $badgeLabel; ?>
                                </span>
                                <span class="submission-time">
                                    <i class="bi bi-clock"></i>
                                    <?php echo date('M j, Y • g:i A', strtotime($submission['submitted_at'])); ?>
                                </span>
                            </div>
                            <div class="submission-contributor">
                                <i class="bi bi-person-circle"></i>
                                <span><?php echo htmlspecialchars($submission['full_name'] ?? 'Unknown'); ?></span>
                            </div>
                        </div>

                        <!-- Item Title -->
                        <h3 class="submission-title">
                            <?php echo htmlspecialchars($submission['item_name'] ?? $submission['existing_item_name'] ?? 'Item'); ?>
                        </h3>

                        <!-- Submission Details -->
                        <div class="submission-details">
                            <?php if ($submission['action_type'] === ACTION_NEW_ITEM || $submission['action_type'] === ACTION_ITEM_EDIT): ?>
                                <!-- New Item / Item Edit Details -->
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-bookmark-fill"></i> Category
                                        </div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars($submission['category_name'] ?? 'N/A'); ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-currency-rupee"></i> Initial Price
                                        </div>
                                        <div class="detail-value price-highlight">
                                            NPR <?php echo number_format($submission['new_price'], 2); ?>
                                            <span class="unit">/ <?php echo htmlspecialchars($submission['unit'] ?? 'unit'); ?></span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-geo-alt-fill"></i> Market Location
                                        </div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars($submission['market_location'] ?? 'N/A'); ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($submission['description'])): ?>
                                        <div class="detail-item detail-full">
                                            <div class="detail-label">
                                                <i class="bi bi-card-text"></i> Description
                                            </div>
                                            <div class="detail-value">
                                                <?php echo nl2br(htmlspecialchars($submission['description'])); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Price Update Details -->
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-arrow-down-circle"></i> Current Price
                                        </div>
                                        <div class="detail-value">
                                            NPR <?php echo number_format((float)$submission['old_price'], 2); ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-arrow-up-circle"></i> New Price
                                        </div>
                                        <div class="detail-value price-highlight">
                                            NPR <?php echo number_format((float)$submission['new_price'], 2); ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-graph-up-arrow"></i> Price Change
                                        </div>
                                        <div class="detail-value">
                                            <?php
                                            // Safely calculate price change percentage
                                            if (!empty($submission['old_price']) && $submission['old_price'] > 0) {
                                                $change = (($submission['new_price'] - $submission['old_price']) / $submission['old_price']) * 100;
                                                $changeClass = $change > 0 ? 'price-increase' : 'price-decrease';
                                                $changeIcon = $change > 0 ? 'bi-arrow-up' : 'bi-arrow-down';
                                            ?>
                                                <span class="<?php echo $changeClass; ?>">
                                                    <i class="bi <?php echo $changeIcon; ?>"></i>
                                                    <?php echo ($change > 0 ? '+' : '') . number_format($change, 1); ?>%
                                                </span>
                                            <?php } else { ?>
                                                <span class="price-increase">
                                                    <i class="bi bi-plus-circle"></i>
                                                    New Price
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-geo-alt-fill"></i> Market Location
                                        </div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars($submission['market_location'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                
                        <!-- Action Buttons -->
                        <div class="submission-actions">
                            <form method="POST" class="action-form" onsubmit="return confirm('Approve this submission?')">
                                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                                <input type="hidden" name="queue_id" value="<?php echo $submission['queue_id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-action btn-approve">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Approve</span>
                                </button>
                            </form>
                            
                            <button type="button" class="btn-action btn-reject" onclick="toggleRejectForm(<?php echo $submission['queue_id']; ?>)">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Reject</span>
                            </button>
                        </div>
                
                        <!-- Rejection Form (Hidden by default) -->
                        <div id="reject-form-<?php echo $submission['queue_id']; ?>" class="reject-form" style="display: none;">
                            <form method="POST" class="reject-form-inner">
                                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                                <input type="hidden" name="queue_id" value="<?php echo $submission['queue_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                
                                <div class="reject-header">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Rejection Reason Required</strong>
                                </div>
                                
                                <textarea 
                                    name="rejection_reason" 
                                    required 
                                    placeholder="Provide a clear explanation for rejecting this submission. The contributor will see this message."
                                    rows="4"></textarea>
                                
                                <div class="reject-actions">
                                    <button type="submit" class="btn-reject-confirm">
                                        <i class="bi bi-send-fill"></i> Confirm Rejection
                                    </button>
                                    <button type="button" class="btn-cancel" onclick="toggleRejectForm(<?php echo $submission['queue_id']; ?>)">
                                        <i class="bi bi-x"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Theme Manager Script -->

<!-- Page Scripts -->
<script>
// Toggle rejection form
function toggleRejectForm(queueId) {
    const form = document.getElementById('reject-form-' + queueId);
    const isHidden = form.style.display === 'none';
    
    // Hide all other rejection forms first
    document.querySelectorAll('.reject-form').forEach(f => f.style.display = 'none');
    
    // Toggle this form
    form.style.display = isHidden ? 'block' : 'none';
    
    // Focus on textarea if showing
    if (isHidden) {
        const textarea = form.querySelector('textarea');
        if (textarea) textarea.focus();
    }
}

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideOutUp 0.5s ease forwards';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Add submission card animations
    const cards = document.querySelectorAll('.submission-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
    });
});

// Search form auto-submit on input (debounced)
let searchTimeout;
const searchInput = document.querySelector('.search-input');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                document.getElementById('searchForm').submit();
            }
        }, 500);
    });
}
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
