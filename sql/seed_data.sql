-- ===========================================================================
-- MULYASUCHI INITIAL DATA SEED
-- ===========================================================================
-- Description: Initial data for the Mulyasuchi platform
-- Default admin user, categories, settings, and tags
-- ===========================================================================

USE mulyasuchi_db;

-- ===========================================================================
-- DEFAULT ADMIN USER
-- ===========================================================================
-- Username: admin
-- Email: admin@mulyasuchi.com
-- Password: Admin@123 (MUST BE CHANGED IN PRODUCTION)
-- Password Hash generated with: password_hash('Admin@123', PASSWORD_BCRYPT)
-- ===========================================================================

INSERT INTO users (username, email, password_hash, full_name, role, status) VALUES
('admin', 'admin@mulyasuchi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Master Admin', 'admin', 'active');

-- ===========================================================================
-- DEFAULT CATEGORIES
-- ===========================================================================

INSERT INTO categories (category_name, category_name_nepali, slug, description, icon_class, display_order) VALUES
('Vegetables', 'तरकारीहरू', 'vegetables', 'Fresh vegetables and produce', 'icon-vegetables', 1),
('Fruits', 'फलफूलहरू', 'fruits', 'Fresh and seasonal fruits', 'icon-fruits', 2),
('Kitchen Appliances', 'भान्साका सामानहरू', 'kitchen-appliances', 'Kitchen tools and appliances', 'icon-kitchen', 3),
('Study Material', 'अध्ययन सामग्री', 'study-material', 'Books, stationery, and learning materials', 'icon-study', 4),
('Clothing', 'लुगाफाटा', 'clothing', 'Garments and accessories', 'icon-clothing', 5),
('Tools', 'औजारहरू', 'tools', 'Hardware and tools', 'icon-tools', 6),
('Electrical Appliances', 'बिजुली उपकरणहरू', 'electrical-appliances', 'Electronic devices and appliances', 'icon-electrical', 7),
('Tech/Gadgets', 'प्रविधि/ग्याजेट्स', 'tech-gadgets', 'Latest technology and gadgets', 'icon-tech', 8),
('Miscellaneous', 'विविध', 'miscellaneous', 'Other items', 'icon-misc', 9);

-- ===========================================================================
-- DEFAULT SITE SETTINGS
-- ===========================================================================

INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Mulyasuchi', 'text', 'Site name'),
('site_tagline', 'Your Trusted Market Intelligence Platform', 'text', 'Site tagline'),
('contact_email', 'contact@mulyasuchi.com', 'text', 'Contact email'),
('contact_phone', '+977-1-XXXXXXX', 'text', 'Contact phone'),
('price_change_alert_threshold', '20', 'number', 'Price change alert threshold percentage'),
('items_per_page', '20', 'number', 'Items per page in listings'),
('enable_nepali_language', '1', 'boolean', 'Enable Nepali language support'),
('ticker_update_interval', '5000', 'number', 'Live ticker update interval in milliseconds'),
('max_tags_per_item', '10', 'number', 'Maximum tags allowed per item');

-- ===========================================================================
-- DEFAULT TAGS
-- ===========================================================================

INSERT INTO tags (tag_name, tag_name_nepali) VALUES
('Seasonal', 'मौसमी'),
('Imported', 'आयातित'),
('Local', 'स्थानीय'),
('Organic', 'अर्गानिक'),
('Fresh', 'ताजा'),
('Frozen', 'जमाएको'),
('Discount', 'छुट'),
('Premium', 'प्रिमियम'),
('New', 'नयाँ'),
('Popular', 'लोकप्रिय');

-- ===========================================================================
-- SAMPLE DATA (OPTIONAL - FOR TESTING)
-- ===========================================================================
-- Sample data for testing - now active!

-- Sample Contributor User
INSERT INTO users (username, email, password_hash, full_name, role, status, created_by) VALUES
('contributor1', 'contributor@mulyasuchi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sample Contributor', 'contributor', 'active', 1);

-- Sample Items (All pre-approved by admin)
INSERT INTO items (item_name, item_name_nepali, slug, category_id, base_price, current_price, unit, market_location, description, status, created_by, validated_by, validated_at) VALUES
('Tomato', 'गोलभेंडा', 'tomato', 1, 80.00, 85.00, 'kg', 'Kalimati Vegetable Market', 'Fresh red tomatoes', 'active', 2, 1, NOW()),
('Potato', 'आलु', 'potato', 1, 45.00, 45.00, 'kg', 'Kalimati Vegetable Market', 'Local potatoes', 'active', 2, 1, NOW()),
('Onion', 'प्याज', 'onion', 1, 70.00, 72.00, 'kg', 'Kalimati Vegetable Market', 'Red onions', 'active', 2, 1, NOW()),
('Apple', 'स्याउ', 'apple', 2, 250.00, 240.00, 'kg', 'Kalimati Fruit Market', 'Fresh apples from Mustang', 'active', 2, 1, NOW()),
('Banana', 'केरा', 'banana', 2, 80.00, 85.00, 'dozen', 'Kalimati Fruit Market', 'Ripe bananas', 'active', 2, 1, NOW()),
('Rice Cooker', 'भात पकाउने भाँडो', 'rice-cooker', 3, 2500.00, 2500.00, 'piece', 'New Road Electronics', 'Electric rice cooker 1.8L', 'active', 2, 1, NOW());

-- Sample Price History
INSERT INTO price_history (item_id, old_price, new_price, updated_by) VALUES
(1, 80.00, 85.00, 1),
(3, 70.00, 72.00, 1),
(4, 250.00, 240.00, 1);

-- Sample Item Tags
INSERT INTO item_tags (item_id, tag_id) VALUES
(1, 5), -- Tomato - Fresh
(1, 3), -- Tomato - Local
(2, 3), -- Potato - Local
(4, 1), -- Apple - Seasonal
(4, 8), -- Apple - Premium
(5, 5); -- Banana - Fresh

