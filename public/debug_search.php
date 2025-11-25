<?php
/**
 * Debug Search - Check what's happening with search
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../includes/functions.php';

$itemObj = new Item();

echo "<h1>Search Debug</h1>";

// Check GET parameters
echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Test search with 'g'
$searchTerm = 'g';
echo "<h2>Testing search for: '$searchTerm'</h2>";

$filters = [
    'search' => $searchTerm,
    'category_id' => null,
    'min_price' => null,
    'max_price' => null,
    'sort_by' => 'name_asc',
    'limit' => 10,
    'offset' => 0
];

echo "<h3>Filters:</h3>";
echo "<pre>";
print_r($filters);
echo "</pre>";

$results = $itemObj->searchProductsAdvanced($filters);

echo "<h3>Results (" . count($results) . " items found):</h3>";
echo "<pre>";
print_r($results);
echo "</pre>";

// Test count
$count = $itemObj->countProductsAdvanced(['search' => $searchTerm]);
echo "<h3>Total Count: $count</h3>";
?>
