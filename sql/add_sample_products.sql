-- Sample Items Only (for products that don't exist)
-- This will add test products to the database

USE mulyasuchi_db;

-- Sample Contributor User (skip if exists)
INSERT IGNORE INTO users (username, email, password_hash, full_name, role, status, created_by) VALUES
('contributor1', 'contributor@mulyasuchi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sample Contributor', 'contributor', 'active', 1);

-- Sample Items (All pre-approved by admin)
INSERT IGNORE INTO items (item_name, item_name_nepali, slug, category_id, base_price, current_price, unit, market_location, description, status, created_by, validated_by, validated_at) VALUES
('Tomato', 'गोलभेंडा', 'tomato', 1, 80.00, 85.00, 'kg', 'Kalimati Vegetable Market', 'Fresh red tomatoes', 'active', 2, 1, NOW()),
('Potato', 'आलु', 'potato', 1, 45.00, 45.00, 'kg', 'Kalimati Vegetable Market', 'Local potatoes', 'active', 2, 1, NOW()),
('Onion', 'प्याज', 'onion', 1, 70.00, 72.00, 'kg', 'Kalimati Vegetable Market', 'Red onions', 'active', 2, 1, NOW()),
('Apple', 'स्याउ', 'apple', 2, 250.00, 240.00, 'kg', 'Kalimati Fruit Market', 'Fresh apples from Mustang', 'active', 2, 1, NOW()),
('Banana', 'केरा', 'banana', 2, 80.00, 85.00, 'dozen', 'Kalimati Fruit Market', 'Ripe bananas', 'active', 2, 1, NOW()),
('Rice Cooker', 'भात पकाउने भाँडो', 'rice-cooker', 3, 2500.00, 2500.00, 'piece', 'New Road Electronics', 'Electric rice cooker 1.8L', 'active', 2, 1, NOW()),
('Cucumber', 'काँक्रो', 'cucumber', 1, 60.00, 65.00, 'kg', 'Kalimati Vegetable Market', 'Fresh green cucumbers', 'active', 2, 1, NOW()),
('Mango', 'आँप', 'mango', 2, 150.00, 160.00, 'kg', 'Kalimati Fruit Market', 'Sweet mangoes', 'active', 2, 1, NOW()),
('Grapes', 'अंगुर', 'grapes', 2, 200.00, 210.00, 'kg', 'Kalimati Fruit Market', 'Fresh green grapes', 'active', 2, 1, NOW());

-- Sample Price History (only for items that were inserted)
INSERT IGNORE INTO price_history (item_id, old_price, new_price, updated_by) VALUES
(1, 80.00, 85.00, 1),
(3, 70.00, 72.00, 1),
(4, 250.00, 240.00, 1);

-- Sample Item Tags
INSERT IGNORE INTO item_tags (item_id, tag_id) VALUES
(1, 5), -- Tomato - Fresh
(1, 3), -- Tomato - Local
(2, 3), -- Potato - Local
(4, 1), -- Apple - Seasonal
(4, 8), -- Apple - Premium
(5, 5), -- Banana - Fresh
(7, 5), -- Cucumber - Fresh
(8, 1), -- Mango - Seasonal
(9, 5); -- Grapes - Fresh
