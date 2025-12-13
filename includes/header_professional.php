<?php
/**
 * Professional Header - Shared across modern pages
 */
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['PHP_SELF'];
$isContributorArea = strpos($currentPath, '/contributor/') !== false;
$isAdminArea = strpos($currentPath, '/admin/') !== false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($metaDescription) ? $metaDescription : SITE_TAGLINE . ' - Real-time market prices across Nepal'; ?>">
    <meta name="keywords" content="<?php echo isset($metaKeywords) ? $metaKeywords : 'price tracking nepal, market prices, sastomahango'; ?>">
    <meta name="author" content="SastoMahango Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($metaDescription) ? $metaDescription : SITE_TAGLINE; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="twitter:description" content="<?php echo isset($metaDescription) ? $metaDescription : SITE_TAGLINE; ?>">
    <meta property="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Core Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/variables.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/core/utilities.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/components/navbar.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/components/footer.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/theme-toggle.css">
    
    <!-- Page Specific Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/pages/landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/wave-dark-mode.css">
    
    <!-- Animation Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/animations/enhanced-animations.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/animations/hero-enhancements.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/pages/enhanced-ui.css">
    
    <!-- Theme -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/themes/dark-mode.css">
    
    <!-- Initialize Theme (Must load BEFORE body) -->
    <script src="<?php echo SITE_URL; ?>/assets/js/core/theme-manager.js"></script>
    <script>
        // Apply saved theme immediately to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('sastomahango-theme');
            const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL . '/assets/css/' . $css; ?>">
        <?php endforeach; ?>
    <?php elseif (isset($additionalCSS)): ?>
        <link rel="stylesheet" href="<?php echo SITE_URL . '/assets/css/' . $additionalCSS; ?>">
    <?php endif; ?>
</head>
<body>
    <?php
    $flash = getFlashMessage();
    if ($flash):
    ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 1rem 1.5rem; border-radius: 0.5rem; background: <?php echo $flash['type'] === 'success' ? '#10b981' : '#ef4444'; ?>; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: slideIn 0.3s ease;">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flashMessage');
            if (flash) {
                flash.style.animation = 'slideIn 0.3s ease reverse';
                setTimeout(() => flash.remove(), 300);
            }
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Professional Navigation Bar -->
    <nav class="main-navbar" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
        <div class="navbar-container" style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; gap: 2rem;">
            <!-- Logo -->
            <a href="<?php echo SITE_URL; ?>/public/index.php" class="navbar-logo" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; font-size: 1.5rem; font-weight: 800; color: #f97316;">
                <i class="bi bi-graph-up-arrow"></i>
                <span class="logo-text">SastoMahango</span>
            </a>

            <!-- Main Menu -->
            <ul class="navbar-menu" id="navbarMenu" style="display: flex; list-style: none; gap: 2rem; margin: 0; padding: 0; align-items: center;">
                <?php if ($isAdminArea): ?>
                    <li><a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="<?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'dashboard.php' ? 'color: #f97316;' : ''; ?>">Dashboard</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin/validation_queue.php" class="<?php echo $currentPage == 'validation_queue.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'validation_queue.php' ? 'color: #f97316;' : ''; ?>">Queue</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin/user_management.php" class="<?php echo $currentPage == 'user_management.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'user_management.php' ? 'color: #f97316;' : ''; ?>">Users</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin/system_logs.php" class="<?php echo $currentPage == 'system_logs.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'system_logs.php' ? 'color: #f97316;' : ''; ?>">Logs</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin/settings.php" class="<?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'settings.php' ? 'color: #f97316;' : ''; ?>">Settings</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>/public/index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'index.php' ? 'color: #f97316;' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/products.php" class="<?php echo $currentPage == 'products.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'products.php' ? 'color: #f97316;' : ''; ?>">Products</a></li>
                    
                    <!-- Search Bar in Navbar -->
                    <li style="margin: 0 0.5rem;">
                        <form action="<?php echo SITE_URL; ?>/public/products.php" method="GET" style="margin: 0;">
                            <div style="position: relative; display: flex; align-items: center;">
                                <input 
                                    type="text" 
                                    name="search" 
                                    placeholder="Search products..." 
                                    style="padding: 0.5rem 2.5rem 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; width: 200px; transition: all 0.3s;"
                                    onmouseover="this.style.borderColor='#22c55e'; this.style.width='240px';"
                                    onmouseout="this.style.borderColor='#d1d5db'; this.style.width='200px';"
                                    onfocus="this.style.borderColor='#22c55e'; this.style.outline='none'; this.style.width='240px';"
                                    onblur="this.style.width='200px';"
                                >
                                <button type="submit" style="position: absolute; right: 8px; background: none; border: none; color: #6b7280; cursor: pointer; padding: 0; display: flex; align-items: center; transition: color 0.3s;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#6b7280';">
                                    <i class="bi bi-search" style="font-size: 1rem;"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                    
                    <li><a href="<?php echo SITE_URL; ?>/public/categories.php" class="<?php echo $currentPage == 'categories.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'categories.php' ? 'color: #f97316;' : ''; ?>">Categories</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/about.php" class="<?php echo $currentPage == 'about.php' ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $currentPage == 'about.php' ? 'color: #f97316;' : ''; ?>">About</a></li>
                    <?php if (Auth::isLoggedIn() && Auth::hasRole(ROLE_CONTRIBUTOR)): ?>
                    <li><a href="<?php echo SITE_URL; ?>/contributor/dashboard.php" class="<?php echo $isContributorArea ? 'active' : ''; ?>" style="text-decoration: none; color: #374151; font-weight: 500; transition: color 0.3s; <?php echo $isContributorArea ? 'color: #f97316; font-weight: 600;' : ''; ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <!-- Right Actions -->
            <div class="navbar-actions" style="display: flex; align-items: center; gap: 1rem;">
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                </button>
                
                <?php if (Auth::isLoggedIn()): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 2rem; padding: 0.5rem 1.5rem; border-color: #f97316; color: #f97316;">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars(Auth::getUsername()); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
                            <?php if (Auth::hasRole(ROLE_ADMIN)): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                            <?php elseif (Auth::hasRole(ROLE_CONTRIBUTOR)): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/contributor/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/<?php echo strtolower(Auth::getUserRole()); ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/contributor/login.php" class="nav-btn nav-btn-outline">Login</a>
                    <a href="<?php echo SITE_URL; ?>/contributor/register.php" class="nav-btn nav-btn-primary">Started</a>
                <?php endif; ?>

                <button class="mobile-menu-toggle" id="mobileMenuToggle" style="display: none; background: none; border: none; font-size: 1.5rem; color: #374151; cursor: pointer;">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>
