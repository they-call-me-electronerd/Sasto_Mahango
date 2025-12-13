<?php
/**
 * Seed Products Script
 * Generates 100 dummy products for testing
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "Fetching categories...\n";
    $stmt = $conn->query("SELECT category_id, slug, category_name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($categories)) {
        die("Error: No categories found. Please run seed_data.sql first.\n");
    }

    // Map slugs to sample items for realistic data
    $sampleData = [
        'vegetables' => ['Cauliflower', 'Cabbage', 'Spinach', 'Carrot', 'Radish', 'Pumpkin', 'Bitter Gourd', 'Bottle Gourd', 'Okra', 'Beans', 'Peas', 'Cucumber'],
        'fruits' => ['Orange', 'Mango', 'Grapes', 'Papaya', 'Watermelon', 'Pineapple', 'Pomegranate', 'Guava', 'Kiwi', 'Strawberry'],
        'groceries' => ['Rice (Sona Mansuli)', 'Rice (Basmati)', 'Lentils (Masoor)', 'Lentils (Moong)', 'Sugar', 'Salt', 'Cooking Oil (Sunflower)', 'Cooking Oil (Mustard)', 'Flour (Atta)', 'Chickpeas', 'Tea Leaves', 'Coffee Powder'],
        'furniture' => ['Wooden Chair', 'Office Desk', 'Dining Table', 'Sofa Set (3 Seater)', 'Wardrobe', 'Bookshelf', 'Bed Frame (Queen)', 'Shoe Rack', 'Coffee Table', 'Study Lamp'],
        'sports-fitness' => ['Football', 'Cricket Bat', 'Badminton Racket', 'Yoga Mat', 'Dumbbells (5kg)', 'Running Shoes', 'Tennis Ball', 'Volleyball', 'Jump Rope', 'Resistance Bands'],
        'kitchen-appliances' => ['Mixer Grinder', 'Gas Stove (2 Burner)', 'Pressure Cooker (5L)', 'Electric Kettle', 'Toaster', 'Sandwich Maker', 'Blender', 'Microwave Oven'],
        'tech-gadgets' => ['Smartphone Stand', 'USB Cable (Type-C)', 'Power Bank (10000mAh)', 'Wireless Earbuds', 'Bluetooth Speaker', 'Laptop Sleeve', 'Mouse (Wireless)', 'Keyboard'],
        'clothing' => ['T-Shirt (Cotton)', 'Jeans (Denim)', 'Jacket (Windcheater)', 'Sweater', 'Kurta', 'Sari', 'Shorts', 'Track Pants'],
        'tools' => ['Hammer', 'Screwdriver Set', 'Pliers', 'Wrench', 'Drill Machine', 'Tape Measure', 'Saw', 'Toolbox'],
        'electrical-appliances' => ['LED Bulb (9W)', 'Extension Cord', 'Ceiling Fan', 'Iron', 'Heater', 'Switch Board', 'Table Fan'],
        'miscellaneous' => ['Notebook', 'Pen Set', 'Umbrella', 'Water Bottle', 'Key Chain', 'Wall Clock', 'Photo Frame']
    ];

    $units = ['kg', 'piece', 'liter', 'dozen', 'pack'];
    $locations = ['Kalimati Market', 'Balkhu Market', 'Asan Bazaar', 'New Road', 'Bhatbhateni Supermarket', 'Local Market'];

    // Get admin user ID for created_by
    $stmt = $conn->query("SELECT user_id FROM users WHERE role = 'admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminId = $admin ? $admin['user_id'] : 1;

    echo "Generating 100 products...\n";
    
    $count = 0;
    $insertStmt = $conn->prepare("INSERT INTO items (item_name, slug, category_id, base_price, current_price, unit, market_location, description, status, created_by, validated_by, validated_at) VALUES (:name, :slug, :cat_id, :base_price, :current_price, :unit, :loc, :desc, 'active', :created_by, :validated_by, NOW())");

    for ($i = 0; $i < 100; $i++) {
        // ... (rest of loop logic remains same until execute) ...
        // Pick random category
        $category = $categories[array_rand($categories)];
        $catSlug = $category['slug'];
        $catId = $category['category_id'];

        // Pick random item name based on category
        $possibleItems = $sampleData[$catSlug] ?? $sampleData['miscellaneous'];
        $baseName = $possibleItems[array_rand($possibleItems)];
        
        // Add variation to make unique
        $variation = rand(1, 1000);
        $itemName = "$baseName";
        if (rand(0, 1)) { 
             // ...
        }

        $slug = strtolower(str_replace([' ', '(', ')'], '-', $itemName)) . '-' . uniqid();
        $price = rand(50, 5000);
        $unit = $units[array_rand($units)];
        $location = $locations[array_rand($locations)];
        $desc = "High quality $itemName available at $location.";

        try {
            $insertStmt->execute([
                ':name' => $itemName,
                ':slug' => $slug,
                ':cat_id' => $catId,
                ':base_price' => $price,
                ':current_price' => $price,
                ':unit' => $unit,
                ':loc' => $location,
                ':desc' => $desc,
                ':created_by' => $adminId,
                ':validated_by' => $adminId
            ]);
            $count++;
            echo ".";
            if ($count % 50 == 0) echo "\n";
        } catch (PDOException $e) {
            echo "x Error: " . $e->getMessage() . "\n"; // Show error
        }
    }

    echo "\nSuccessfully inserted $count products.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
