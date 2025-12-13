<?php
/**
 * Seed Products for New Categories
 * Adds sample products for the newly added categories
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Get admin user ID for created_by
    $stmt = $conn->query("SELECT user_id FROM users WHERE role = 'admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminId = $admin ? $admin['user_id'] : 1;

    // Get category IDs for new categories
    $stmt = $conn->query("
        SELECT category_id, slug, category_name 
        FROM categories 
        WHERE slug IN ('household-items', 'electrical-appliances', 'clothing', 'study-material', 'tools-hardware')
        ORDER BY display_order
    ");
    $newCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($newCategories)) {
        die("Error: New categories not found. Please run add_new_categories.sql first.\n");
    }

    echo "Adding products for new categories...\n\n";

    // Product data for each category
    $productData = [
        'household-items' => [
            ['name' => 'Laundry Detergent (1kg)', 'nepali' => 'लुगा धुने साबुन', 'price' => 250, 'unit' => 'kg', 'market' => 'Bhatbhateni'],
            ['name' => 'Dish Soap (500ml)', 'nepali' => 'भाँडा धुने साबुन', 'price' => 120, 'unit' => 'ml', 'market' => 'Asan Bazaar'],
            ['name' => 'Floor Cleaner (1L)', 'nepali' => 'भुइँ सफा गर्ने', 'price' => 180, 'unit' => 'liter', 'market' => 'Bhatbhateni'],
            ['name' => 'Toilet Cleaner (500ml)', 'nepali' => 'शौचालय सफा गर्ने', 'price' => 150, 'unit' => 'ml', 'market' => 'Bhatbhateni'],
            ['name' => 'Hand Wash (250ml)', 'nepali' => 'हात धुने साबुन', 'price' => 95, 'unit' => 'ml', 'market' => 'Asan Bazaar'],
            ['name' => 'Mop', 'nepali' => 'पोछा', 'price' => 350, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Broom', 'nepali' => 'कुचो', 'price' => 120, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Dustpan', 'nepali' => 'फोहोर बटुल्ने', 'price' => 80, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Garbage Bags (20pcs)', 'nepali' => 'फोहोरको झोला', 'price' => 100, 'unit' => 'pack', 'market' => 'Bhatbhateni'],
            ['name' => 'Air Freshener', 'nepali' => 'सुगन्धित स्प्रे', 'price' => 200, 'unit' => 'piece', 'market' => 'Bhatbhateni'],
        ],
        'electrical-appliances' => [
            ['name' => 'LED Bulb 9W', 'nepali' => 'एलईडी बल्ब', 'price' => 150, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Table Fan', 'nepali' => 'टेबल फ्यान', 'price' => 1800, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Electric Kettle', 'nepali' => 'बिजुली केतली', 'price' => 1500, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Iron Box', 'nepali' => 'इस्त्री', 'price' => 1200, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Extension Cord 4 Socket', 'nepali' => 'एक्सटेन्सन कर्ड', 'price' => 450, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Rice Cooker', 'nepali' => 'भात पकाउने', 'price' => 2500, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Mixer Grinder', 'nepali' => 'मिक्सर ग्राइन्डर', 'price' => 3500, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Electric Heater', 'nepali' => 'बिजुली हिटर', 'price' => 2800, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Vacuum Cleaner', 'nepali' => 'भ्याकुम क्लिनर', 'price' => 5500, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Hair Dryer', 'nepali' => 'कपाल सुकाउने', 'price' => 1200, 'unit' => 'piece', 'market' => 'New Road'],
        ],
        'clothing' => [
            ['name' => 'Men\'s T-Shirt', 'nepali' => 'पुरुष टी-शर्ट', 'price' => 450, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Women\'s Kurta', 'nepali' => 'महिला कुर्ता', 'price' => 800, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Jeans Pant', 'nepali' => 'जिन्स पाइन्ट', 'price' => 1200, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Cotton Socks (pair)', 'nepali' => 'मोजा', 'price' => 100, 'unit' => 'pair', 'market' => 'Asan Bazaar'],
            ['name' => 'Belt', 'nepali' => 'पेटी', 'price' => 300, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Cap/Hat', 'nepali' => 'टोपी', 'price' => 250, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Scarf', 'nepali' => 'गुन्यू चोलो', 'price' => 200, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Handkerchief (pack of 3)', 'nepali' => 'रुमाल', 'price' => 150, 'unit' => 'pack', 'market' => 'Asan Bazaar'],
            ['name' => 'Umbrella', 'nepali' => 'छाता', 'price' => 400, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Backpack', 'nepali' => 'झोला', 'price' => 1500, 'unit' => 'piece', 'market' => 'New Road'],
        ],
        'study-material' => [
            ['name' => 'Notebook A4 (100 pages)', 'nepali' => 'कापी', 'price' => 80, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Pen (blue)', 'nepali' => 'कलम', 'price' => 20, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Pencil Box', 'nepali' => 'पेन्सिल बक्स', 'price' => 150, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Geometry Box', 'nepali' => 'ज्यामिति बक्स', 'price' => 200, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Color Pencils (12 colors)', 'nepali' => 'रङ पेन्सिल', 'price' => 120, 'unit' => 'box', 'market' => 'Asan Bazaar'],
            ['name' => 'Eraser', 'nepali' => 'रबर', 'price' => 10, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Sharpener', 'nepali' => 'छिलाउने', 'price' => 15, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Ruler (30cm)', 'nepali' => 'स्केल', 'price' => 25, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'Drawing Book', 'nepali' => 'चित्र कोर्ने कापी', 'price' => 100, 'unit' => 'piece', 'market' => 'Asan Bazaar'],
            ['name' => 'School Bag', 'nepali' => 'स्कूल झोला', 'price' => 1200, 'unit' => 'piece', 'market' => 'New Road'],
        ],
        'tools-hardware' => [
            ['name' => 'Hammer', 'nepali' => 'हथौडा', 'price' => 300, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Screwdriver Set', 'nepali' => 'पेचकस सेट', 'price' => 450, 'unit' => 'set', 'market' => 'Balkhu Market'],
            ['name' => 'Pliers', 'nepali' => 'चिमटा', 'price' => 250, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Wrench Set', 'nepali' => 'रेन्च सेट', 'price' => 600, 'unit' => 'set', 'market' => 'Balkhu Market'],
            ['name' => 'Drill Machine', 'nepali' => 'ड्रिल मेसिन', 'price' => 3500, 'unit' => 'piece', 'market' => 'New Road'],
            ['name' => 'Measuring Tape 5m', 'nepali' => 'नाप्ने फित्ता', 'price' => 150, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Saw', 'nepali' => 'आरा', 'price' => 400, 'unit' => 'piece', 'market' => 'Balkhu Market'],
            ['name' => 'Paint Brush Set', 'nepali' => 'रंग गर्ने ब्रस', 'price' => 200, 'unit' => 'set', 'market' => 'Balkhu Market'],
            ['name' => 'Nails (1kg)', 'nepali' => 'किला', 'price' => 180, 'unit' => 'kg', 'market' => 'Balkhu Market'],
            ['name' => 'Spirit Level', 'nepali' => 'लेभल', 'price' => 350, 'unit' => 'piece', 'market' => 'Balkhu Market'],
        ]
    ];

    $conn->beginTransaction();

    $insertStmt = $conn->prepare("
        INSERT INTO items 
        (item_name, item_name_nepali, slug, category_id, base_price, current_price, unit, market_location, description, status, created_by, validated_by, validated_at) 
        VALUES 
        (:name, :name_nepali, :slug, :cat_id, :base_price, :current_price, :unit, :market, :desc, 'active', :created_by, :validated_by, NOW())
    ");

    $totalAdded = 0;

    foreach ($newCategories as $category) {
        $categorySlug = $category['slug'];
        $categoryId = $category['category_id'];
        $categoryName = $category['category_name'];

        if (!isset($productData[$categorySlug])) {
            echo "⚠ No product data for category: $categoryName\n";
            continue;
        }

        echo "Adding products for: $categoryName\n";
        $products = $productData[$categorySlug];

        foreach ($products as $product) {
            $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9\s-]/', '', $product['name'])));
            
            $insertStmt->execute([
                ':name' => $product['name'],
                ':name_nepali' => $product['nepali'],
                ':slug' => $slug . '-' . uniqid(),
                ':cat_id' => $categoryId,
                ':base_price' => $product['price'],
                ':current_price' => $product['price'],
                ':unit' => $product['unit'],
                ':market' => $product['market'],
                ':desc' => $product['name'] . ' - Available at ' . $product['market'],
                ':created_by' => $adminId,
                ':validated_by' => $adminId
            ]);

            $totalAdded++;
        }

        echo "  ✓ Added " . count($products) . " products\n";
    }

    $conn->commit();

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ SUCCESS! Added $totalAdded products across " . count($newCategories) . " new categories\n";
    echo str_repeat("=", 60) . "\n\n";

    // Verification
    echo "Verification:\n";
    echo str_repeat("-", 60) . "\n";
    
    $stmt = $conn->query("
        SELECT c.category_name, COUNT(i.item_id) as count
        FROM categories c
        LEFT JOIN items i ON c.category_id = i.category_id AND i.status = 'active'
        WHERE c.slug IN ('household-items', 'electrical-appliances', 'clothing', 'study-material', 'tools-hardware')
        GROUP BY c.category_id, c.category_name
        ORDER BY c.display_order
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("  %-30s : %3d products\n", $row['category_name'], $row['count']);
    }
    
    echo str_repeat("-", 60) . "\n";

    // Total items in database
    $stmt = $conn->query("SELECT COUNT(*) as count FROM items WHERE status = 'active'");
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nTotal Active Products in Database: $totalProducts\n";

} catch (PDOException $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
