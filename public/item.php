<?php
/**
 * Item Detail Page - Modern Redesign
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../includes/functions.php';

$itemObj = new Item();

// Get item by ID or slug
$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug) {
    $item = $itemObj->getItemBySlug($slug);
} else {
    $item = $itemObj->getItemById($itemId);
}

if (!$item) {
    header('Location: products.php');
    exit;
}

$itemHasImage = itemHasImage($item['image_path'] ?? null);
$itemImageUrl = $itemHasImage ? getItemImageUrl($item['image_path']) : null;

$pageTitle = $item['item_name'] . ' - Price Details';
$metaDescription = "View details and price history for " . htmlspecialchars($item['item_name']) . " in Nepal. Current price: " . formatPrice($item['current_price']);
$additionalCSS = ['pages/item.css'];
$additionalJS = ['https://cdn.jsdelivr.net/npm/chart.js', 'components/chart.js'];

// Get price history for statistics (last 30 days)
$priceHistory = $itemObj->getPriceHistory($item['item_id'], 30);

// Get extended price history for chart (last 90 days for better visualization)
$priceHistoryExtended = $itemObj->getPriceHistory($item['item_id'], 90);

// Get tags
$tags = $itemObj->getItemTags($item['item_id']);

// Calculate price statistics
$priceStats = [
    'min' => $item['current_price'],
    'max' => $item['current_price'],
    'avg' => $item['current_price'],
    'change' => 0
];

if (!empty($priceHistory)) {
    // Get all prices from history
    $prices = array_column($priceHistory, 'new_price');
    
    // Always include current price in statistics
    $prices[] = (float)$item['current_price'];
    
    $priceStats['min'] = min($prices);
    $priceStats['max'] = max($prices);
    $priceStats['avg'] = array_sum($prices) / count($prices);
    
    // Calculate price change from oldest to newest
    if (count($priceHistory) > 0) {
        $oldest = end($priceHistory)['new_price'];
        $newest = (float)$item['current_price'];
        if ($oldest > 0) {
            $priceStats['change'] = (($newest - $oldest) / $oldest) * 100;
        }
    }
}

include __DIR__ . '/../includes/header_professional.php';
?>

<main class="item-page">
    <!-- Breadcrumb -->
    <div class="item-breadcrumb">
        <div class="breadcrumb-container">
            <a href="index.php" class="breadcrumb-link">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </a>
            <i class="bi bi-chevron-right breadcrumb-separator"></i>
            <a href="products.php" class="breadcrumb-link">Products</a>
            <i class="bi bi-chevron-right breadcrumb-separator"></i>
            <a href="products.php?category=<?php echo urlencode($item['category_slug'] ?? ''); ?>" class="breadcrumb-link">
                <?php echo htmlspecialchars($item['category_name']); ?>
            </a>
            <i class="bi bi-chevron-right breadcrumb-separator"></i>
            <span class="breadcrumb-current"><?php echo htmlspecialchars($item['item_name']); ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="item-container">
        <!-- Product Hero Section -->
        <section class="product-hero">
            <div class="product-hero-grid">
                <!-- Image Gallery -->
                <div class="product-gallery">
                    <div class="gallery-main">
                        <?php 
                                    if (str_contains(htmlspecialchars($item['image_path']),"http")) {
                                        $imageSrc = htmlspecialchars($item['image_path']);
                                    }
                                    else{
                                    $imageSrc = (SITE_URL . '/contributor/assets/uploads/items/' . htmlspecialchars($item['image_path']));}
                                    ?>
                        <?php if ($imageSrc): ?>
                            <img src="<?php echo $imageSrc; ?>" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                 class="main-image">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="bi bi-image"></i>
                                <span>No Image Available</span>
                            </div>
                        <?php endif; ?>
                        
                        <span class="product-status <?php echo ($item['status'] ?? 'active') === 'active' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ($item['status'] ?? 'active') === 'active' ? 'Available' : 'Out of Stock'; ?>
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <!-- Category & Name -->
                    <div class="product-header">
                        <span class="product-category">
                            <i class="bi bi-tag-fill"></i>
                            <?php echo htmlspecialchars($item['category_name']); ?>
                        </span>
                        <h1 class="product-title"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                        <?php if (!empty($item['item_name_nepali'])): ?>
                            <p class="product-title-nepali"><?php echo htmlspecialchars($item['item_name_nepali']); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Price Card -->
                    <div class="price-card">
                        <div class="price-main">
                            <span class="price-label">Current Market Price</span>
                            <div class="price-value">
                                <span class="currency">NPR</span>
                                <span class="amount"><?php echo number_format($item['current_price'], 2); ?></span>
                            </div>
                            <span class="price-unit">per <?php echo htmlspecialchars($item['unit'] ?? 'unit'); ?></span>
                        </div>
                        
                        <?php if ($priceStats['change'] != 0): ?>
                        <div class="price-trend <?php echo $priceStats['change'] > 0 ? 'trend-up' : 'trend-down'; ?>">
                            <i class="bi <?php echo $priceStats['change'] > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'; ?>"></i>
                            <span><?php echo abs(round($priceStats['change'], 1)); ?>%</span>
                            <small>vs last month</small>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Stats -->
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="bi bi-arrow-down-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo formatPrice($priceStats['min']); ?></span>
                                <span class="stat-label">30-Day Low</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="bi bi-arrow-up-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo formatPrice($priceStats['max']); ?></span>
                                <span class="stat-label">30-Day High</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo formatPrice($priceStats['avg']); ?></span>
                                <span class="stat-label">Average Price</span>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Meta -->
                    <div class="product-meta-grid">
                        <?php if (!empty($item['market_location'])): ?>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <span class="meta-label">Market Location</span>
                                <span class="meta-value"><?php echo htmlspecialchars($item['market_location']); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="bi bi-clock-fill"></i>
                            <div>
                                <span class="meta-label">Last Updated</span>
                                <span class="meta-value"><?php echo date('M j, Y', strtotime($item['updated_at'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-person-fill"></i>
                            <div>
                                <span class="meta-label">Contributed By</span>
                                <span class="meta-value"><?php echo htmlspecialchars($item['created_by_name'] ?? 'System'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="product-tags">
                        <?php foreach ($tags as $tag): ?>
                            <span class="tag"><?php echo htmlspecialchars($tag['tag_name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <?php if (Auth::isLoggedIn() && Auth::hasRole(ROLE_CONTRIBUTOR)): ?>
                            <a href="<?php echo SITE_URL; ?>/contributor/update_price.php?item_id=<?php echo $item['item_id']; ?>" class="btn-primary">
                                <i class="bi bi-pencil-square"></i>
                                Update Price
                            </a>
                        <?php endif; ?>
                        <a href="products.php" class="btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Description Section -->
        <?php if (!empty($item['description'])): ?>
        <section class="product-section description-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    About This Product
                </h2>
            </div>
            <div class="section-content">
                <p class="description-text"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
            </div>
        </section>
        <?php endif; ?>

        <!-- Price History Section -->
        <?php if (!empty($priceHistory)): ?>
        <section class="product-section price-history-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="bi bi-graph-up-arrow"></i>
                    Price History
                </h2>
                <p class="section-subtitle">Track price changes over the last 30 days</p>
            </div>
            
            <!-- Price Chart -->
            <div class="chart-container">
                <canvas id="priceChart"></canvas>
            </div>

            <!-- Price History Table -->
            <div class="history-table-container">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Previous Price</th>
                            <th>New Price</th>
                            <th>Change</th>
                            <th>Updated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($priceHistory as $history): 
                            $changePercent = $history['price_change_percent'] ?? 0;
                            $changeClass = $changePercent > 0 ? 'change-up' : ($changePercent < 0 ? 'change-down' : 'change-neutral');
                        ?>
                        <tr>
                            <td>
                                <span class="date-cell">
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo date('M j, Y', strtotime($history['updated_at'])); ?>
                                </span>
                            </td>
                            <td><?php echo formatPrice($history['old_price']); ?></td>
                            <td><strong><?php echo formatPrice($history['new_price']); ?></strong></td>
                            <td>
                                <span class="price-change <?php echo $changeClass; ?>">
                                    <?php if ($changePercent > 0): ?>
                                        <i class="bi bi-caret-up-fill"></i>
                                    <?php elseif ($changePercent < 0): ?>
                                        <i class="bi bi-caret-down-fill"></i>
                                    <?php else: ?>
                                        <i class="bi bi-dash"></i>
                                    <?php endif; ?>
                                    <?php echo abs(round($changePercent, 1)); ?>%
                                </span>
                            </td>
                            <td>
                                <span class="contributor-cell">
                                    <i class="bi bi-person-circle"></i>
                                    <?php echo htmlspecialchars($history['updated_by_name'] ?? 'System'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>

<script>
// Price chart data - prepare data with current price
<?php 
// Use extended history for chart (90 days) for better visualization
$chartData = array_reverse($priceHistoryExtended);

// If we have history, include the old_price points as well for complete visualization
if (!empty($chartData)) {
    $enhancedChartData = [];
    foreach ($chartData as $entry) {
        // Add the old price point first (if it's different from previous)
        if (!empty($entry['old_price']) && $entry['old_price'] != $entry['new_price']) {
            // Create a point slightly before this update to show the old price
            $oldPriceTime = date('Y-m-d H:i:s', strtotime($entry['updated_at']) - 60);
            $enhancedChartData[] = [
                'new_price' => $entry['old_price'],
                'updated_at' => $oldPriceTime,
                'updated_by_name' => 'Previous'
            ];
        }
        // Add the actual update point
        $enhancedChartData[] = $entry;
    }
    $chartData = $enhancedChartData;
}

// Add current price as most recent point
if (!empty($item['current_price'])) {
    $currentPricePoint = [
        'new_price' => $item['current_price'],
        'updated_at' => $item['updated_at'] ?? date('Y-m-d H:i:s'),
        'updated_by_name' => 'Current Price'
    ];
    $chartData[] = $currentPricePoint;
}

// If no history at all, create a single point with current price
if (empty($chartData)) {
    $chartData = [[
        'new_price' => $item['current_price'],
        'updated_at' => $item['created_at'] ?? date('Y-m-d H:i:s'),
        'updated_by_name' => 'Initial Price'
    ]];
}
?>
const priceData = <?php echo json_encode($chartData); ?>;
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
