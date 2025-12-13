<?php
/**
 * Get Price Ticker Data
 * AJAX endpoint for live price updates
 */

header('Content-Type: application/json');

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Item.php';

$itemObj = new Item();
$changes = $itemObj->getSignificantPriceChanges(PRICE_CHANGE_THRESHOLD, 10);

$response = [];

foreach ($changes as $change) {
    $response[] = [
        'item_id' => $change['item_id'],
        'item_name' => $change['item_name'],
        'slug' => $change['slug'],
        'old_price' => number_format($change['old_price'], 2),
        'new_price' => number_format($change['new_price'], 2),
        'change_percent' => round($change['price_change_percent'], 2),
        'direction' => $change['price_change_percent'] > 0 ? 'up' : 'down'
    ];
}

echo json_encode($response);
?>
