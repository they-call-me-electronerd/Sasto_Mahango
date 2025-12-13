-- =====================================================
-- Fresh 150 Products Dataset for SastoMahango
-- Includes Nepali locations, realistic prices, and diverse categories
-- =====================================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing products
TRUNCATE TABLE items;

-- Reset auto increment
ALTER TABLE items AUTO_INCREMENT = 1;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert 150 Fresh Products
-- =====================================================

-- VEGETABLES (30 products)
INSERT INTO items (item_name, item_name_nepali, category_id, current_price, unit, description, market_location, status, created_by, created_at) VALUES
('Potato', 'आलु', 1, 45.00, 'kg', 'Fresh local potato', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Tomato', 'गोलभेडा', 1, 60.00, 'kg', 'Red ripe tomatoes', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Onion', 'प्याज', 1, 55.00, 'kg', 'Fresh red onion', 'Balkhu Market', 'active', 3, NOW()),
('Cauliflower', 'काउली', 1, 50.00, 'kg', 'White cauliflower', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Cabbage', 'बन्दा', 1, 40.00, 'kg', 'Green cabbage', 'Asan Bazaar', 'active', 3, NOW()),
('Carrot', 'गाजर', 1, 70.00, 'kg', 'Orange carrot', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Spinach', 'पालुङ्गो', 1, 35.00, 'kg', 'Fresh green spinach', 'Balkhu Market', 'active', 4, NOW()),
('Brinjal', 'भण्टा', 1, 55.00, 'kg', 'Purple eggplant', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Cucumber', 'काँक्रो', 1, 65.00, 'kg', 'Fresh cucumber', 'Kalimati Vegetable Market', 'active', 3, NOW()),
('Green Chili', 'खुर्सानी', 1, 120.00, 'kg', 'Spicy green chili', 'Asan Bazaar', 'active', 2, NOW()),
('Pumpkin', 'फर्सी', 1, 45.00, 'kg', 'Orange pumpkin', 'Balkhu Market', 'active', 4, NOW()),
('Radish', 'मुला', 1, 40.00, 'kg', 'White radish', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Bitter Gourd', 'तितो करेला', 1, 80.00, 'kg', 'Fresh bitter gourd', 'Asan Bazaar', 'active', 3, NOW()),
('Bottle Gourd', 'लौका', 1, 50.00, 'kg', 'Long bottle gourd', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Ridge Gourd', 'घिरौंला', 1, 60.00, 'kg', 'Green ridge gourd', 'Balkhu Market', 'active', 4, NOW()),
('Lady Finger', 'रामतोरिया', 1, 75.00, 'kg', 'Fresh okra', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Green Beans', 'सिमी', 1, 90.00, 'kg', 'String beans', 'Asan Bazaar', 'active', 3, NOW()),
('Sweet Potato', 'सखरखण्ड', 1, 55.00, 'kg', 'Orange sweet potato', 'Balkhu Market', 'active', 2, NOW()),
('Beetroot', 'गोलमुला', 1, 70.00, 'kg', 'Red beetroot', 'Kalimati Vegetable Market', 'active', 4, NOW()),
('Ginger', 'अदुवा', 1, 180.00, 'kg', 'Fresh ginger root', 'Asan Bazaar', 'active', 2, NOW()),
('Garlic', 'लसुन', 1, 220.00, 'kg', 'Local garlic', 'Balkhu Market', 'active', 3, NOW()),
('Coriander Leaves', 'धनिया पात', 1, 30.00, 'bundle', 'Fresh coriander', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Mint Leaves', 'पुदिना', 1, 25.00, 'bundle', 'Fresh mint', 'Asan Bazaar', 'active', 4, NOW()),
('Mushroom', 'च्याउ', 1, 250.00, 'kg', 'Button mushroom', 'New Road', 'active', 3, NOW()),
('Peas', 'केराउ', 1, 100.00, 'kg', 'Green peas', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Bell Pepper', 'भेडे खुर्सानी', 1, 150.00, 'kg', 'Capsicum', 'Asan Bazaar', 'active', 4, NOW()),
('Broccoli', 'ब्रोकाउली', 1, 180.00, 'kg', 'Green broccoli', 'New Road', 'active', 3, NOW()),
('Lettuce', 'सलाद पात', 1, 120.00, 'kg', 'Fresh lettuce', 'Kalimati Vegetable Market', 'active', 2, NOW()),
('Spring Onion', 'हरियो प्याज', 1, 50.00, 'bundle', 'Green onion', 'Balkhu Market', 'active', 4, NOW()),
('Zucchini', 'जुकिनी', 1, 130.00, 'kg', 'Green zucchini', 'New Road', 'active', 3, NOW()),

-- FRUITS (30 products)
('Apple', 'स्याउ', 2, 240.00, 'kg', 'Red delicious apple', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Banana', 'केरा', 2, 85.00, 'dozen', 'Yellow banana', 'Kalimati Fruit Market', 'active', 3, NOW()),
('Orange', 'सुन्तला', 2, 120.00, 'kg', 'Sweet orange', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Mango', 'आँप', 2, 180.00, 'kg', 'Sweet mango', 'Balkhu Market', 'active', 4, NOW()),
('Grapes', 'अंगुर', 2, 210.00, 'kg', 'Green grapes', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Papaya', 'मेवा', 2, 70.00, 'kg', 'Ripe papaya', 'Asan Bazaar', 'active', 3, NOW()),
('Watermelon', 'तरबुज', 2, 40.00, 'kg', 'Sweet watermelon', 'Balkhu Market', 'active', 2, NOW()),
('Pineapple', 'भुइँकटहर', 2, 90.00, 'piece', 'Fresh pineapple', 'Kalimati Fruit Market', 'active', 4, NOW()),
('Pomegranate', 'अनार', 2, 280.00, 'kg', 'Red pomegranate', 'New Road', 'active', 3, NOW()),
('Guava', 'अम्बा', 2, 80.00, 'kg', 'White guava', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Lemon', 'कागती', 2, 150.00, 'kg', 'Fresh lemon', 'Asan Bazaar', 'active', 4, NOW()),
('Pear', 'नासपाती', 2, 220.00, 'kg', 'Sweet pear', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Litchi', 'लिची', 2, 320.00, 'kg', 'Fresh litchi', 'New Road', 'active', 3, NOW()),
('Jackfruit', 'रुख कटहर', 2, 60.00, 'kg', 'Ripe jackfruit', 'Balkhu Market', 'active', 2, NOW()),
('Dragon Fruit', 'ड्रागन फल', 2, 350.00, 'kg', 'White dragon fruit', 'New Road', 'active', 4, NOW()),
('Kiwi', 'किवी', 2, 450.00, 'kg', 'Green kiwi', 'New Road', 'active', 3, NOW()),
('Strawberry', 'स्ट्रबेरी', 2, 380.00, 'kg', 'Fresh strawberry', 'New Road', 'active', 2, NOW()),
('Avocado', 'एभोकाडो', 2, 420.00, 'kg', 'Ripe avocado', 'New Road', 'active', 4, NOW()),
('Plum', 'आलुबोखरा', 2, 250.00, 'kg', 'Red plum', 'Kalimati Fruit Market', 'active', 3, NOW()),
('Peach', 'आरु', 2, 280.00, 'kg', 'Fresh peach', 'Balkhu Market', 'active', 2, NOW()),
('Apricot', 'खुर्पानी', 2, 320.00, 'kg', 'Sweet apricot', 'Kalimati Fruit Market', 'active', 4, NOW()),
('Cherry', 'चेरी', 2, 550.00, 'kg', 'Red cherry', 'New Road', 'active', 3, NOW()),
('Blackberry', 'काँटो काफल', 2, 280.00, 'kg', 'Fresh blackberry', 'Balkhu Market', 'active', 2, NOW()),
('Coconut', 'नरिवल', 2, 60.00, 'piece', 'Mature coconut', 'Asan Bazaar', 'active', 4, NOW()),
('Sweet Lime', 'मुसम्बी', 2, 140.00, 'kg', 'Sweet lime', 'Kalimati Fruit Market', 'active', 2, NOW()),
('Custard Apple', 'सरिफा', 2, 200.00, 'kg', 'Ripe custard apple', 'Balkhu Market', 'active', 3, NOW()),
('Fig', 'अंजीर', 2, 380.00, 'kg', 'Dried fig', 'New Road', 'active', 2, NOW()),
('Melon', 'खरबुजा', 2, 50.00, 'kg', 'Sweet melon', 'Kalimati Fruit Market', 'active', 4, NOW()),
('Passion Fruit', 'प्यासन फल', 2, 420.00, 'kg', 'Fresh passion fruit', 'New Road', 'active', 3, NOW()),
('Date', 'खजुर', 2, 480.00, 'kg', 'Imported dates', 'New Road', 'active', 2, NOW()),

-- GROCERIES (25 products)
('Rice (Basmati)', 'बासमती चामल', 3, 120.00, 'kg', 'Long grain basmati', 'Asan Bazaar', 'active', 2, NOW()),
('Rice (Masino)', 'मासिनो चामल', 3, 85.00, 'kg', 'Fine rice', 'Balkhu Market', 'active', 3, NOW()),
('Rice (Atap)', 'अटप चामल', 3, 75.00, 'kg', 'Regular rice', 'Asan Bazaar', 'active', 2, NOW()),
('Wheat Flour', 'गहुँको पिठो', 3, 60.00, 'kg', 'Whole wheat flour', 'Balkhu Market', 'active', 4, NOW()),
('Corn Flour', 'मकैको पिठो', 3, 70.00, 'kg', 'Fine corn flour', 'Asan Bazaar', 'active', 2, NOW()),
('Lentils (Musuro)', 'मसुरो दाल', 3, 140.00, 'kg', 'Red lentils', 'Balkhu Market', 'active', 3, NOW()),
('Lentils (Moong)', 'मुंग दाल', 3, 160.00, 'kg', 'Green gram', 'Asan Bazaar', 'active', 2, NOW()),
('Chickpeas', 'चना', 3, 130.00, 'kg', 'Kabuli chana', 'Balkhu Market', 'active', 4, NOW()),
('Black Gram', 'कालो दाल', 3, 150.00, 'kg', 'Black lentils', 'Asan Bazaar', 'active', 3, NOW()),
('Kidney Beans', 'राजमा', 3, 180.00, 'kg', 'Red kidney beans', 'Balkhu Market', 'active', 2, NOW()),
('Cooking Oil (Sunflower)', 'सुर्यमुखी तेल', 3, 280.00, 'liter', 'Refined sunflower oil', 'Bhatbhateni Supermarket', 'active', 4, NOW()),
('Cooking Oil (Mustard)', 'तोरीको तेल', 3, 320.00, 'liter', 'Pure mustard oil', 'Asan Bazaar', 'active', 2, NOW()),
('Salt', 'नुन', 3, 25.00, 'kg', 'Iodized salt', 'Balkhu Market', 'active', 3, NOW()),
('Sugar', 'चिनी', 3, 80.00, 'kg', 'White sugar', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Tea Leaves', 'चिया पत्ती', 3, 420.00, 'kg', 'Premium tea leaves', 'Asan Bazaar', 'active', 4, NOW()),
('Coffee Powder', 'कफी', 3, 650.00, 'kg', 'Instant coffee', 'New Road', 'active', 3, NOW()),
('Milk Powder', 'दूध पाउडर', 3, 580.00, 'kg', 'Full cream milk powder', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Butter', 'माखन', 3, 520.00, 'kg', 'Fresh butter', 'Bhatbhateni Supermarket', 'active', 4, NOW()),
('Ghee (Clarified Butter)', 'घिउ', 3, 850.00, 'kg', 'Pure cow ghee', 'Asan Bazaar', 'active', 3, NOW()),
('Noodles', 'चाउचाउ', 3, 45.00, 'packet', 'Instant noodles pack', 'Balkhu Market', 'active', 2, NOW()),
('Biscuits', 'बिस्कुट', 3, 80.00, 'packet', 'Assorted biscuits', 'Bhatbhateni Supermarket', 'active', 4, NOW()),
('Bread', 'रोटी', 3, 60.00, 'loaf', 'White bread loaf', 'Balkhu Market', 'active', 2, NOW()),
('Jam', 'ज्याम', 3, 280.00, 'bottle', 'Mixed fruit jam', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Honey', 'मह', 3, 650.00, 'kg', 'Pure honey', 'Asan Bazaar', 'active', 2, NOW()),
('Vinegar', 'सिरका', 3, 120.00, 'bottle', 'White vinegar', 'Bhatbhateni Supermarket', 'active', 4, NOW()),

-- DAIRY PRODUCTS (15 products)
('Fresh Milk', 'ताजा दूध', 4, 75.00, 'liter', 'Full cream milk', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Curd (Yogurt)', 'दही', 4, 90.00, 'kg', 'Fresh curd', 'Balkhu Market', 'active', 3, NOW()),
('Paneer (Cottage Cheese)', 'पनिर', 4, 380.00, 'kg', 'Fresh paneer', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Cheese Slice', 'चीज', 4, 450.00, 'kg', 'Processed cheese', 'New Road', 'active', 4, NOW()),
('Cream', 'क्रिम', 4, 420.00, 'liter', 'Fresh cream', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Condensed Milk', 'गाढा दूध', 4, 320.00, 'tin', 'Sweetened condensed milk', 'New Road', 'active', 2, NOW()),
('Ice Cream', 'आइसक्रिम', 4, 280.00, 'liter', 'Vanilla ice cream', 'Bhatbhateni Supermarket', 'active', 4, NOW()),
('Butter Milk', 'मोही', 4, 50.00, 'liter', 'Fresh buttermilk', 'Balkhu Market', 'active', 2, NOW()),
('Flavored Milk', 'फ्लेवर दूध', 4, 85.00, 'liter', 'Chocolate milk', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Lassi', 'लस्सी', 4, 70.00, 'liter', 'Sweet lassi', 'Asan Bazaar', 'active', 2, NOW()),
('Mozzarella Cheese', 'मोजारेला चीज', 4, 680.00, 'kg', 'Pizza cheese', 'New Road', 'active', 4, NOW()),
('Cheddar Cheese', 'चेडर चीज', 4, 720.00, 'kg', 'Aged cheddar', 'New Road', 'active', 3, NOW()),
('Sour Cream', 'साउर क्रिम', 4, 380.00, 'kg', 'Fresh sour cream', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Khoa (Mawa)', 'खोवा', 4, 450.00, 'kg', 'Pure khoa', 'Asan Bazaar', 'active', 4, NOW()),
('Whipping Cream', 'ह्विपिङ क्रिम', 4, 520.00, 'liter', 'Heavy cream', 'New Road', 'active', 3, NOW()),

-- MEAT & FISH (15 products)
('Chicken', 'कुखुराको मासु', 5, 380.00, 'kg', 'Fresh chicken', 'Kalimati Market', 'active', 2, NOW()),
('Mutton', 'खसीको मासु', 5, 850.00, 'kg', 'Goat meat', 'Asan Bazaar', 'active', 3, NOW()),
('Buffalo Meat', 'भैंसीको मासु', 5, 620.00, 'kg', 'Buffalo meat', 'Balkhu Market', 'active', 2, NOW()),
('Pork', 'सुँगुरको मासु', 5, 520.00, 'kg', 'Fresh pork', 'Kalimati Market', 'active', 4, NOW()),
('Fish (Rohu)', 'रहु माछा', 5, 450.00, 'kg', 'Fresh rohu fish', 'Kalimati Market', 'active', 3, NOW()),
('Fish (Catla)', 'भकुर माछा', 5, 480.00, 'kg', 'Catla fish', 'Balkhu Market', 'active', 2, NOW()),
('Prawns', 'झिंगे माछा', 5, 950.00, 'kg', 'Fresh prawns', 'New Road', 'active', 4, NOW()),
('Eggs (Chicken)', 'अण्डा', 5, 180.00, 'dozen', 'Farm fresh eggs', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Duck Meat', 'हाँसको मासु', 5, 520.00, 'kg', 'Duck meat', 'Kalimati Market', 'active', 3, NOW()),
('Quail Eggs', 'बट्टाईको अण्डा', 5, 280.00, 'dozen', 'Quail eggs', 'New Road', 'active', 4, NOW()),
('Dry Fish', 'सुकेको माछा', 5, 750.00, 'kg', 'Dried fish', 'Asan Bazaar', 'active', 2, NOW()),
('Sausage', 'सॉसेज', 5, 650.00, 'kg', 'Chicken sausage', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Bacon', 'बेकन', 5, 780.00, 'kg', 'Smoked bacon', 'New Road', 'active', 4, NOW()),
('Salami', 'सलामी', 5, 850.00, 'kg', 'Beef salami', 'New Road', 'active', 2, NOW()),
('Liver (Chicken)', 'कलेजो', 5, 280.00, 'kg', 'Chicken liver', 'Kalimati Market', 'active', 3, NOW()),

-- SPICES (20 products)
('Turmeric Powder', 'बेसार', 6, 280.00, 'kg', 'Pure turmeric', 'Asan Bazaar', 'active', 2, NOW()),
('Red Chili Powder', 'रातो खुर्सानी', 6, 320.00, 'kg', 'Hot chili powder', 'Asan Bazaar', 'active', 3, NOW()),
('Coriander Powder', 'धनिया पाउडर', 6, 240.00, 'kg', 'Ground coriander', 'Balkhu Market', 'active', 2, NOW()),
('Cumin Seeds', 'जीरा', 6, 380.00, 'kg', 'Whole cumin', 'Asan Bazaar', 'active', 4, NOW()),
('Black Pepper', 'मरिच', 6, 950.00, 'kg', 'Whole black pepper', 'Asan Bazaar', 'active', 3, NOW()),
('Cardamom', 'अलैंची', 6, 2800.00, 'kg', 'Green cardamom', 'New Road', 'active', 2, NOW()),
('Cinnamon', 'दालचिनी', 6, 1200.00, 'kg', 'Cinnamon sticks', 'Asan Bazaar', 'active', 4, NOW()),
('Cloves', 'ल्वाङ', 6, 1800.00, 'kg', 'Whole cloves', 'New Road', 'active', 3, NOW()),
('Bay Leaves', 'तेजपात', 6, 320.00, 'kg', 'Dried bay leaves', 'Asan Bazaar', 'active', 2, NOW()),
('Fennel Seeds', 'सौंफ', 6, 280.00, 'kg', 'Fennel seeds', 'Balkhu Market', 'active', 4, NOW()),
('Fenugreek Seeds', 'मेथी', 6, 220.00, 'kg', 'Fenugreek seeds', 'Asan Bazaar', 'active', 3, NOW()),
('Mustard Seeds', 'तोरी', 6, 180.00, 'kg', 'Yellow mustard seeds', 'Balkhu Market', 'active', 2, NOW()),
('Sesame Seeds', 'तिल', 6, 320.00, 'kg', 'White sesame', 'Asan Bazaar', 'active', 4, NOW()),
('Ajwain (Carom Seeds)', 'ज्वानो', 6, 420.00, 'kg', 'Carom seeds', 'Asan Bazaar', 'active', 3, NOW()),
('Asafoetida', 'हिङ', 6, 1500.00, 'kg', 'Pure asafoetida', 'New Road', 'active', 2, NOW()),
('Star Anise', 'चक्रफुल', 6, 850.00, 'kg', 'Whole star anise', 'New Road', 'active', 4, NOW()),
('Nutmeg', 'जायफल', 6, 1200.00, 'kg', 'Whole nutmeg', 'New Road', 'active', 3, NOW()),
('Mace', 'जावित्री', 6, 1400.00, 'kg', 'Dried mace', 'New Road', 'active', 2, NOW()),
('Garam Masala', 'गरम मसला', 6, 480.00, 'kg', 'Mixed spice powder', 'Asan Bazaar', 'active', 4, NOW()),
('Curry Powder', 'करी मसला', 6, 380.00, 'kg', 'Curry powder mix', 'Balkhu Market', 'active', 3, NOW()),

-- HOUSEHOLD ITEMS (15 products)
('Detergent Powder', 'डिटर्जेन्ट', 7, 180.00, 'kg', 'Washing powder', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Soap Bar', 'साबुन', 7, 45.00, 'piece', 'Bathing soap', 'Balkhu Market', 'active', 3, NOW()),
('Shampoo', 'स्याम्पु', 7, 280.00, 'bottle', 'Hair shampoo', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Toothpaste', 'टूथपेस्ट', 7, 120.00, 'tube', 'Dental care', 'Balkhu Market', 'active', 4, NOW()),
('Toothbrush', 'दाँत ब्रस', 7, 35.00, 'piece', 'Soft bristle brush', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Toilet Paper', 'टिस्यु पेपर', 7, 150.00, 'roll', 'Soft tissue roll', 'Bhatbhateni Supermarket', 'active', 2, NOW()),
('Dish Soap', 'भाँडा साबुन', 7, 95.00, 'bottle', 'Dishwashing liquid', 'Balkhu Market', 'active', 4, NOW()),
('Floor Cleaner', 'फ्लोर क्लिनर', 7, 220.00, 'bottle', 'Floor cleaning liquid', 'Bhatbhateni Supermarket', 'active', 3, NOW()),
('Phenyl', 'फिनाइल', 7, 180.00, 'bottle', 'Disinfectant', 'Balkhu Market', 'active', 2, NOW()),
('Bleach', 'ब्लिच', 7, 150.00, 'bottle', 'Whitening bleach', 'Bhatbhateni Supermarket', 'active', 4, NOW()),
('Air Freshener', 'एयर फ्रेसनर', 7, 280.00, 'can', 'Room freshener', 'New Road', 'active', 3, NOW()),
('Mosquito Coil', 'धुपकाठी', 7, 35.00, 'packet', 'Mosquito repellent', 'Balkhu Market', 'active', 2, NOW()),
('Candles', 'मैनबत्ती', 7, 25.00, 'packet', 'Wax candles', 'Asan Bazaar', 'active', 4, NOW()),
('Match Box', 'सलाई', 7, 5.00, 'box', 'Safety matches', 'Balkhu Market', 'active', 3, NOW()),
('Garbage Bags', 'फोहर थैली', 7, 120.00, 'packet', 'Large garbage bags', 'Bhatbhateni Supermarket', 'active', 2, NOW());

-- Update status to add variety
UPDATE items SET status = 'inactive' WHERE item_id IN (SELECT item_id FROM (SELECT item_id FROM items ORDER BY RAND() LIMIT 5) AS temp);

-- Verification Query
SELECT 
    c.category_name,
    COUNT(i.item_id) as product_count,
    MIN(i.current_price) as min_price,
    MAX(i.current_price) as max_price,
    AVG(i.current_price) as avg_price
FROM items i
JOIN categories c ON i.category_id = c.category_id
GROUP BY c.category_id, c.category_name
ORDER BY c.category_name;

-- Final count
SELECT COUNT(*) as total_products, 
       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
       SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_products
FROM items;
