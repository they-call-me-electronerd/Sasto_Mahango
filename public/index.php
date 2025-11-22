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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/pages/landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/wave-dark-mode.css">
    
    <!-- Theme -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/themes/dark-mode.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Manrope', sans-serif; }
        
        /* Hero Section Alignment & Spacing */
        /* .hero-section { padding-top: 8rem !important; min-height: 90vh; display: flex; align-items: center; } */
        /* .hero-container { display: flex; align-items: center; justify-content: space-between; } */
        .hero-content { text-align: left; max-width: 600px; }
        
        /* Search Bar Sizing */
        /* Search Bar Sizing & Alignment */
        .hero-search-box { max-width: 480px !important; margin-left: 0 !important; margin-right: auto !important; }
        
        /* Stats Alignment */
        .hero-stats { justify-content: flex-start !important; }
        
        /* Section Header Alignment */
        /* section .text-center { text-align: left !important; } */
        /* section .d-inline-block { display: inline-block; margin-left: 0; } */
        /* section p { margin-left: 0; margin-right: auto; } */
        
        /* Font Size Fixes */
        .hero-title { font-size: 3rem !important; }
        .hero-subtitle { font-size: 1.125rem !important; }
        .stat-number { font-size: 1.75rem !important; }
        .stat-label { font-size: 0.875rem !important; }
        
        /* Dark Theme Support - Bluish Gradient */
        [data-theme="dark"] body { background: #0f172a; color: #e2e8f0; }
        [data-theme="dark"] section { background: #1e293b !important; }
        [data-theme="dark"] .hero-section { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important; }
        [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, 
        [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 { color: #f1f5f9 !important; }
        [data-theme="dark"] p { color: #cbd5e1 !important; }
        [data-theme="dark"] .card, [data-theme="dark"] .category-card-inner { background: #1e293b !important; border: 1px solid #334155; }
        [data-theme="dark"] .professional-footer { background: #0f172a !important; border-top-color: #1e3a8a !important; }
        
        /* Animation */
        @keyframes growUp { from { height: 0; opacity: 0; } to { opacity: 1; } }
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
       
        <!-- Nepal Skyline Image -->
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; z-index: 1; pointer-events: none;">
            <img src="<?php echo SITE_URL; ?>/assets/images/nepal-skyline.png" 
                 alt="Nepal Skyline" 
                 style="width: 100%; height: auto; opacity: 0.25; display: block;">
        </div>
        
        <!-- Bottom Wave Divider -->
        <div class="section-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
        <div class="nepali-divider"></div>
    </section>

    <!-- What is Mulyasuchi Section -->
    <section class="section-colored" style="padding: 6rem 0; position: relative;">
       
        </div>
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1.5rem;">
                        <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">ABOUT US</span>
                    </div>
                    <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1.5rem; line-height: 1.2;">What is <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Mulyasuchi</span>?</h2>
                    <p style="font-size: 1.125rem; color: #6b7280; line-height: 1.8; margin-bottom: 2rem;">Mulyasuchi (मूल्यसूची) is Nepal's first comprehensive price tracking platform that brings transparency to the marketplace. We collect, verify, and publish real-time prices of everyday products from markets across Nepal.</p>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 48px; height: 48px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);"><i class="bi bi-check2-circle" style="font-size: 1.5rem; color: white;"></i></div>
                            <div><h4 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">Verified Data</h4><p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Prices verified by our contributors</p></div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);"><i class="bi bi-clock-history" style="font-size: 1.5rem; color: white;"></i></div>
                            <div><h4 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">Daily Updates</h4><p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Fresh prices every single day</p></div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);"><i class="bi bi-geo-alt" style="font-size: 1.5rem; color: white;"></i></div>
                            <div><h4 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">Nationwide</h4><p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Coverage across Nepal</p></div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);"><i class="bi bi-shield-check" style="font-size: 1.5rem; color: white;"></i></div>
                            <div><h4 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">100% Free</h4><p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Always free for everyone</p></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background: white; border-radius: 24px; padding: 3rem; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: center; margin-bottom: 2rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Our Impact</h3><p style="color: #6b7280; font-size: 0.875rem;">Real-time statistics</p></div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 2rem;">
                            <div style="text-align: center;"><div style="width: 100px; height: 100px; margin: 0 auto 1rem; position: relative;"><svg viewBox="0 0 100 100" style="transform: rotate(-90deg);"><circle cx="50" cy="50" r="40" fill="none" stroke="#f3f4f6" stroke-width="8"/><circle cx="50" cy="50" r="40" fill="none" stroke="#f97316" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-linecap="round"/></svg><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.25rem; font-weight: 800; color: #f97316;">75%</div></div><p style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">1,245+</p><p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Products</p></div>
                            <div style="text-align: center;"><div style="width: 100px; height: 100px; margin: 0 auto 1rem; position: relative;"><svg viewBox="0 0 100 100" style="transform: rotate(-90deg);"><circle cx="50" cy="50" r="40" fill="none" stroke="#f3f4f6" stroke-width="8"/><circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="100.48" stroke-linecap="round"/></svg><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.25rem; font-weight: 800; color: #3b82f6;">60%</div></div><p style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">50+</p><p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Markets</p></div>
                            <div style="text-align: center;"><div style="width: 100px; height: 100px; margin: 0 auto 1rem; position: relative;"><svg viewBox="0 0 100 100" style="transform: rotate(-90deg);"><circle cx="50" cy="50" r="40" fill="none" stroke="#f3f4f6" stroke-width="8"/><circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="37.68" stroke-linecap="round"/></svg><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.25rem; font-weight: 800; color: #10b981;">85%</div></div><p style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">10K+</p><p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Users</p></div>
                        </div>
                        <div style="background: #f9fafb; border-radius: 16px; padding: 1.5rem;"><p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Daily Price Updates</p><div style="display: flex; align-items-end; gap: 0.75rem; height: 100px;"><div style="flex: 1; background: linear-gradient(180deg, #f97316, #ea580c); border-radius: 8px 8px 0 0; height: 60%; animation: growUp 1.5s ease;"></div><div style="flex: 1; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 8px 8px 0 0; height: 80%; animation: growUp 1.7s ease;"></div><div style="flex: 1; background: linear-gradient(180deg, #10b981, #059669); border-radius: 8px 8px 0 0; height: 100%; animation: growUp 1.9s ease;"></div><div style="flex: 1; background: linear-gradient(180deg, #8b5cf6, #7c3aed); border-radius: 8px 8px 0 0; height: 70%; animation: growUp 2.1s ease;"></div><div style="flex: 1; background: linear-gradient(180deg, #f97316, #ea580c); border-radius: 8px 8px 0 0; height: 90%; animation: growUp 2.3s ease;"></div></div><div style="display: flex; justify-content: space-between; margin-top: 0.5rem;"><span style="font-size: 0.75rem; color: #9ca3af;">Mon</span><span style="font-size: 0.75rem; color: #9ca3af;">Tue</span><span style="font-size: 0.75rem; color: #9ca3af;">Wed</span><span style="font-size: 0.75rem; color: #9ca3af;">Thu</span><span style="font-size: 0.75rem; color: #9ca3af;">Fri</span></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section style="padding: 6rem 0; background: #f9fafb; position: relative;">
        
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;"><span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">WHY US</span></div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">Why Choose Mulyasuchi?</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">We're committed to bringing price transparency and helping you make informed purchasing decisions</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3"><div style="background: linear-gradient(135deg, #fff5f0, #ffffff); border-radius: 20px; padding: 2rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#f97316'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(249, 115, 22, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"><div style="width: 64px; height: 64px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);"><i class="bi bi-lightning-charge-fill" style="font-size: 2rem; color: white;"></i></div><h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Real-Time Updates</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Get the latest prices updated daily from verified sources across Nepal's markets</p></div></div>
                <div class="col-md-6 col-lg-3"><div style="background: linear-gradient(135deg, #eff6ff, #ffffff); border-radius: 20px; padding: 2rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#3b82f6'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(59, 130, 246, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"><div style="width: 64px; height: 64px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);"><i class="bi bi-shield-check" style="font-size: 2rem; color: white;"></i></div><h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Verified Data</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">All prices are verified by our network of trusted contributors and market researchers</p></div></div>
                <div class="col-md-6 col-lg-3"><div style="background: linear-gradient(135deg, #f0fdf4, #ffffff); border-radius: 20px; padding: 2rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(16, 185, 129, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"><div style="width: 64px; height: 64px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);"><i class="bi bi-graph-up-arrow" style="font-size: 2rem; color: white;"></i></div><h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Price Trends</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Track price changes over time and make smart purchasing decisions based on trends</p></div></div>
                <div class="col-md-6 col-lg-3"><div style="background: linear-gradient(135deg, #faf5ff, #ffffff); border-radius: 20px; padding: 2rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#8b5cf6'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(139, 92, 246, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"><div style="width: 64px; height: 64px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);"><i class="bi bi-people-fill" style="font-size: 2rem; color: white;"></i></div><h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Community Driven</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Built by the community, for the community. Anyone can contribute and help</p></div></div>
            </div>
        </div>
        
        <!-- Bottom Wave Divider -->
    </section>
    </section>

    <!-- How It Works Section -->
    <section style="padding: 6rem 0; background: white; position: relative;">

        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;"><span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">PROCESS</span></div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">How It Works</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">Simple, transparent, and effective - here's how we bring you accurate prices</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div style="position: relative;">
                        <div style="position: absolute; left: 32px; top: 80px; bottom: 80px; width: 2px; background: linear-gradient(180deg, #f97316, #3b82f6, #10b981); opacity: 0.3;"></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">1</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Data Collection</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Our network of contributors visits local markets and collects price information for various products daily</p></div></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">2</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Verification</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Each price entry is verified through multiple sources to ensure accuracy and reliability</p></div></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">3</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Publishing</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Verified prices are published on our platform, making them accessible to everyone for free</p></div></div>
                        <div style="display: flex; gap: 1.5rem; position: relative;"><div style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">4</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">You Benefit</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Make informed decisions, compare prices, and shop smarter with real-time market data</p></div></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background: white; border-radius: 24px; padding: 3rem; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);">
                        <svg viewBox="0 0 400 400" style="width: 100%; height: auto;"><rect x="50" y="200" width="120" height="150" fill="#f97316" rx="8"/><rect x="70" y="220" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="120" y="220" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="70" y="280" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="120" y="280" width="30" height="40" fill="#fff" opacity="0.8"/><polygon points="50,200 110,160 170,200" fill="#ea580c"/><path d="M 180 275 L 220 275" stroke="#3b82f6" stroke-width="4" fill="none" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><polygon points="220,275 210,270 210,280" fill="#3b82f6"/><ellipse cx="280" cy="260" rx="50" ry="20" fill="#3b82f6"/><rect x="230" y="260" width="100" height="60" fill="#3b82f6"/><ellipse cx="280" cy="320" rx="50" ry="20" fill="#2563eb"/><rect x="245" y="275" width="70" height="5" fill="#fff" opacity="0.3"/><rect x="245" y="285" width="70" height="5" fill="#fff" opacity="0.3"/><rect x="245" y="295" width="70" height="5" fill="#fff" opacity="0.3"/><circle cx="110" cy="100" r="25" fill="#10b981"/><path d="M 110 125 L 110 160 M 95 140 L 125 140 M 110 160 L 95 190 M 110 160 L 125 190" stroke="#10b981" stroke-width="8" stroke-linecap="round"/><circle cx="280" cy="100" r="25" fill="#8b5cf6"/><path d="M 280 125 L 280 160 M 265 140 L 295 140 M 280 160 L 265 190 M 280 160 L 295 190" stroke="#8b5cf6" stroke-width="8" stroke-linecap="round"/><path d="M 110 190 L 110 200" stroke="#10b981" stroke-width="3" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><path d="M 280 190 Q 280 230 280 260" stroke="#8b5cf6" stroke-width="3" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><circle cx="200" cy="150" r="30" fill="#10b981" opacity="0.2"><animate attributeName="r" values="30;35;30" dur="2s" repeatCount="indefinite"/></circle><path d="M 185 150 L 195 160 L 215 140" stroke="#10b981" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Categories Section -->
    <section style="padding: 6rem 0; background: #f9fafb; position: relative;">

        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;">
                    <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">EXPLORE</span>
                </div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">Browse by Category</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 700px; margin: 0 auto;">Find verified prices for thousands of products across different categories - from fresh produce to electronics</p>
            </div>

            <div class="row g-4">
                <?php 
                $categoryColors = [
                    'vegetables' => ['from' => '#10b981', 'to' => '#059669', 'bg' => '#f0fdf4'],
                    'fruits' => ['from' => '#ef4444', 'to' => '#dc2626', 'bg' => '#fef2f2'],
                    'kitchen-appliances' => ['from' => '#f59e0b', 'to' => '#d97706', 'bg' => '#fffbeb'],
                    'study-material' => ['from' => '#3b82f6', 'to' => '#2563eb', 'bg' => '#eff6ff'],
                    'clothing' => ['from' => '#ec4899', 'to' => '#db2777', 'bg' => '#fdf2f8'],
                    'tools' => ['from' => '#6366f1', 'to' => '#4f46e5', 'bg' => '#eef2ff'],
                    'electrical-appliances' => ['from' => '#8b5cf6', 'to' => '#7c3aed', 'bg' => '#faf5ff'],
                    'tech-gadgets' => ['from' => '#06b6d4', 'to' => '#0891b2', 'bg' => '#ecfeff'],
                    'miscellaneous' => ['from' => '#f97316', 'to' => '#ea580c', 'bg' => '#fff7ed']
                ];
                $icons = ['vegetables'=>'🥦','fruits'=>'🍎','kitchen-appliances'=>'🍳','study-material'=>'📚','clothing'=>'👕','tools'=>'🔧','electrical-appliances'=>'💡','tech-gadgets'=>'📱','miscellaneous'=>'📦'];
                foreach ($categories as $category): 
                    $slug = $category['slug'];
                    $colors = $categoryColors[$slug] ?? ['from' => '#f97316', 'to' => '#ea580c', 'bg' => '#fff7ed'];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="browse.php?category=<?php echo $slug; ?>" class="text-decoration-none d-block h-100" style="position: relative; overflow: hidden; border-radius: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-12px) scale(1.02)'; this.querySelector('.category-card-inner').style.boxShadow='0 25px 50px -12px rgba(0, 0, 0, 0.25)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.querySelector('.category-card-inner').style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';">
                        <div class="category-card-inner" style="background: <?php echo $colors['bg']; ?>; border-radius: 20px; padding: 2rem 1.5rem; text-align: left; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.4s ease;">
                            <div style="position: absolute; top: -50%; right: -50%; width: 150%; height: 150%; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); opacity: 0.05; border-radius: 50%;"></div>
                            <div style="position: relative; z-index: 2; width: 80px; height: 80px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);"><span style="font-size: 2.5rem;"><?php echo $icons[$slug] ?? '📦'; ?></span></div>
                            <h3 style="position: relative; z-index: 2; font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p style="position: relative; z-index: 2; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; font-weight: 500;"><?php echo $category['category_name_nepali']; ?></p>
                            <div style="position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 0.5rem; background: white; padding: 0.5rem 1rem; border-radius: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"><div style="width: 8px; height: 8px; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); border-radius: 50%;"></div><span style="font-weight: 700; font-size: 0.875rem; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo $category['item_count']; ?> items</span></div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
                
                <div class="col-6 col-md-4 col-lg-3"><a href="browse.php?category=groceries" class="text-decoration-none d-block h-100" style="position: relative; overflow: hidden; border-radius: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-12px) scale(1.02)'; this.querySelector('.category-card-inner').style.boxShadow='0 25px 50px -12px rgba(0, 0, 0, 0.25)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.querySelector('.category-card-inner').style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';"><div class="category-card-inner" style="background: #fefce8; border-radius: 20px; padding: 2rem 1.5rem; text-align: left; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);"><div style="position: absolute; top: -50%; right: -50%; width: 150%; height: 150%; background: linear-gradient(135deg, #eab308, #ca8a04); opacity: 0.05; border-radius: 50%;"></div><div style="position: relative; z-index: 2; width: 80px; height: 80px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, #eab308, #ca8a04); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);"><span style="font-size: 2.5rem;">🛒</span></div><h3 style="position: relative; z-index: 2; font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Groceries</h3><p style="position: relative; z-index: 2; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; font-weight: 500;">किराना सामान</p><div style="position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 0.5rem; background: white; padding: 0.5rem 1rem; border-radius: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"><div style="width: 8px; height: 8px; background: linear-gradient(135deg, #eab308, #ca8a04); border-radius: 50%;"></div><span style="font-weight: 700; font-size: 0.875rem; background: linear-gradient(135deg, #eab308, #ca8a04); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Coming Soon</span></div></div></a></div>
                <div class="col-6 col-md-4 col-lg-3"><a href="browse.php?category=furniture" class="text-decoration-none d-block h-100" style="position: relative; overflow: hidden; border-radius: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-12px) scale(1.02)'; this.querySelector('.category-card-inner').style.boxShadow='0 25px 50px -12px rgba(0, 0, 0, 0.25)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.querySelector('.category-card-inner').style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';"><div class="category-card-inner" style="background: #fef2f2; border-radius: 20px; padding: 2rem 1.5rem; text-align: left; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);"><div style="position: absolute; top: -50%; right: -50%; width: 150%; height: 150%; background: linear-gradient(135deg, #dc2626, #b91c1c); opacity: 0.05; border-radius: 50%;"></div><div style="position: relative; z-index: 2; width: 80px; height: 80px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, #dc2626, #b91c1c); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);"><span style="font-size: 2.5rem;">🛋️</span></div><h3 style="position: relative; z-index: 2; font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Furniture</h3><p style="position: relative; z-index: 2; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; font-weight: 500;">फर्निचर</p><div style="position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 0.5rem; background: white; padding: 0.5rem 1rem; border-radius: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"><div style="width: 8px; height: 8px; background: linear-gradient(135deg, #dc2626, #b91c1c); border-radius: 50%;"></div><span style="font-weight: 700; font-size: 0.875rem; background: linear-gradient(135deg, #dc2626, #b91c1c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Coming Soon</span></div></div></a></div>
                <div class="col-6 col-md-4 col-lg-3"><a href="browse.php?category=sports" class="text-decoration-none d-block h-100" style="position: relative; overflow: hidden; border-radius: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-12px) scale(1.02)'; this.querySelector('.category-card-inner').style.boxShadow='0 25px 50px -12px rgba(0, 0, 0, 0.25)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.querySelector('.category-card-inner').style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';"><div class="category-card-inner" style="background: #f0fdfa; border-radius: 20px; padding: 2rem 1.5rem; text-align: left; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);"><div style="position: absolute; top: -50%; right: -50%; width: 150%; height: 150%; background: linear-gradient(135deg, #14b8a6, #0d9488); opacity: 0.05; border-radius: 50%;"></div><div style="position: relative; z-index: 2; width: 80px; height: 80px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);"><span style="font-size: 2.5rem;">⚽</span></div><h3 style="position: relative; z-index: 2; font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Sports & Fitness</h3><p style="position: relative; z-index: 2; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; font-weight: 500;">खेलकुद</p><div style="position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 0.5rem; background: white; padding: 0.5rem 1rem; border-radius: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"><div style="width: 8px; height: 8px; background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 50%;"></div><span style="font-weight: 700; font-size: 0.875rem; background: linear-gradient(135deg, #14b8a6, #0d9488); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Coming Soon</span></div></div></a></div>
            </div>
            
            <div style="text-align: center; margin-top: 3rem;">
                <a href="browse.php" style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 1rem 2.5rem; border-radius: 3rem; font-weight: 700; font-size: 1.125rem; text-decoration: none; box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 35px rgba(249, 115, 22, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(249, 115, 22, 0.3)';"><span>Explore All Categories</span><i class="bi bi-arrow-right" style="font-size: 1.25rem;"></i></a>
            </div>
        </div>
    </section>

    <?php if (!empty($recentItems)): ?>
    <!-- Recent Updates Section -->
    <section style="padding: 6rem 0; background: white; position: relative;">

        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;">
                    <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">FRESH ARRIVALS</span>
                </div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">Recent Updates</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">Latest price updates from markets near you</p>
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
