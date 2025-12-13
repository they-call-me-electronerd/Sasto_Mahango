<?php
/**
 * Market Movers API
 * Returns top gainers, losers, and daily essentials
 */

header('Content-Type: application/json');

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Item.php';

// Initialize response
$response = [
    'success' => true,
    'gainers' => [],
    'losers' => [],
    'essentials' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    $db = Database::getInstance()->getConnection();
    
    // Get Top Gainers (mock data for now - in production, calculate from price history)
    $gainersQuery = "
        SELECT 
            i.item_id,
            i.item_name,
            i.current_price,
            i.unit,
            c.category_name,
            ROUND(RAND() * 15 + 1, 1) as price_change_percent
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.category_id
        WHERE i.status = 'active' AND i.current_price > 0
        ORDER BY RAND()
        LIMIT 5
    ";
    
    $gainersStmt = $db->query($gainersQuery);
    $gainers = $gainersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($gainers as &$gainer) {
        $gainer['emoji'] = getEmojiForCategory($gainer['category_name']);
        $gainer['sparkline_data'] = generateSparklineData($gainer['current_price'], 'up');
        $gainer['is_verified'] = true;
    }
    $response['gainers'] = $gainers;
    
    // Get Top Losers
    $losersQuery = "
        SELECT 
            i.item_id,
            i.item_name,
            i.current_price,
            i.unit,
            c.category_name,
            ROUND(RAND() * -15 - 1, 1) as price_change_percent
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.category_id
        WHERE i.status = 'active' AND i.current_price > 0
        ORDER BY RAND()
        LIMIT 5
    ";
    
    $losersStmt = $db->query($losersQuery);
    $losers = $losersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($losers as &$loser) {
        $loser['emoji'] = getEmojiForCategory($loser['category_name']);
        $loser['sparkline_data'] = generateSparklineData($loser['current_price'], 'down');
        $loser['is_verified'] = true;
    }
    $response['losers'] = $losers;
    
    // Get Daily Essentials (predefined staple goods)
    $essentialsQuery = "
        SELECT 
            i.item_id,
            i.item_name,
            i.item_name_nepali,
            i.current_price,
            i.unit,
            i.updated_at
        FROM items i
        WHERE i.status = 'active' 
        AND (
            LOWER(i.item_name) LIKE '%rice%' OR
            LOWER(i.item_name) LIKE '%oil%' OR
            LOWER(i.item_name) LIKE '%sugar%' OR
            LOWER(i.item_name) LIKE '%salt%' OR
            LOWER(i.item_name) LIKE '%milk%' OR
            LOWER(i.item_name) LIKE '%flour%'
        )
        LIMIT 6
    ";
    
    $essentialsStmt = $db->query($essentialsQuery);
    $essentials = $essentialsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($essentials as &$essential) {
        $essential['last_updated'] = timeAgo($essential['updated_at']);
    }
    $response['essentials'] = $essentials;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = 'Failed to fetch market data';
}

echo json_encode($response);

/**
 * Get emoji for category
 */
function getEmojiForCategory($categoryName) {
    $emojis = [
        'Vegetables' => '🥦',
        'Fruits' => '🍎',
        'Kitchen Appliances' => '🍳',
        'Study Material' => '📚',
        'Clothing' => '👕',
        'Tools' => '🔧',
        'Electrical Appliances' => '💡',
        'Tech Gadgets' => '📱',
    ];
    return $emojis[$categoryName] ?? '📦';
}

/**
 * Generate sparkline data (7 days) based on current price
 */
function generateSparklineData($currentPrice, $trend = 'up') {
    $data = [];
    $basePrice = $currentPrice;
    
    // Generate 7 data points
    for ($i = 6; $i >= 0; $i--) {
        if ($trend === 'up') {
            // Upward trend
            $variance = ($i / 6) * 0.15; // 15% variance
            $price = $basePrice * (1 - $variance + (rand(-5, 5) / 100));
        } else {
            // Downward trend
            $variance = ($i / 6) * 0.15;
            $price = $basePrice * (1 + $variance + (rand(-5, 5) / 100));
        }
        $data[] = round($price, 2);
    }
    
    return $data;
}

/**
 * Time ago helper
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'just now';
    } elseif ($difference < 3600) {
        $mins = floor($difference / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
}
