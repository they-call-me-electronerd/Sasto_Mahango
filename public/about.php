<?php
/**
 * About Page - SastoMahango
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../includes/functions.php';

// Get stats
$itemObj = new Item();
$categoryObj = new Category();
$totalProducts = $itemObj->countItems();
$totalMarkets = $itemObj->countMarkets();
$totalCategories = count($categoryObj->getActiveCategories());

$pageTitle = 'About Us';
$metaDescription = 'Learn about SastoMahango - Nepal\'s premier price tracking platform bringing transparency to the marketplace.';
$metaKeywords = 'about sastomahango, price tracking nepal, market transparency, how it works';
$additionalCSS = ['pages/about.css'];

include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="badge-tag">ABOUT SASTOMAHANGO</div>
                <h1 class="hero-title">Bringing <span class="highlight">Transparency</span> to Nepal's Marketplace</h1>
                <p class="hero-subtitle">
                    SastoMahango (सस्तोमहँगो) is Nepal's first comprehensive price tracking platform that empowers consumers with real-time market information.
                </p>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number"><?php echo $totalProducts; ?>+</div>
                        <div class="stat-label">Products Tracked</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?php echo $totalMarkets; ?>+</div>
                        <div class="stat-label">Markets Covered</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?php echo $totalCategories; ?></div>
                        <div class="stat-label">Categories</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <div class="floating-card">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h3>Real-time Updates</h3>
                        <p>Daily price tracking</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="mission-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="mission-card">
                    <div class="icon-wrapper">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h2>Our Mission</h2>
                    <p>To empower every Nepali consumer with accurate, real-time price information, enabling informed purchasing decisions and promoting market transparency across the nation.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mission-card">
                    <div class="icon-wrapper">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h2>Our Vision</h2>
                    <p>To become the most trusted source of market price information in Nepal, creating a fair and transparent marketplace where both consumers and sellers thrive.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <div class="badge-tag">HOW IT WORKS</div>
            <h2>Simple & Transparent Process</h2>
            <p>We make price tracking easy and accessible for everyone</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <div class="step-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3>Community Contributors</h3>
                <p>Verified contributors from markets across Nepal submit daily price updates</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">02</div>
                <div class="step-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>Verification</h3>
                <p>Our admin team reviews and verifies all submitted prices for accuracy</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">03</div>
                <div class="step-icon">
                    <i class="bi bi-cloud-upload-fill"></i>
                </div>
                <h3>Real-time Updates</h3>
                <p>Verified prices are published instantly on our platform</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">04</div>
                <div class="step-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3>Easy Access</h3>
                <p>Users can search and compare prices anytime, completely free</p>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="values-section">
    <div class="container">
        <div class="section-header text-center">
            <div class="badge-tag">OUR VALUES</div>
            <h2>What Drives Us</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h3>Transparency</h3>
                    <p>Open and honest pricing information for all</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <i class="bi bi-check2-circle"></i>
                    <h3>Accuracy</h3>
                    <p>Verified data you can trust and rely on</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <i class="bi bi-heart-fill"></i>
                    <h3>Community</h3>
                    <p>Built by Nepalis, for Nepalis</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Join Us CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2>Become a Contributor</h2>
                    <p>Help us build a transparent marketplace for Nepal. Join our network of verified contributors today.</p>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="<?php echo SITE_URL; ?>/contributor/register.php" class="btn-cta">
                        Join Now
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
