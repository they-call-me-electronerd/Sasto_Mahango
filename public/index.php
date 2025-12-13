<?php
/**
 * Landing Page - SastoMahango
 */

// Set UTF-8 encoding for proper Nepali text display
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: text/html; charset=utf-8');

// Bootstrap application
define('SASTOMAHANGO_APP', true);
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
$metaDescription = "SastoMahango is Nepal's premier price tracking platform. Track daily market prices of vegetables, fruits, and essential commodities across 50+ markets in Nepal.";
$metaKeywords = "price tracking nepal, vegetable prices nepal, kalimati market price, daily rates, sastomahango, commodity prices";
$additionalCSS = ['pages/landing.css', 'pages/home-categories.css', 'animations/enhanced-animations.css', 'animations/hero-enhancements.css', 'components/ad-carousel.css', 'components/ad-banner.css'];
$additionalJS = ['animations/counter-animation.js', 'animations/scroll-animations.js', 'components/ticker.js', 'components/ad-carousel.js', 'components/ad-banner.js', 'components/category-carousel.js', 'components/landing-ad-carousel.js'];

// Get categories with item counts
$categoryObj = new Category();
$categories = $categoryObj->getCategoriesWithItemCounts();

// Get recent items
$itemObj = new Item();
$recentItems = $itemObj->getActiveItems(8);

// Get stats
$totalProducts = $itemObj->countItems();
$totalMarkets = $itemObj->countMarkets();

include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Price Ticker -->
<div class="price-ticker-container">
    <div class="price-ticker">
        <!-- Ticker items will be loaded via AJAX -->
    </div>
</div>

<main>
    <!-- Landing Advertisement Section -->
    <section class="landing-ad-section" style="background: #f8f9fa; padding: 6rem 0 4rem; position: relative; margin: 0; width: 100vw; margin-left: calc(-50vw + 50%); overflow: hidden;">
        <div style="width: 100%; margin: 0; padding: 0;">
            <!-- Advertisement Carousel -->
            <div class="landing-ad-carousel" style="width: 100%; position: relative;">
                <div class="landing-ad-container" style="position: relative; overflow: hidden; width: 100vw; background: transparent;">
                    <!-- Ad Slides Track -->
                    <div class="landing-ad-track" style="display: flex; transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); will-change: transform;">
                        <!-- Ad 1: Shadow Headphone -->
                        <div class="landing-ad-slide" style="flex: 0 0 100%; width: 100%;">
                            <a href="#" target="_blank" rel="noopener noreferrer" class="ad-link" style="display: block; width: 100%; position: relative;">
                                <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/shadow-headphone.jpg" 
                                     alt="Shadow Headphone - Exclusive Launch Offer" 
                                     class="ad-image"
                                     style="width: 100%; height: auto; display: block; object-fit: contain;">
                                <div class="ad-badge" style="position: absolute; top: 2rem; right: 2rem; background: rgba(0, 0, 0, 0.85); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 0.95rem; font-weight: 600; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);">
                                    <i class="bi bi-badge-ad"></i> Ad
                                </div>
                            </a>
                        </div>
                        
                        <!-- Ad 2: Ultima Power Bank -->
                        <div class="landing-ad-slide" style="flex: 0 0 100%; width: 100%;">
                            <a href="#" target="_blank" rel="noopener noreferrer" class="ad-link" style="display: block; width: 100%; position: relative;">
                                <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/ad1.jpg" 
                                     alt="Ultima Boost 20K Ultra Max - Coming Soon" 
                                     class="ad-image"
                                     style="width: 100%; height: auto; display: block; object-fit: contain;">
                                <div class="ad-badge" style="position: absolute; top: 2rem; right: 2rem; background: rgba(0, 0, 0, 0.85); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 0.95rem; font-weight: 600; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);">
                                    <i class="bi bi-badge-ad"></i> Ad
                                </div>
                            </a>
                        </div>
                        
                        <!-- Ad 3: Cetaphil Products -->
                        <div class="landing-ad-slide" style="flex: 0 0 100%; width: 100%;">
                            <a href="#" target="_blank" rel="noopener noreferrer" class="ad-link" style="display: block; width: 100%; position: relative;">
                                <img src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/uploads/ads/ad2.jpg" 
                                     alt="Cetaphil - Up to 10% Off" 
                                     class="ad-image"
                                     style="width: 100%; height: auto; display: block; object-fit: contain;">
                                <div class="ad-badge" style="position: absolute; top: 2rem; right: 2rem; background: rgba(0, 0, 0, 0.85); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 0.95rem; font-weight: 600; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);">
                                    <i class="bi bi-badge-ad"></i> Ad
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button class="landing-ad-prev" aria-label="Previous ad" style="position: absolute; left: 2rem; top: 50%; transform: translateY(-50%); z-index: 10; width: 60px; height: 60px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid rgba(0, 0, 0, 0.1); color: #333; font-size: 1.75rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="landing-ad-next" aria-label="Next ad" style="position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); z-index: 10; width: 60px; height: 60px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid rgba(0, 0, 0, 0.1); color: #333; font-size: 1.75rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    
                    <!-- Progress Indicators -->
                    <div class="landing-ad-indicators" style="position: absolute; bottom: 2.5rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.75rem; z-index: 10;">
                        <button class="landing-ad-indicator active" data-slide="0" aria-label="Go to ad 1" style="width: 40px; height: 4px; border-radius: 2px; background: #333; border: none; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <span class="progress-bar" style="position: absolute; top: 0; left: 0; height: 100%; width: 0; background: #22c55e; transition: width 2s linear;"></span>
                        </button>
                        <button class="landing-ad-indicator" data-slide="1" aria-label="Go to ad 2" style="width: 40px; height: 4px; border-radius: 2px; background: rgba(0, 0, 0, 0.3); border: none; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <span class="progress-bar" style="position: absolute; top: 0; left: 0; height: 100%; width: 0; background: #22c55e; transition: width 2s linear;"></span>
                        </button>
                        <button class="landing-ad-indicator" data-slide="2" aria-label="Go to ad 3" style="width: 40px; height: 4px; border-radius: 2px; background: rgba(0, 0, 0, 0.3); border: none; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <span class="progress-bar" style="position: absolute; top: 0; left: 0; height: 100%; width: 0; background: #22c55e; transition: width 2s linear;"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    // Inline Landing Ad Carousel - Immediate Execution
    (function() {
        const track = document.querySelector('.landing-ad-track');
        const prevBtn = document.querySelector('.landing-ad-prev');
        const nextBtn = document.querySelector('.landing-ad-next');
        const indicators = document.querySelectorAll('.landing-ad-indicator');
        
        if (!track || !indicators.length) return;
        
        let currentIndex = 0;
        const totalSlides = 3;
        let autoScrollInterval = null;
        
        function goToSlide(index) {
            currentIndex = index;
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
            
            indicators.forEach((ind, i) => {
                if (i === currentIndex) {
                    ind.classList.add('active');
                    ind.style.background = '#333';
                    const progressBar = ind.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.transition = 'none';
                        progressBar.style.width = '0%';
                        setTimeout(() => {
                            progressBar.style.transition = 'width 2s linear';
                            progressBar.style.width = '100%';
                        }, 10);
                    }
                } else {
                    ind.classList.remove('active');
                    ind.style.background = 'rgba(0, 0, 0, 0.3)';
                    const progressBar = ind.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.transition = 'none';
                        progressBar.style.width = '0%';
                    }
                }
            });
        }
        
        function next() {
            const nextIndex = (currentIndex + 1) % totalSlides;
            goToSlide(nextIndex);
        }
        
        function prev() {
            const prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            goToSlide(prevIndex);
        }
        
        function startAutoScroll() {
            stopAutoScroll();
            goToSlide(currentIndex);
            autoScrollInterval = setInterval(next, 2000);
        }
        
        function stopAutoScroll() {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }
        }
        
        // Event listeners
        if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAutoScroll(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAutoScroll(); });
        
        indicators.forEach((ind, index) => {
            ind.addEventListener('click', () => { goToSlide(index); startAutoScroll(); });
        });
        
        const container = document.querySelector('.landing-ad-container');
        if (container) {
            container.addEventListener('mouseenter', stopAutoScroll);
            container.addEventListener('mouseleave', startAutoScroll);
        }
        
        // Start
        startAutoScroll();
    })();
    </script>

    <!-- Popular Products Marquee Ribbon -->
    <div class="marquee-ribbon" style="padding: 1rem 0; overflow: hidden; position: relative;">
        <div style="display: flex; align-items: center; max-width: 100%; margin: 0 auto;">
            <!-- Trending Label -->
            <div class="trending-label" style="padding: 0 2rem; flex-shrink: 0; font-weight: 700; font-size: 0.875rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-graph-up-arrow" style="font-size: 1rem;"></i>
                TRENDING
            </div>
            
            <!-- Marquee Container -->
            <div class="marquee-container" style="overflow: hidden; position: relative; flex: 1;">
                <div class="marquee-content" style="display: flex; gap: 2.5rem; animation: marquee 40s linear infinite; white-space: nowrap;">
                    <?php 
                    $popularItems = $itemObj->getActiveItems(12);
                    $marqueeItems = array_merge($popularItems, $popularItems); // Duplicate for seamless loop
                    foreach ($marqueeItems as $popularItem): 
                        // Determine trend direction (random for demo, you can add logic based on price history)
                        $trendUp = rand(0, 1) == 1;
                        $trendIcon = $trendUp ? '↑' : '↓';
                        $trendColor = $trendUp ? '#10b981' : '#ef4444';
                    ?>
                    <div class="marquee-item" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.25rem 0; min-width: fit-content;">
                        <div class="marquee-item-name" style="font-weight: 600; font-size: 0.9rem; white-space: nowrap;">
                            <?php echo htmlspecialchars($popularItem['item_name']); ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="marquee-item-price" style="font-weight: 500; font-size: 0.85rem; white-space: nowrap;">
                                <?php echo formatPrice($popularItem['current_price']); ?>
                            </span>
                            <span style="color: <?php echo $trendColor; ?>; font-weight: 700; font-size: 0.95rem;">
                                <?php echo $trendIcon; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Marquee Ribbon Styles */
        .marquee-ribbon {
            background: white;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .trending-label {
            color: #1f2937;
        }
        
        .marquee-item-name {
            color: #1f2937;
        }
        
        .marquee-item-price {
            color: #6b7280;
        }
        
        /* Dark Theme Support */
        [data-theme="dark"] .marquee-ribbon,
        body.dark-mode .marquee-ribbon {
            background: #1e293b;
            border-top: 1px solid #334155;
            border-bottom: 1px solid #334155;
        }
        
        [data-theme="dark"] .trending-label,
        body.dark-mode .trending-label {
            color: #f1f5f9;
        }
        
        [data-theme="dark"] .marquee-item-name,
        body.dark-mode .marquee-item-name {
            color: #f1f5f9;
        }
        
        [data-theme="dark"] .marquee-item-price,
        body.dark-mode .marquee-item-price {
            color: #94a3b8;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .marquee-container:hover .marquee-content {
            animation-play-state: paused;
        }
    </style>

    <!-- Categories Section -->
    <section class="scroll-reveal section-padding categories-section" aria-label="Product Categories" style="position: relative; padding: 6rem 0;">
        <!-- Decorative Elements -->
        <div class="categories-decorative" style="position: absolute; top: 0; left: 0; right: 0; height: 200px; pointer-events: none;"></div>
        
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div class="categories-badge" style="display: inline-block; padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem; backdrop-filter: blur(10px);">
                    <span style="background: linear-gradient(135deg, #22c55e, #16a34a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem; letter-spacing: 0.05em;">EXPLORE CATEGORIES</span>
                </div>
                <h2 class="categories-title" style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; font-family: 'Manrope', sans-serif;">Browse by Category</h2>
                <p class="categories-subtitle" style="font-size: 1.125rem; max-width: 700px; margin: 0 auto; line-height: 1.7;">Find verified prices for thousands of products across different categories</p>
            </div>

            <div class="row g-4">
                <?php 
                $categoryColors = [
                    'vegetables' => ['from' => '#10b981', 'to' => '#059669', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => '#dcfce7'],
                    'fruits' => ['from' => '#ef4444', 'to' => '#dc2626', 'bg' => 'rgba(254, 226, 226, 0.5)', 'light' => '#fee2e2'],
                    'groceries' => ['from' => '#eab308', 'to' => '#ca8a04', 'bg' => 'rgba(254, 249, 195, 0.5)', 'light' => '#fef9c3'],
                    'dairy-products' => ['from' => '#60a5fa', 'to' => '#3b82f6', 'bg' => 'rgba(219, 234, 254, 0.5)', 'light' => '#dbeafe'],
                    'meat-fish' => ['from' => '#f87171', 'to' => '#ef4444', 'bg' => 'rgba(254, 226, 226, 0.5)', 'light' => '#fee2e2'],
                    'spices' => ['from' => '#fb923c', 'to' => '#f97316', 'bg' => 'rgba(255, 237, 213, 0.5)', 'light' => '#ffedd5'],
                    'kitchen-appliances' => ['from' => '#f59e0b', 'to' => '#d97706', 'bg' => 'rgba(254, 243, 199, 0.5)', 'light' => '#fef3c7'],
                    'household-items' => ['from' => '#fb923c', 'to' => '#f97316', 'bg' => 'rgba(255, 237, 213, 0.5)', 'light' => '#ffedd5'],
                    'electrical-appliances' => ['from' => '#8b5cf6', 'to' => '#7c3aed', 'bg' => 'rgba(237, 233, 254, 0.5)', 'light' => '#ede9fe'],
                    'clothing' => ['from' => '#ec4899', 'to' => '#db2777', 'bg' => 'rgba(252, 231, 243, 0.5)', 'light' => '#fce7f3'],
                    'study-material' => ['from' => '#3b82f6', 'to' => '#2563eb', 'bg' => 'rgba(219, 234, 254, 0.5)', 'light' => '#dbeafe'],
                    'tools-hardware' => ['from' => '#f97316', 'to' => '#ea580c', 'bg' => 'rgba(255, 237, 213, 0.5)', 'light' => '#ffedd5']
                ];
                $icons = [
                    'vegetables'=>'🥦', 'fruits'=>'🍎', 'groceries'=>'🛒', 'dairy-products'=>'🥛',
                    'meat-fish'=>'🐟', 'spices'=>'🌶️', 'kitchen-appliances'=>'🔍',
                    'household-items'=>'📦', 'electrical-appliances'=>'💡', 'clothing'=>'👕',
                    'study-material'=>'📚', 'tools-hardware'=>'🔧'
                ];
                foreach ($categories as $category): 
                    $slug = $category['slug'];
                    $colors = $categoryColors[$slug] ?? ['from' => '#22c55e', 'to' => '#16a34a', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => '#dcfce7'];
                ?>
                <div class="col-6 col-md-4 col-lg-2 category-card-animate">
                    <a href="products.php?category=<?php echo $category['category_id']; ?>" style="text-decoration: none; display: block; height: 100%;">
                        <div class="modern-category-card" data-category="<?php echo $slug; ?>" style="
                            background: <?php echo $colors['light']; ?>;
                            backdrop-filter: blur(20px);
                            border-radius: 24px;
                            padding: 2rem 1.5rem;
                            text-align: left;
                            position: relative;
                            overflow: hidden;
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            cursor: pointer;
                            height: 320px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            border: 1px solid rgba(255, 255, 255, 0.8);
                        " 
                        onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';"
                        onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';">
                            
                            <!-- Gradient Orb -->
                            <div class="card-orb" style="position: absolute; top: -20%; right: -20%; width: 120px; height: 120px; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); opacity: 0.1; border-radius: 50%; filter: blur(30px);"></div>
                            
                            <div>
                                <!-- Icon -->
                                <div style="position: relative; z-index: 2; width: 80px; height: 80px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;">
                                    <span style="font-size: 2.25rem; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));"><?php echo $icons[$slug] ?? '📦'; ?></span>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="card-title" style="position: relative; z-index: 2; font-size: 1.075rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.3; height: 2.6rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                
                                <!-- Description -->
                                <p class="card-description" style="position: relative; z-index: 2; font-size: 0.8rem; margin-bottom: 1rem; line-height: 1.5; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?php echo htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            
                            <!-- Footer -->
                            <div style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between;">
                                <div class="card-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 100px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                    <div style="width: 6px; height: 6px; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); border-radius: 50%;"></div>
                                    <span style="font-weight: 700; font-size: 0.875rem; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo $category['item_count'] > 0 ? $category['item_count'] . ' items' : 'Soon'; ?></span>
                                </div>
                                <i class="bi bi-arrow-right" style="font-size: 1.25rem; color: <?php echo $colors['from']; ?>; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 4rem;">
                <a href="products.php" class="explore-all-btn" style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; padding: 1.1rem 2.5rem; border-radius: 100px; font-weight: 700; font-size: 1.125rem; text-decoration: none; box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 35px rgba(34, 197, 94, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(34, 197, 94, 0.3)';"><span>Explore All Categories</span><i class="bi bi-arrow-right" style="font-size: 1.25rem;"></i></a>
            </div>
        </div>
    </section>

    <?php if (!empty($recentItems)): ?>
    <!-- Recent Updates Section -->
    <section class="scroll-reveal section-padding" aria-label="Recent Price Updates" style="background: #f9fafb; position: relative;">
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
                <div class="col-md-6 col-lg-4">
                    <a href="item.php?id=<?php echo $item['item_id']; ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100" style="transition: all 0.2s ease; border-radius: 16px;"
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px -5px rgba(0, 0, 0, 0.1)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <!-- Image/Icon -->
                                <div style="width: 64px; height: 64px; flex-shrink: 0; border-radius: 12px; overflow: hidden; background: #f3f4f6; position: relative;">
                                    <?php 
                                        $recentHasImage = itemHasImage($item['image_path'] ?? null);
                                        $recentImageUrl = $recentHasImage ? getItemImageUrl($item['image_path']) : null;
                                    ?>
                                    <?php if ($recentHasImage && $recentImageUrl): ?>
                                        <img src="<?php echo $recentImageUrl; ?>" 
                                             alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #9ca3af; font-weight: 700; background: #f9fafb;">
                                            <?php echo mb_substr($item['item_name'], 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div style="flex: 1; min-width: 0;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h5 class="mb-0 text-truncate" style="font-size: 1rem; font-weight: 600; color: #111827; max-width: 100%;">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                        </h5>
                                        <?php if ($item['market_location']): ?>
                                            <small style="color: #9ca3af; font-size: 0.75rem; white-space: nowrap; margin-left: 0.5rem;">
                                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($item['market_location']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-weight: 500; font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                            <?php echo htmlspecialchars($item['category_name']); ?>
                                        </span>
                                        <small style="color: #6b7280; font-size: 0.75rem;">
                                            <i class="bi bi-clock"></i> <?php echo timeAgo($item['updated_at']); ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="text-end ps-2" style="min-width: 80px;">
                                    <div style="font-weight: 700; color: #f97316; font-size: 1.125rem;">
                                        <?php echo formatPrice($item['current_price']); ?>
                                    </div>
                                    <small style="color: #9ca3af; font-size: 0.75rem;">/ <?php echo $item['unit']; ?></small>
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

    <!-- What is SastoMahango Section -->
    <section class="section-colored scroll-reveal section-padding" aria-label="About SastoMahango" style="position: relative;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1.5rem;">
                        <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">ABOUT US</span>
                    </div>
                    <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1.5rem; line-height: 1.2;">What is <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SastoMahango</span>?</h2>
                    <p style="font-size: 1.125rem; color: #6b7280; line-height: 1.8; margin-bottom: 2rem;">SastoMahango (सस्तोमहँगो) is Nepal's first comprehensive price tracking platform that brings transparency to the marketplace. We collect, verify, and publish real-time prices of everyday products from markets across Nepal.</p>
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
    <section class="scroll-reveal section-padding" aria-label="Why Choose Us" style="background: #f9fafb; position: relative;">
        
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;"><span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">WHY US</span></div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">Why Choose SastoMahango?</h2>
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
    <section class="scroll-reveal section-padding" aria-label="How It Works" style="background: white; position: relative;">

        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(59, 130, 246, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;"><span class="text-gradient-blue" style="font-weight: 700; font-size: 0.875rem;">PROCESS</span></div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">How It Works</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">Simple, transparent, and effective - here's how we bring you accurate prices</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div style="position: relative;">
                        <div style="position: absolute; left: 32px; top: 80px; bottom: 80px; width: 2px; background: linear-gradient(180deg, #f97316, #3b82f6, #10b981); opacity: 0.3;"></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div class="floating-card" style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">1</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Data Collection</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Our network of contributors visits local markets and collects price information for various products daily</p></div></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div class="floating-card" style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">2</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Verification</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Each price entry is verified through multiple sources to ensure accuracy and reliability</p></div></div>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; position: relative;"><div class="floating-card" style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">3</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Publishing</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Verified prices are published on our platform, making them accessible to everyone for free</p></div></div>
                        <div style="display: flex; gap: 1.5rem; position: relative;"><div class="floating-card" style="flex-shrink: 0; width: 64px; height: 64px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4); position: relative; z-index: 1;"><span style="font-size: 1.5rem; font-weight: 800; color: white;">4</span></div><div style="flex: 1; padding-top: 0.5rem;"><h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">You Benefit</h3><p style="color: #6b7280; line-height: 1.7; margin: 0;">Make informed decisions, compare prices, and shop smarter with real-time market data</p></div></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="floating-card" style="background: white; border-radius: 24px; padding: 3rem; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);">
                        <svg viewBox="0 0 400 400" style="width: 100%; height: auto;"><rect x="50" y="200" width="120" height="150" fill="#f97316" rx="8"/><rect x="70" y="220" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="120" y="220" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="70" y="280" width="30" height="40" fill="#fff" opacity="0.8"/><rect x="120" y="280" width="30" height="40" fill="#fff" opacity="0.8"/><polygon points="50,200 110,160 170,200" fill="#ea580c"/><path d="M 180 275 L 220 275" stroke="#3b82f6" stroke-width="4" fill="none" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><polygon points="220,275 210,270 210,280" fill="#3b82f6"/><ellipse cx="280" cy="260" rx="50" ry="20" fill="#3b82f6"/><rect x="230" y="260" width="100" height="60" fill="#3b82f6"/><ellipse cx="280" cy="320" rx="50" ry="20" fill="#2563eb"/><rect x="245" y="275" width="70" height="5" fill="#fff" opacity="0.3"/><rect x="245" y="285" width="70" height="5" fill="#fff" opacity="0.3"/><rect x="245" y="295" width="70" height="5" fill="#fff" opacity="0.3"/><circle cx="110" cy="100" r="25" fill="#10b981"/><path d="M 110 125 L 110 160 M 95 140 L 125 140 M 110 160 L 95 190 M 110 160 L 125 190" stroke="#10b981" stroke-width="8" stroke-linecap="round"/><circle cx="280" cy="100" r="25" fill="#8b5cf6"/><path d="M 280 125 L 280 160 M 265 140 L 295 140 M 280 160 L 265 190 M 280 160 L 295 190" stroke="#8b5cf6" stroke-width="8" stroke-linecap="round"/><path d="M 110 190 L 110 200" stroke="#10b981" stroke-width="3" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><path d="M 280 190 Q 280 230 280 260" stroke="#8b5cf6" stroke-width="3" stroke-dasharray="5,5"><animate attributeName="stroke-dashoffset" from="0" to="-10" dur="1s" repeatCount="indefinite"/></path><circle cx="200" cy="150" r="30" fill="#10b981" opacity="0.2"><animate attributeName="r" values="30;35;30" dur="2s" repeatCount="indefinite"/></circle><path d="M 185 150 L 195 160 L 215 140" stroke="#10b981" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Professional Footer -->
    <footer class="professional-footer">
        <div class="footer-main">
            <div class="row g-4 mb-4">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-4">
                        <h3 class="brand-name mb-3">
                            <i class="bi bi-graph-up-arrow me-2"></i>
                            <span style="background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SastoMahango</span>
                        </h3>
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
                        <li><a href="products.php"><i class="bi bi-chevron-right"></i> Browse Products</a></li>
                        <li><a href="about.php"><i class="bi bi-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i> How It Works</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-heading">Categories</h5>
                    <ul class="footer-links">
                        <li><a href="products.php?category=vegetables"><i class="bi bi-chevron-right"></i> Vegetables</a></li>
                        <li><a href="products.php?category=fruits"><i class="bi bi-chevron-right"></i> Fruits</a></li>
                        <li><a href="products.php?category=kitchen-appliances"><i class="bi bi-chevron-right"></i> Kitchen</a></li>
                        <li><a href="products.php?category=tech-gadgets"><i class="bi bi-chevron-right"></i> Electronics</a></li>
                        <li><a href="products.php"><i class="bi bi-chevron-right"></i> View All</a></li>
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
                            <span>info@sastomahango.com</span>
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
                            &copy; <?php echo date('Y'); ?> SastoMahango. All rights reserved. 
                            <span class="ms-2">Built by Team Urja on ISTN Hackathon</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="privacy-policy.php" class="footer-legal-link">Privacy Policy</a>
                        <span class="mx-2">•</span>
                        <a href="terms-of-service.php" class="footer-legal-link">Terms of Service</a>
                        <span class="mx-2">•</span>
                        <a href="cookie-policy.php" class="footer-legal-link">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/core/utils.js"></script>
    
    <!-- Component Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/components/navbar.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/components/footer.js"></script>
    
    <!-- Animation Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/animations/scroll-animations.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/animations/counter-animation.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/animations/hero-interactions.js"></script>
    
    <!-- Category Dropdown Functionality -->
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .category-dropdown-menu {
            scrollbar-width: thin;
            scrollbar-color: #f97316 #f3f4f6;
        }
        
        .category-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .category-dropdown-menu::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 3px;
        }
        
        .category-dropdown-menu::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 3px;
        }
        
        .category-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }
    </style>
    
    <script>
        // Category dropdown toggle functionality
        let activeDropdown = null;
        
        function toggleCategoryDropdown(trigger) {
            const wrapper = trigger.closest('.category-dropdown-wrapper');
            const dropdown = wrapper.querySelector('.category-dropdown-menu');
            const chevron = trigger.querySelector('.bi-chevron-down');
            const card = trigger.querySelector('.category-card-trigger');
            
            // Close other dropdowns
            if (activeDropdown && activeDropdown !== dropdown) {
                activeDropdown.style.display = 'none';
                const activeChevron = activeDropdown.parentElement.querySelector('.bi-chevron-down');
                if (activeChevron) {
                    activeChevron.style.transform = 'rotate(0deg)';
                }
            }
            
            // Toggle current dropdown
            if (dropdown.style.display === 'none' || !dropdown.style.display) {
                dropdown.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
                card.style.transform = 'translateY(-4px)';
                activeDropdown = dropdown;
            } else {
                dropdown.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
                card.style.transform = 'translateY(0)';
                activeDropdown = null;
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.category-dropdown-wrapper')) {
                if (activeDropdown) {
                    activeDropdown.style.display = 'none';
                    const activeChevron = activeDropdown.parentElement.querySelector('.bi-chevron-down');
                    if (activeChevron) {
                        activeChevron.style.transform = 'rotate(0deg)';
                    }
                    activeDropdown = null;
                }
            }
        });
        
        // Prevent card click from bubbling to document
        document.querySelectorAll('.category-card-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>
