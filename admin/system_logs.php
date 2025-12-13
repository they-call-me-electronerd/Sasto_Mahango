<?php
/**
 * Admin System Logs
 * Audit trail of system activities
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin login
Auth::requireRole(ROLE_ADMIN, SITE_URL . '/admin/login.php');

$pageTitle = 'System Logs - SastoMahango Admin';

// Get logs
$logs = Logger::getRecentLogs(100);

$additionalCSS = ['pages/auth-admin.css'];
include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Main Content -->
<main class="dashboard-layout" style="padding-top: 100px;">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">System Logs</h1>
                <p class="dashboard-subtitle">Audit trail of recent system activities</p>
            </div>
        </div>

        <!-- Logs Table -->
        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-journal-check"></i>
                </div>
                <h2>No Logs Found</h2>
                <p>System activity will appear here.</p>
            </div>
        <?php else: ?>
            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="white-space: nowrap;">
                                <?php echo date('M j, Y H:i:s', strtotime($log['created_at'])); ?>
                            </td>
                            <td>
                                <?php if ($log['username']): ?>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="bi bi-person-circle" style="color: var(--brand-primary);"></i>
                                        <span><?php echo htmlspecialchars($log['username']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--dash-text-secondary);">System</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="user-role-badge" style="background: var(--bg-secondary); color: var(--text-primary);">
                                    <?php echo htmlspecialchars($log['action_type']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($log['description']); ?>
                                <?php if ($log['entity_type'] && $log['entity_id']): ?>
                                    <span style="color: var(--dash-text-secondary); font-size: 0.85em;">
                                        (<?php echo htmlspecialchars($log['entity_type']); ?> #<?php echo $log['entity_id']; ?>)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-family: monospace; color: var(--dash-text-secondary);">
                                <?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>

<!-- Theme Manager Script -->
<script src="<?php echo SITE_URL; ?>/assets/js/core/theme-manager.js"></script>

</body>
</html>
