<?php
/**
 * Products Page - Advanced Search & Filter
 * Search products by category, rate, quantity, and item name
 */

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

$pageTitle = 'Products - Browse All Items';
$metaDescription = 'Browse and search all products on SastoMahango. Filter by category, price range, and more. Track real-time market prices across Nepal.';
$metaKeywords = 'products nepal, market prices, vegetables, fruits, commodities, price tracking, sastomahango products';
$additionalCSS = ['pages/products.css'];

$itemObj = new Item();
$categoryObj = new Category();

// Get all categories for filter dropdown
$categories = $categoryObj->getActiveCategories();

// Get filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryInput = isset($_GET['category']) ? $_GET['category'] : null;
$categoryFilter = null;

// Handle category filter (ID or Slug)
if ($categoryInput) {
    if (is_numeric($categoryInput)) {
        $categoryFilter = intval($categoryInput);
    } else {
        $category = $categoryObj->getCategoryBySlug($categoryInput);
        if ($category) {
            $categoryFilter = $category['category_id'];
        }
    }
}

$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$itemsPerPage = 30;
$offset = ($currentPage - 1) * $itemsPerPage;

// Build the query based on filters
$items = $itemObj->searchProductsAdvanced([
    'search' => $searchQuery,
    'category_id' => $categoryFilter,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'sort_by' => $sortBy,
    'limit' => $itemsPerPage,
    'offset' => $offset
]);

$totalItems = $itemObj->countProductsAdvanced([
    'search' => $searchQuery,
    'category_id' => $categoryFilter,
    'min_price' => $minPrice,
    'max_price' => $maxPrice
]);

$totalPages = ceil($totalItems / $itemsPerPage);

include __DIR__ . '/../includes/header_professional.php';
?>

    <!-- Main Products Section -->
    <section class="products-main">
        <div class="products-layout">
            <!-- Filter Sidebar -->
            <aside class="filter-sidebar">
                <form id="filterForm">
                    <div class="filter-header">
                        <svg class="filter-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="filter-title">Filters</h2>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="filter-group">
                        <label class="filter-label">Search by Name</label>
                        <div class="search-input-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search products..." 
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                                class="filter-input"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select name="category" class="filter-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option 
                                    value="<?php echo $category['category_id']; ?>"
                                    <?php echo $categoryFilter == $category['category_id'] ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Price Range (NPR)</label>
                        <div class="price-range">
                            <input 
                                type="number" 
                                name="min_price" 
                                placeholder="Min" 
                                value="<?php echo $minPrice ? $minPrice : ''; ?>"
                                class="filter-input"
                                min="0"
                                step="0.01"
                            >
                            <span class="price-separator">-</span>
                            <input 
                                type="number" 
                                name="max_price" 
                                placeholder="Max" 
                                value="<?php echo $maxPrice ? $maxPrice : ''; ?>"
                                class="filter-input"
                                min="0"
                                step="0.01"
                            >
                        </div>
                    </div>

                    <!-- Sort By -->
                    <div class="filter-group">
                        <label class="filter-label">Sort By</label>
                        <select name="sort" class="filter-select">
                            <option value="name_asc" <?php echo $sortBy === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                            <option value="name_desc" <?php echo $sortBy === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                            <option value="price_asc" <?php echo $sortBy === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                            <option value="price_desc" <?php echo $sortBy === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                            <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sortBy === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="filter-actions">
                        <button type="submit" class="btn-apply">
                            <i class="bi bi-funnel-fill"></i>
                            <span>Apply Filters</span>
                        </button>
                        <button type="button" class="btn-reset" onclick="window.location.href='products.php'">
                            <i class="bi bi-x-circle"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </form>
            </aside>

            <!-- Products Content -->
            <main class="products-content">
                <!-- Results Header -->
                <div class="results-header">
                    <div class="results-count">
                        <span class="count"><?php echo $totalItems; ?></span> 
                        <?php echo $totalItems === 1 ? 'product' : 'products'; ?> found
                    </div>

                    <!-- Header Search Bar -->
                    <div class="header-search-container">
                        <form class="header-search-form" id="headerSearchForm">
                            <div class="header-search-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" name="search" class="header-search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>" autocomplete="off">
                                <?php if (!empty($searchQuery)): ?>
                                    <a href="products.php" class="search-clear" title="Clear search">
                                        <i class="bi bi-x"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" title="Grid View">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </button>
                        <button class="view-btn" data-view="list" title="List View">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (empty($items)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <h3 class="empty-title">No products found</h3>
                        <p class="empty-text">Try adjusting your filters or search criteria</p>
                        <a href="products.php" class="btn-apply">Clear All Filters</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid" id="productsGrid">
                        <?php 
                        $isAdmin = Auth::isLoggedIn() && Auth::hasRole(ROLE_ADMIN);
                        $delay = 0;
                        foreach ($items as $item): 
                            $status = $item['status'] ?? 'active';
                            $statusClass = $status === 'active' ? 'status-active' : 'status-inactive';
                            $statusLabel = $status === 'active' ? 'Available' : 'Out of Stock';
                            
                            // Get category slug for icon styling
                            $categorySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $item['category_name'] ?? 'general'));
                            $categoryClass = 'category-' . trim($categorySlug, '-');
                            $cardHasImage = itemHasImage($item['image_path'] ?? null);
                            $cardImageUrl = $cardHasImage ? getItemImageUrl($item['image_path']) : null;
                        ?>
                            <div class="product-card <?php echo $categoryClass; ?>" style="animation-delay: <?php echo $delay; ?>s;">
                                <div class="product-image">
                                    <span class="product-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                    <?php if ($cardHasImage && $cardImageUrl): ?>
                                        <img src="<?php echo $cardImageUrl; ?>" 
                                             alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="product-image-placeholder"></div>
                                    <?php endif; ?>
                                </div>

                                <div class="product-details">
                                    <span class="product-category">
                                        <?php echo htmlspecialchars($item['category_name']); ?>
                                    </span>
                                    
                                    <h3 class="product-name" style="color: #000000;">
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                    </h3>
                                    
                                    <?php if (!empty($item['item_name_nepali'])): ?>
                                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">
                                            <?php echo htmlspecialchars($item['item_name_nepali']); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="product-meta">
                                        <span class="meta-item">
                                            <i class="bi bi-tag"></i>
                                            Per <?php echo htmlspecialchars($item['unit'] ?? 'unit'); ?>
                                        </span>
                                        <?php if (!empty($item['market_location'])): ?>
                                            <span class="meta-item">
                                                <i class="bi bi-geo-alt"></i>
                                                <?php echo htmlspecialchars($item['market_location']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="product-price" style="color: #000000;">
                                        NPR <?php echo number_format($item['current_price'], 2); ?>
                                    </div>

                                    <div class="product-actions">
                                        <a href="item.php?slug=<?php echo urlencode($item['slug']); ?>" class="btn-view">
                                            View Details
                                            <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                        <?php if ($isAdmin): ?>
                                            <a href="<?php echo SITE_URL; ?>/admin/edit_item.php?id=<?php echo $item['item_id']; ?>" class="btn-edit-icon" title="Edit Item">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        <?php elseif (Auth::isLoggedIn() && Auth::hasRole(ROLE_CONTRIBUTOR)): ?>
                                            <a href="<?php echo SITE_URL; ?>/contributor/edit_item.php?id=<?php echo $item['item_id']; ?>" class="btn-edit-icon" title="Request Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        $delay += 0.05;
                        endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                    <?php if ($totalPages > 1): ?>
                            <ul class="pagination">
                                <?php
                                $queryParams = $_GET;
                                
                                // Previous Page
                                if ($currentPage > 1): 
                                    $queryParams['page'] = (int)$currentPage - 1;
                                ?>
                                    <li>
                                        <a href="?<?php echo http_build_query($queryParams); ?>" class="page-link">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <span class="page-link disabled">
                                            <i class="bi bi-chevron-left"></i>
                                        </span>
                                    </li>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, (int)$currentPage - 2);
                                $endPage = min($totalPages, (int)$currentPage + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                    $queryParams['page'] = $i;
                                ?>
                                    <li>
                                        <a href="?<?php echo http_build_query($queryParams); ?>" 
                                           class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next Page -->
                                <?php if ($currentPage < $totalPages): 
                                    $queryParams['page'] = (int)$currentPage + 1;
                                ?>
                                    <li>
                                        <a href="?<?php echo http_build_query($queryParams); ?>" class="page-link">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <span class="page-link disabled">
                                            <i class="bi bi-chevron-right"></i>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
<script src="<?php echo SITE_URL; ?>/assets/js/products.js"></script>
</body>
</html>
