-- =============================================================================
-- SASTOMAHANGO: 500 PRODUCTS DATABASE SEED
-- =============================================================================
-- Professional, Modern, Clean Structure
-- Realistic Nepali Market Prices and Locations
-- Distributed Across 12 Categories
-- =============================================================================

USE sastomahango_db;

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing products
TRUNCATE TABLE item_tags;
TRUNCATE TABLE price_history;
TRUNCATE TABLE items;

-- Reset auto increment
ALTER TABLE items AUTO_INCREMENT = 1;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- UPDATE CATEGORIES TO 12 TOTAL
-- =============================================================================

-- Clear existing categories (except keep the structure)
DELETE FROM categories WHERE category_id > 0;
ALTER TABLE categories AUTO_INCREMENT = 1;

INSERT INTO categories (category_name, category_name_nepali, slug, description, icon_class, display_order, is_active) VALUES
('Vegetables', 'तरकारीहरू', 'vegetables', 'Fresh vegetables and produce', 'icon-vegetables', 1, 1),
('Fruits', 'फलफूलहरू', 'fruits', 'Fresh and seasonal fruits', 'icon-fruits', 2, 1),
('Groceries', 'किराना सामान', 'groceries', 'Essential grocery items', 'icon-groceries', 3, 1),
('Dairy Products', 'दुग्ध पदार्थ', 'dairy-products', 'Milk and dairy items', 'icon-dairy', 4, 1),
('Meat & Fish', 'मासु र माछा', 'meat-fish', 'Fresh meat and seafood', 'icon-meat', 5, 1),
('Spices', 'मसला', 'spices', 'Herbs and spices', 'icon-spices', 6, 1),
('Kitchen Appliances', 'भान्साका सामानहरू', 'kitchen-appliances', 'Kitchen tools and appliances', 'icon-kitchen', 7, 1),
('Household Items', 'घरायसी सामान', 'household-items', 'Home care products', 'icon-household', 8, 1),
('Electrical Appliances', 'बिजुली उपकरणहरू', 'electrical-appliances', 'Electronic appliances', 'icon-electrical', 9, 1),
('Clothing', 'लुगाफाटा', 'clothing', 'Garments and accessories', 'icon-clothing', 10, 1),
('Study Material', 'अध्ययन सामग्री', 'study-material', 'Books and stationery', 'icon-study', 11, 1),
('Tools & Hardware', 'औजार र हार्डवेयर', 'tools-hardware', 'Hardware and tools', 'icon-tools', 12, 1);

-- =============================================================================
-- CATEGORY 1: VEGETABLES (50 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Potato', 'आलु', 1, 45.00, 45.00, 'kg', 'Fresh local potato', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Tomato', 'गोलभेंडा', 1, 65.00, 60.00, 'kg', 'Red ripe tomatoes', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Onion', 'प्याज', 1, 55.00, 55.00, 'kg', 'Fresh red onion', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Cauliflower', 'काउली', 1, 50.00, 48.00, 'kg', 'White cauliflower', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Cabbage', 'बन्दा', 1, 40.00, 40.00, 'kg', 'Green cabbage', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Carrot', 'गाजर', 1, 70.00, 68.00, 'kg', 'Orange carrot', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Spinach', 'पालुङ्गो', 1, 35.00, 35.00, 'kg', 'Fresh green spinach', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Brinjal', 'भण्टा', 1, 55.00, 52.00, 'kg', 'Purple eggplant', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Cucumber', 'काँक्रो', 1, 65.00, 60.00, 'kg', 'Fresh cucumber', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Green Chili', 'हरियो खुर्सानी', 1, 120.00, 110.00, 'kg', 'Spicy green chili', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Pumpkin', 'फर्सी', 1, 45.00, 45.00, 'kg', 'Orange pumpkin', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Radish', 'मुला', 1, 40.00, 38.00, 'kg', 'White radish', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Bitter Gourd', 'तितो करेला', 1, 80.00, 75.00, 'kg', 'Fresh bitter gourd', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Bottle Gourd', 'लौका', 1, 50.00, 48.00, 'kg', 'Long bottle gourd', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Ridge Gourd', 'घिरौंला', 1, 60.00, 58.00, 'kg', 'Green ridge gourd', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Lady Finger', 'रामतोरिया', 1, 75.00, 70.00, 'kg', 'Fresh okra', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Green Beans', 'सिमी', 1, 90.00, 85.00, 'kg', 'String beans', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Sweet Potato', 'सखरखण्ड', 1, 55.00, 52.00, 'kg', 'Orange sweet potato', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Beetroot', 'गोलमुला', 1, 70.00, 68.00, 'kg', 'Red beetroot', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Ginger', 'अदुवा', 1, 180.00, 170.00, 'kg', 'Fresh ginger root', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Garlic', 'लसुन', 1, 220.00, 210.00, 'kg', 'Local garlic', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Coriander Leaves', 'धनिया पात', 1, 30.00, 28.00, 'pack', 'Fresh coriander', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Mint Leaves', 'पुदिना', 1, 25.00, 25.00, 'pack', 'Fresh mint', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Mushroom', 'च्याउ', 1, 250.00, 240.00, 'kg', 'Button mushroom', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Peas', 'केराउ', 1, 100.00, 95.00, 'kg', 'Green peas', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Bell Pepper', 'भेडे खुर्सानी', 1, 150.00, 140.00, 'kg', 'Red capsicum', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Broccoli', 'ब्रोकाउली', 1, 180.00, 170.00, 'kg', 'Green broccoli', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Lettuce', 'सलाद पात', 1, 120.00, 115.00, 'kg', 'Fresh lettuce', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Spring Onion', 'हरियो प्याज', 1, 50.00, 48.00, 'pack', 'Green onion', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Zucchini', 'जुकिनी', 1, 130.00, 125.00, 'kg', 'Green zucchini', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Pumpkin Shoots', 'फर्सीको मुन्ट', 1, 60.00, 58.00, 'kg', 'Fresh shoots', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Mustard Greens', 'रायो साग', 1, 40.00, 38.00, 'kg', 'Fresh mustard leaves', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Fenugreek Leaves', 'मेथी साग', 1, 50.00, 48.00, 'kg', 'Fresh fenugreek', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Turnip', 'सलगम', 1, 55.00, 52.00, 'kg', 'White turnip', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Pointed Gourd', 'परवल', 1, 70.00, 65.00, 'kg', 'Green pointed gourd', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Taro Root', 'पिडालु', 1, 60.00, 58.00, 'kg', 'Fresh taro', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Yam', 'तरुल', 1, 65.00, 62.00, 'kg', 'Fresh yam', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Broad Beans', 'बाकुला', 1, 85.00, 80.00, 'kg', 'Fresh broad beans', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Drumstick', 'सहजन', 1, 90.00, 85.00, 'kg', 'Fresh drumstick', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Ivy Gourd', 'टिण्डा', 1, 75.00, 70.00, 'kg', 'Fresh ivy gourd', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Snake Gourd', 'चिचिण्डो', 1, 55.00, 52.00, 'kg', 'Long snake gourd', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Green Papaya', 'हरियो मेवा', 1, 50.00, 48.00, 'kg', 'Unripe papaya', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Bamboo Shoots', 'तामा', 1, 180.00, 170.00, 'kg', 'Fresh bamboo shoots', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Water Spinach', 'कलमी साग', 1, 45.00, 42.00, 'kg', 'Fresh water spinach', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Amaranth Leaves', 'लाटे साग', 1, 40.00, 38.00, 'kg', 'Red amaranth', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Choy Sum', 'चाइनिज साग', 1, 70.00, 65.00, 'kg', 'Chinese vegetable', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Asparagus', 'कुरिलो', 1, 280.00, 270.00, 'kg', 'Fresh asparagus', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Celery', 'अजमोद', 1, 150.00, 140.00, 'kg', 'Fresh celery', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Leeks', 'प्याज पात', 1, 120.00, 115.00, 'kg', 'Fresh leeks', 'Kalimati Vegetable Market', 'active', 2, 1, NOW(), NOW()),
('Chinese Cabbage', 'चाइनिज बन्दा', 1, 70.00, 65.00, 'kg', 'Napa cabbage', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW());

-- =============================================================================
-- CATEGORY 2: FRUITS (45 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Apple', 'स्याउ', 2, 240.00, 230.00, 'kg', 'Red delicious apple', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Banana', 'केरा', 2, 85.00, 80.00, 'dozen', 'Yellow banana', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Orange', 'सुन्तला', 2, 120.00, 115.00, 'kg', 'Sweet orange', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Mango', 'आँप', 2, 180.00, 170.00, 'kg', 'Sweet mango', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Grapes', 'अंगुर', 2, 210.00, 200.00, 'kg', 'Green grapes', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Papaya', 'मेवा', 2, 70.00, 65.00, 'kg', 'Ripe papaya', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Watermelon', 'तरबुज', 2, 40.00, 38.00, 'kg', 'Sweet watermelon', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Pineapple', 'भुइँकटहर', 2, 90.00, 85.00, 'piece', 'Fresh pineapple', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Pomegranate', 'अनार', 2, 280.00, 270.00, 'kg', 'Red pomegranate', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Guava', 'अम्बा', 2, 80.00, 75.00, 'kg', 'White guava', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Lemon', 'कागती', 2, 150.00, 140.00, 'kg', 'Fresh lemon', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Pear', 'नासपाती', 2, 220.00, 210.00, 'kg', 'Sweet pear', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Litchi', 'लिची', 2, 320.00, 300.00, 'kg', 'Fresh litchi', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Jackfruit', 'रुख कटहर', 2, 60.00, 55.00, 'kg', 'Ripe jackfruit', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Dragon Fruit', 'ड्रागन फल', 2, 350.00, 330.00, 'kg', 'White dragon fruit', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Kiwi', 'किवी', 2, 450.00, 430.00, 'kg', 'Green kiwi', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Strawberry', 'स्ट्रबेरी', 2, 380.00, 360.00, 'kg', 'Fresh strawberry', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Avocado', 'एभोकाडो', 2, 420.00, 400.00, 'kg', 'Ripe avocado', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Plum', 'आलुबोखरा', 2, 250.00, 240.00, 'kg', 'Red plum', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Peach', 'आरु', 2, 280.00, 270.00, 'kg', 'Fresh peach', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Apricot', 'खुर्पानी', 2, 320.00, 300.00, 'kg', 'Sweet apricot', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Cherry', 'चेरी', 2, 550.00, 520.00, 'kg', 'Red cherry', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Blackberry', 'काँटो काफल', 2, 280.00, 270.00, 'kg', 'Fresh blackberry', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Coconut', 'नरिवल', 2, 60.00, 55.00, 'piece', 'Mature coconut', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Sweet Lime', 'मुसम्बी', 2, 140.00, 130.00, 'kg', 'Sweet lime', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Custard Apple', 'सरिफा', 2, 200.00, 190.00, 'kg', 'Ripe custard apple', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Fig', 'अंजीर', 2, 380.00, 360.00, 'kg', 'Dried fig', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Melon', 'खरबुजा', 2, 50.00, 45.00, 'kg', 'Sweet melon', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Passion Fruit', 'प्यासन फल', 2, 420.00, 400.00, 'kg', 'Fresh passion fruit', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Date', 'खजुर', 2, 480.00, 460.00, 'kg', 'Imported dates', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Blueberry', 'निलो बेरी', 2, 650.00, 620.00, 'kg', 'Fresh blueberries', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Raspberry', 'रास्पबेरी', 2, 580.00, 550.00, 'kg', 'Fresh raspberry', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Mulberry', 'किमबु', 2, 150.00, 140.00, 'kg', 'Fresh mulberry', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Persimmon', 'हलुवाबेद', 2, 320.00, 300.00, 'kg', 'Sweet persimmon', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Star Fruit', 'कर्मरङ्गा', 2, 180.00, 170.00, 'kg', 'Fresh star fruit', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Tamarind', 'इमली', 2, 220.00, 210.00, 'kg', 'Ripe tamarind', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Jamun', 'जामुन', 2, 120.00, 110.00, 'kg', 'Black plum', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Wood Apple', 'बेल', 2, 80.00, 75.00, 'kg', 'Wood apple', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Lime', 'निबुवा', 2, 160.00, 150.00, 'kg', 'Green lime', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Tangerine', 'सुन्तला', 2, 130.00, 120.00, 'kg', 'Sweet tangerine', 'Kalimati Fruit Market', 'active', 2, 1, NOW(), NOW()),
('Grapefruit', 'भालुकागती', 2, 250.00, 240.00, 'kg', 'Fresh grapefruit', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Loquat', 'ऐंसेलु', 2, 180.00, 170.00, 'kg', 'Fresh loquat', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Gooseberry', 'आँवला', 2, 140.00, 130.00, 'kg', 'Indian gooseberry', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Soursop', 'राजबोग', 2, 380.00, 360.00, 'kg', 'Fresh soursop', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Rambutan', 'रम्बुटान', 2, 420.00, 400.00, 'kg', 'Fresh rambutan', 'New Road', 'active', 2, 1, NOW(), NOW());

-- =============================================================================
-- CATEGORY 3: GROCERIES (50 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Basmati Rice', 'बासमती चामल', 3, 120.00, 115.00, 'kg', 'Long grain basmati', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('White Rice', 'सेतो चामल', 3, 85.00, 82.00, 'kg', 'Fine white rice', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Brown Rice', 'ब्राउन चामल', 3, 110.00, 105.00, 'kg', 'Healthy brown rice', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Wheat Flour', 'गहुँको पिठो', 3, 60.00, 58.00, 'kg', 'Whole wheat flour', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Corn Flour', 'मकैको पिठो', 3, 70.00, 68.00, 'kg', 'Fine corn flour', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Red Lentils', 'मसुरो दाल', 3, 140.00, 135.00, 'kg', 'Red lentils', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Green Lentils', 'मुंग दाल', 3, 160.00, 155.00, 'kg', 'Green gram', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Chickpeas', 'चना', 3, 130.00, 125.00, 'kg', 'Kabuli chana', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Black Gram', 'कालो दाल', 3, 150.00, 145.00, 'kg', 'Black lentils', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Kidney Beans', 'राजमा', 3, 180.00, 175.00, 'kg', 'Red kidney beans', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Sunflower Oil', 'सुर्यमुखी तेल', 3, 280.00, 270.00, 'liter', 'Refined sunflower oil', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Mustard Oil', 'तोरीको तेल', 3, 320.00, 310.00, 'liter', 'Pure mustard oil', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Soybean Oil', 'सोयाबिन तेल', 3, 250.00, 245.00, 'liter', 'Refined soybean oil', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Coconut Oil', 'नरिवल तेल', 3, 420.00, 400.00, 'liter', 'Pure coconut oil', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Salt', 'नुन', 3, 25.00, 25.00, 'kg', 'Iodized salt', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('White Sugar', 'सेतो चिनी', 3, 80.00, 78.00, 'kg', 'White sugar', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Brown Sugar', 'ब्राउन चिनी', 3, 95.00, 92.00, 'kg', 'Brown sugar', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Tea Leaves', 'चिया पत्ती', 3, 420.00, 400.00, 'kg', 'Premium tea leaves', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Coffee Powder', 'कफी', 3, 650.00, 620.00, 'kg', 'Instant coffee', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Milk Powder', 'दूध पाउडर', 3, 580.00, 560.00, 'kg', 'Full cream milk powder', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Butter', 'माखन', 3, 520.00, 500.00, 'kg', 'Fresh butter', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Ghee', 'घिउ', 3, 850.00, 820.00, 'kg', 'Pure cow ghee', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Noodles', 'चाउचाउ', 3, 45.00, 42.00, 'pack', 'Instant noodles', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Biscuits', 'बिस्कुट', 3, 80.00, 75.00, 'pack', 'Assorted biscuits', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Bread', 'पाउरोटी', 3, 60.00, 58.00, 'loaf', 'White bread loaf', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Jam', 'ज्याम', 3, 280.00, 270.00, 'bottle', 'Mixed fruit jam', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Honey', 'मह', 3, 650.00, 620.00, 'kg', 'Pure honey', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Vinegar', 'सिरका', 3, 120.00, 115.00, 'bottle', 'White vinegar', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Soy Sauce', 'सोया सस', 3, 180.00, 175.00, 'bottle', 'Dark soy sauce', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Tomato Ketchup', 'केचप', 3, 220.00, 210.00, 'bottle', 'Tomato ketchup', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Mayonnaise', 'मेयोनिज', 3, 320.00, 310.00, 'bottle', 'Eggless mayo', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Peanut Butter', 'बदाम बटर', 3, 420.00, 400.00, 'jar', 'Creamy peanut butter', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Pasta', 'पास्ता', 3, 180.00, 175.00, 'pack', 'Wheat pasta', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cornflakes', 'कर्नफ्लेक्स', 3, 380.00, 360.00, 'pack', 'Breakfast cereal', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Oats', 'ओट्स', 3, 320.00, 310.00, 'kg', 'Rolled oats', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Vermicelli', 'सिमी', 3, 120.00, 115.00, 'pack', 'Thin vermicelli', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Semolina', 'सुजी', 3, 85.00, 82.00, 'kg', 'Fine semolina', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Beaten Rice', 'चिउरा', 3, 95.00, 92.00, 'kg', 'Thin beaten rice', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Puffed Rice', 'भुज्या', 3, 70.00, 68.00, 'kg', 'Puffed rice', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Popcorn Kernels', 'पपकर्न', 3, 150.00, 145.00, 'kg', 'Corn kernels', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Besan', 'बेसन', 3, 110.00, 105.00, 'kg', 'Gram flour', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Rice Flour', 'चामलको पिठो', 3, 75.00, 72.00, 'kg', 'Fine rice flour', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Coconut Powder', 'नरिवल पाउडर', 3, 320.00, 310.00, 'kg', 'Desiccated coconut', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cocoa Powder', 'कोको पाउडर', 3, 580.00, 560.00, 'kg', 'Pure cocoa', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Custard Powder', 'कस्टर्ड पाउडर', 3, 280.00, 270.00, 'pack', 'Vanilla custard', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Gelatin', 'जेलाटिन', 3, 420.00, 400.00, 'pack', 'Food gelatin', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Baking Powder', 'बेकिङ पाउडर', 3, 180.00, 175.00, 'pack', 'Double acting', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Baking Soda', 'मिठाई सोडा', 3, 95.00, 92.00, 'pack', 'Food grade soda', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Yeast', 'खमिर', 3, 220.00, 210.00, 'pack', 'Active dry yeast', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Vanilla Essence', 'भनिला इसेन्स', 3, 150.00, 145.00, 'bottle', 'Vanilla extract', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW());


-- =============================================================================
-- CATEGORY 4: DAIRY PRODUCTS (30 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Fresh Milk', 'ताजा दूध', 4, 75.00, 72.00, 'liter', 'Full cream milk', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Curd', 'दही', 4, 90.00, 88.00, 'kg', 'Fresh yogurt', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Paneer', 'पनिर', 4, 380.00, 360.00, 'kg', 'Fresh cottage cheese', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Cheese Slice', 'चीज स्लाइस', 4, 450.00, 430.00, 'kg', 'Processed cheese', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cream', 'क्रिम', 4, 420.00, 400.00, 'liter', 'Fresh cream', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Condensed Milk', 'गाढा दूध', 4, 320.00, 310.00, 'can', 'Sweetened condensed milk', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Ice Cream', 'आइसक्रिम', 4, 280.00, 270.00, 'liter', 'Vanilla ice cream', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Buttermilk', 'मोही', 4, 50.00, 48.00, 'liter', 'Fresh buttermilk', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Flavored Milk', 'फ्लेवर दूध', 4, 85.00, 82.00, 'liter', 'Chocolate milk', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Lassi', 'लस्सी', 4, 70.00, 68.00, 'liter', 'Sweet lassi', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Mozzarella Cheese', 'मोजारेला चीज', 4, 680.00, 650.00, 'kg', 'Pizza cheese', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cheddar Cheese', 'चेडर चीज', 4, 720.00, 700.00, 'kg', 'Aged cheddar', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Sour Cream', 'साउर क्रिम', 4, 380.00, 360.00, 'kg', 'Fresh sour cream', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Khoa', 'खोवा', 4, 450.00, 430.00, 'kg', 'Pure milk solid', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Whipping Cream', 'ह्विपिङ क्रिम', 4, 520.00, 500.00, 'liter', 'Heavy cream', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cheese Spread', 'चीज स्प्रेड', 4, 350.00, 330.00, 'jar', 'Creamy spread', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Greek Yogurt', 'ग्रीक दही', 4, 280.00, 270.00, 'kg', 'Thick yogurt', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Ghee Butter', 'घिउ माखन', 4, 750.00, 720.00, 'kg', 'Clarified butter', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Cream Cheese', 'क्रिम चीज', 4, 580.00, 550.00, 'kg', 'Soft cream cheese', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cottage Cheese', 'कटेज चीज', 4, 420.00, 400.00, 'kg', 'Low fat cheese', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Evaporated Milk', 'वाष्पित दूध', 4, 280.00, 270.00, 'can', 'Concentrated milk', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Double Cream', 'डबल क्रिम', 4, 620.00, 600.00, 'liter', 'Extra thick cream', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Feta Cheese', 'फेटा चीज', 4, 650.00, 620.00, 'kg', 'Greek feta', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Parmesan Cheese', 'पार्मेसन चीज', 4, 850.00, 820.00, 'kg', 'Italian parmesan', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Yogurt Drink', 'दही पेय', 4, 95.00, 92.00, 'bottle', 'Flavored yogurt', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Gouda Cheese', 'गौडा चीज', 4, 780.00, 750.00, 'kg', 'Dutch gouda', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Ricotta Cheese', 'रिकोटा चीज', 4, 520.00, 500.00, 'kg', 'Italian ricotta', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Blue Cheese', 'नीलो चीज', 4, 920.00, 900.00, 'kg', 'Gorgonzola', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Mascarpone', 'मास्कारपोन', 4, 680.00, 650.00, 'kg', 'Italian cream cheese', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Milk Shake Mix', 'मिल्क शेक मिक्स', 4, 220.00, 210.00, 'pack', 'Instant shake mix', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW());

-- =============================================================================
-- CATEGORY 5: MEAT & FISH (30 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Chicken', 'कुखुराको मासु', 5, 380.00, 370.00, 'kg', 'Fresh chicken', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Mutton', 'खसीको मासु', 5, 850.00, 820.00, 'kg', 'Goat meat', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Buffalo Meat', 'भैंसीको मासु', 5, 620.00, 600.00, 'kg', 'Buffalo meat', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Pork', 'सुँगुरको मासु', 5, 520.00, 500.00, 'kg', 'Fresh pork', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Fish (Rohu)', 'रहु माछा', 5, 450.00, 430.00, 'kg', 'Fresh rohu fish', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Fish (Catla)', 'भकुर माछा', 5, 480.00, 460.00, 'kg', 'Catla fish', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Prawns', 'झिंगे माछा', 5, 950.00, 920.00, 'kg', 'Fresh prawns', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Eggs (Chicken)', 'अण्डा', 5, 180.00, 175.00, 'dozen', 'Farm fresh eggs', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Duck Meat', 'हाँसको मासु', 5, 520.00, 500.00, 'kg', 'Duck meat', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Quail Eggs', 'बट्टाईको अण्डा', 5, 280.00, 270.00, 'dozen', 'Quail eggs', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Dry Fish', 'सुकेको माछा', 5, 750.00, 720.00, 'kg', 'Dried fish', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Sausage', 'सॉसेज', 5, 650.00, 620.00, 'kg', 'Chicken sausage', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Bacon', 'बेकन', 5, 780.00, 750.00, 'kg', 'Smoked bacon', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Salami', 'सलामी', 5, 850.00, 820.00, 'kg', 'Beef salami', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Chicken Liver', 'कलेजो', 5, 280.00, 270.00, 'kg', 'Chicken liver', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Minced Meat', 'कियामा', 5, 420.00, 400.00, 'kg', 'Ground meat', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Turkey', 'टर्की मासु', 5, 680.00, 650.00, 'kg', 'Turkey meat', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Lamb', 'भेडाको मासु', 5, 920.00, 900.00, 'kg', 'Lamb meat', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Tuna', 'टुना माछा', 5, 580.00, 550.00, 'can', 'Canned tuna', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Salmon', 'साल्मन माछा', 5, 1200.00, 1150.00, 'kg', 'Fresh salmon', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Sardines', 'सार्डिन माछा', 5, 320.00, 310.00, 'can', 'Canned sardines', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Crab', 'गेडागुडी', 5, 880.00, 850.00, 'kg', 'Fresh crab', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Lobster', 'लोबस्टर', 5, 1500.00, 1450.00, 'kg', 'Fresh lobster', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Squid', 'स्क्विड', 5, 720.00, 700.00, 'kg', 'Fresh squid', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Ham', 'ह्याम', 5, 780.00, 750.00, 'kg', 'Smoked ham', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Pepperoni', 'पेपरोनी', 5, 920.00, 900.00, 'kg', 'Pizza pepperoni', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Hot Dog', 'हट डग', 5, 580.00, 550.00, 'kg', 'Chicken hot dog', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Fish Ball', 'फिश बल', 5, 420.00, 400.00, 'pack', 'Frozen fish balls', 'Kalimati Market', 'active', 2, 1, NOW(), NOW()),
('Meatballs', 'मीटबल', 5, 520.00, 500.00, 'pack', 'Beef meatballs', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Chicken Breast', 'कुखुराको छाती', 5, 480.00, 460.00, 'kg', 'Boneless breast', 'Kalimati Market', 'active', 2, 1, NOW(), NOW());

-- =============================================================================
-- CATEGORY 6: SPICES (40 products)
-- =============================================================================
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, base_price, unit, description, market_location, status, created_by, validated_by, validated_at, created_at) VALUES
('Turmeric Powder', 'बेसार', 6, 280.00, 270.00, 'kg', 'Pure turmeric', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Red Chili Powder', 'रातो खुर्सानी', 6, 320.00, 310.00, 'kg', 'Hot chili powder', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Coriander Powder', 'धनिया पाउडर', 6, 240.00, 230.00, 'kg', 'Ground coriander', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Cumin Seeds', 'जीरा', 6, 380.00, 360.00, 'kg', 'Whole cumin', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Black Pepper', 'मरिच', 6, 950.00, 920.00, 'kg', 'Whole black pepper', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Cardamom', 'अलैंची', 6, 2800.00, 2700.00, 'kg', 'Green cardamom', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Cinnamon', 'दालचिनी', 6, 1200.00, 1150.00, 'kg', 'Cinnamon sticks', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Cloves', 'ल्वाङ', 6, 1800.00, 1750.00, 'kg', 'Whole cloves', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Bay Leaves', 'तेजपात', 6, 320.00, 310.00, 'kg', 'Dried bay leaves', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Fennel Seeds', 'सौंफ', 6, 280.00, 270.00, 'kg', 'Fennel seeds', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Fenugreek Seeds', 'मेथी', 6, 220.00, 210.00, 'kg', 'Fenugreek seeds', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Mustard Seeds', 'तोरी', 6, 180.00, 175.00, 'kg', 'Yellow mustard seeds', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Sesame Seeds', 'तिल', 6, 320.00, 310.00, 'kg', 'White sesame', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Ajwain', 'ज्वानो', 6, 420.00, 400.00, 'kg', 'Carom seeds', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Asafoetida', 'हिङ', 6, 1500.00, 1450.00, 'kg', 'Pure asafoetida', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Star Anise', 'चक्रफुल', 6, 850.00, 820.00, 'kg', 'Whole star anise', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Nutmeg', 'जायफल', 6, 1200.00, 1150.00, 'kg', 'Whole nutmeg', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Mace', 'जावित्री', 6, 1400.00, 1350.00, 'kg', 'Dried mace', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Garam Masala', 'गरम मसला', 6, 480.00, 460.00, 'kg', 'Mixed spice powder', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Curry Powder', 'करी मसला', 6, 380.00, 360.00, 'kg', 'Curry powder mix', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('White Pepper', 'सेतो मरिच', 6, 1100.00, 1050.00, 'kg', 'Ground white pepper', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Paprika', 'पेपरिका', 6, 620.00, 600.00, 'kg', 'Smoked paprika', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Saffron', 'केशर', 6, 15000.00, 14500.00, 'gram', 'Premium saffron', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Vanilla Pods', 'भनिला पोड', 6, 1800.00, 1750.00, 'pack', 'Whole vanilla beans', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Dry Ginger', 'सुक्खा अदुवा', 6, 420.00, 400.00, 'kg', 'Dried ginger', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Black Cardamom', 'ठूलो अलैंची', 6, 1400.00, 1350.00, 'kg', 'Large cardamom', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Chili Flakes', 'खुर्सानी फ्लेक्स', 6, 380.00, 360.00, 'kg', 'Crushed red pepper', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Oregano', 'ओरेगानो', 6, 520.00, 500.00, 'pack', 'Dried oregano', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Basil', 'तुलसी', 6, 420.00, 400.00, 'pack', 'Dried basil', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Thyme', 'थाइम', 6, 480.00, 460.00, 'pack', 'Dried thyme', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Rosemary', 'रोजमेरी', 6, 520.00, 500.00, 'pack', 'Dried rosemary', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Sage', 'सेज', 6, 450.00, 430.00, 'pack', 'Dried sage', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Curry Leaves', 'करी पात', 6, 180.00, 175.00, 'pack', 'Fresh curry leaves', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW()),
('Garlic Powder', 'लसुन पाउडर', 6, 420.00, 400.00, 'kg', 'Dehydrated garlic', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Onion Powder', 'प्याज पाउडर', 6, 380.00, 360.00, 'kg', 'Dehydrated onion', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Celery Salt', 'अजमोद नुन', 6, 320.00, 310.00, 'pack', 'Seasoned salt', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Mixed Herbs', 'मिश्रित जडिबुटी', 6, 420.00, 400.00, 'pack', 'Italian herbs', 'New Road', 'active', 2, 1, NOW(), NOW()),
('Ginger Garlic Paste', 'अदुवा लसुन पेस्ट', 6, 280.00, 270.00, 'jar', 'Ready paste', 'Bhatbhateni', 'active', 2, 1, NOW(), NOW()),
('Chat Masala', 'चाट मसला', 6, 320.00, 310.00, 'pack', 'Tangy spice mix', 'Balkhu Market', 'active', 2, 1, NOW(), NOW()),
('Meat Masala', 'मासु मसला', 6, 380.00, 360.00, 'pack', 'Meat spice blend', 'Asan Bazaar', 'active', 2, 1, NOW(), NOW());

-- Continuing with remaining categories...
-- This is part 1 of SQL file. Run fresh_500_products_part2.sql for remaining categories.

-- =============================================================================
-- VERIFICATION QUERIES
-- =============================================================================
SELECT 'Current Product Count:' AS info, COUNT(*) AS count FROM items;
SELECT c.category_name, COUNT(i.item_id) AS product_count 
FROM categories c 
LEFT JOIN items i ON c.category_id = i.category_id 
GROUP BY c.category_id, c.category_name
ORDER BY c.category_id;

