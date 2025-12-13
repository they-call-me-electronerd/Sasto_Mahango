<?php
/**
 * Filter Products AJAX Endpoint
 * Returns filtered products based on search, category, price range, and sort criteria
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Item.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../../includes/functions.php';

try {
    $itemObj = new Item();
    $categoryObj = new Category();
    
    // Get filter parameters
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    $categoryInput = isset($_GET['category']) ? $_GET['category'] : null;
    $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
    $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
    $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $itemsPerPage = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 30;
    $offset = ($page - 1) * $itemsPerPage;
    
    // Handle category filter (ID or Slug)
    $categoryFilter = null;
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
    
    // Build filters array
    $filters = [
        'search' => $searchQuery,
        'category_id' => $categoryFilter,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
        'sort_by' => $sortBy,
        'limit' => $itemsPerPage,
        'offset' => $offset
    ];
    
    // Get items and count
    $items = $itemObj->searchProductsAdvanced($filters);
    $totalItems = $itemObj->countProductsAdvanced([
        'search' => $searchQuery,
        'category_id' => $categoryFilter,
        'min_price' => $minPrice,
        'max_price' => $maxPrice
    ]);
    
    $totalPages = ceil($totalItems / $itemsPerPage);
    $isAdmin = Auth::isLoggedIn() && Auth::hasRole(ROLE_ADMIN);
    
    // Format items for JSON response
    $formattedItems = [];
    foreach ($items as $item) {
        $status = $item['status'] ?? 'active';
        $statusClass = $status === 'active' ? 'status-active' : 'status-inactive';
        $statusLabel = $status === 'active' ? 'Available' : 'Out of Stock';
        $categorySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $item['category_name'] ?? 'general'));
        $categoryClass = 'category-' . trim($categorySlug, '-');
        
        // Check if image exists
        $hasImage = itemHasImage($item['image_path'] ?? null);
        $imagePath = $hasImage ? getItemImageUrl($item['image_path']) : null;
        
        $formattedItems[] = [
            'item_id' => $item['item_id'],
            'item_name' => htmlspecialchars($item['item_name']),
            'item_name_nepali' => htmlspecialchars($item['item_name_nepali'] ?? ''),
            'slug' => $item['slug'],
            'category_name' => htmlspecialchars($item['category_name']),
            'category_class' => $categoryClass,
            'current_price' => number_format($item['current_price'], 2),
            'unit' => htmlspecialchars($item['unit'] ?? 'unit'),
            'market_location' => htmlspecialchars($item['market_location'] ?? ''),
            'status' => $status,
            'status_class' => $statusClass,
            'status_label' => $statusLabel,
            'image_path' => $imagePath,
            'has_image' => $hasImage
        ];
    }
    
    $response = [
        'success' => true,
        'items' => $formattedItems,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'items_per_page' => $itemsPerPage
        ],
        'is_admin' => $isAdmin,
        'site_url' => SITE_URL
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Filter products error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while filtering products',
        'items' => [],
        'pagination' => [
            'current_page' => 1,
            'total_pages' => 0,
            'total_items' => 0,
            'items_per_page' => 30
        ]
    ]);
}
?>
