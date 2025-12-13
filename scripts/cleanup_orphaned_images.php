<?php
/**
 * Cleanup Orphaned Image References
 * 
 * This script:
 * 1. Finds items with image_path in database but missing files
 * 2. Sets image_path to NULL for those items
 * 3. Reports what was cleaned up
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "=== Orphaned Image Cleanup Utility ===\n\n";

// Check items table
echo "Checking items table...\n";
$stmt = $pdo->query("SELECT item_id, item_name, image_path FROM items WHERE image_path IS NOT NULL");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orphaned = [];
foreach ($items as $item) {
    $filePath = UPLOAD_DIR . $item['image_path'];
    if (!file_exists($filePath)) {
        $orphaned[] = [
            'id' => $item['item_id'],
            'name' => $item['item_name'],
            'path' => $item['image_path'],
            'table' => 'items'
        ];
    }
}

if (count($orphaned) > 0) {
    echo "\nFound " . count($orphaned) . " orphaned image references:\n";
    foreach ($orphaned as $item) {
        echo "  - {$item['name']} (ID: {$item['id']}) -> {$item['path']}\n";
    }
    
    echo "\nCleaning up orphaned references...\n";
    foreach ($orphaned as $item) {
        $stmt = $pdo->prepare("UPDATE items SET image_path = NULL WHERE item_id = ?");
        $stmt->execute([$item['id']]);
        echo "  ✓ Cleared image_path for {$item['name']}\n";
    }
} else {
    echo "No orphaned images found in items table.\n";
}

// Check validation_queue table
echo "\n\nChecking validation_queue table...\n";
$stmt = $pdo->query("SELECT queue_id, item_name, image_path, status FROM validation_queue WHERE image_path IS NOT NULL");
$queueItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$queueOrphaned = [];
foreach ($queueItems as $item) {
    $filePath = UPLOAD_DIR . $item['image_path'];
    if (!file_exists($filePath)) {
        $queueOrphaned[] = [
            'id' => $item['queue_id'],
            'name' => $item['item_name'],
            'path' => $item['image_path'],
            'status' => $item['status']
        ];
    }
}

if (count($queueOrphaned) > 0) {
    echo "\nFound " . count($queueOrphaned) . " orphaned image references in validation queue:\n";
    foreach ($queueOrphaned as $item) {
        echo "  - {$item['name']} (Queue ID: {$item['id']}, Status: {$item['status']}) -> {$item['path']}\n";
    }
    
    echo "\nCleaning up orphaned references...\n";
    foreach ($queueOrphaned as $item) {
        $stmt = $pdo->prepare("UPDATE validation_queue SET image_path = NULL WHERE queue_id = ?");
        $stmt->execute([$item['id']]);
        echo "  ✓ Cleared image_path for {$item['name']}\n";
    }
} else {
    echo "No orphaned images found in validation queue.\n";
}

echo "\n\n=== Cleanup Complete ===\n";
echo "Summary:\n";
echo "  - Items cleaned: " . count($orphaned) . "\n";
echo "  - Queue entries cleaned: " . count($queueOrphaned) . "\n";
echo "\nYou can now re-upload these items with images.\n";
