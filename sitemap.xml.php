<?php
/**
 * Sitemap Generator
 * Generates XML sitemap for search engines
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Category.php';

// Set XML headers
header('Content-Type: application/xml; charset=utf-8');

$itemObj = new Item();
$categoryObj = new Category();

// Get all active items
$items = $itemObj->getActiveItems(1000); // Limit to 1000 for performance
$categories = $categoryObj->getActiveCategories();

// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Homepage
echo '<url>';
echo '<loc>' . htmlspecialchars(SITE_URL . '/public/index.php') . '</loc>';
echo '<changefreq>daily</changefreq>';
echo '<priority>1.0</priority>';
echo '</url>';

// Static pages
$staticPages = [
    '/public/products.php' => ['daily', '0.9'],
    '/public/categories.php' => ['weekly', '0.8'],
    '/public/about.php' => ['monthly', '0.6'],
    '/public/privacy-policy.php' => ['monthly', '0.5'],
    '/public/terms-of-service.php' => ['monthly', '0.5'],
    '/public/cookie-policy.php' => ['monthly', '0.5']
];

foreach ($staticPages as $page => $config) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . $page) . '</loc>';
    echo '<changefreq>' . $config[0] . '</changefreq>';
    echo '<priority>' . $config[1] . '</priority>';
    echo '</url>';
}

// Categories
foreach ($categories as $category) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/public/browse.php?category=' . $category['slug']) . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Items
foreach ($items as $item) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/public/item.php?slug=' . $item['slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['updated_at'])) . '</lastmod>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
