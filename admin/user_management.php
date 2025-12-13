<?php
/**
 * Admin User Management
 * Modern, Professional User Administration System
 */

define('SASTOMAHANGO_APP', true);
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

$pageTitle = 'User Management - SastoMahango Admin';
$userObj = new User();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('Invalid security token', 'error');
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $fullName = sanitizeInput($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($email) || empty($fullName) || empty($password)) {
                setFlashMessage('All fields are required', 'error');
            } else {
                $userId = $userObj->createUser($username, $email, $password, $fullName, ROLE_CONTRIBUTOR);
                if ($userId) {
                    setFlashMessage("User '$username' created successfully!", 'success');
                    Logger::log('user_created', "Admin created new user: $username");
                } else {
                    setFlashMessage('Failed to create user. Username or email may already exist.', 'error');
                }
            }
        } elseif ($action === 'update_status') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            if ($userObj->updateUserStatus($userId, $status)) {
                setFlashMessage('User status updated successfully', 'success');
                Logger::log('user_status_updated', "User #$userId status changed to $status");
            } else {
                setFlashMessage('Failed to update status', 'error');
            }
        } elseif ($action === 'delete') {
            $userId = (int)($_POST['user_id'] ?? 0);
            if ($userObj->deleteUser($userId)) {
                setFlashMessage('User deleted successfully', 'success');
                Logger::log('user_deleted', "User #$userId deleted");
            } else {
                setFlashMessage('Failed to delete user', 'error');
            }
        }
        redirect(SITE_URL . '/admin/user_management.php');
    }
}

// Search and filter parameters
$searchTerm = sanitizeInput($_GET['search'] ?? '');
$filterRole = $_GET['role'] ?? '';
$filterStatus = $_GET['status'] ?? '';

// Get all users
$allUsers = $userObj->getAllUsers();
$users = $allUsers;

// Calculate stats (from all users before filtering)
$totalUsers = count($allUsers);
$activeUsers = count(array_filter($allUsers, fn($u) => $u['status'] === 'active'));
$contributorUsers = count(array_filter($allUsers, fn($u) => $u['role'] === ROLE_CONTRIBUTOR));

// Apply filters
if (!empty($searchTerm)) {
    $users = array_filter($users, function($user) use ($searchTerm) {
        return stripos($user['username'], $searchTerm) !== false ||
               stripos($user['full_name'], $searchTerm) !== false ||
               stripos($user['email'], $searchTerm) !== false;
    });
}

if (!empty($filterRole)) {
    $users = array_filter($users, fn($user) => $user['role'] === $filterRole);
}

if (!empty($filterStatus)) {
    $users = array_filter($users, fn($user) => $user['status'] === $filterStatus);
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
                <h1 class="dashboard-title">User Management</h1>
                <p class="dashboard-subtitle">Create and manage contributor accounts</p>
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
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                    <div class="stat-trend">All accounts</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value"><?php echo $activeUsers; ?></div>
                    <div class="stat-trend">Currently active</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Contributors</div>
                    <div class="stat-value"><?php echo $contributorUsers; ?></div>
                    <div class="stat-trend">Content creators</div>
                </div>
            </div>
        </div>

        <!-- Create User Section -->
        <div class="create-user-section">
            <h2>
                <i class="bi bi-person-plus-fill"></i>
                Create New Contributor
            </h2>
            <form method="POST" id="createUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="create">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person"></i> Username *
                        </label>
                        <input 
                            type="text" 
                            name="username" 
                            required 
                            placeholder="johndoe"
                            pattern="[a-zA-Z0-9_]{3,20}"
                            title="3-20 characters, letters, numbers, and underscores only">
                    </div>
                    <div class="form-group">
                        <label>
                            <i class="bi bi-envelope"></i> Email *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person-badge"></i> Full Name *
                        </label>
                        <input 
                            type="text" 
                            name="full_name" 
                            required 
                            placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>
                            <i class="bi bi-key"></i> Password *
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            minlength="8"
                            placeholder="Min. 8 characters">
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="bi bi-plus-circle"></i> Create Contributor
                </button>
            </form>
        </div>

        <!-- Search and Filter Bar -->
        <div class="admin-controls">
            <form method="GET" class="search-form" id="searchForm">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by username, name, or email..." 
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="search-input">
                </div>
                
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="<?php echo ROLE_ADMIN; ?>" <?php echo $filterRole === ROLE_ADMIN ? 'selected' : ''; ?>>Admin</option>
                    <option value="<?php echo ROLE_CONTRIBUTOR; ?>" <?php echo $filterRole === ROLE_CONTRIBUTOR ? 'selected' : ''; ?>>Contributor</option>
                </select>

                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="suspended" <?php echo $filterStatus === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                </select>
                
                <button type="submit" class="btn-search">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                
                <?php if ($searchTerm || $filterRole || $filterStatus): ?>
                    <a href="user_management.php" class="btn-clear">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Users Table -->
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h2>No Users Found</h2>
                <p>Try adjusting your search or filter criteria.</p>
            </div>
        <?php else: ?>
            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--brand-primary);"></i>
                                    <div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <div style="font-size: 0.875rem; color: var(--dash-text-secondary);">
                                            <?php echo htmlspecialchars($user['full_name']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="user-role-badge">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="user-status-badge status-<?php echo $user['status']; ?>">
                                    <i class="bi bi-<?php echo $user['status'] === 'active' ? 'check-circle-fill' : 'x-circle-fill'; ?>"></i>
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['last_login']): ?>
                                    <span title="<?php echo date('M j, Y g:i A', strtotime($user['last_login'])); ?>">
                                        <?php echo date('M j, Y', strtotime($user['last_login'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--dash-text-secondary); font-style: italic;">Never</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['role'] !== ROLE_ADMIN): ?>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <select 
                                                name="status" 
                                                class="filter-select" 
                                                style="padding: 0.4rem 0.6rem; font-size: 0.75rem;"
                                                onchange="if(confirm('Update user status?')) this.form.submit()">
                                                <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspend</option>
                                            </select>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--dash-text-secondary); font-size: 0.875rem;">
                                        <i class="bi bi-shield-lock-fill"></i> Protected
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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

// Form validation
const createUserForm = document.getElementById('createUserForm');
if (createUserForm) {
    createUserForm.addEventListener('submit', function(e) {
        const password = this.querySelector('[name="password"]').value;
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
        }
    });
}

// Filter select styling
const filterSelects = document.querySelectorAll('.filter-select');
filterSelects.forEach(select => {
    select.style.padding = '0.875rem 1rem';
    select.style.background = 'var(--auth-input-bg)';
    select.style.border = '1px solid var(--auth-input-border)';
    select.style.borderRadius = '12px';
    select.style.fontSize = 'var(--font-size-base)';
    select.style.color = 'var(--auth-input-text)';
    select.style.cursor = 'pointer';
});
</script>

<?php include '../includes/footer_professional.php'; ?>
