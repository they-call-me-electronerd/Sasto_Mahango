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
$additionalCSS = ['pages/landing.css', 'pages/landing-pro.css', 'pages/home-categories.css', 'animations/enhanced-animations.css', 'animations/hero-enhancements.css', 'components/ad-carousel.css', 'components/ad-banner.css'];
$additionalJS = ['animations/counter-animation.js', 'animations/scroll-animations.js', 'components/ticker.js', 'components/ad-carousel.js', 'components/ad-banner.js', 'components/category-carousel.js', 'components/landing-ad-carousel.js'];

// Get categories with item counts
$categoryObj = new Category();
$categories = $categoryObj->getCategoriesWithItemCounts();

// Get recent items
$itemObj = new Item();
$recentItems = $itemObj->getActiveItems(9);

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
    <section class="scroll-reveal section-padding categories-section" aria-label="Product Categories" style="position: relative; padding: 6rem 0; background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 50%, #f0fdf4 100%); overflow: hidden;">
        <!-- Decorative Background Pattern -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.4; z-index: 1; pointer-events: none;">
            <div style="position: absolute; top: 10%; left: 5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(34, 197, 94, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px);"></div>
            <div style="position: absolute; top: 50%; right: 10%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px);"></div>
            <div style="position: absolute; bottom: 10%; left: 50%; transform: translateX(-50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(20, 184, 166, 0.1) 0%, transparent 70%); border-radius: 50%; filter: blur(80px);"></div>
        </div>
        
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div class="categories-badge" style="display: inline-block; background: rgba(255, 255, 255, 0.9); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(34, 197, 94, 0.1);">
                    <span style="background: linear-gradient(135deg, #22c55e, #16a34a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem; letter-spacing: 0.05em;">EXPLORE CATEGORIES</span>
                </div>
                <h2 class="categories-title" style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; font-family: 'Manrope', sans-serif; color: #111827;">Browse by Category</h2>
                <p class="categories-subtitle" style="font-size: 1.125rem; max-width: 700px; margin: 0 auto; line-height: 1.7; color: #374151;">Find verified prices for thousands of products across different categories</p>
            </div>

            <div class="row g-4">
                <?php 
                $categoryColors = [
                    'vegetables' => ['from' => '#22c55e', 'to' => '#16a34a', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'fruits' => ['from' => '#34d399', 'to' => '#10b981', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'groceries' => ['from' => '#4ade80', 'to' => '#22c55e', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'dairy-products' => ['from' => '#14b8a6', 'to' => '#0d9488', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'meat-fish' => ['from' => '#10b981', 'to' => '#059669', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'spices' => ['from' => '#84cc16', 'to' => '#65a30d', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'kitchen-appliances' => ['from' => '#22c55e', 'to' => '#16a34a', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'household-items' => ['from' => '#34d399', 'to' => '#10b981', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'electrical-appliances' => ['from' => '#14b8a6', 'to' => '#0d9488', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'clothing' => ['from' => '#10b981', 'to' => '#059669', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'study-material' => ['from' => '#4ade80', 'to' => '#22c55e', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)'],
                    'tools-hardware' => ['from' => '#84cc16', 'to' => '#65a30d', 'bg' => 'rgba(220, 252, 231, 0.5)', 'light' => 'rgba(255, 255, 255, 0.95)']
                ];
                $icons = [
                    'vegetables' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 21h10"/><path d="M12 21a9 9 0 0 0 9-9H3a9 9 0 0 0 9 9Z"/><path d="M11.38 12a2.4 2.4 0 0 1-.4-4.77 2.4 2.4 0 0 1 3.2-2.77 2.4 2.4 0 0 1 3.47-.63 2.4 2.4 0 0 1 3.37 3.37 2.4 2.4 0 0 1-1.1 3.7 2.51 2.51 0 0 1 .03 1.1"/></svg>',
                    'fruits' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3c0 .68.23 1.31.62 1.81C8.71 6.94 8 8.41 8 10a6 6 0 0 0 12 0c0-1.59-.71-3.06-1.62-4.19.39-.5.62-1.13.62-1.81a3 3 0 0 0-3-3z"/><path d="M12 2v5"/><path d="M8 14a6 6 0 1 0 8 0"/></svg>',
                    'groceries' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
                    'dairy-products' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2h8l4 10H4L8 2z"/><path d="M16 12v10a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V12"/><path d="M8 7h8"/></svg>',
                    'meat-fish' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 12c.94-3.46 4.94-6 8.5-6 3.56 0 6.06 2.54 7 6-.94 3.47-3.44 6-7 6s-7.56-2.53-8.5-6Z"/><path d="M18 12v.5"/><path d="M16 17.93a9.77 9.77 0 0 1-2 .07 9.77 9.77 0 0 1-2-.07"/><path d="M7 10.67C7 8 5.58 5.97 2.73 5.5c-1 1.5-1 5 .23 6.5-1.24 1.5-1.24 5-.23 6.5C5.58 18.03 7 16 7 13.33m8-2.66C15 8 16.42 5.97 19.27 5.5c1 1.5 1 5-.23 6.5 1.24 1.5 1.24 5 .23 6.5C16.42 18.03 15 16 15 13.33"/></svg>',
                    'spices' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/></svg>',
                    'kitchen-appliances' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
                    'household-items' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 16h6"/><path d="M16 20h6"/><path d="M6 20h.01"/><path d="M10 20h.01"/><rect width="16" height="16" x="2" y="4" rx="2"/></svg>',
                    'electrical-appliances' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>',
                    'clothing' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 11c.5-1.2 1.5-2.1 2-3 .6-1.2 1-2.5 1-4 0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2 0 1.5.4 2.8 1 4 .5.9 1.5 1.8 2 3H6Z"/><path d="M6 11v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V11"/></svg>',
                    'study-material' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>',
                    'tools-hardware' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>'
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
                            border-radius: 20px;
                            padding: 2rem 1.5rem;
                            text-align: left;
                            position: relative;
                            overflow: hidden;
                            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            cursor: pointer;
                            height: 320px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            border: 1px solid rgba(34, 197, 94, 0.1);
                        " 
                        onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 24px rgba(34, 197, 94, 0.15), 0 4px 8px rgba(0, 0, 0, 0.05)'; this.style.borderColor='rgba(34, 197, 94, 0.2)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(34, 197, 94, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05)'; this.style.borderColor='rgba(34, 197, 94, 0.1)';">
                            
                            <!-- Subtle Gradient Accent -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); opacity: 0.6;"></div>
                            
                            <div>
                                <!-- Icon -->
                                <div style="position: relative; z-index: 2; width: 72px; height: 72px; margin: 0 0 1.5rem 0; background: linear-gradient(135deg, <?php echo $colors['from']; ?>, <?php echo $colors['to']; ?>); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
                                    <?php echo $icons[$slug] ?? '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 16h6"/><path d="M16 20h6"/><path d="M6 20h.01"/><path d="M10 20h.01"/><rect width="16" height="16" x="2" y="4" rx="2"/></svg>'; ?>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="card-title" style="position: relative; z-index: 2; font-size: 1.075rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.3; height: 2.6rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; color: #111827;"><?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                
                                <!-- Description -->
                                <p class="card-description" style="position: relative; z-index: 2; font-size: 0.8rem; margin-bottom: 1rem; line-height: 1.5; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; color: #6b7280;"><?php echo htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            
                            <!-- Footer -->
                            <div style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between;">
                                <div class="card-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(34, 197, 94, 0.08); padding: 0.5rem 1rem; border-radius: 100px; border: 1px solid rgba(34, 197, 94, 0.15);">
                                    <div style="width: 5px; height: 5px; background: <?php echo $colors['from']; ?>; border-radius: 50%;"></div>
                                    <span style="font-weight: 600; font-size: 0.875rem; color: <?php echo $colors['from']; ?>;"><?php echo $category['item_count'] > 0 ? $category['item_count'] . ' items' : 'Soon'; ?></span>
                                </div>
                                <div style="width: 32px; height: 32px; background: rgba(34, 197, 94, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-arrow-right" style="font-size: 1rem; color: <?php echo $colors['from']; ?>;"></i>
                                </div>
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
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;">
                    <span style="background: linear-gradient(135deg, #22c55e, #16a34a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">FRESH ARRIVALS</span>
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
                                        <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; font-weight: 500; font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                            <?php echo htmlspecialchars($item['category_name']); ?>
                                        </span>
                                        <small style="color: #6b7280; font-size: 0.75rem;">
                                            <i class="bi bi-clock"></i> <?php echo timeAgo($item['updated_at']); ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="text-end ps-2" style="min-width: 80px;">
                                    <div style="font-weight: 700; color: #22c55e; font-size: 1.125rem;">
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

    <!-- Our Network Section -->
    <section class="scroll-reveal section-padding" aria-label="Our Network" style="background: white; position: relative;">

        <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 3;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1)); padding: 0.5rem 1.5rem; border-radius: 2rem; margin-bottom: 1rem;"><span style="background: linear-gradient(135deg, #22c55e, #16a34a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 0.875rem;">OUR NETWORK</span></div>
                <h2 style="font-size: 3rem; font-weight: 800; color: #111827; margin-bottom: 1rem;">Built for Everyone</h2>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">A comprehensive ecosystem serving consumers, agents, and businesses</p>
            </div>
            
            <div class="row g-4">
                <!-- For Consumers -->
                <div class="col-lg-4">
                    <div style="background: linear-gradient(135deg, #f0fdf4, #ffffff); border-radius: 24px; padding: 2.5rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(34, 197, 94, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);">
                            <i class="bi bi-person-fill" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">For Consumers</h3>
                        <p style="color: #6b7280; line-height: 1.8; margin-bottom: 2rem;">Everyday users looking to make informed purchasing decisions</p>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #22c55e; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Real-Time Price Access</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Get instant access to current market prices across 500+ products</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #22c55e; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Service Rate Information</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Know standard rates for 100+ services before hiring</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #22c55e; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Price Comparison</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Compare prices across 50+ markets without leaving home</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #22c55e; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">100% Free Forever</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">No subscriptions, no paywalls, always free</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- For Agents -->
                <div class="col-lg-4">
                    <div style="background: linear-gradient(135deg, #f0fdfa, #ffffff); border-radius: 24px; padding: 2.5rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(16, 185, 129, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                            <i class="bi bi-people-fill" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">For Agents</h3>
                        <p style="color: #6b7280; line-height: 1.8; margin-bottom: 2rem;">Registered contributors who gather and verify market data</p>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Earn Reputation Points</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Build your profile with every verified contribution</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Easy Data Submission</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Simple interface to update prices and service rates</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Community Impact</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Help fellow Nepalis make informed decisions</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Verification Badge</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Get recognized as a trusted community contributor</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="<?php echo SITE_URL; ?>/contributor/register.php" style="display: block; margin-top: 2rem; text-align: center; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.875rem 1.5rem; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(16, 185, 129, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            Become an Agent <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- For Businesses -->
                <div class="col-lg-4">
                    <div style="background: linear-gradient(135deg, #f0fdfa, #ffffff); border-radius: 24px; padding: 2.5rem; height: 100%; border: 2px solid transparent; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#14b8a6'; this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(20, 184, 166, 0.2)';" onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; box-shadow: 0 8px 20px rgba(20, 184, 166, 0.3);">
                            <i class="bi bi-building" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">For Businesses</h3>
                        <p style="color: #6b7280; line-height: 1.8; margin-bottom: 2rem;">Companies looking to reach engaged consumers</p>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #14b8a6; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Sponsored Listings</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Featured placement above search results with custom contracts</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #14b8a6; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Banner Advertisements</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Strategic placements on high-traffic pages</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #14b8a6; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Targeted Audience</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Reach consumers actively searching for products</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-check-circle-fill" style="color: #14b8a6; font-size: 1.25rem; flex-shrink: 0; margin-top: 0.15rem;"></i>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">SEO Benefits</h4>
                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Benefit from our high-ranking search visibility</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" style="display: block; margin-top: 2rem; text-align: center; background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; padding: 0.875rem 1.5rem; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(20, 184, 166, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            Partner With Us <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="scroll-reveal services-section" aria-label="Services" style="padding: 3.5rem 0; position: relative;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <h2 class="services-title" style="font-size: 1.875rem; font-weight: 700; margin-bottom: 0;">Services</h2>
            </div>
            <div class="row g-3" style="max-width: 1000px; margin: 0 auto;">
                <div class="col-sm-6 col-lg-3">
                    <div class="service-card" style="border-radius: 12px; padding: 1.5rem; text-align: center; height: 100%; transition: all 0.3s ease;">
                        <div class="service-icon-wrapper" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-lightning-charge-fill" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="service-card-title" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Real-Time Updates</h3>
                        <p class="service-card-text" style="line-height: 1.5; margin: 0; font-size: 0.875rem;">Latest prices updated daily from verified sources across Nepal's markets</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="service-card" style="border-radius: 12px; padding: 1.5rem; text-align: center; height: 100%; transition: all 0.3s ease;">
                        <div class="service-icon-wrapper" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-shield-check" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="service-card-title" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Verified Data</h3>
                        <p class="service-card-text" style="line-height: 1.5; margin: 0; font-size: 0.875rem;">All prices verified by our network of trusted contributors and market researchers</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="service-card" style="border-radius: 12px; padding: 1.5rem; text-align: center; height: 100%; transition: all 0.3s ease;">
                        <div class="service-icon-wrapper" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-graph-up-arrow" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="service-card-title" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Price Trends</h3>
                        <p class="service-card-text" style="line-height: 1.5; margin: 0; font-size: 0.875rem;">Track price changes over time and make smart purchasing decisions based on trends</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="service-card" style="border-radius: 12px; padding: 1.5rem; text-align: center; height: 100%; transition: all 0.3s ease;">
                        <div class="service-icon-wrapper" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="service-card-title" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Community Driven</h3>
                        <p class="service-card-text" style="line-height: 1.5; margin: 0; font-size: 0.875rem;">Built by the community, for the community. Anyone can contribute and help</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Light Mode Styles */
        .services-section {
            background: #ffffff;
        }
        
        .services-title {
            color: #111827;
        }
        
        .service-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }
        
        .service-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }
        
        .service-icon-wrapper {
            background: #ecfdf5;
        }
        
        .service-icon-wrapper i {
            color: #10b981;
        }
        
        .service-card-title {
            color: #111827;
        }
        
        .service-card-text {
            color: #6b7280;
        }
        
        /* Dark Mode Styles */
        [data-theme="dark"] .services-section {
            background: #1f2937;
        }
        
        [data-theme="dark"] .services-title {
            color: #f9fafb;
        }
        
        [data-theme="dark"] .service-card {
            background: #374151;
            border: 1px solid #4b5563;
        }
        
        [data-theme="dark"] .service-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        
        [data-theme="dark"] .service-icon-wrapper {
            background: rgba(16, 185, 129, 0.15);
        }
        
        [data-theme="dark"] .service-icon-wrapper i {
            color: #34d399;
        }
        
        [data-theme="dark"] .service-card-title {
            color: #f9fafb;
        }
        
        [data-theme="dark"] .service-card-text {
            color: #d1d5db;
        }
    </style>

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
