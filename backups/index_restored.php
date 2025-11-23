<?php
/**
 * Landing Page - Mulyasuchi
 */

// Bootstrap application
define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Home';
$additionalCSS = 'public.css';
$additionalJS = 'ticker.js';

// Get categories with item counts
$categoryObj = new Category();
$categories = $categoryObj->getCategoriesWithItemCounts();

// Get recent items
$itemObj = new Item();
$recentItems = $itemObj->getActiveItems(8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
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
    
    <!-- Page Specific Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/pages/landing.css">
    
    <!-- Theme -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/themes/dark-mode.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body>

    <!-- Professional Navigation Bar -->
    <nav class="main-navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a href="<?php echo SITE_URL; ?>" class="navbar-logo">
                <i class="bi bi-graph-up-arrow"></i>
                <span class="logo-text">Mulyasuchi</span>
            </a>

            <!-- Main Menu -->
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li><a href="browse.php">Products</a></li>
                <li><a href="browse.php">Categories</a></li>
                <li><a href="about.php">Insights</a></li>
                <li><a href="about.php">About</a></li>
            </ul>

            <!-- Right Actions -->
            <div class="navbar-actions">
                <button class="nav-search-icon" id="searchToggle">
                    <i class="bi bi-search"></i>
                </button>
                
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                </button>
                
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="dropdown">
                        <button class="nav-btn nav-btn-login dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-xl mt-2">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/contributor/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/contributor/login.php" class="nav-btn nav-btn-login">Login</a>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" class="nav-btn nav-btn-admin">Admin</a>
                <?php endif; ?>

                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <!-- Left Content -->
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Real-time Price Updates</span>
                </div>

                <h1 class="hero-title">
                    What is the price of <span class="highlight">anything</span> today?
                </h1>

                <p class="hero-subtitle">
                    Get instant, verified prices for thousands of products across Nepal. From vegetables to electronics, we track it all.
                </p>

                <!-- Search Box -->
                <form action="browse.php" method="GET" class="hero-search-box">
                    <i class="bi bi-search search-icon-left"></i>
                    <input 
                        type="text" 
                        name="q" 
                        class="hero-search-input" 
                        placeholder="Search for rice, tomatoes, mobile phones..."
                        autocomplete="off"
                    >
                    <button type="submit" class="hero-search-btn">
                        Search
                    </button>
                </form>

                <!-- Stats -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">1,245+</span>
                        <span class="stat-label">Products Tracked</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Markets Covered</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">Daily</span>
                        <span class="stat-label">Price Updates</span>
                    </div>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="hero-illustration">
                <div class="illustration-wrapper">
                    <div class="illustration-circle"></div>
                    <!-- SVG Illustration -->
                    <svg class="illustration-img" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="250" cy="250" r="200" fill="url(#gradient1)" opacity="0.1"/>
                        <ellipse cx="250" cy="280" rx="80" ry="100" fill="#f97316"/>
                        <circle cx="250" cy="180" r="70" fill="#f97316"/>
                        <circle cx="230" cy="170" r="8" fill="#1f2937"/>
                        <circle cx="270" cy="170" r="8" fill="#1f2937"/>
                        <circle cx="232" cy="168" r="3" fill="white"/>
                        <circle cx="272" cy="168" r="3" fill="white"/>
                        <path d="M 230 190 Q 250 200 270 190" stroke="#1f2937" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <ellipse cx="180" cy="250" rx="20" ry="50" fill="#ea580c" transform="rotate(-20 180 250)"/>
                        <ellipse cx="320" cy="250" rx="20" ry="50" fill="#ea580c" transform="rotate(20 320 250)"/>
                        <rect x="140" y="230" width="50" height="35" rx="5" fill="white" stroke="#f97316" stroke-width="2"/>
                        <text x="165" y="252" font-size="16" font-weight="bold" fill="#f97316" text-anchor="middle">NPR</text>
                        <rect x="310" y="230" width="45" height="50" rx="5" fill="#3b82f6"/>
                        <path d="M 320 240 Q 332 230 345 240" stroke="#1e40af" stroke-width="3" stroke-linecap="round" fill="none"/>
                        <circle cx="100" cy="150" r="20" fill="#ef4444"/>
                        <ellipse cx="100" cy="135" rx="8" ry="5" fill="#10b981"/>
                        <path d="M 400 180 L 410 220 L 390 220 Z" fill="#f97316"/>
                        <path d="M 405 175 L 408 165 L 402 165 Z" fill="#10b981"/>
                        <rect x="90" cy="320" width="40" height="60" rx="8" fill="#1f2937"/>
                        <rect x="95" y="328" width="30" height="45" fill="#3b82f6"/>
                        <circle cx="380" cy="300" r="25" fill="#fbbf24"/>
                        <text x="380" y="310" font-size="20" font-weight="bold" fill="#92400e" text-anchor="middle">₹</text>
                        <defs>
                            <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#f97316;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section style="padding: 5rem 0; background: white;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Browse by Category</h2>
                <p style="font-size: 1.125rem; color: #6b7280;">Find prices for thousands of products across different categories</p>
            </div>

            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="browse.php?category=<?php echo $category['slug']; ?>" 
                       class="card border-0 shadow-sm h-100 text-decoration-none"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';">
                        <div class="card-body text-center p-4">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">
                                <?php 
                                $icons = ['vegetables'=>'🥦','fruits'=>'🍎','kitchen-appliances'=>'🍳','study-material'=>'📚','clothing'=>'👕','tools'=>'🔧','electrical-appliances'=>'💡','tech-gadgets'=>'📱','miscellaneous'=>'📦'];
                                echo $icons[$category['slug']] ?? '📦';
                                ?>
                            </div>
                            <h5 class="card-title mb-2" style="color: #111827; font-weight: 600;">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </h5>
                            <p class="card-text mb-3" style="color: #6b7280; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($category['category_name_nepali']); ?>
                            </p>
                            <span class="badge" style="background: rgba(249, 115, 22, 0.1); color: #f97316; padding: 0.5rem 1rem; font-weight: 600; border-radius: 2rem;">
                                <?php echo $category['item_count']; ?> items
                            </span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Recent Updates Section -->
    <?php if (!empty($recentItems)): ?>
    <section style="padding: 5rem 0; background: #f9fafb;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Latest Price Updates</h2>
                    <p style="font-size: 1.125rem; color: #6b7280;">Fresh prices updated daily from trusted sources</p>
                </div>
                <a href="browse.php" class="btn btn-lg" style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; border: none; padding: 0.75rem 2rem; border-radius: 0.75rem; font-weight: 600;">
                    View All <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($recentItems as $item): ?>
                <div class="col-md-6 col-lg-3">
                    <a href="item.php?id=<?php echo $item['item_id']; ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100" style="transition: all 0.3s ease; overflow: hidden;"
                             onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';">
                            
                            <div style="height: 200px; background: #f3f4f6; position: relative; overflow: hidden;">
                                <?php if ($item['image_path']): ?>
                                    <img src="<?php echo UPLOAD_URL . $item['image_path']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #d1d5db; font-weight: 700;">
                                        <?php echo mb_substr($item['item_name'], 0, 1); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(249, 115, 22, 0.95); color: white; padding: 0.375rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-patch-check-fill me-1"></i>Verified
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <span class="badge mb-2" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-weight: 600; font-size: 0.75rem; padding: 0.375rem 0.75rem;">
                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                </span>

                                <h5 class="card-title mb-3" style="color: #111827; font-weight: 600; line-height: 1.4;">
                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                </h5>

                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <span style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">NPR</span>
                                    <span style="font-size: 2rem; font-weight: 800; color: #111827;">
                                        <?php echo formatPrice($item['current_price']); ?>
                                    </span>
                                    <span style="font-size: 0.875rem; color: #9ca3af;">/ <?php echo $item['unit']; ?></span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <small style="color: #6b7280; font-size: 0.8125rem;">
                                        <i class="bi bi-clock me-1"></i> <?php echo timeAgo($item['updated_at']); ?>
                                    </small>
                                    <?php if ($item['market_location']): ?>
                                        <small style="color: #6b7280; font-size: 0.8125rem;">
                                            <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($item['market_location']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Professional Footer -->
    <footer class="professional-footer">
        <div class="footer-main">
            <div class="row g-5 mb-5">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-4">
                        <h3 class="brand-name mb-3">
                            <i class="bi bi-graph-up-arrow me-2"></i>
                            <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Mulyasuchi</span>
                        </h3>
                        <p class="brand-tagline">मूल्यसूची - Nepal's Price Tracker</p>
                    </div>
                    <p class="footer-description">
                        Nepal's most trusted platform for real-time price information. Get verified prices for thousands of products updated daily from markets across Nepal.
                    </p>
                    
                    <!-- Social Media Icons -->
                    <div class="social-icons mt-4">
                        <a href="#" class="social-icon" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo SITE_URL; ?>"><i class="bi bi-chevron-right"></i> Home</a></li>
                        <li><a href="browse.php"><i class="bi bi-chevron-right"></i> Browse Products</a></li>
                        <li><a href="about.php"><i class="bi bi-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i> How It Works</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-heading">Categories</h5>
                    <ul class="footer-links">
                        <li><a href="browse.php?category=vegetables"><i class="bi bi-chevron-right"></i> Vegetables</a></li>
                        <li><a href="browse.php?category=fruits"><i class="bi bi-chevron-right"></i> Fruits</a></li>
                        <li><a href="browse.php?category=kitchen-appliances"><i class="bi bi-chevron-right"></i> Kitchen</a></li>
                        <li><a href="browse.php?category=tech-gadgets"><i class="bi bi-chevron-right"></i> Electronics</a></li>
                        <li><a href="browse.php"><i class="bi bi-chevron-right"></i> View All</a></li>
                    </ul>
                </div>

                <!-- Contact & Newsletter -->
                <div class="col-lg-4 col-md-12">
                    <h5 class="footer-heading">Stay Connected</h5>
                    <p class="mb-3">Subscribe to get daily price updates and market insights delivered to your inbox.</p>
                    
                    <!-- Newsletter Form -->
                    <form class="newsletter-form mb-4" id="newsletterForm">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input" placeholder="Enter your email" required aria-label="Email address">
                            <button class="btn newsletter-btn" type="submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Contact Info -->
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="bi bi-envelope-fill"></i>
                            <span>info@mulyasuchi.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-telephone-fill"></i>
                            <span>+977 1-XXXXXXX</span>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Kathmandu, Nepal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> Mulyasuchi. All rights reserved. 
                            <span class="ms-2">Made with <i class="bi bi-heart-fill text-danger"></i> in Nepal</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="#" class="footer-legal-link">Privacy Policy</a>
                        <span class="mx-2">•</span>
                        <a href="#" class="footer-legal-link">Terms of Service</a>
                        <span class="mx-2">•</span>
                        <a href="#" class="footer-legal-link">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/core/utils.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/core/theme-manager.js"></script>
    
    <!-- Component Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/components/navbar.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/components/footer.js"></script>
    
    <script>
        console.log('✨ Mulyasuchi - Organized & Enhanced UI Loaded!');
    </script>
</body>
</html>
