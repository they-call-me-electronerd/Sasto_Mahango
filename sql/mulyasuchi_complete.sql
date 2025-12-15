-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 03:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sastomahango_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_approve_validation` (IN `p_queue_id` INT UNSIGNED, IN `p_admin_id` INT UNSIGNED)   BEGIN
    DECLARE v_item_id INT UNSIGNED;
    DECLARE v_action_type VARCHAR(20);
    DECLARE v_old_price DECIMAL(10,2);
    DECLARE v_new_price DECIMAL(10,2);
    DECLARE v_item_name VARCHAR(255);
    DECLARE v_category_id INT UNSIGNED;
    DECLARE v_unit VARCHAR(20);
    DECLARE v_market_location VARCHAR(255);
    DECLARE v_description TEXT;
    DECLARE v_image_path VARCHAR(255);
    DECLARE v_current_price DECIMAL(10,2);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error approving validation';
    END;
    
    START TRANSACTION;
    
    
    SELECT item_id, action_type, old_price, new_price, item_name, category_id, 
           unit, market_location, description, image_path
    INTO v_item_id, v_action_type, v_old_price, v_new_price, v_item_name, 
         v_category_id, v_unit, v_market_location, v_description, v_image_path
    FROM validation_queue
    WHERE queue_id = p_queue_id AND status = 'pending';
    
    IF v_action_type = 'new_item' THEN
        
        INSERT INTO items (
            item_name, item_name_nepali, slug, category_id, base_price, current_price, unit,
            market_location, description, image_path, status,
            created_by, validated_by, validated_at
        )
        SELECT 
            item_name, item_name, 
            LOWER(REPLACE(REPLACE(item_name, ' ', '-'), ',', '')),
            category_id, new_price, new_price, unit,
            market_location, description, image_path, 'active',
            submitted_by, p_admin_id, NOW()
        FROM validation_queue
        WHERE queue_id = p_queue_id;
        
        SET v_item_id = LAST_INSERT_ID();
        
    ELSEIF v_action_type = 'price_update' THEN
        
        UPDATE items
        SET current_price = v_new_price,
            validated_by = p_admin_id,
            validated_at = NOW(),
            updated_at = NOW()
        WHERE item_id = v_item_id;
        
        
        INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
        VALUES (v_item_id, v_old_price, v_new_price, p_admin_id, NOW());
        
    ELSEIF v_action_type = 'item_edit' THEN
        
        SELECT current_price INTO v_current_price
        FROM items
        WHERE item_id = v_item_id;
        
        
        UPDATE items
        SET item_name = COALESCE(v_item_name, item_name),
            category_id = COALESCE(v_category_id, category_id),
            current_price = COALESCE(v_new_price, current_price),
            unit = COALESCE(v_unit, unit),
            market_location = COALESCE(v_market_location, market_location),
            description = COALESCE(v_description, description),
            image_path = COALESCE(v_image_path, image_path),
            slug = LOWER(REPLACE(REPLACE(COALESCE(v_item_name, item_name), ' ', '-'), ',', '')),
            validated_by = p_admin_id,
            validated_at = NOW(),
            updated_at = NOW()
        WHERE item_id = v_item_id;
        
        
        IF v_new_price IS NOT NULL AND v_new_price != v_current_price THEN
            INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
            VALUES (v_item_id, v_current_price, v_new_price, p_admin_id, NOW());
        END IF;
    END IF;
    
    
    UPDATE validation_queue
    SET status = 'approved',
        validated_by = p_admin_id,
        validated_at = NOW()
    WHERE queue_id = p_queue_id;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reject_validation` (IN `p_queue_id` INT UNSIGNED, IN `p_admin_id` INT UNSIGNED, IN `p_reason` TEXT)   BEGIN
    UPDATE validation_queue
    SET status = 'rejected',
        validated_by = p_admin_id,
        validated_at = NOW(),
        rejection_reason = p_reason
    WHERE queue_id = p_queue_id AND status = 'pending';
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_name_nepali` varchar(100) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_class` varchar(50) DEFAULT NULL,
  `display_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_name_nepali`, `slug`, `description`, `icon_class`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Vegetables', 'à¤¤à¤°à¤•à¤¾à¤°à¥€à¤¹à¤°à¥‚', 'vegetables', 'Fresh vegetables and produce', 'icon-vegetables', 1, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(2, 'Fruits', 'à¤«à¤²à¤«à¥‚à¤²à¤¹à¤°à¥‚', 'fruits', 'Fresh and seasonal fruits', 'icon-fruits', 2, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(3, 'Groceries', 'à¤•à¤¿à¤°à¤¾à¤¨à¤¾ à¤¸à¤¾à¤®à¤¾à¤¨', 'groceries', 'Essential grocery items', 'icon-groceries', 3, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(4, 'Dairy Products', 'à¤¦à¥à¤—à¥à¤§ à¤ªà¤¦à¤¾à¤°à¥à¤¥', 'dairy-products', 'Milk and dairy items', 'icon-dairy', 4, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(5, 'Meat & Fish', 'à¤®à¤¾à¤¸à¥ à¤° à¤®à¤¾à¤›à¤¾', 'meat-fish', 'Fresh meat and seafood', 'icon-meat', 5, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(6, 'Spices', 'à¤®à¤¸à¤²à¤¾', 'spices', 'Herbs and spices', 'icon-spices', 6, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(7, 'Kitchen Appliances', 'à¤­à¤¾à¤¨à¥à¤¸à¤¾à¤•à¤¾ à¤¸à¤¾à¤®à¤¾à¤¨à¤¹à¤°à¥‚', 'kitchen-appliances', 'Kitchen tools and appliances', 'icon-kitchen', 7, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(8, 'Household Items', 'à¤˜à¤°à¤¾à¤¯à¤¸à¥€ à¤¸à¤¾à¤®à¤¾à¤¨', 'household-items', 'Home care products', 'icon-household', 8, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(9, 'Electrical Appliances', 'à¤¬à¤¿à¤œà¥à¤²à¥€ à¤‰à¤ªà¤•à¤°à¤£à¤¹à¤°à¥‚', 'electrical-appliances', 'Electronic appliances', 'icon-electrical', 9, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(10, 'Clothing', 'à¤²à¥à¤—à¤¾à¤«à¤¾à¤Ÿà¤¾', 'clothing', 'Garments and accessories', 'icon-clothing', 10, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(11, 'Study Material', 'à¤…à¤§à¥à¤¯à¤¯à¤¨ à¤¸à¤¾à¤®à¤—à¥à¤°à¥€', 'study-material', 'Books and stationery', 'icon-study', 11, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29'),
(12, 'Tools & Hardware', 'à¤”à¤œà¤¾à¤° à¤° à¤¹à¤¾à¤°à¥à¤¡à¤µà¥‡à¤¯à¤°', 'tools-hardware', 'Hardware and tools', 'icon-tools', 12, 1, '2025-11-25 16:32:29', '2025-11-25 16:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `item_name_nepali` varchar(200) DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `unit` enum('kg','piece','liter','dozen','gram','pack','meter','sq_meter') NOT NULL DEFAULT 'piece',
  `market_location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg',
  `status` enum('active','pending','inactive') NOT NULL DEFAULT 'pending',
  `created_by` int(10) UNSIGNED NOT NULL,
  `validated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `validated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_name_nepali`, `slug`, `category_id`, `base_price`, `current_price`, `unit`, `market_location`, `description`, `image_path`, `status`, `created_by`, `validated_by`, `created_at`, `updated_at`, `validated_at`, `deleted_at`) VALUES
(1, 'Potato', '', 'potato', 1, 47.70, 53.00, 'kg', 'Asan Bazaar', 'Fresh potato available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(2, 'Tomato', '', 'tomato', 1, 71.10, 79.00, 'kg', 'Koteshwor Market', 'Fresh tomato available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(3, 'Onion', '', 'onion', 1, 64.80, 72.00, 'kg', 'Koteshwor Market', 'Fresh onion available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(4, 'Cauliflower', '', 'cauliflower', 1, 62.10, 69.00, 'kg', 'Local Market', 'Fresh cauliflower available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(5, 'Cabbage', '', 'cabbage', 1, 33.30, 37.00, 'kg', 'Koteshwor Market', 'Fresh cabbage available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(6, 'Carrot', '', 'carrot', 1, 87.30, 97.00, 'kg', 'Koteshwor Market', 'Fresh carrot available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(7, 'Spinach', '', 'spinach', 1, 38.70, 43.00, 'kg', 'Balkhu Market', 'Fresh spinach available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(8, 'Brinjal', '', 'brinjal', 1, 58.50, 65.00, 'kg', 'New Road', 'Fresh brinjal available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(9, 'Cucumber', '', 'cucumber', 1, 62.10, 69.00, 'kg', 'Local Market', 'Fresh cucumber available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(10, 'Green Chili', '', 'green-chili', 1, 113.40, 126.00, 'kg', 'Balkhu Market', 'Fresh green chili available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(11, 'Pumpkin', '', 'pumpkin', 1, 52.20, 58.00, 'kg', 'Kalimati Vegetable Market', 'Fresh pumpkin available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(12, 'Radish', '', 'radish', 1, 30.60, 34.00, 'kg', 'Balkhu Market', 'Fresh radish available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(13, 'Bitter Gourd', '', 'bitter-gourd', 1, 80.10, 89.00, 'kg', 'New Road', 'Fresh bitter gourd available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(14, 'Bottle Gourd', '', 'bottle-gourd', 1, 42.30, 47.00, 'kg', 'Balkhu Market', 'Fresh bottle gourd available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(15, 'Okra', '', 'okra', 1, 55.80, 62.00, 'kg', 'Koteshwor Market', 'Fresh okra available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(16, 'Green Beans', '', 'green-beans', 1, 89.10, 99.00, 'kg', 'Local Market', 'Fresh green beans available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(17, 'Sweet Potato', '', 'sweet-potato', 1, 70.20, 78.00, 'kg', 'Balkhu Market', 'Fresh sweet potato available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(18, 'Beetroot', '', 'beetroot', 1, 81.00, 90.00, 'kg', 'Balkhu Market', 'Fresh beetroot available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(19, 'Ginger', '', 'ginger', 1, 189.90, 211.00, 'kg', 'Kalimati Vegetable Market', 'Fresh ginger available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(20, 'Garlic', '', 'garlic', 1, 244.80, 272.00, 'kg', 'New Road', 'Fresh garlic available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(21, 'Mushroom', '', 'mushroom', 1, 239.40, 266.00, 'kg', 'Asan Bazaar', 'Fresh mushroom available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(22, 'Broccoli', '', 'broccoli', 1, 117.90, 131.00, 'kg', 'Kalimati Vegetable Market', 'Fresh broccoli available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(23, 'Lettuce', '', 'lettuce', 1, 111.60, 124.00, 'kg', 'Asan Bazaar', 'Fresh lettuce available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(24, 'Asparagus', '', 'asparagus', 1, 380.70, 423.00, 'kg', 'Asan Bazaar', 'Fresh asparagus available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(25, 'Bamboo Shoots', '', 'bamboo-shoots', 1, 99.90, 111.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh bamboo shoots available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(26, 'Gundruk', '', 'gundruk', 1, 225.90, 251.00, 'kg', 'New Road', 'Fresh gundruk available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(27, 'Soybean', '', 'soybean', 1, 133.20, 148.00, 'kg', 'Kalimati Vegetable Market', 'Fresh soybean available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(28, 'Apple', '', 'apple', 2, 248.40, 276.00, 'kg', 'Local Market', 'Fresh apple available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:16:35', NULL, NULL),
(29, 'Banana', '', 'banana', 2, 95.40, 106.00, 'dozen', 'Bhatbhateni Supermarket', 'Fresh banana available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(30, 'Orange', '', 'orange', 2, 90.90, 101.00, 'kg', 'Kalimati Vegetable Market', 'Fresh orange available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(31, 'Mango', '', 'mango', 2, 150.30, 167.00, 'kg', 'Local Market', 'Fresh mango available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(32, 'Grapes', '', 'grapes', 2, 259.20, 288.00, 'kg', 'Balkhu Market', 'Fresh grapes available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(33, 'Papaya', '', 'papaya', 2, 87.30, 97.00, 'kg', 'New Road', 'Fresh papaya available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(34, 'Pineapple', '', 'pineapple', 2, 100.80, 112.00, 'piece', 'New Road', 'Fresh pineapple available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(35, 'Watermelon', '', 'watermelon', 2, 41.40, 46.00, 'kg', 'Koteshwor Market', 'Fresh watermelon available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(36, 'Pomegranate', '', 'pomegranate', 2, 313.20, 348.00, 'kg', 'New Road', 'Fresh pomegranate available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(37, 'Guava', '', 'guava', 2, 81.00, 90.00, 'kg', 'Kalimati Vegetable Market', 'Fresh guava available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(38, 'Litchi', '', 'litchi', 2, 177.30, 197.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh litchi available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(39, 'Strawberry', '', 'strawberry', 2, 432.90, 481.00, 'kg', 'Koteshwor Market', 'Fresh strawberry available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(40, 'Kiwi', '', 'kiwi', 2, 282.60, 314.00, 'kg', 'Koteshwor Market', 'Fresh kiwi available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(41, 'Dragon Fruit', '', 'dragon-fruit', 2, 369.00, 410.00, 'kg', 'Balkhu Market', 'Fresh dragon fruit available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(42, 'Avocado', '', 'avocado', 2, 365.40, 406.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh avocado available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(43, 'Coconut', '', 'coconut', 2, 66.60, 74.00, 'piece', 'Bhatbhateni Supermarket', 'Fresh coconut available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(44, 'Lemon', '', 'lemon', 2, 14.40, 16.00, 'piece', 'Asan Bazaar', 'Fresh lemon available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(45, 'Pear', '', 'pear', 2, 94.50, 105.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh pear available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(46, 'Peach', '', 'peach', 2, 196.20, 218.00, 'kg', 'Asan Bazaar', 'Fresh peach available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(47, 'Plum', '', 'plum', 2, 197.10, 219.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh plum available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(48, 'Rice (Jeera Masino)', '', 'rice-jeera-masino', 3, 1480.50, 1645.00, 'pack', 'New Road', 'Fresh rice (jeera masino) available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(49, 'Rice (Sona Mansuli)', '', 'rice-sona-mansuli', 3, 1376.10, 1529.00, 'pack', 'Local Market', 'Fresh rice (sona mansuli) available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(50, 'Rice (Basmati)', '', 'rice-basmati', 3, 2492.10, 2769.00, 'pack', 'Kalimati Vegetable Market', 'Fresh rice (basmati) available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(51, 'Lentil (Masur)', '', 'lentil-masur', 3, 146.70, 163.00, 'kg', 'Koteshwor Market', 'Fresh lentil (masur) available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(52, 'Lentil (Moong)', '', 'lentil-moong', 3, 148.50, 165.00, 'kg', 'Kalimati Vegetable Market', 'Fresh lentil (moong) available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(53, 'Lentil (Rahar)', '', 'lentil-rahar', 3, 179.10, 199.00, 'kg', 'Balkhu Market', 'Fresh lentil (rahar) available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(54, 'Chickpeas', '', 'chickpeas', 3, 111.60, 124.00, 'kg', 'New Road', 'Fresh chickpeas available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(55, 'Kidney Beans', '', 'kidney-beans', 3, 165.60, 184.00, 'kg', 'Asan Bazaar', 'Fresh kidney beans available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(56, 'Black Gram', '', 'black-gram', 3, 140.40, 156.00, 'kg', 'Local Market', 'Fresh black gram available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(57, 'Sugar', '', 'sugar', 3, 89.10, 99.00, 'kg', 'New Road', 'Fresh sugar available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(58, 'Salt', '', 'salt', 3, 24.30, 27.00, 'pack', 'Balkhu Market', 'Fresh salt available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(59, 'Flour (Atta)', '', 'flour-atta', 3, 54.90, 61.00, 'kg', 'Koteshwor Market', 'Fresh flour (atta) available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(60, 'Flour (Maida)', '', 'flour-maida', 3, 54.00, 60.00, 'kg', 'Local Market', 'Fresh flour (maida) available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(61, 'Oil (Sunflower)', '', 'oil-sunflower', 3, 202.50, 225.00, 'liter', 'New Road', 'Fresh oil (sunflower) available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(62, 'Oil (Mustard)', '', 'oil-mustard', 3, 275.40, 306.00, 'liter', 'Balkhu Market', 'Fresh oil (mustard) available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(63, 'Oil (Soybean)', '', 'oil-soybean', 3, 167.40, 186.00, 'liter', 'Asan Bazaar', 'Fresh oil (soybean) available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(64, 'Ghee', '', 'ghee', 3, 810.00, 900.00, 'liter', 'Kalimati Vegetable Market', 'Fresh ghee available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(65, 'Tea Leaves', '', 'tea-leaves', 3, 358.20, 398.00, 'kg', 'Koteshwor Market', 'Fresh tea leaves available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(66, 'Coffee', '', 'coffee', 3, 576.00, 640.00, 'pack', 'Asan Bazaar', 'Fresh coffee available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(67, 'Noodles', '', 'noodles', 3, 22.50, 25.00, 'pack', 'Kalimati Vegetable Market', 'Fresh noodles available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(68, 'Biscuits', '', 'biscuits', 3, 20.70, 23.00, 'pack', 'Bhatbhateni Supermarket', 'Fresh biscuits available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(69, 'Beaten Rice', '', 'beaten-rice', 3, 79.20, 88.00, 'kg', 'Koteshwor Market', 'Fresh beaten rice available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(70, 'Milk', '', 'milk', 4, 72.00, 80.00, 'liter', 'Asan Bazaar', 'Fresh milk available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(71, 'Curd', '', 'curd', 4, 128.70, 143.00, 'liter', 'Local Market', 'Fresh curd available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(72, 'Paneer', '', 'paneer', 4, 717.30, 797.00, 'kg', 'New Road', 'Fresh paneer available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(73, 'Butter', '', 'butter', 4, 746.10, 829.00, 'kg', 'Local Market', 'Fresh butter available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(74, 'Cheese', '', 'cheese', 4, 945.00, 1050.00, 'kg', 'Kalimati Vegetable Market', 'Fresh cheese available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(75, 'Cream', '', 'cream', 4, 392.40, 436.00, 'liter', 'Kalimati Vegetable Market', 'Fresh cream available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(76, 'Ice Cream', '', 'ice-cream', 4, 154.80, 172.00, 'pack', 'Balkhu Market', 'Fresh ice cream available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(77, 'Chhurpi', '', 'chhurpi', 4, 786.60, 874.00, 'kg', 'New Road', 'Fresh chhurpi available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(78, 'Chicken', '', 'chicken', 5, 282.60, 314.00, 'kg', 'Asan Bazaar', 'Fresh chicken available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(79, 'Mutton', '', 'mutton', 5, 967.50, 1075.00, 'kg', 'Local Market', 'Fresh mutton available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(80, 'Buff', '', 'buff', 5, 383.40, 426.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh buff available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(81, 'Pork', '', 'pork', 5, 523.80, 582.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh pork available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(82, 'Fish (Rohu)', '', 'fish-rohu', 5, 404.10, 449.00, 'kg', 'Asan Bazaar', 'Fresh fish (rohu) available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(83, 'Fish (Bachhwa)', '', 'fish-bachhwa', 5, 462.60, 514.00, 'kg', 'Balkhu Market', 'Fresh fish (bachhwa) available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(84, 'Eggs', '', 'eggs', 5, 350.10, 389.00, '', 'Koteshwor Market', 'Fresh eggs available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(85, 'Sausage', '', 'sausage', 5, 419.40, 466.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh sausage available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(86, 'Cumin Seeds', '', 'cumin-seeds', 6, 462.60, 514.00, 'kg', 'Local Market', 'Fresh cumin seeds available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(87, 'Coriander Seeds', '', 'coriander-seeds', 6, 250.20, 278.00, 'kg', 'Koteshwor Market', 'Fresh coriander seeds available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(88, 'Turmeric Powder', '', 'turmeric-powder', 6, 232.20, 258.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh turmeric powder available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(89, 'Chili Powder', '', 'chili-powder', 6, 277.20, 308.00, 'kg', 'Kalimati Vegetable Market', 'Fresh chili powder available at Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(90, 'Cardamom (Small)', '', 'cardamom-small', 6, 2381.40, 2646.00, 'kg', 'New Road', 'Fresh cardamom (small) available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(91, 'Cardamom (Large)', '', 'cardamom-large', 6, 1134.90, 1261.00, 'kg', 'Local Market', 'Fresh cardamom (large) available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(92, 'Cloves', '', 'cloves', 6, 1503.00, 1670.00, 'kg', 'Koteshwor Market', 'Fresh cloves available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(93, 'Cinnamon', '', 'cinnamon', 6, 488.70, 543.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh cinnamon available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(94, 'Black Pepper', '', 'black-pepper', 6, 1023.30, 1137.00, 'kg', 'Asan Bazaar', 'Fresh black pepper available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(95, 'Fenugreek', '', 'fenugreek', 6, 171.00, 190.00, 'kg', 'Balkhu Market', 'Fresh fenugreek available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(96, 'Mustard Seeds', '', 'mustard-seeds', 6, 100.80, 112.00, 'kg', 'Balkhu Market', 'Fresh mustard seeds available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(97, 'Soap', '', 'soap', 7, 51.30, 57.00, 'piece', 'Asan Bazaar', 'Fresh soap available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(98, 'Detergent', '', 'detergent', 7, 90.90, 101.00, 'kg', 'Koteshwor Market', 'Fresh detergent available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(99, 'Toothpaste', '', 'toothpaste', 7, 53.10, 59.00, 'pack', 'Koteshwor Market', 'Fresh toothpaste available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(100, 'Toothbrush', '', 'toothbrush', 7, 66.60, 74.00, 'piece', 'Asan Bazaar', 'Fresh toothbrush available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(101, 'Shampoo', '', 'shampoo', 7, 213.30, 237.00, 'pack', 'Koteshwor Market', 'Fresh shampoo available at Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(102, 'Conditioner', '', 'conditioner', 7, 320.40, 356.00, 'pack', 'Asan Bazaar', 'Fresh conditioner available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(103, 'Face Wash', '', 'face-wash', 7, 143.10, 159.00, 'pack', 'Balkhu Market', 'Fresh face wash available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(104, 'Body Lotion', '', 'body-lotion', 7, 253.80, 282.00, 'pack', 'Local Market', 'Fresh body lotion available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(105, 'Dishwash Bar', '', 'dishwash-bar', 7, 33.30, 37.00, 'piece', 'Balkhu Market', 'Fresh dishwash bar available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(106, 'Floor Cleaner', '', 'floor-cleaner', 7, 104.40, 116.00, 'liter', 'Bhatbhateni Supermarket', 'Fresh floor cleaner available at Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(107, 'Toilet Cleaner', '', 'toilet-cleaner', 7, 191.70, 213.00, 'pack', 'Local Market', 'Fresh toilet cleaner available at Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(108, 'Broom', '', 'broom', 7, 128.70, 143.00, 'piece', 'New Road', 'Fresh broom available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(109, 'Premium Chhurpi', '', 'premium-chhurpi-505', 4, 1275.30, 1417.00, 'kg', 'Koteshwor Market', 'Premium quality chhurpi from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(110, 'Imported Dragon Fruit', '', 'imported-dragon-fruit-715', 2, 567.00, 630.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality dragon fruit from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(111, 'Standard Black Pepper', '', 'standard-black-pepper-663', 6, 1005.30, 1117.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality black pepper from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(112, 'Local Sausage', '', 'local-sausage-834', 5, 366.30, 407.00, 'kg', 'Balkhu Market', 'Local quality sausage from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(113, 'Fresh Watermelon', '', 'fresh-watermelon-871', 2, 43.20, 48.00, 'kg', 'Local Market', 'Fresh quality watermelon from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(114, 'Bulk Lentil (Masur)', '', 'bulk-lentil-masur-708', 3, 145.80, 162.00, 'kg', 'Local Market', 'Bulk quality lentil (masur) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(115, 'Organic Butter', '', 'organic-butter-344', 4, 909.00, 1010.00, 'kg', 'Balkhu Market', 'Organic quality butter from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(116, 'Fresh Lentil (Masur)', '', 'fresh-lentil-masur-229', 3, 156.60, 174.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality lentil (masur) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(117, 'Standard Face Wash', '', 'standard-face-wash-260', 7, 258.30, 287.00, 'pack', 'Local Market', 'Standard quality face wash from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(118, 'Standard Coriander Seeds', '', 'standard-coriander-seeds-414', 6, 216.90, 241.00, 'kg', 'Local Market', 'Standard quality coriander seeds from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(119, 'Imported Pomegranate', '', 'imported-pomegranate-210', 2, 374.40, 416.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality pomegranate from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(120, 'Organic Buff', '', 'organic-buff-188', 5, 720.00, 800.00, 'kg', 'Kalimati Vegetable Market', 'Organic quality buff from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(121, 'Standard Grapes', '', 'standard-grapes-997', 2, 270.00, 300.00, 'kg', 'New Road', 'Standard quality grapes from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(122, 'Premium Strawberry', '', 'premium-strawberry-953', 2, 497.70, 553.00, 'kg', 'Local Market', 'Premium quality strawberry from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(123, 'Imported Fish (Rohu)', '', 'imported-fish-rohu-933', 5, 495.00, 550.00, 'kg', 'New Road', 'Imported quality fish (rohu) from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(124, 'Fresh Fish (Bachhwa)', '', 'fresh-fish-bachhwa-524', 5, 536.40, 596.00, 'kg', 'Asan Bazaar', 'Fresh quality fish (bachhwa) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(125, 'Organic Chili Powder', '', 'organic-chili-powder-635', 6, 553.50, 615.00, 'kg', 'Asan Bazaar', 'Organic quality chili powder from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(126, 'Standard Ice Cream', '', 'standard-ice-cream-231', 4, 132.30, 147.00, 'pack', 'Bhatbhateni Supermarket', 'Standard quality ice cream from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(127, 'Standard Cheese', '', 'standard-cheese-395', 4, 1314.00, 1460.00, 'kg', 'Asan Bazaar', 'Standard quality cheese from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(128, 'Organic Paneer', '', 'organic-paneer-816', 4, 775.80, 862.00, 'kg', 'Bhatbhateni Supermarket', 'Organic quality paneer from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(129, 'Bulk Oil (Sunflower)', '', 'bulk-oil-sunflower-246', 3, 160.20, 178.00, 'liter', 'Bhatbhateni Supermarket', 'Bulk quality oil (sunflower) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(130, 'Local Soybean', '', 'local-soybean-693', 1, 99.90, 111.00, 'kg', 'Asan Bazaar', 'Local quality soybean from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(131, 'Premium Fish (Rohu)', '', 'premium-fish-rohu-353', 5, 366.30, 407.00, 'kg', 'Local Market', 'Premium quality fish (rohu) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(132, 'Imported Chicken', '', 'imported-chicken-356', 5, 602.10, 669.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality chicken from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(133, 'Standard Mutton', '', 'standard-mutton-557', 5, 1178.10, 1309.00, 'kg', 'Balkhu Market', 'Standard quality mutton from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(134, 'Premium Black Pepper', '', 'premium-black-pepper-220', 6, 1155.60, 1284.00, 'kg', 'New Road', 'Premium quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(135, 'Standard Fenugreek', '', 'standard-fenugreek-127', 6, 139.50, 155.00, 'kg', 'New Road', 'Standard quality fenugreek from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(136, 'Standard Sausage', '', 'standard-sausage-387', 5, 468.00, 520.00, 'kg', 'Asan Bazaar', 'Standard quality sausage from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(137, 'Standard Chili Powder', '', 'standard-chili-powder-470', 6, 363.60, 404.00, 'kg', 'Kalimati Vegetable Market', 'Standard quality chili powder from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(138, 'Premium Okra', '', 'premium-okra-519', 1, 84.60, 94.00, 'kg', 'Koteshwor Market', 'Premium quality okra from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(139, 'Imported Broom', '', 'imported-broom-348', 7, 134.10, 149.00, 'piece', 'Balkhu Market', 'Imported quality broom from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(140, 'Imported Lentil (Masur)', '', 'imported-lentil-masur-311', 3, 173.70, 193.00, 'kg', 'Local Market', 'Imported quality lentil (masur) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(141, 'Imported Cinnamon', '', 'imported-cinnamon-518', 6, 775.80, 862.00, 'kg', 'Local Market', 'Imported quality cinnamon from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(142, 'Local Potato', '', 'local-potato-465', 1, 36.00, 40.00, 'kg', 'Asan Bazaar', 'Local quality potato from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(143, 'Premium Toothpaste', '', 'premium-toothpaste-190', 7, 128.70, 143.00, 'pack', 'Balkhu Market', 'Premium quality toothpaste from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(144, 'Local Cauliflower', '', 'local-cauliflower-932', 1, 48.60, 54.00, 'kg', 'Local Market', 'Local quality cauliflower from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(145, 'Bulk Ice Cream', '', 'bulk-ice-cream-535', 4, 114.30, 127.00, 'pack', 'Bhatbhateni Supermarket', 'Bulk quality ice cream from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(146, 'Local Ghee', '', 'local-ghee-796', 3, 873.90, 971.00, 'liter', 'Bhatbhateni Supermarket', 'Local quality ghee from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(147, 'Organic Tea Leaves', '', 'organic-tea-leaves-391', 3, 651.60, 724.00, 'kg', 'Koteshwor Market', 'Organic quality tea leaves from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(148, 'Fresh Asparagus', '', 'fresh-asparagus-152', 1, 395.10, 439.00, 'kg', 'Balkhu Market', 'Fresh quality asparagus from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(149, 'Organic Coconut', '', 'organic-coconut-761', 2, 54.00, 60.00, 'piece', 'Local Market', 'Organic quality coconut from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(150, 'Fresh Cardamom (Large)', '', 'fresh-cardamom-large-260', 6, 949.50, 1055.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality cardamom (large) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(151, 'Local Curd', '', 'local-curd-128', 4, 108.00, 120.00, 'liter', 'Kalimati Vegetable Market', 'Local quality curd from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(152, 'Premium Carrot', '', 'premium-carrot-482', 1, 65.70, 73.00, 'kg', 'New Road', 'Premium quality carrot from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(153, 'Imported Black Pepper', '', 'imported-black-pepper-314', 6, 1248.30, 1387.00, 'kg', 'New Road', 'Imported quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(154, 'Imported Oil (Mustard)', '', 'imported-oil-mustard-154', 3, 396.90, 441.00, 'liter', 'Kalimati Vegetable Market', 'Imported quality oil (mustard) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(155, 'Standard Mustard Seeds', '', 'standard-mustard-seeds-835', 6, 128.70, 143.00, 'kg', 'New Road', 'Standard quality mustard seeds from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(156, 'Organic Pomegranate', '', 'organic-pomegranate-356', 2, 392.40, 436.00, 'kg', 'Balkhu Market', 'Organic quality pomegranate from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(157, 'Organic Shampoo', '', 'organic-shampoo-174', 7, 497.70, 553.00, 'pack', 'New Road', 'Organic quality shampoo from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(158, 'Standard Brinjal', '', 'standard-brinjal-963', 1, 61.20, 68.00, 'kg', 'Balkhu Market', 'Standard quality brinjal from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(159, 'Imported Toilet Cleaner', '', 'imported-toilet-cleaner-403', 7, 177.30, 197.00, 'pack', 'Balkhu Market', 'Imported quality toilet cleaner from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(160, 'Imported Chickpeas', '', 'imported-chickpeas-378', 3, 161.10, 179.00, 'kg', 'Balkhu Market', 'Imported quality chickpeas from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(161, 'Standard Broom', '', 'standard-broom-596', 7, 163.80, 182.00, 'piece', 'Local Market', 'Standard quality broom from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(162, 'Premium Cumin Seeds', '', 'premium-cumin-seeds-157', 6, 706.50, 785.00, 'kg', 'New Road', 'Premium quality cumin seeds from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(163, 'Imported Pork', '', 'imported-pork-531', 5, 679.50, 755.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality pork from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(164, 'Standard Turmeric Powder', '', 'standard-turmeric-powder-513', 6, 236.70, 263.00, 'kg', 'Balkhu Market', 'Standard quality turmeric powder from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(165, 'Standard Cream', '', 'standard-cream-653', 4, 504.90, 561.00, 'liter', 'Balkhu Market', 'Standard quality cream from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(166, 'Organic Ghee', '', 'organic-ghee-334', 3, 999.00, 1110.00, 'liter', 'Balkhu Market', 'Organic quality ghee from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(167, 'Premium Toilet Cleaner', '', 'premium-toilet-cleaner-168', 7, 189.00, 210.00, 'pack', 'New Road', 'Premium quality toilet cleaner from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(168, 'Bulk Dishwash Bar', '', 'bulk-dishwash-bar-636', 7, 34.20, 38.00, 'piece', 'Bhatbhateni Supermarket', 'Bulk quality dishwash bar from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(169, 'Standard Green Chili', '', 'standard-green-chili-532', 1, 113.40, 126.00, 'kg', 'Balkhu Market', 'Standard quality green chili from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL);
INSERT INTO `items` (`item_id`, `item_name`, `item_name_nepali`, `slug`, `category_id`, `base_price`, `current_price`, `unit`, `market_location`, `description`, `image_path`, `status`, `created_by`, `validated_by`, `created_at`, `updated_at`, `validated_at`, `deleted_at`) VALUES
(170, 'Local Coffee', '', 'local-coffee-778', 3, 482.40, 536.00, 'pack', 'Kalimati Vegetable Market', 'Local quality coffee from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(171, 'Fresh Carrot', '', 'fresh-carrot-792', 1, 69.30, 77.00, 'kg', 'Asan Bazaar', 'Fresh quality carrot from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(172, 'Standard Coriander Seeds', '', 'standard-coriander-seeds-925', 6, 188.10, 209.00, 'kg', 'Koteshwor Market', 'Standard quality coriander seeds from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(173, 'Organic Ice Cream', '', 'organic-ice-cream-965', 4, 130.50, 145.00, 'pack', 'Kalimati Vegetable Market', 'Organic quality ice cream from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(174, 'Organic Avocado', '', 'organic-avocado-787', 2, 527.40, 586.00, 'kg', 'Asan Bazaar', 'Organic quality avocado from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(175, 'Local Coriander Seeds', '', 'local-coriander-seeds-547', 6, 195.30, 217.00, 'kg', 'New Road', 'Local quality coriander seeds from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(176, 'Standard Toothpaste', '', 'standard-toothpaste-980', 7, 65.70, 73.00, 'pack', 'Kalimati Vegetable Market', 'Standard quality toothpaste from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(177, 'Standard Face Wash', '', 'standard-face-wash-674', 7, 137.70, 153.00, 'pack', 'New Road', 'Standard quality face wash from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(178, 'Fresh Mutton', '', 'fresh-mutton-920', 5, 1116.90, 1241.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality mutton from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(179, 'Bulk Watermelon', '', 'bulk-watermelon-828', 2, 43.20, 48.00, 'kg', 'Asan Bazaar', 'Bulk quality watermelon from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(180, 'Standard Black Gram', '', 'standard-black-gram-235', 3, 161.10, 179.00, 'kg', 'Local Market', 'Standard quality black gram from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(181, 'Standard Milk', '', 'standard-milk-802', 4, 72.00, 80.00, 'liter', 'Balkhu Market', 'Standard quality milk from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(182, 'Organic Noodles', '', 'organic-noodles-567', 3, 35.10, 39.00, 'pack', 'Asan Bazaar', 'Organic quality noodles from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(183, 'Imported Butter', '', 'imported-butter-140', 4, 1155.60, 1284.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality butter from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(184, 'Imported Pear', '', 'imported-pear-149', 2, 134.10, 149.00, 'kg', 'New Road', 'Imported quality pear from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(185, 'Bulk Black Pepper', '', 'bulk-black-pepper-812', 6, 692.10, 769.00, 'kg', 'New Road', 'Bulk quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(186, 'Imported Milk', '', 'imported-milk-790', 4, 108.00, 120.00, 'liter', 'Koteshwor Market', 'Imported quality milk from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(187, 'Fresh Cloves', '', 'fresh-cloves-423', 6, 1827.90, 2031.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality cloves from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(188, 'Fresh Biscuits', '', 'fresh-biscuits-864', 3, 38.70, 43.00, 'pack', 'New Road', 'Fresh quality biscuits from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(189, 'Bulk Ice Cream', '', 'bulk-ice-cream-650', 4, 86.40, 96.00, 'pack', 'Koteshwor Market', 'Bulk quality ice cream from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(190, 'Premium Conditioner', '', 'premium-conditioner-717', 7, 518.40, 576.00, 'pack', 'Bhatbhateni Supermarket', 'Premium quality conditioner from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(191, 'Standard Litchi', '', 'standard-litchi-630', 2, 144.00, 160.00, 'kg', 'Balkhu Market', 'Standard quality litchi from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(192, 'Organic Carrot', '', 'organic-carrot-264', 1, 126.90, 141.00, 'kg', 'Local Market', 'Organic quality carrot from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(193, 'Bulk Paneer', '', 'bulk-paneer-328', 4, 567.00, 630.00, 'kg', 'Asan Bazaar', 'Bulk quality paneer from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(194, 'Fresh Toothpaste', '', 'fresh-toothpaste-768', 7, 102.60, 114.00, 'pack', 'Bhatbhateni Supermarket', 'Fresh quality toothpaste from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(195, 'Local Sugar', '', 'local-sugar-554', 3, 75.60, 84.00, 'kg', 'Kalimati Vegetable Market', 'Local quality sugar from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(196, 'Fresh Grapes', '', 'fresh-grapes-197', 2, 190.80, 212.00, 'kg', 'New Road', 'Fresh quality grapes from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(197, 'Local Banana', '', 'local-banana-940', 2, 97.20, 108.00, 'dozen', 'Asan Bazaar', 'Local quality banana from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(198, 'Imported Chhurpi', '', 'imported-chhurpi-436', 4, 1578.60, 1754.00, 'kg', 'Koteshwor Market', 'Imported quality chhurpi from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(199, 'Standard Pork', '', 'standard-pork-412', 5, 416.70, 463.00, 'kg', 'New Road', 'Standard quality pork from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(200, 'Bulk Spinach', '', 'bulk-spinach-781', 1, 33.30, 37.00, 'kg', 'Local Market', 'Bulk quality spinach from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(201, 'Imported Salt', '', 'imported-salt-326', 3, 36.90, 41.00, 'pack', 'Local Market', 'Imported quality salt from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(202, 'Bulk Sausage', '', 'bulk-sausage-276', 5, 375.30, 417.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality sausage from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(203, 'Organic Rice (Jeera Masino)', '', 'organic-rice-jeera-masino-790', 3, 2088.90, 2321.00, 'pack', 'Kalimati Vegetable Market', 'Organic quality rice (jeera masino) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(204, 'Standard Kiwi', '', 'standard-kiwi-754', 2, 403.20, 448.00, 'kg', 'Local Market', 'Standard quality kiwi from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(205, 'Imported Pumpkin', '', 'imported-pumpkin-634', 1, 80.10, 89.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality pumpkin from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(206, 'Imported Kiwi', '', 'imported-kiwi-651', 2, 578.70, 643.00, 'kg', 'Balkhu Market', 'Imported quality kiwi from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(207, 'Organic Body Lotion', '', 'organic-body-lotion-595', 7, 385.20, 428.00, 'pack', 'Asan Bazaar', 'Organic quality body lotion from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(208, 'Local Black Pepper', '', 'local-black-pepper-691', 6, 1051.20, 1168.00, 'kg', 'Balkhu Market', 'Local quality black pepper from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(209, 'Organic Bottle Gourd', '', 'organic-bottle-gourd-507', 1, 80.10, 89.00, 'kg', 'Balkhu Market', 'Organic quality bottle gourd from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(210, 'Premium Cheese', '', 'premium-cheese-801', 4, 1345.50, 1495.00, 'kg', 'Koteshwor Market', 'Premium quality cheese from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(211, 'Imported Coconut', '', 'imported-coconut-347', 2, 59.40, 66.00, 'piece', 'Balkhu Market', 'Imported quality coconut from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(212, 'Standard Mutton', '', 'standard-mutton-380', 5, 1258.20, 1398.00, 'kg', 'Koteshwor Market', 'Standard quality mutton from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(213, 'Local Cucumber', '', 'local-cucumber-380', 1, 64.80, 72.00, 'kg', 'New Road', 'Local quality cucumber from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(214, 'Fresh Milk', '', 'fresh-milk-762', 4, 87.30, 97.00, 'liter', 'Bhatbhateni Supermarket', 'Fresh quality milk from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(215, 'Premium Pork', '', 'premium-pork-717', 5, 555.30, 617.00, 'kg', 'New Road', 'Premium quality pork from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(216, 'Fresh Eggs', '', 'fresh-eggs-322', 5, 333.90, 371.00, '', 'Balkhu Market', 'Fresh quality eggs from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(217, 'Imported Broom', '', 'imported-broom-550', 7, 126.00, 140.00, 'piece', 'New Road', 'Imported quality broom from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(218, 'Standard Lemon', '', 'standard-lemon-417', 2, 14.40, 16.00, 'piece', 'New Road', 'Standard quality lemon from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(219, 'Standard Butter', '', 'standard-butter-665', 4, 851.40, 946.00, 'kg', 'Asan Bazaar', 'Standard quality butter from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(220, 'Standard Eggs', '', 'standard-eggs-143', 5, 333.00, 370.00, '', 'Asan Bazaar', 'Standard quality eggs from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(221, 'Organic Rice (Basmati)', '', 'organic-rice-basmati-390', 3, 2934.00, 3260.00, 'pack', 'Kalimati Vegetable Market', 'Organic quality rice (basmati) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(222, 'Fresh Eggs', '', 'fresh-eggs-223', 5, 317.70, 353.00, '', 'Bhatbhateni Supermarket', 'Fresh quality eggs from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(223, 'Premium Paneer', '', 'premium-paneer-559', 4, 742.50, 825.00, 'kg', 'Koteshwor Market', 'Premium quality paneer from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(224, 'Bulk Noodles', '', 'bulk-noodles-145', 3, 17.10, 19.00, 'pack', 'Asan Bazaar', 'Bulk quality noodles from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(225, 'Standard Fish (Bachhwa)', '', 'standard-fish-bachhwa-769', 5, 478.80, 532.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality fish (bachhwa) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(226, 'Premium Chili Powder', '', 'premium-chili-powder-117', 6, 624.60, 694.00, 'kg', 'New Road', 'Premium quality chili powder from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(227, 'Fresh Dishwash Bar', '', 'fresh-dishwash-bar-146', 7, 27.00, 30.00, 'piece', 'Kalimati Vegetable Market', 'Fresh quality dishwash bar from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(228, 'Imported Fish (Rohu)', '', 'imported-fish-rohu-732', 5, 437.40, 486.00, 'kg', 'Asan Bazaar', 'Imported quality fish (rohu) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(229, 'Bulk Rice (Basmati)', '', 'bulk-rice-basmati-712', 3, 2428.20, 2698.00, 'pack', 'Koteshwor Market', 'Bulk quality rice (basmati) from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(230, 'Fresh Avocado', '', 'fresh-avocado-736', 2, 389.70, 433.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality avocado from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(231, 'Bulk Strawberry', '', 'bulk-strawberry-985', 2, 385.20, 428.00, 'kg', 'Bhatbhateni Supermarket', 'Bulk quality strawberry from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(232, 'Local Carrot', '', 'local-carrot-670', 1, 89.10, 99.00, 'kg', 'Koteshwor Market', 'Local quality carrot from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(233, 'Local Shampoo', '', 'local-shampoo-214', 7, 365.40, 406.00, 'pack', 'New Road', 'Local quality shampoo from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(234, 'Bulk Gundruk', '', 'bulk-gundruk-422', 1, 153.90, 171.00, 'kg', 'New Road', 'Bulk quality gundruk from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(235, 'Bulk Pork', '', 'bulk-pork-778', 5, 338.40, 376.00, 'kg', 'Asan Bazaar', 'Bulk quality pork from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(236, 'Imported Detergent', '', 'imported-detergent-449', 7, 139.50, 155.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality detergent from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(237, 'Premium Eggs', '', 'premium-eggs-872', 5, 429.30, 477.00, '', 'Koteshwor Market', 'Premium quality eggs from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(238, 'Organic Kidney Beans', '', 'organic-kidney-beans-996', 3, 241.20, 268.00, 'kg', 'Asan Bazaar', 'Organic quality kidney beans from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(239, 'Local Toilet Cleaner', '', 'local-toilet-cleaner-125', 7, 201.60, 224.00, 'pack', 'Asan Bazaar', 'Local quality toilet cleaner from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(240, 'Local Bamboo Shoots', '', 'local-bamboo-shoots-176', 1, 75.60, 84.00, 'kg', 'Koteshwor Market', 'Local quality bamboo shoots from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(241, 'Bulk Soybean', '', 'bulk-soybean-212', 1, 78.30, 87.00, 'kg', 'Koteshwor Market', 'Bulk quality soybean from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(242, 'Standard Mushroom', '', 'standard-mushroom-549', 1, 193.50, 215.00, 'kg', 'Koteshwor Market', 'Standard quality mushroom from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(243, 'Fresh Lentil (Moong)', '', 'fresh-lentil-moong-867', 3, 197.10, 219.00, 'kg', 'Balkhu Market', 'Fresh quality lentil (moong) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(244, 'Fresh Pear', '', 'fresh-pear-728', 2, 125.10, 139.00, 'kg', 'Local Market', 'Fresh quality pear from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(245, 'Bulk Cardamom (Small)', '', 'bulk-cardamom-small-335', 6, 1446.30, 1607.00, 'kg', 'Local Market', 'Bulk quality cardamom (small) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(246, 'Local Spinach', '', 'local-spinach-487', 1, 41.40, 46.00, 'kg', 'New Road', 'Local quality spinach from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(247, 'Standard Curd', '', 'standard-curd-826', 4, 124.20, 138.00, 'liter', 'Bhatbhateni Supermarket', 'Standard quality curd from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(248, 'Fresh Flour (Maida)', '', 'fresh-flour-maida-243', 3, 55.80, 62.00, 'kg', 'Asan Bazaar', 'Fresh quality flour (maida) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(249, 'Premium Mustard Seeds', '', 'premium-mustard-seeds-617', 6, 179.10, 199.00, 'kg', 'New Road', 'Premium quality mustard seeds from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(250, 'Standard Paneer', '', 'standard-paneer-458', 4, 619.20, 688.00, 'kg', 'New Road', 'Standard quality paneer from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(251, 'Bulk Milk', '', 'bulk-milk-944', 4, 77.40, 86.00, 'liter', 'Koteshwor Market', 'Bulk quality milk from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(252, 'Imported Ice Cream', '', 'imported-ice-cream-303', 4, 216.00, 240.00, 'pack', 'Balkhu Market', 'Imported quality ice cream from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(253, 'Bulk Butter', '', 'bulk-butter-253', 4, 690.30, 767.00, 'kg', 'Koteshwor Market', 'Bulk quality butter from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(254, 'Organic Cardamom (Small)', '', 'organic-cardamom-small-516', 6, 3536.10, 3929.00, 'kg', 'Koteshwor Market', 'Organic quality cardamom (small) from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(255, 'Imported Chicken', '', 'imported-chicken-580', 5, 582.30, 647.00, 'kg', 'Asan Bazaar', 'Imported quality chicken from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(256, 'Organic Kiwi', '', 'organic-kiwi-835', 2, 555.30, 617.00, 'kg', 'Asan Bazaar', 'Organic quality kiwi from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(257, 'Bulk Ghee', '', 'bulk-ghee-799', 3, 918.00, 1020.00, 'liter', 'Kalimati Vegetable Market', 'Bulk quality ghee from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(258, 'Local Butter', '', 'local-butter-666', 4, 813.60, 904.00, 'kg', 'Bhatbhateni Supermarket', 'Local quality butter from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(259, 'Bulk Cardamom (Small)', '', 'bulk-cardamom-small-681', 6, 2392.20, 2658.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality cardamom (small) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(260, 'Organic Spinach', '', 'organic-spinach-641', 1, 54.90, 61.00, 'kg', 'New Road', 'Organic quality spinach from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(261, 'Standard Ghee', '', 'standard-ghee-610', 3, 1064.70, 1183.00, 'liter', 'Kalimati Vegetable Market', 'Standard quality ghee from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(262, 'Organic Grapes', '', 'organic-grapes-151', 2, 392.40, 436.00, 'kg', 'Balkhu Market', 'Organic quality grapes from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(263, 'Premium Kidney Beans', '', 'premium-kidney-beans-811', 3, 188.10, 209.00, 'kg', 'New Road', 'Premium quality kidney beans from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(264, 'Local Chicken', '', 'local-chicken-890', 5, 310.50, 345.00, 'kg', 'New Road', 'Local quality chicken from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(265, 'Local Pork', '', 'local-pork-694', 5, 450.90, 501.00, 'kg', 'Asan Bazaar', 'Local quality pork from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(266, 'Premium Watermelon', '', 'premium-watermelon-899', 2, 46.80, 52.00, 'kg', 'Koteshwor Market', 'Premium quality watermelon from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(267, 'Bulk Tea Leaves', '', 'bulk-tea-leaves-282', 3, 236.70, 263.00, 'kg', 'Koteshwor Market', 'Bulk quality tea leaves from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(268, 'Local Soap', '', 'local-soap-641', 7, 32.40, 36.00, 'piece', 'New Road', 'Local quality soap from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(269, 'Imported Black Pepper', '', 'imported-black-pepper-594', 6, 1563.30, 1737.00, 'kg', 'Local Market', 'Imported quality black pepper from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(270, 'Imported Rice (Jeera Masino)', '', 'imported-rice-jeera-masino-353', 3, 2493.90, 2771.00, 'pack', 'Kalimati Vegetable Market', 'Imported quality rice (jeera masino) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(271, 'Local Cucumber', '', 'local-cucumber-285', 1, 56.70, 63.00, 'kg', 'Asan Bazaar', 'Local quality cucumber from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(272, 'Bulk Pomegranate', '', 'bulk-pomegranate-995', 2, 193.50, 215.00, 'kg', 'Koteshwor Market', 'Bulk quality pomegranate from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(273, 'Standard Onion', '', 'standard-onion-371', 1, 59.40, 66.00, 'kg', 'Local Market', 'Standard quality onion from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(274, 'Organic Cheese', '', 'organic-cheese-878', 4, 1976.40, 2196.00, 'kg', 'Bhatbhateni Supermarket', 'Organic quality cheese from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(275, 'Local Carrot', '', 'local-carrot-406', 1, 66.60, 74.00, 'kg', 'Asan Bazaar', 'Local quality carrot from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(276, 'Local Pear', '', 'local-pear-158', 2, 108.90, 121.00, 'kg', 'Bhatbhateni Supermarket', 'Local quality pear from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(277, 'Standard Chili Powder', '', 'standard-chili-powder-567', 6, 404.10, 449.00, 'kg', 'Koteshwor Market', 'Standard quality chili powder from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(278, 'Bulk Paneer', '', 'bulk-paneer-441', 4, 615.60, 684.00, 'kg', 'Balkhu Market', 'Bulk quality paneer from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(279, 'Organic Mutton', '', 'organic-mutton-516', 5, 1488.60, 1654.00, 'kg', 'Koteshwor Market', 'Organic quality mutton from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(280, 'Imported Sausage', '', 'imported-sausage-716', 5, 518.40, 576.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality sausage from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(281, 'Standard Kidney Beans', '', 'standard-kidney-beans-463', 3, 173.70, 193.00, 'kg', 'New Road', 'Standard quality kidney beans from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(282, 'Premium Fenugreek', '', 'premium-fenugreek-728', 6, 330.30, 367.00, 'kg', 'Local Market', 'Premium quality fenugreek from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(283, 'Fresh Paneer', '', 'fresh-paneer-968', 4, 703.80, 782.00, 'kg', 'Koteshwor Market', 'Fresh quality paneer from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(284, 'Fresh Fish (Bachhwa)', '', 'fresh-fish-bachhwa-433', 5, 385.20, 428.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality fish (bachhwa) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(285, 'Imported Tomato', '', 'imported-tomato-350', 1, 95.40, 106.00, 'kg', 'New Road', 'Imported quality tomato from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(286, 'Local Cheese', '', 'local-cheese-434', 4, 1148.40, 1276.00, 'kg', 'Local Market', 'Local quality cheese from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(287, 'Local Pork', '', 'local-pork-352', 5, 478.80, 532.00, 'kg', 'Kalimati Vegetable Market', 'Local quality pork from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(288, 'Imported Body Lotion', '', 'imported-body-lotion-745', 7, 404.10, 449.00, 'pack', 'Koteshwor Market', 'Imported quality body lotion from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(289, 'Organic Toothpaste', '', 'organic-toothpaste-816', 7, 85.50, 95.00, 'pack', 'Koteshwor Market', 'Organic quality toothpaste from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(290, 'Standard Chickpeas', '', 'standard-chickpeas-732', 3, 128.70, 143.00, 'kg', 'Asan Bazaar', 'Standard quality chickpeas from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(291, 'Premium Ghee', '', 'premium-ghee-987', 3, 1363.50, 1515.00, 'liter', 'Balkhu Market', 'Premium quality ghee from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(292, 'Bulk Coriander Seeds', '', 'bulk-coriander-seeds-272', 6, 201.60, 224.00, 'kg', 'Balkhu Market', 'Bulk quality coriander seeds from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(293, 'Bulk Sugar', '', 'bulk-sugar-574', 3, 57.60, 64.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality sugar from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(294, 'Premium Fish (Rohu)', '', 'premium-fish-rohu-992', 5, 566.10, 629.00, 'kg', 'Bhatbhateni Supermarket', 'Premium quality fish (rohu) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(295, 'Premium Oil (Mustard)', '', 'premium-oil-mustard-616', 3, 312.30, 347.00, 'liter', 'Asan Bazaar', 'Premium quality oil (mustard) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(296, 'Fresh Radish', '', 'fresh-radish-582', 1, 28.80, 32.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality radish from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(297, 'Bulk Rice (Basmati)', '', 'bulk-rice-basmati-980', 3, 1704.60, 1894.00, 'pack', 'Kalimati Vegetable Market', 'Bulk quality rice (basmati) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(298, 'Fresh Tomato', '', 'fresh-tomato-463', 1, 64.80, 72.00, 'kg', 'Balkhu Market', 'Fresh quality tomato from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(299, 'Organic Pork', '', 'organic-pork-852', 5, 678.60, 754.00, 'kg', 'Balkhu Market', 'Organic quality pork from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(300, 'Standard Noodles', '', 'standard-noodles-818', 3, 18.90, 21.00, 'pack', 'Balkhu Market', 'Standard quality noodles from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(301, 'Premium Plum', '', 'premium-plum-808', 2, 314.10, 349.00, 'kg', 'Balkhu Market', 'Premium quality plum from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(302, 'Fresh Beetroot', '', 'fresh-beetroot-146', 1, 60.30, 67.00, 'kg', 'Balkhu Market', 'Fresh quality beetroot from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(303, 'Organic Chili Powder', '', 'organic-chili-powder-948', 6, 483.30, 537.00, 'kg', 'Local Market', 'Organic quality chili powder from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(304, 'Standard Watermelon', '', 'standard-watermelon-744', 2, 40.50, 45.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality watermelon from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(305, 'Premium Cumin Seeds', '', 'premium-cumin-seeds-503', 6, 804.60, 894.00, 'kg', 'Bhatbhateni Supermarket', 'Premium quality cumin seeds from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(306, 'Organic Cinnamon', '', 'organic-cinnamon-902', 6, 619.20, 688.00, 'kg', 'Asan Bazaar', 'Organic quality cinnamon from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(307, 'Premium Flour (Atta)', '', 'premium-flour-atta-559', 3, 59.40, 66.00, 'kg', 'Asan Bazaar', 'Premium quality flour (atta) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(308, 'Local Cardamom (Small)', '', 'local-cardamom-small-556', 6, 2057.40, 2286.00, 'kg', 'Asan Bazaar', 'Local quality cardamom (small) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(309, 'Standard Onion', '', 'standard-onion-524', 1, 75.60, 84.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality onion from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(310, 'Bulk Paneer', '', 'bulk-paneer-145', 4, 444.60, 494.00, 'kg', 'Asan Bazaar', 'Bulk quality paneer from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(311, 'Bulk Soap', '', 'bulk-soap-803', 7, 33.30, 37.00, 'piece', 'New Road', 'Bulk quality soap from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(312, 'Imported Face Wash', '', 'imported-face-wash-373', 7, 261.90, 291.00, 'pack', 'Koteshwor Market', 'Imported quality face wash from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(313, 'Local Milk', '', 'local-milk-830', 4, 85.50, 95.00, 'liter', 'Local Market', 'Local quality milk from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(314, 'Organic Cheese', '', 'organic-cheese-518', 4, 1151.10, 1279.00, 'kg', 'Bhatbhateni Supermarket', 'Organic quality cheese from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(315, 'Imported Cumin Seeds', '', 'imported-cumin-seeds-204', 6, 539.10, 599.00, 'kg', 'Asan Bazaar', 'Imported quality cumin seeds from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(316, 'Imported Cream', '', 'imported-cream-401', 4, 571.50, 635.00, 'liter', 'Kalimati Vegetable Market', 'Imported quality cream from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(317, 'Local Cream', '', 'local-cream-353', 4, 504.90, 561.00, 'liter', 'Bhatbhateni Supermarket', 'Local quality cream from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(318, 'Organic Peach', '', 'organic-peach-307', 2, 174.60, 194.00, 'kg', 'New Road', 'Organic quality peach from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(319, 'Bulk Pork', '', 'bulk-pork-657', 5, 369.00, 410.00, 'kg', 'Local Market', 'Bulk quality pork from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(320, 'Standard Rice (Sona Mansuli)', '', 'standard-rice-sona-mansuli-349', 3, 1135.80, 1262.00, 'pack', 'Balkhu Market', 'Standard quality rice (sona mansuli) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(321, 'Standard Ice Cream', '', 'standard-ice-cream-183', 4, 136.80, 152.00, 'pack', 'New Road', 'Standard quality ice cream from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(322, 'Local Dragon Fruit', '', 'local-dragon-fruit-592', 2, 375.30, 417.00, 'kg', 'Local Market', 'Local quality dragon fruit from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(323, 'Organic Shampoo', '', 'organic-shampoo-275', 7, 601.20, 668.00, 'pack', 'Asan Bazaar', 'Organic quality shampoo from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(324, 'Imported Milk', '', 'imported-milk-713', 4, 94.50, 105.00, 'liter', 'Koteshwor Market', 'Imported quality milk from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(325, 'Local Conditioner', '', 'local-conditioner-359', 7, 188.10, 209.00, 'pack', 'Local Market', 'Local quality conditioner from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(326, 'Standard Mutton', '', 'standard-mutton-800', 5, 901.80, 1002.00, 'kg', 'Balkhu Market', 'Standard quality mutton from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(327, 'Imported Pomegranate', '', 'imported-pomegranate-910', 2, 468.00, 520.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality pomegranate from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(328, 'Standard Cinnamon', '', 'standard-cinnamon-356', 6, 393.30, 437.00, 'kg', 'Kalimati Vegetable Market', 'Standard quality cinnamon from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(329, 'Premium Garlic', '', 'premium-garlic-839', 1, 339.30, 377.00, 'kg', 'Koteshwor Market', 'Premium quality garlic from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(330, 'Standard Black Gram', '', 'standard-black-gram-949', 3, 144.00, 160.00, 'kg', 'Local Market', 'Standard quality black gram from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(331, 'Local Sugar', '', 'local-sugar-128', 3, 76.50, 85.00, 'kg', 'Koteshwor Market', 'Local quality sugar from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(332, 'Imported Body Lotion', '', 'imported-body-lotion-512', 7, 539.10, 599.00, 'pack', 'Bhatbhateni Supermarket', 'Imported quality body lotion from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(333, 'Premium Fish (Bachhwa)', '', 'premium-fish-bachhwa-511', 5, 462.60, 514.00, 'kg', 'New Road', 'Premium quality fish (bachhwa) from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL);
INSERT INTO `items` (`item_id`, `item_name`, `item_name_nepali`, `slug`, `category_id`, `base_price`, `current_price`, `unit`, `market_location`, `description`, `image_path`, `status`, `created_by`, `validated_by`, `created_at`, `updated_at`, `validated_at`, `deleted_at`) VALUES
(334, 'Fresh Milk', '', 'fresh-milk-574', 4, 73.80, 82.00, 'liter', 'Bhatbhateni Supermarket', 'Fresh quality milk from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(335, 'Local Fenugreek', '', 'local-fenugreek-697', 6, 220.50, 245.00, 'kg', 'Koteshwor Market', 'Local quality fenugreek from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(336, 'Imported Oil (Mustard)', '', 'imported-oil-mustard-324', 3, 428.40, 476.00, 'liter', 'Balkhu Market', 'Imported quality oil (mustard) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(337, 'Bulk Banana', '', 'bulk-banana-684', 2, 66.60, 74.00, 'dozen', 'New Road', 'Bulk quality banana from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(338, 'Local Chickpeas', '', 'local-chickpeas-539', 3, 125.10, 139.00, 'kg', 'New Road', 'Local quality chickpeas from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(339, 'Fresh Tomato', '', 'fresh-tomato-606', 1, 48.60, 54.00, 'kg', 'Local Market', 'Fresh quality tomato from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(340, 'Premium Conditioner', '', 'premium-conditioner-357', 7, 306.90, 341.00, 'pack', 'Local Market', 'Premium quality conditioner from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(341, 'Standard Pork', '', 'standard-pork-592', 5, 365.40, 406.00, 'kg', 'Kalimati Vegetable Market', 'Standard quality pork from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(342, 'Organic Butter', '', 'organic-butter-100', 4, 1333.80, 1482.00, 'kg', 'New Road', 'Organic quality butter from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(343, 'Standard Eggs', '', 'standard-eggs-410', 5, 337.50, 375.00, '', 'Bhatbhateni Supermarket', 'Standard quality eggs from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(344, 'Fresh Chicken', '', 'fresh-chicken-409', 5, 315.90, 351.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality chicken from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(345, 'Fresh Buff', '', 'fresh-buff-672', 5, 400.50, 445.00, 'kg', 'New Road', 'Fresh quality buff from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(346, 'Bulk Milk', '', 'bulk-milk-531', 4, 61.20, 68.00, 'liter', 'Kalimati Vegetable Market', 'Bulk quality milk from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(347, 'Standard Chhurpi', '', 'standard-chhurpi-522', 4, 754.20, 838.00, 'kg', 'Local Market', 'Standard quality chhurpi from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(348, 'Imported Chicken', '', 'imported-chicken-879', 5, 394.20, 438.00, 'kg', 'Local Market', 'Imported quality chicken from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(349, 'Local Chicken', '', 'local-chicken-951', 5, 402.30, 447.00, 'kg', 'Kalimati Vegetable Market', 'Local quality chicken from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(350, 'Imported Toothpaste', '', 'imported-toothpaste-555', 7, 65.70, 73.00, 'pack', 'Kalimati Vegetable Market', 'Imported quality toothpaste from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(351, 'Standard Soybean', '', 'standard-soybean-357', 1, 123.30, 137.00, 'kg', 'Kalimati Vegetable Market', 'Standard quality soybean from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(352, 'Premium Cinnamon', '', 'premium-cinnamon-938', 6, 690.30, 767.00, 'kg', 'Kalimati Vegetable Market', 'Premium quality cinnamon from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(353, 'Organic Soap', '', 'organic-soap-150', 7, 75.60, 84.00, 'piece', 'Balkhu Market', 'Organic quality soap from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(354, 'Fresh Flour (Atta)', '', 'fresh-flour-atta-227', 3, 55.80, 62.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality flour (atta) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(355, 'Organic Black Pepper', '', 'organic-black-pepper-779', 6, 1600.20, 1778.00, 'kg', 'Local Market', 'Organic quality black pepper from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(356, 'Organic Pork', '', 'organic-pork-233', 5, 675.90, 751.00, 'kg', 'Koteshwor Market', 'Organic quality pork from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(357, 'Local Eggs', '', 'local-eggs-428', 5, 342.90, 381.00, '', 'Koteshwor Market', 'Local quality eggs from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(358, 'Local Mutton', '', 'local-mutton-821', 5, 1017.00, 1130.00, 'kg', 'Bhatbhateni Supermarket', 'Local quality mutton from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(359, 'Imported Buff', '', 'imported-buff-194', 5, 780.30, 867.00, 'kg', 'Koteshwor Market', 'Imported quality buff from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(360, 'Imported Asparagus', '', 'imported-asparagus-601', 1, 620.10, 689.00, 'kg', 'Asan Bazaar', 'Imported quality asparagus from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(361, 'Local Chhurpi', '', 'local-chhurpi-156', 4, 722.70, 803.00, 'kg', 'Asan Bazaar', 'Local quality chhurpi from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(362, 'Fresh Milk', '', 'fresh-milk-441', 4, 72.90, 81.00, 'liter', 'Bhatbhateni Supermarket', 'Fresh quality milk from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(363, 'Bulk Toilet Cleaner', '', 'bulk-toilet-cleaner-640', 7, 108.90, 121.00, 'pack', 'New Road', 'Bulk quality toilet cleaner from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(364, 'Bulk Grapes', '', 'bulk-grapes-777', 2, 182.70, 203.00, 'kg', 'Local Market', 'Bulk quality grapes from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(365, 'Organic Chili Powder', '', 'organic-chili-powder-417', 6, 602.10, 669.00, 'kg', 'Kalimati Vegetable Market', 'Organic quality chili powder from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(366, 'Imported Buff', '', 'imported-buff-617', 5, 694.80, 772.00, 'kg', 'Asan Bazaar', 'Imported quality buff from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(367, 'Imported Milk', '', 'imported-milk-522', 4, 97.20, 108.00, 'liter', 'Koteshwor Market', 'Imported quality milk from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(368, 'Fresh Fish (Rohu)', '', 'fresh-fish-rohu-968', 5, 403.20, 448.00, 'kg', 'Asan Bazaar', 'Fresh quality fish (rohu) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(369, 'Organic Pomegranate', '', 'organic-pomegranate-953', 2, 387.90, 431.00, 'kg', 'Koteshwor Market', 'Organic quality pomegranate from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(370, 'Organic Sausage', '', 'organic-sausage-262', 5, 573.30, 637.00, 'kg', 'Local Market', 'Organic quality sausage from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(371, 'Bulk Spinach', '', 'bulk-spinach-389', 1, 40.50, 45.00, 'kg', 'Koteshwor Market', 'Bulk quality spinach from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(372, 'Premium Noodles', '', 'premium-noodles-734', 3, 33.30, 37.00, 'pack', 'Koteshwor Market', 'Premium quality noodles from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(373, 'Bulk Peach', '', 'bulk-peach-979', 2, 140.40, 156.00, 'kg', 'New Road', 'Bulk quality peach from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(374, 'Imported Turmeric Powder', '', 'imported-turmeric-powder-394', 6, 229.50, 255.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality turmeric powder from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(375, 'Organic Sausage', '', 'organic-sausage-786', 5, 784.80, 872.00, 'kg', 'Kalimati Vegetable Market', 'Organic quality sausage from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(376, 'Premium Rice (Jeera Masino)', '', 'premium-rice-jeera-masino-478', 3, 1840.50, 2045.00, 'pack', 'Balkhu Market', 'Premium quality rice (jeera masino) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(377, 'Premium Fish (Rohu)', '', 'premium-fish-rohu-948', 5, 466.20, 518.00, 'kg', 'Balkhu Market', 'Premium quality fish (rohu) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(378, 'Local Ice Cream', '', 'local-ice-cream-208', 4, 175.50, 195.00, 'pack', 'New Road', 'Local quality ice cream from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(379, 'Local Cardamom (Large)', '', 'local-cardamom-large-195', 6, 1063.80, 1182.00, 'kg', 'Asan Bazaar', 'Local quality cardamom (large) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(380, 'Organic Mango', '', 'organic-mango-628', 2, 255.60, 284.00, 'kg', 'Bhatbhateni Supermarket', 'Organic quality mango from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(381, 'Premium Milk', '', 'premium-milk-279', 4, 91.80, 102.00, 'liter', 'Asan Bazaar', 'Premium quality milk from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(382, 'Organic Lemon', '', 'organic-lemon-995', 2, 13.50, 15.00, 'piece', 'Koteshwor Market', 'Organic quality lemon from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(383, 'Organic Flour (Atta)', '', 'organic-flour-atta-488', 3, 92.70, 103.00, 'kg', 'Local Market', 'Organic quality flour (atta) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(384, 'Local Face Wash', '', 'local-face-wash-777', 7, 139.50, 155.00, 'pack', 'Kalimati Vegetable Market', 'Local quality face wash from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(385, 'Standard Body Lotion', '', 'standard-body-lotion-131', 7, 406.80, 452.00, 'pack', 'Kalimati Vegetable Market', 'Standard quality body lotion from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(386, 'Imported Ice Cream', '', 'imported-ice-cream-208', 4, 248.40, 276.00, 'pack', 'Asan Bazaar', 'Imported quality ice cream from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(387, 'Bulk Dishwash Bar', '', 'bulk-dishwash-bar-751', 7, 25.20, 28.00, 'piece', 'New Road', 'Bulk quality dishwash bar from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(388, 'Bulk Paneer', '', 'bulk-paneer-410', 4, 482.40, 536.00, 'kg', 'Asan Bazaar', 'Bulk quality paneer from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(389, 'Standard Milk', '', 'standard-milk-240', 4, 86.40, 96.00, 'liter', 'Koteshwor Market', 'Standard quality milk from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(390, 'Organic Soap', '', 'organic-soap-457', 7, 67.50, 75.00, 'piece', 'New Road', 'Organic quality soap from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(391, 'Bulk Turmeric Powder', '', 'bulk-turmeric-powder-672', 6, 165.60, 184.00, 'kg', 'Local Market', 'Bulk quality turmeric powder from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(392, 'Premium Oil (Mustard)', '', 'premium-oil-mustard-114', 3, 391.50, 435.00, 'liter', 'Asan Bazaar', 'Premium quality oil (mustard) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(393, 'Organic Soap', '', 'organic-soap-891', 7, 57.60, 64.00, 'piece', 'Asan Bazaar', 'Organic quality soap from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(394, 'Standard Salt', '', 'standard-salt-788', 3, 27.00, 30.00, 'pack', 'Kalimati Vegetable Market', 'Standard quality salt from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(395, 'Premium Brinjal', '', 'premium-brinjal-221', 1, 58.50, 65.00, 'kg', 'Kalimati Vegetable Market', 'Premium quality brinjal from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(396, 'Organic Cheese', '', 'organic-cheese-493', 4, 1732.50, 1925.00, 'kg', 'Koteshwor Market', 'Organic quality cheese from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(397, 'Fresh Curd', '', 'fresh-curd-544', 4, 125.10, 139.00, 'liter', 'Koteshwor Market', 'Fresh quality curd from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(398, 'Fresh Oil (Mustard)', '', 'fresh-oil-mustard-101', 3, 309.60, 344.00, 'liter', 'Kalimati Vegetable Market', 'Fresh quality oil (mustard) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(399, 'Organic Green Chili', '', 'organic-green-chili-975', 1, 169.20, 188.00, 'kg', 'New Road', 'Organic quality green chili from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(400, 'Bulk Soybean', '', 'bulk-soybean-663', 1, 73.80, 82.00, 'kg', 'New Road', 'Bulk quality soybean from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(401, 'Local Cinnamon', '', 'local-cinnamon-747', 6, 363.60, 404.00, 'kg', 'Asan Bazaar', 'Local quality cinnamon from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(402, 'Imported Mango', '', 'imported-mango-647', 2, 212.40, 236.00, 'kg', 'Asan Bazaar', 'Imported quality mango from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(403, 'Standard Black Pepper', '', 'standard-black-pepper-522', 6, 852.30, 947.00, 'kg', 'New Road', 'Standard quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(404, 'Standard Cardamom (Small)', '', 'standard-cardamom-small-159', 6, 1983.60, 2204.00, 'kg', 'New Road', 'Standard quality cardamom (small) from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(405, 'Standard Pork', '', 'standard-pork-217', 5, 455.40, 506.00, 'kg', 'Kalimati Vegetable Market', 'Standard quality pork from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(406, 'Premium Ice Cream', '', 'premium-ice-cream-330', 4, 86.40, 96.00, 'pack', 'New Road', 'Premium quality ice cream from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(407, 'Premium Fish (Bachhwa)', '', 'premium-fish-bachhwa-828', 5, 510.30, 567.00, 'kg', 'Koteshwor Market', 'Premium quality fish (bachhwa) from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(408, 'Organic Lemon', '', 'organic-lemon-541', 2, 18.00, 20.00, 'piece', 'Koteshwor Market', 'Organic quality lemon from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(409, 'Premium Avocado', '', 'premium-avocado-115', 2, 644.40, 716.00, 'kg', 'Bhatbhateni Supermarket', 'Premium quality avocado from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(410, 'Premium Black Pepper', '', 'premium-black-pepper-981', 6, 867.60, 964.00, 'kg', 'New Road', 'Premium quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(411, 'Bulk Fish (Bachhwa)', '', 'bulk-fish-bachhwa-644', 5, 475.20, 528.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality fish (bachhwa) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(412, 'Fresh Chicken', '', 'fresh-chicken-836', 5, 306.00, 340.00, 'kg', 'New Road', 'Fresh quality chicken from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(413, 'Imported Banana', '', 'imported-banana-708', 2, 113.40, 126.00, 'dozen', 'Balkhu Market', 'Imported quality banana from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(414, 'Standard Pomegranate', '', 'standard-pomegranate-932', 2, 250.20, 278.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality pomegranate from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(415, 'Local Lentil (Masur)', '', 'local-lentil-masur-723', 3, 171.00, 190.00, 'kg', 'Koteshwor Market', 'Local quality lentil (masur) from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(416, 'Imported Chhurpi', '', 'imported-chhurpi-351', 4, 1144.80, 1272.00, 'kg', 'New Road', 'Imported quality chhurpi from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(417, 'Bulk Plum', '', 'bulk-plum-720', 2, 185.40, 206.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality plum from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(418, 'Fresh Buff', '', 'fresh-buff-408', 5, 501.30, 557.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality buff from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(419, 'Imported Face Wash', '', 'imported-face-wash-806', 7, 192.60, 214.00, 'pack', 'New Road', 'Imported quality face wash from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(420, 'Fresh Curd', '', 'fresh-curd-738', 4, 96.30, 107.00, 'liter', 'Local Market', 'Fresh quality curd from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(421, 'Standard Ice Cream', '', 'standard-ice-cream-262', 4, 90.00, 100.00, 'pack', 'Local Market', 'Standard quality ice cream from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(422, 'Imported Mango', '', 'imported-mango-199', 2, 324.00, 360.00, 'kg', 'Local Market', 'Imported quality mango from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(423, 'Fresh Beaten Rice', '', 'fresh-beaten-rice-699', 3, 74.70, 83.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality beaten rice from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(424, 'Local Papaya', '', 'local-papaya-384', 2, 106.20, 118.00, 'kg', 'Balkhu Market', 'Local quality papaya from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(425, 'Local Body Lotion', '', 'local-body-lotion-785', 7, 321.30, 357.00, 'pack', 'Local Market', 'Local quality body lotion from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(426, 'Organic Bitter Gourd', '', 'organic-bitter-gourd-207', 1, 104.40, 116.00, 'kg', 'Kalimati Vegetable Market', 'Organic quality bitter gourd from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(427, 'Fresh Pork', '', 'fresh-pork-792', 5, 392.40, 436.00, 'kg', 'Asan Bazaar', 'Fresh quality pork from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(428, 'Bulk Rice (Basmati)', '', 'bulk-rice-basmati-277', 3, 2202.30, 2447.00, 'pack', 'Bhatbhateni Supermarket', 'Bulk quality rice (basmati) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(429, 'Imported Ginger', '', 'imported-ginger-420', 1, 164.70, 183.00, 'kg', 'Asan Bazaar', 'Imported quality ginger from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(430, 'Organic Toothbrush', '', 'organic-toothbrush-368', 7, 40.50, 45.00, 'piece', 'Bhatbhateni Supermarket', 'Organic quality toothbrush from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(431, 'Organic Fish (Bachhwa)', '', 'organic-fish-bachhwa-854', 5, 480.60, 534.00, 'kg', 'Bhatbhateni Supermarket', 'Organic quality fish (bachhwa) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(432, 'Organic Strawberry', '', 'organic-strawberry-764', 2, 525.60, 584.00, 'kg', 'New Road', 'Organic quality strawberry from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(433, 'Local Grapes', '', 'local-grapes-418', 2, 214.20, 238.00, 'kg', 'Balkhu Market', 'Local quality grapes from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(434, 'Standard Turmeric Powder', '', 'standard-turmeric-powder-195', 6, 236.70, 263.00, 'kg', 'Asan Bazaar', 'Standard quality turmeric powder from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(435, 'Fresh Asparagus', '', 'fresh-asparagus-127', 1, 414.00, 460.00, 'kg', 'Local Market', 'Fresh quality asparagus from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(436, 'Standard Cauliflower', '', 'standard-cauliflower-611', 1, 37.80, 42.00, 'kg', 'Balkhu Market', 'Standard quality cauliflower from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(437, 'Bulk Strawberry', '', 'bulk-strawberry-954', 2, 277.20, 308.00, 'kg', 'New Road', 'Bulk quality strawberry from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(438, 'Bulk Curd', '', 'bulk-curd-178', 4, 98.10, 109.00, 'liter', 'Bhatbhateni Supermarket', 'Bulk quality curd from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(439, 'Fresh Bottle Gourd', '', 'fresh-bottle-gourd-559', 1, 38.70, 43.00, 'kg', 'New Road', 'Fresh quality bottle gourd from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(440, 'Organic Cream', '', 'organic-cream-563', 4, 438.30, 487.00, 'liter', 'Asan Bazaar', 'Organic quality cream from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(441, 'Organic Sweet Potato', '', 'organic-sweet-potato-678', 1, 85.50, 95.00, 'kg', 'Balkhu Market', 'Organic quality sweet potato from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(442, 'Standard Eggs', '', 'standard-eggs-112', 5, 369.90, 411.00, '', 'Koteshwor Market', 'Standard quality eggs from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(443, 'Bulk Face Wash', '', 'bulk-face-wash-320', 7, 147.60, 164.00, 'pack', 'Local Market', 'Bulk quality face wash from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(444, 'Premium Soap', '', 'premium-soap-282', 7, 36.00, 40.00, 'piece', 'Balkhu Market', 'Premium quality soap from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(445, 'Organic Body Lotion', '', 'organic-body-lotion-533', 7, 583.20, 648.00, 'pack', 'Asan Bazaar', 'Organic quality body lotion from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(446, 'Standard Face Wash', '', 'standard-face-wash-216', 7, 240.30, 267.00, 'pack', 'Koteshwor Market', 'Standard quality face wash from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(447, 'Bulk Chicken', '', 'bulk-chicken-783', 5, 270.00, 300.00, 'kg', 'Asan Bazaar', 'Bulk quality chicken from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(448, 'Fresh Fish (Rohu)', '', 'fresh-fish-rohu-645', 5, 297.00, 330.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality fish (rohu) from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(449, 'Standard Lentil (Rahar)', '', 'standard-lentil-rahar-839', 3, 188.10, 209.00, 'kg', 'Balkhu Market', 'Standard quality lentil (rahar) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(450, 'Fresh Asparagus', '', 'fresh-asparagus-333', 1, 437.40, 486.00, 'kg', 'Kalimati Vegetable Market', 'Fresh quality asparagus from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(451, 'Bulk Eggs', '', 'bulk-eggs-251', 5, 252.00, 280.00, '', 'Asan Bazaar', 'Bulk quality eggs from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(452, 'Organic Turmeric Powder', '', 'organic-turmeric-powder-624', 6, 331.20, 368.00, 'kg', 'Local Market', 'Organic quality turmeric powder from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(453, 'Imported Turmeric Powder', '', 'imported-turmeric-powder-716', 6, 265.50, 295.00, 'kg', 'Balkhu Market', 'Imported quality turmeric powder from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(454, 'Fresh Broccoli', '', 'fresh-broccoli-947', 1, 91.80, 102.00, 'kg', 'New Road', 'Fresh quality broccoli from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(455, 'Imported Chickpeas', '', 'imported-chickpeas-268', 3, 162.90, 181.00, 'kg', 'Koteshwor Market', 'Imported quality chickpeas from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(456, 'Imported Eggs', '', 'imported-eggs-721', 5, 486.00, 540.00, '', 'New Road', 'Imported quality eggs from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(457, 'Imported Cheese', '', 'imported-cheese-861', 4, 1195.20, 1328.00, 'kg', 'Bhatbhateni Supermarket', 'Imported quality cheese from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(458, 'Organic Chicken', '', 'organic-chicken-469', 5, 600.30, 667.00, 'kg', 'Kalimati Vegetable Market', 'Organic quality chicken from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(459, 'Organic Shampoo', '', 'organic-shampoo-986', 7, 386.10, 429.00, 'pack', 'Local Market', 'Organic quality shampoo from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(460, 'Premium Face Wash', '', 'premium-face-wash-483', 7, 225.00, 250.00, 'pack', 'Local Market', 'Premium quality face wash from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(461, 'Bulk Eggs', '', 'bulk-eggs-468', 5, 330.30, 367.00, '', 'Koteshwor Market', 'Bulk quality eggs from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(462, 'Local Oil (Mustard)', '', 'local-oil-mustard-194', 3, 286.20, 318.00, 'liter', 'New Road', 'Local quality oil (mustard) from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(463, 'Imported Papaya', '', 'imported-papaya-622', 2, 128.70, 143.00, 'kg', 'Koteshwor Market', 'Imported quality papaya from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(464, 'Bulk Fenugreek', '', 'bulk-fenugreek-646', 6, 127.80, 142.00, 'kg', 'Asan Bazaar', 'Bulk quality fenugreek from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(465, 'Organic Cardamom (Large)', '', 'organic-cardamom-large-493', 6, 1391.40, 1546.00, 'kg', 'Asan Bazaar', 'Organic quality cardamom (large) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(466, 'Fresh Mushroom', '', 'fresh-mushroom-458', 1, 233.10, 259.00, 'kg', 'Bhatbhateni Supermarket', 'Fresh quality mushroom from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(467, 'Organic Avocado', '', 'organic-avocado-110', 2, 630.00, 700.00, 'kg', 'New Road', 'Organic quality avocado from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(468, 'Imported Shampoo', '', 'imported-shampoo-817', 7, 238.50, 265.00, 'pack', 'Local Market', 'Imported quality shampoo from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(469, 'Imported Face Wash', '', 'imported-face-wash-804', 7, 264.60, 294.00, 'pack', 'Koteshwor Market', 'Imported quality face wash from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(470, 'Bulk Chhurpi', '', 'bulk-chhurpi-101', 4, 598.50, 665.00, 'kg', 'Kalimati Vegetable Market', 'Bulk quality chhurpi from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(471, 'Fresh Cream', '', 'fresh-cream-423', 4, 516.60, 574.00, 'liter', 'Local Market', 'Fresh quality cream from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(472, 'Premium Buff', '', 'premium-buff-471', 5, 612.90, 681.00, 'kg', 'Asan Bazaar', 'Premium quality buff from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(473, 'Fresh Fish (Rohu)', '', 'fresh-fish-rohu-797', 5, 440.10, 489.00, 'kg', 'Balkhu Market', 'Fresh quality fish (rohu) from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(474, 'Standard Tea Leaves', '', 'standard-tea-leaves-463', 3, 320.40, 356.00, 'kg', 'Bhatbhateni Supermarket', 'Standard quality tea leaves from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(475, 'Premium Rice (Basmati)', '', 'premium-rice-basmati-880', 3, 3196.80, 3552.00, 'pack', 'Kalimati Vegetable Market', 'Premium quality rice (basmati) from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(476, 'Local Kidney Beans', '', 'local-kidney-beans-497', 3, 165.60, 184.00, 'kg', 'Bhatbhateni Supermarket', 'Local quality kidney beans from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(477, 'Imported Mushroom', '', 'imported-mushroom-497', 1, 276.30, 307.00, 'kg', 'Kalimati Vegetable Market', 'Imported quality mushroom from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(478, 'Premium Pork', '', 'premium-pork-354', 5, 620.10, 689.00, 'kg', 'Kalimati Vegetable Market', 'Premium quality pork from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(479, 'Local Black Pepper', '', 'local-black-pepper-501', 6, 823.50, 915.00, 'kg', 'New Road', 'Local quality black pepper from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(480, 'Imported Pork', '', 'imported-pork-416', 5, 600.30, 667.00, 'kg', 'Koteshwor Market', 'Imported quality pork from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(481, 'Imported Cabbage', '', 'imported-cabbage-441', 1, 39.60, 44.00, 'kg', 'Local Market', 'Imported quality cabbage from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(482, 'Local Sugar', '', 'local-sugar-471', 3, 86.40, 96.00, 'kg', 'Asan Bazaar', 'Local quality sugar from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(483, 'Imported Apple', '', 'imported-apple-652', 2, 224.10, 249.00, 'kg', 'New Road', 'Imported quality apple from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(484, 'Premium Cumin Seeds', '', 'premium-cumin-seeds-310', 6, 563.40, 626.00, 'kg', 'Kalimati Vegetable Market', 'Premium quality cumin seeds from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(485, 'Premium Orange', '', 'premium-orange-111', 2, 176.40, 196.00, 'kg', 'Balkhu Market', 'Premium quality orange from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(486, 'Imported Oil (Sunflower)', '', 'imported-oil-sunflower-657', 3, 308.70, 343.00, 'liter', 'Local Market', 'Imported quality oil (sunflower) from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(487, 'Organic Cheese', '', 'organic-cheese-872', 4, 1323.90, 1471.00, 'kg', 'Asan Bazaar', 'Organic quality cheese from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(488, 'Organic Curd', '', 'organic-curd-148', 4, 188.10, 209.00, 'liter', 'Kalimati Vegetable Market', 'Organic quality curd from Kalimati Vegetable Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(489, 'Bulk Green Chili', '', 'bulk-green-chili-474', 1, 81.00, 90.00, 'kg', 'Local Market', 'Bulk quality green chili from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(490, 'Premium Pork', '', 'premium-pork-906', 5, 468.00, 520.00, 'kg', 'Balkhu Market', 'Premium quality pork from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(491, 'Bulk Oil (Mustard)', '', 'bulk-oil-mustard-367', 3, 269.10, 299.00, 'liter', 'Asan Bazaar', 'Bulk quality oil (mustard) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(492, 'Local Oil (Sunflower)', '', 'local-oil-sunflower-197', 3, 189.90, 211.00, 'liter', 'Asan Bazaar', 'Local quality oil (sunflower) from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(493, 'Bulk Shampoo', '', 'bulk-shampoo-991', 7, 198.00, 220.00, 'pack', 'Local Market', 'Bulk quality shampoo from Local Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(494, 'Bulk Mutton', '', 'bulk-mutton-200', 5, 732.60, 814.00, 'kg', 'Balkhu Market', 'Bulk quality mutton from Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(495, 'Premium Mango', '', 'premium-mango-800', 2, 239.40, 266.00, 'kg', 'New Road', 'Premium quality mango from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(496, 'Fresh Body Lotion', '', 'fresh-body-lotion-348', 7, 321.30, 357.00, 'pack', 'New Road', 'Fresh quality body lotion from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL);
INSERT INTO `items` (`item_id`, `item_name`, `item_name_nepali`, `slug`, `category_id`, `base_price`, `current_price`, `unit`, `market_location`, `description`, `image_path`, `status`, `created_by`, `validated_by`, `created_at`, `updated_at`, `validated_at`, `deleted_at`) VALUES
(497, 'Fresh Eggs', '', 'fresh-eggs-583', 5, 352.80, 392.00, '', 'Koteshwor Market', 'Fresh quality eggs from Koteshwor Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(498, 'Fresh Rice (Jeera Masino)', '', 'fresh-rice-jeera-masino-590', 3, 1790.10, 1989.00, 'pack', 'New Road', 'Fresh quality rice (jeera masino) from New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(499, 'Fresh Curd', '', 'fresh-curd-233', 4, 90.00, 100.00, 'liter', 'Bhatbhateni Supermarket', 'Fresh quality curd from Bhatbhateni Supermarket', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(500, 'Fresh Avocado', '', 'fresh-avocado-785', 2, 315.00, 350.00, 'kg', 'Asan Bazaar', 'Fresh quality avocado from Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 2, NULL, '2025-11-25 16:51:52', '2025-12-13 18:17:46', NULL, NULL),
(501, 'Laundry Detergent (1kg)', '', 'laundry-detergent-1kg-6925e21892b78', 8, 250.00, 250.00, 'kg', 'Bhatbhateni', 'Laundry Detergent (1kg) - Available at Bhatbhateni', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(502, 'Dish Soap (500ml)', '', 'dish-soap-500ml-6925e21893582', 8, 120.00, 120.00, '', 'Asan Bazaar', 'Dish Soap (500ml) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(503, 'Floor Cleaner (1L)', '', 'floor-cleaner-1l-6925e218937a0', 8, 180.00, 180.00, 'liter', 'Bhatbhateni', 'Floor Cleaner (1L) - Available at Bhatbhateni', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(504, 'Toilet Cleaner (500ml)', '', 'toilet-cleaner-500ml-6925e218938a8', 8, 150.00, 150.00, '', 'Bhatbhateni', 'Toilet Cleaner (500ml) - Available at Bhatbhateni', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(505, 'Hand Wash (250ml)', '', 'hand-wash-250ml-6925e21893a0f', 8, 95.00, 95.00, '', 'Asan Bazaar', 'Hand Wash (250ml) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(506, 'Mop', '', 'mop-6925e21893c2b', 8, 350.00, 350.00, 'piece', 'Balkhu Market', 'Mop - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(507, 'Broom', '', 'broom-6925e21893e05', 8, 120.00, 120.00, 'piece', 'Asan Bazaar', 'Broom - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(508, 'Dustpan', '', 'dustpan-6925e21893f54', 8, 80.00, 80.00, 'piece', 'Asan Bazaar', 'Dustpan - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(509, 'Garbage Bags (20pcs)', '', 'garbage-bags-20pcs-6925e21894042', 8, 100.00, 100.00, 'pack', 'Bhatbhateni', 'Garbage Bags (20pcs) - Available at Bhatbhateni', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(510, 'Air Freshener', '', 'air-freshener-6925e21894113', 8, 200.00, 200.00, 'piece', 'Bhatbhateni', 'Air Freshener - Available at Bhatbhateni', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:16:35', '2025-11-25 17:06:32', NULL),
(511, 'LED Bulb 9W', '', 'led-bulb-9w-6925e21894322', 9, 150.00, 150.00, 'piece', 'New Road', 'LED Bulb 9W - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(512, 'Table Fan', '', 'table-fan-6925e218944a0', 9, 1800.00, 1800.00, 'piece', 'New Road', 'Table Fan - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(513, 'Electric Kettle', '', 'electric-kettle-6925e218945c9', 9, 1500.00, 1500.00, 'piece', 'New Road', 'Electric Kettle - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(514, 'Iron Box', '', 'iron-box-6925e2189476f', 9, 1200.00, 1200.00, 'piece', 'New Road', 'Iron Box - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(515, 'Extension Cord 4 Socket', '', 'extension-cord-4-socket-6925e2189488f', 9, 450.00, 450.00, 'piece', 'Balkhu Market', 'Extension Cord 4 Socket - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(516, 'Rice Cooker', '', 'rice-cooker-6925e21894988', 9, 2500.00, 2500.00, 'piece', 'New Road', 'Rice Cooker - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(517, 'Mixer Grinder', '', 'mixer-grinder-6925e21894aae', 9, 3500.00, 3500.00, 'piece', 'New Road', 'Mixer Grinder - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(518, 'Electric Heater', '', 'electric-heater-6925e21894bba', 9, 2800.00, 2800.00, 'piece', 'New Road', 'Electric Heater - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(519, 'Vacuum Cleaner', '', 'vacuum-cleaner-6925e21894cec', 9, 5500.00, 5500.00, 'piece', 'New Road', 'Vacuum Cleaner - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(520, 'Hair Dryer', '', 'hair-dryer-6925e21894e05', 9, 1200.00, 1200.00, 'piece', 'New Road', 'Hair Dryer - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(521, 'Men\'s T-Shirt', '', 'mens-t-shirt-6925e21895271', 10, 450.00, 450.00, 'piece', 'New Road', 'Men\'s T-Shirt - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(522, 'Women\'s Kurta', '', 'womens-kurta-6925e2189560a', 10, 800.00, 800.00, 'piece', 'New Road', 'Women\'s Kurta - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(523, 'Jeans Pant', '', 'jeans-pant-6925e218956de', 10, 1200.00, 1200.00, 'piece', 'New Road', 'Jeans Pant - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(524, 'Cotton Socks (pair)', '', 'cotton-socks-pair-6925e218957a0', 10, 100.00, 100.00, '', 'Asan Bazaar', 'Cotton Socks (pair) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(525, 'Belt', '', 'belt-6925e21895878', 10, 300.00, 300.00, 'piece', 'New Road', 'Belt - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(526, 'Cap/Hat', '', 'caphat-6925e21895978', 10, 250.00, 250.00, 'piece', 'Asan Bazaar', 'Cap/Hat - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(527, 'Scarf', '', 'scarf-6925e21895ab9', 10, 200.00, 200.00, 'piece', 'Asan Bazaar', 'Scarf - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(528, 'Handkerchief (pack of 3)', '', 'handkerchief-pack-of-3-6925e21895c75', 10, 150.00, 150.00, 'pack', 'Asan Bazaar', 'Handkerchief (pack of 3) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(529, 'Umbrella', '', 'umbrella-6925e21895e0a', 10, 400.00, 400.00, 'piece', 'New Road', 'Umbrella - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(530, 'Backpack', '', 'backpack-6925e21895f53', 10, 1500.00, 1500.00, 'piece', 'New Road', 'Backpack - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(531, 'Notebook A4 (100 pages)', '', 'notebook-a4-100-pages-6925e21896171', 11, 80.00, 80.00, 'piece', 'Asan Bazaar', 'Notebook A4 (100 pages) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(532, 'Pen (blue)', '', 'pen-blue-6925e21896323', 11, 20.00, 20.00, 'piece', 'Asan Bazaar', 'Pen (blue) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(533, 'Pencil Box', '', 'pencil-box-6925e218964f8', 11, 150.00, 150.00, 'piece', 'Asan Bazaar', 'Pencil Box - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(534, 'Geometry Box', '', 'geometry-box-6925e218966be', 11, 200.00, 200.00, 'piece', 'Asan Bazaar', 'Geometry Box - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(535, 'Color Pencils (12 colors)', '', 'color-pencils-12-colors-6925e218967db', 11, 120.00, 120.00, '', 'Asan Bazaar', 'Color Pencils (12 colors) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(536, 'Eraser', '', 'eraser-6925e218969f7', 11, 10.00, 10.00, 'piece', 'Asan Bazaar', 'Eraser - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(537, 'Sharpener', '', 'sharpener-6925e2189715b', 11, 15.00, 15.00, 'piece', 'Asan Bazaar', 'Sharpener - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(538, 'Ruler (30cm)', '', 'ruler-30cm-6925e21897286', 11, 25.00, 25.00, 'piece', 'Asan Bazaar', 'Ruler (30cm) - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(539, 'Drawing Book', '', 'drawing-book-6925e2189741e', 11, 100.00, 100.00, 'piece', 'Asan Bazaar', 'Drawing Book - Available at Asan Bazaar', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(540, 'School Bag', '', 'school-bag-6925e21897575', 11, 1200.00, 1200.00, 'piece', 'New Road', 'School Bag - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(541, 'Hammer', '', 'hammer-6925e2189777d', 12, 300.00, 300.00, 'piece', 'Balkhu Market', 'Hammer - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(542, 'Screwdriver Set', '', 'screwdriver-set-6925e2189791e', 12, 450.00, 450.00, '', 'Balkhu Market', 'Screwdriver Set - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(543, 'Pliers', '', 'pliers-6925e21897ac1', 12, 250.00, 250.00, 'piece', 'Balkhu Market', 'Pliers - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(544, 'Wrench Set', '', 'wrench-set-6925e21897c15', 12, 600.00, 600.00, '', 'Balkhu Market', 'Wrench Set - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(545, 'Drill Machine', '', 'drill-machine-6925e21897d57', 12, 3500.00, 3500.00, 'piece', 'New Road', 'Drill Machine - Available at New Road', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(546, 'Measuring Tape 5m', '', 'measuring-tape-5m-6925e21897fe3', 12, 150.00, 150.00, 'piece', 'Balkhu Market', 'Measuring Tape 5m - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(547, 'Saw', '', 'saw-6925e218981c1', 12, 400.00, 400.00, 'piece', 'Balkhu Market', 'Saw - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(548, 'Paint Brush Set', '', 'paint-brush-set-6925e21898349', 12, 200.00, 200.00, '', 'Balkhu Market', 'Paint Brush Set - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(549, 'Nails (1kg)', '', 'nails-1kg-6925e218984a2', 12, 180.00, 180.00, 'kg', 'Balkhu Market', 'Nails (1kg) - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(550, 'Spirit Level', '', 'spirit-level-6925e218985b3', 12, 350.00, 350.00, 'piece', 'Balkhu Market', 'Spirit Level - Available at Balkhu Market', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 1, 1, '2025-11-25 17:06:32', '2025-12-13 18:17:46', '2025-11-25 17:06:32', NULL),
(551, 'cx', '', 'cx', 11, 1445.00, 1445.00, '', '32', '53 (Nepali Name: xc)', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-11-25 17:19:02', '2025-12-13 18:17:46', '2025-11-25 17:19:02', NULL),
(553, 'momo', '', 'momo', 4, 5620.00, 5620.00, '', '44', 'gvcfgvbh (Nepali Name: Apple)', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-11-25 17:29:31', '2025-12-13 18:17:46', '2025-11-25 17:29:31', NULL),
(554, 'Apple', '', 'apple', 11, 1520.00, 1520.00, 'kg', 'kalimati', 'in the course', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-11-25 17:41:11', '2025-12-13 18:16:35', '2025-11-25 17:41:11', NULL),
(555, 'Air Freshener', '', 'air-freshener', 3, 54455445.00, 8878.00, 'kg', '55', '545445', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-11-25 17:44:46', '2025-12-13 18:16:35', '2025-12-01 07:01:23', NULL),
(557, 'se', '', 'se', 12, 554.00, 554.00, '', 'dcv', 'dfvd', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-11-25 18:06:31', '2025-12-13 18:16:35', '2025-11-25 18:06:31', NULL),
(558, 'red bull', '', 'red-bull', 6, 110.00, 110.00, 'kg', 'Texas college', 'it tastes too', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-12-13 13:03:37', '2025-12-13 18:17:46', '2025-12-13 13:03:37', NULL),
(559, 'sakshyam', '', 'sakshyam', 5, 2.69, 2.69, 'kg', 'Thamel', 'ok (Nepali Name: local maal)', 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg', 'active', 5, 1, '2025-12-13 17:11:07', '2025-12-13 18:16:35', '2025-12-13 17:11:07', NULL),
(560, 'cable', NULL, 'cable', 9, 95.00, 154.00, 'kg', 'Ason , Bishal Bazar', 'Good product at reasonable price.', 'item_693db02c7d0605.09917644_6c94607ab8fa7041.jpg', 'active', 8, 1, '2025-12-13 18:28:27', '2025-12-13 18:36:37', '2025-12-13 18:36:37', NULL),
(561, 'Black Arabic Aura Watch', NULL, 'black-arabic-aura-watch', 8, 550.00, 550.00, '', 'Durbar Marg Rolex Store', 'it is a good watch (Nepali Name: कालो अरबिक अउरा घडी)', 'item_693dbf738f60c0.35091426_03c0704dae7008ed.jpeg', 'active', 8, 1, '2025-12-13 19:38:37', '2025-12-13 19:38:37', '2025-12-13 19:38:37', NULL),
(562, 'Fantech Mouse', NULL, 'fantech-mouse', 12, 1200.00, 1200.00, '', 'Bishal Bazar', 'It is a good mouse (Nepali Name: फानटेक माउस)', 'item_693dbfc8a340e5.12749033_5eddf373c9692a97.jpeg', 'active', 8, 1, '2025-12-13 19:38:41', '2025-12-13 19:38:41', '2025-12-13 19:38:41', NULL),
(563, 'All in one cable', NULL, 'all-in-one-cable', 12, 120.00, 120.00, '', 'Kailash Marg Nagarkot', 'it is very useful (Nepali Name: अल ईन वान केबल)', 'item_693dc077212431.80818999_6ce3eb6b2513a513.jpg', 'active', 8, 1, '2025-12-13 19:38:44', '2025-12-13 19:38:44', '2025-12-13 19:38:44', NULL),
(564, 'Muffin', NULL, 'muffin', 4, 12.50, 12.50, '', 'krishna pauroti, putalisadak', 'it is very tasty (Nepali Name: मफिन)', 'item_693dc0a7ed5af0.20449284_d71f339d240b5de0.jpeg', 'active', 8, 1, '2025-12-13 19:38:47', '2025-12-13 19:38:47', '2025-12-13 19:38:47', NULL),
(565, 'White Hoodie', NULL, 'white-hoodie', 10, 1200.00, 1200.00, '', 'ason', 'this is agood hoodie (Nepali Name: सेतो हुडी)', 'item_693dc65f8145d1.91216948_cea6c093298bf1c9.jpeg', 'active', 8, 1, '2025-12-13 20:08:56', '2025-12-13 20:08:56', '2025-12-13 20:08:56', NULL),
(566, 'Red Bull', NULL, 'red-bull-693dc7db456c6', 2, 110.00, 110.00, '', 'sifal', 'very tasty (Nepali Name: रातो साँडे)', 'item_693dc6aa458031.34229917_c3be888b8a9c1b06.jpeg', 'active', 8, 1, '2025-12-13 20:08:59', '2025-12-13 20:08:59', '2025-12-13 20:08:59', NULL),
(567, 'Casio Calculator', NULL, 'casio-calculator', 12, 1200.00, 1200.00, '', 'bhotahiti', 'very good scientific calculator (Nepali Name: कासियो काल्कुलेटर)', 'item_693dc72fcba2d7.35399646_4f0905db282f45ab.jpeg', 'active', 8, 1, '2025-12-13 20:09:02', '2025-12-13 20:09:02', '2025-12-13 20:09:02', NULL),
(568, 'Black Hoodie', NULL, 'black-hoodie', 10, 1800.00, 1800.00, '', 'basantapur', 'good quality (Nepali Name: कालो हुडी)', 'item_693dc7579860e4.75589938_62fbc884a3f6e3c9.jpeg', 'active', 8, 1, '2025-12-13 20:09:05', '2025-12-13 20:09:05', '2025-12-13 20:09:05', NULL),
(569, 'Green Hoodie', '', 'green-hoodie', 10, 1100.00, 2100.00, 'piece', 'basundhara', 'not that great (Nepali Name: हरियो हुडी)', 'item_693dc77aa771c1.10029550_483bdfee9c369863.jpeg', 'active', 8, 1, '2025-12-13 20:09:07', '2025-12-13 20:12:32', '2025-12-13 20:09:07', NULL),
(570, 'Pocket Tissue', '', 'pocket-tissue', 10, 20.00, 20.00, 'piece', 'chakrapath', 'this tissues comes handy (Nepali Name: गोजि रुमाल)', 'item_693dc7cb4210d4.50725305_07d00fb0c135f428.jpeg', 'active', 8, 1, '2025-12-13 20:09:10', '2025-12-13 20:11:40', '2025-12-13 20:09:10', NULL),
(571, 'all in one cable', NULL, 'all-in-one-cable-693deb8d49802', 9, 110.00, 110.00, 'kg', 'Bangemuda shop no 32', 'it is a really amazing product cause i have all the cables that i need to work wth.', 'item_693deb6cb5b8a5.83872154_620cffddb01028bd.jpg', 'active', 8, 1, '2025-12-13 22:41:17', '2025-12-13 22:41:17', '2025-12-13 22:41:17', NULL),
(572, 'data cable', NULL, 'data-cable', 9, 100.00, 100.00, 'kg', 'kamal pokhari , Pranish Store', 'Good service looks nice according to the prize', 'item_693decdfccece2.64227110_615b7ba3726de736.jpg', 'active', 8, 1, '2025-12-13 22:47:16', '2025-12-13 22:47:16', '2025-12-13 22:47:16', NULL),
(573, 'tomato', NULL, 'tomato-693e5dd307e5d', 1, 120.00, 120.00, 'kg', 'kalimati', 'it is fresh (Nepali Name: टोमाटर)', 'item_693e5da8dbb1f9.28836287_7ff4f8923dfb7f2b.png', 'active', 8, 1, '2025-12-14 06:48:51', '2025-12-14 06:48:51', '2025-12-14 06:48:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_tags`
--

CREATE TABLE `item_tags` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `item_tags`
--
DELIMITER $$
CREATE TRIGGER `trg_after_delete_item_tags` AFTER DELETE ON `item_tags` FOR EACH ROW BEGIN
    UPDATE tags
    SET usage_count = usage_count - 1
    WHERE tag_id = OLD.tag_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_insert_item_tags` AFTER INSERT ON `item_tags` FOR EACH ROW BEGIN
    UPDATE tags
    SET usage_count = usage_count + 1
    WHERE tag_id = NEW.tag_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `price_history`
--

CREATE TABLE `price_history` (
  `history_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `price_change` decimal(10,2) GENERATED ALWAYS AS (`new_price` - `old_price`) STORED,
  `price_change_percent` decimal(5,2) GENERATED ALWAYS AS ((`new_price` - `old_price`) / `old_price` * 100) STORED,
  `market_location` varchar(100) DEFAULT NULL,
  `source` varchar(200) DEFAULT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_history`
--

INSERT INTO `price_history` (`history_id`, `item_id`, `old_price`, `new_price`, `market_location`, `source`, `updated_by`, `updated_at`) VALUES
(1, 555, 54455445.00, 259.00, NULL, NULL, 1, '2025-12-01 07:00:03'),
(2, 555, 259.00, 8878.00, NULL, NULL, 1, '2025-12-01 07:01:23'),
(3, 560, 95.00, 154.00, NULL, NULL, 1, '2025-12-13 18:36:37'),
(4, 570, 20.00, 22.00, NULL, NULL, 1, '2025-12-13 20:11:19'),
(5, 570, 22.00, 21.00, NULL, NULL, 1, '2025-12-13 20:11:31'),
(6, 570, 21.00, 20.00, NULL, NULL, 1, '2025-12-13 20:11:40'),
(7, 569, 1100.00, 2100.00, NULL, NULL, 1, '2025-12-13 20:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'Sasto Mahango', 'text', 'Site name', NULL, '2025-11-21 16:03:33'),
(2, 'site_tagline', 'Your Trusted Market Intelligence Platform', 'text', 'Site tagline', NULL, '2025-11-21 16:03:33'),
(3, 'contact_email', 'contact@sastomahango.com', 'text', 'Contact email', NULL, '2025-11-21 16:03:33'),
(4, 'contact_phone', '+977-1-XXXXXXX', 'text', 'Contact phone', NULL, '2025-11-21 16:03:33'),
(5, 'price_change_alert_threshold', '20', 'number', 'Price change alert threshold percentage', NULL, '2025-11-21 16:03:33'),
(6, 'items_per_page', '20', 'number', 'Items per page in listings', NULL, '2025-11-21 16:03:33'),
(7, 'enable_nepali_language', '1', 'boolean', 'Enable Nepali language support', NULL, '2025-11-21 16:03:33'),
(8, 'ticker_update_interval', '5000', 'number', 'Live ticker update interval in milliseconds', NULL, '2025-11-21 16:03:33'),
(9, 'max_tags_per_item', '10', 'number', 'Maximum tags allowed per item', NULL, '2025-11-21 16:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `user_id`, `action_type`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 16:55:00'),
(2, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 16:58:48'),
(3, NULL, 'create', 'user', 2, 'User manual_test_1763744561 registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:02:41'),
(4, NULL, 'create', 'user', 3, 'User seq_test_1763744628 registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:03:48'),
(5, NULL, 'create', 'user', 4, 'User log_test_user registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:05:45'),
(6, 4, 'login', 'user', 4, 'User log_test_user logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:05:45'),
(7, NULL, 'create', 'user', 5, 'User Bhu Pu Sainik registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:08:44'),
(8, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:08:44'),
(9, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:14:04'),
(10, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:46:56'),
(11, 4, 'create', 'validation_queue', 1, 'New item submitted for validation: Test Momo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:50:09'),
(12, 5, 'create', 'validation_queue', 2, 'New item submitted for validation: momo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:52:20'),
(13, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:52:28'),
(14, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:52:43'),
(15, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 18:00:36'),
(16, 1, 'validate', 'validation_queue', 1, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 18:04:16'),
(17, 1, 'validate', 'validation_queue', 2, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 18:04:20'),
(18, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 18:04:54'),
(19, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 03:47:52'),
(20, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 04:04:00'),
(21, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 04:10:40'),
(22, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 10:44:00'),
(23, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 12:28:18'),
(24, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 12:46:23'),
(25, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:21:48'),
(26, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:22:21'),
(27, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:59:21'),
(28, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 14:14:48'),
(29, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 14:55:21'),
(30, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 14:55:36'),
(31, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 15:02:49'),
(32, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 15:19:32'),
(33, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 15:19:49'),
(34, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 15:37:01'),
(35, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 15:37:20'),
(36, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 16:11:21'),
(37, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 16:11:29'),
(38, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:08:42'),
(39, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:09:38'),
(40, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:21:55'),
(41, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:22:45'),
(42, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:23:03'),
(43, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:29:30'),
(44, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:29:44'),
(45, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:31:16'),
(46, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:31:26'),
(47, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:36:14'),
(48, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:36:56'),
(49, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:40:12'),
(50, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:44:26'),
(51, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:51:46'),
(52, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:56:28'),
(53, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 11:07:33'),
(54, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 11:31:00'),
(55, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 11:31:00'),
(56, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 11:31:17'),
(57, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:06:23'),
(58, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:06:43'),
(59, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:10:08'),
(60, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:10:23'),
(61, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:10:29'),
(62, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 12:11:00'),
(63, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 16:56:54'),
(64, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:22:24'),
(65, 5, 'create', 'validation_queue', 3, 'New item submitted for validation: se', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:22:49'),
(66, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:23:13'),
(67, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:23:48'),
(68, 1, 'reject', 'validation_queue', 3, 'Validation rejected: dsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:24:08'),
(69, 1, 'validation_rejected', 'Validation queue #3 rejected', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:24:08'),
(70, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 07:24:50'),
(71, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 09:40:38'),
(72, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 09:41:09'),
(73, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 09:41:33'),
(74, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 09:46:04'),
(75, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:36:45'),
(76, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:37:33'),
(77, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:39:40'),
(78, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:39:57'),
(79, 5, 'create', 'validation_queue', 4, 'New item submitted for validation: f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:41:23'),
(80, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:41:59'),
(81, 1, 'validate', 'validation_queue', 4, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:42:14'),
(82, 1, 'validation_approved', 'Validation queue #4 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:42:14'),
(83, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:42:45'),
(84, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:43:58'),
(85, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:01:19'),
(86, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:01:46'),
(87, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:02:52'),
(88, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:03:00'),
(89, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:03:41'),
(90, 1, 'item_updated', 'Item #26 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:04:15'),
(91, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:04:55'),
(92, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:05:04'),
(93, 5, 'create', 'validation_queue', 5, 'Item edit submitted for item ID: 26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:13:32'),
(94, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:14:13'),
(95, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:14:29'),
(96, 1, 'validate', 'validation_queue', 5, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:16:26'),
(97, 1, 'validation_approved', 'Validation queue #5 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:16:26'),
(98, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:16:46'),
(99, 5, 'create', 'validation_queue', 6, 'Price update submitted for item ID: 82', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:18:03'),
(100, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:18:31'),
(101, 1, 'validate', 'validation_queue', 6, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:18:44'),
(102, 1, 'validation_approved', 'Validation queue #6 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:18:44'),
(103, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:19:08'),
(104, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:19:33'),
(105, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:20:50'),
(106, 5, 'create', 'validation_queue', 7, 'Price update submitted for item ID: 44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:21:21'),
(107, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:21:51'),
(108, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:23:30'),
(109, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:23:44'),
(110, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:25:25'),
(111, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:27:21'),
(112, 5, 'create', 'validation_queue', 8, 'Price update submitted for item ID: 26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:18'),
(113, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:32'),
(114, 1, 'validate', 'validation_queue', 7, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:40'),
(115, 1, 'validation_approved', 'Validation queue #7 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:40'),
(116, 1, 'validate', 'validation_queue', 8, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:43'),
(117, 1, 'validation_approved', 'Validation queue #8 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:28:43'),
(118, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:29:07'),
(119, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:41:33'),
(120, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 12:42:48'),
(121, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:22:59'),
(122, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:24:27'),
(123, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:24:40'),
(124, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:28:27'),
(125, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:29:58'),
(126, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:30:08'),
(127, 5, 'create', 'validation_queue', 9, 'New item submitted for validation: Apple', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:30:36'),
(128, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:31:12'),
(129, 1, 'validate', 'validation_queue', 9, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:31:38'),
(130, 1, 'validation_approved', 'Validation queue #9 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:31:38'),
(131, 1, 'item_updated', 'Item #104 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:32:12'),
(132, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:43:01'),
(133, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:43:09'),
(134, 5, 'create', 'validation_queue', 10, 'Item edit submitted for item ID: 104', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:43:28'),
(135, 1, 'login', 'user', 1, 'User Admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:43:52'),
(136, 1, 'validate', 'validation_queue', 10, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:44:35'),
(137, 1, 'validation_approved', 'Validation queue #10 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:44:35'),
(138, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:45:14'),
(139, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:45:22'),
(140, 5, 'create', 'validation_queue', 11, 'Price update submitted for item ID: 104', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:47:00'),
(141, 1, 'login', 'user', 1, 'User admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:47:17'),
(142, 1, 'validate', 'validation_queue', 11, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:47:26'),
(143, 1, 'validation_approved', 'Validation queue #11 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:47:26'),
(144, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:50:38'),
(145, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:50:47'),
(146, 5, 'create', 'validation_queue', 12, 'Price update submitted for item ID: 29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:28:40'),
(147, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:29:06'),
(148, 1, 'validate', 'validation_queue', 12, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:29:13'),
(149, 1, 'validation_approved', 'Validation queue #12 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:29:13'),
(150, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:36:38'),
(151, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:49:16'),
(152, 5, 'create', 'validation_queue', 13, 'Item edit submitted for item ID: 108', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 14:54:54'),
(153, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:12:57'),
(154, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:40:14'),
(155, 5, 'create', 'validation_queue', 14, 'Price update submitted for item ID: 51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:40:38'),
(156, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:40:52'),
(157, 1, 'validate', 'validation_queue', 13, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:41:01'),
(158, 1, 'validation_approved', 'Validation queue #13 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:41:01'),
(159, 1, 'validate', 'validation_queue', 14, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:41:04'),
(160, 1, 'validation_approved', 'Validation queue #14 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:41:04'),
(161, 1, 'item_updated', 'Item #10 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:50:08'),
(162, 1, 'item_updated', 'Item #146 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:50:26'),
(163, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:52:43'),
(164, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:57:18'),
(165, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 15:57:28'),
(166, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 16:51:19'),
(167, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:12:53'),
(168, 5, 'create', 'validation_queue', 15, 'New item submitted for validation: cx', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:18:34'),
(169, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:18:55'),
(170, 1, 'validate', 'validation_queue', 15, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:19:02'),
(171, 1, 'validation_approved', 'Validation queue #15 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:19:02'),
(172, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:19:15'),
(173, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:21:43'),
(174, 5, 'create', 'validation_queue', 16, 'New item submitted for validation: turi', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:22:28'),
(175, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:22:44'),
(176, 1, 'validate', 'validation_queue', 16, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:22:54'),
(177, 1, 'validation_approved', 'Validation queue #16 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:22:54'),
(178, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:23:20'),
(179, 5, 'create', 'validation_queue', 17, 'New item submitted for validation: momo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:29:00'),
(180, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:29:24'),
(181, 1, 'validate', 'validation_queue', 17, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:29:31'),
(182, 1, 'validation_approved', 'Validation queue #17 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:29:31'),
(183, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:29:59'),
(184, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:37:55'),
(185, 1, 'item_updated', 'Item #510 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:38:26'),
(186, 1, 'item_updated', 'Item #510 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:38:35'),
(187, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:39:59'),
(188, 5, 'create', 'validation_queue', 18, 'New item submitted for validation: Apple', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:40:41'),
(189, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:41:05'),
(190, 1, 'validate', 'validation_queue', 18, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:41:11'),
(191, 1, 'validation_approved', 'Validation queue #18 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:41:11'),
(192, 5, 'create', 'validation_queue', 19, 'New item submitted for validation: Air Freshener', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:44:25'),
(193, 1, 'validate', 'validation_queue', 19, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:44:46'),
(194, 1, 'validation_approved', 'Validation queue #19 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:44:46'),
(195, 5, 'create', 'validation_queue', 20, 'New item submitted for validation: turi', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 17:59:02'),
(196, 1, 'validate', 'validation_queue', 20, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 18:01:19'),
(197, 1, 'validation_approved', 'Validation queue #20 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 18:01:19'),
(198, 5, 'create', 'validation_queue', 21, 'New item submitted for validation: se', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 18:06:18'),
(199, 1, 'validate', 'validation_queue', 21, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 18:06:31'),
(200, 1, 'validation_approved', 'Validation queue #21 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 18:06:31'),
(201, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:48:32'),
(202, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:49:28'),
(203, 5, 'create', 'validation_queue', 22, 'Price update submitted for item ID: 555', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:50:30'),
(204, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:53:30'),
(205, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:53:40'),
(206, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:53:40'),
(207, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:53:45'),
(208, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin@mulyasuchi.com from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:56:56'),
(209, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:57:01'),
(210, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:58:20'),
(211, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:58:20'),
(212, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 06:59:53'),
(213, 1, 'validate', 'validation_queue', 22, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:00:03'),
(214, 1, 'validation_approved', 'Validation queue #22 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:00:03'),
(215, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:00:43'),
(216, 5, 'create', 'validation_queue', 23, 'Price update submitted for item ID: 555', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:01:13'),
(217, 1, 'validate', 'validation_queue', 23, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:01:23'),
(218, 1, 'validation_approved', 'Validation queue #23 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:01:23'),
(219, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:34:06'),
(220, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 07:55:05'),
(221, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 01:49:13'),
(222, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 09:50:56'),
(223, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for admin from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 09:51:02'),
(224, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 09:51:09'),
(225, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 10:41:05'),
(226, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:02:20'),
(227, 5, 'create', 'validation_queue', 24, 'New item submitted for validation: red bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:07'),
(228, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:16'),
(229, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:30'),
(230, 1, 'validate', 'validation_queue', 24, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:37'),
(231, 1, 'validation_approved', 'Validation queue #24 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:37'),
(232, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:03:54'),
(233, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:35:59'),
(234, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:35:59'),
(235, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 15:52:39'),
(236, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 15:53:18'),
(237, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:03:46'),
(238, 1, 'item_updated', 'Item #555 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:07:26'),
(239, 1, 'item_updated', 'Item #28 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:08:58'),
(240, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:09:37');
INSERT INTO `system_logs` (`log_id`, `user_id`, `action_type`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(241, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:09:49'),
(242, 5, 'create', 'validation_queue', 25, 'New item submitted for validation: sakshyam', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:10:40'),
(243, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:10:45'),
(244, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:10:58'),
(245, 1, 'validate', 'validation_queue', 25, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:11:07'),
(246, 1, 'validation_approved', 'Validation queue #25 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:11:07'),
(247, 1, 'item_updated', 'Item #559 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:14:03'),
(248, 1, 'item_updated', 'Item #559 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:14:44'),
(249, 1, 'item_edit_attempt', 'Editing item #559 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:25:26'),
(250, 1, 'item_image_upload', 'Item #559 - New image uploaded: item_1765646726_69', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:25:26'),
(251, 1, 'item_image_update', 'Item #559 - Image changed from \"assets/uploads/ite', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:25:26'),
(252, 1, 'item_updated', 'Item #559 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-13 17:25:26'),
(253, 1, 'item_edit_attempt', 'Editing item #555 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:26:13'),
(254, 1, 'item_image_upload', 'Item #555 - New image uploaded: item_1765646773_69', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:26:13'),
(255, 1, 'item_image_update', 'Item #555 - Image changed from \"assets/uploads/ite', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:26:13'),
(256, 1, 'item_updated', 'Item #555 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:26:13'),
(257, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:30:47'),
(258, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for Bhu Pu Sainik from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:31:28'),
(259, 5, 'login', 'user', 5, 'User Bhu Pu Sainik logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 17:31:32'),
(260, 5, 'logout', 'user', 5, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:11:47'),
(261, NULL, 'create', 'user', 8, 'User sakshyam Bastakoti registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:25:52'),
(262, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:25:52'),
(263, 8, 'create', 'validation_queue', 26, 'New item submitted for validation: cable', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:27:56'),
(264, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:28:07'),
(265, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:28:20'),
(266, 1, 'validate', 'validation_queue', 26, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:28:27'),
(267, 1, 'validation_approved', 'Validation queue #26 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:28:27'),
(268, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:28:32'),
(269, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:33:21'),
(270, 8, 'create', 'validation_queue', 27, 'Item edit submitted for item ID: 555', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:33:54'),
(271, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:34:00'),
(272, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:34:36'),
(273, 1, 'validate', 'validation_queue', 27, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:34:43'),
(274, 1, 'validation_approved', 'Validation queue #27 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:34:43'),
(275, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:35:27'),
(276, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:35:34'),
(277, 8, 'create', 'validation_queue', 28, 'Price update submitted for item ID: 560', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:35:46'),
(278, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:08'),
(279, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:13'),
(280, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:17'),
(281, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:29'),
(282, 1, 'validate', 'validation_queue', 28, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:37'),
(283, 1, 'validation_approved', 'Validation queue #28 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:37'),
(284, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:43'),
(285, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:36:50'),
(286, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 18:37:43'),
(287, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:17:45'),
(288, 8, 'create', 'validation_queue', 29, 'New item submitted for validation: Black Arabic Aura Watch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:33:07'),
(289, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:33:30'),
(290, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:33:30'),
(291, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:33:35'),
(292, 8, 'create', 'validation_queue', 30, 'New item submitted for validation: Fantech Mouse', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:34:32'),
(293, 8, 'create', 'validation_queue', 31, 'New item submitted for validation: All in one cable', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:37:27'),
(294, 8, 'create', 'validation_queue', 32, 'New item submitted for validation: Muffin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:15'),
(295, 1, 'validate', 'validation_queue', 29, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:37'),
(296, 1, 'validation_approved', 'Validation queue #29 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:37'),
(297, 1, 'validate', 'validation_queue', 30, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:41'),
(298, 1, 'validation_approved', 'Validation queue #30 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:41'),
(299, 1, 'validate', 'validation_queue', 31, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:44'),
(300, 1, 'validation_approved', 'Validation queue #31 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:44'),
(301, 1, 'validate', 'validation_queue', 32, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:47'),
(302, 1, 'validation_approved', 'Validation queue #32 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:38:47'),
(303, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 19:51:39'),
(304, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:01:15'),
(305, 8, 'create', 'validation_queue', 33, 'New item submitted for validation: White Hoodie', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:02:39'),
(306, 8, 'create', 'validation_queue', 34, 'New item submitted for validation: Red Bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:03:54'),
(307, 8, 'create', 'validation_queue', 35, 'New item submitted for validation: Casio Calculator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:06:07'),
(308, 8, 'create', 'validation_queue', 36, 'New item submitted for validation: Black Hoodie', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:06:47'),
(309, 8, 'create', 'validation_queue', 37, 'New item submitted for validation: Green Hoodie', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:07:22'),
(310, 8, 'create', 'validation_queue', 38, 'New item submitted for validation: Pocket Tissue', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:08:43'),
(311, 1, 'validate', 'validation_queue', 33, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:08:56'),
(312, 1, 'validation_approved', 'Validation queue #33 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:08:56'),
(313, 1, 'validate', 'validation_queue', 34, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:08:59'),
(314, 1, 'validation_approved', 'Validation queue #34 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:08:59'),
(315, 1, 'validate', 'validation_queue', 35, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:02'),
(316, 1, 'validation_approved', 'Validation queue #35 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:02'),
(317, 1, 'validate', 'validation_queue', 36, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:05'),
(318, 1, 'validation_approved', 'Validation queue #36 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:05'),
(319, 1, 'validate', 'validation_queue', 37, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:07'),
(320, 1, 'validation_approved', 'Validation queue #37 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:07'),
(321, 1, 'validate', 'validation_queue', 38, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:10'),
(322, 1, 'validation_approved', 'Validation queue #38 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:09:10'),
(323, 1, 'item_edit_attempt', 'Editing item #570 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:19'),
(324, 1, 'item_image_update', 'Item #570 - Image unchanged: \"item_693dc7cb4210d4.', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:19'),
(325, 1, 'item_updated', 'Item #570 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:19'),
(326, 1, 'item_edit_attempt', 'Editing item #570 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:31'),
(327, 1, 'item_image_update', 'Item #570 - Image unchanged: \"item_693dc7cb4210d4.', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:31'),
(328, 1, 'item_updated', 'Item #570 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:31'),
(329, 1, 'item_edit_attempt', 'Editing item #570 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:40'),
(330, 1, 'item_image_update', 'Item #570 - Image unchanged: \"item_693dc7cb4210d4.', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:40'),
(331, 1, 'item_updated', 'Item #570 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:11:40'),
(332, 1, 'item_edit_attempt', 'Editing item #569 - Files received: {\"item_image\":', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:12:32'),
(333, 1, 'item_image_update', 'Item #569 - Image unchanged: \"item_693dc77aa771c1.', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:12:32'),
(334, 1, 'item_updated', 'Item #569 updated by admin', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 20:12:32'),
(335, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:33:14'),
(336, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:34:25'),
(337, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:37:15'),
(338, 8, 'create', 'validation_queue', 39, 'New item submitted for validation: all in one cable', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:40:44'),
(339, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:40:52'),
(340, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:41:09'),
(341, 1, 'validate', 'validation_queue', 39, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:41:17'),
(342, 1, 'validation_approved', 'Validation queue #39 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:41:17'),
(343, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:42:03'),
(344, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:42:13'),
(345, 8, 'create', 'validation_queue', 40, 'New item submitted for validation: data cable', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:46:55'),
(346, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:47:09'),
(347, 1, 'validate', 'validation_queue', 40, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:47:16'),
(348, 1, 'validation_approved', 'Validation queue #40 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:47:16'),
(349, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:47:27'),
(350, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:53:15'),
(351, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:57:23'),
(352, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for Sakshyam Bastakoti from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:57:47'),
(353, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for sakshyam Bastakoti from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:59:01'),
(354, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for sakshyam Bastakoti from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:59:12'),
(355, NULL, 'failed_login_attempt', 'user', NULL, 'Failed login attempt for sakshyam Bastakoti from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:59:19'),
(356, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 22:59:56'),
(357, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 03:36:59'),
(358, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 04:05:46'),
(359, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 06:47:03'),
(360, 8, 'create', 'validation_queue', 41, 'New item submitted for validation: tomato', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 06:48:08'),
(361, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 06:48:35'),
(362, 1, 'validate', 'validation_queue', 41, 'Validation approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 06:48:51'),
(363, 1, 'validation_approved', 'Validation queue #41 approved', NULL, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-14 06:48:51'),
(364, 8, 'login', 'user', 8, 'User sakshyam Bastakoti logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 01:25:10'),
(365, 8, 'logout', 'user', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 01:26:35'),
(366, 1, 'login', 'user', 1, 'User admin logged in from ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 01:26:49'),
(367, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 02:15:36');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `tag_name` varchar(50) NOT NULL,
  `tag_name_nepali` varchar(50) DEFAULT NULL,
  `usage_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`, `tag_name_nepali`, `usage_count`, `created_at`) VALUES
(1, 'Seasonal', 'Ã Â¤Â®Ã Â¥Å’Ã Â¤Â¸Ã Â¤Â®Ã Â¥â‚¬', 2, '2025-11-21 16:03:33'),
(2, 'Imported', 'Ã Â¤â€ Ã Â¤Â¯Ã Â¤Â¾Ã Â¤Â¤Ã Â¤Â¿Ã Â¤Â¤', 0, '2025-11-21 16:03:33'),
(3, 'Local', 'Ã Â¤Â¸Ã Â¥ÂÃ Â¤Â¥Ã Â¤Â¾Ã Â¤Â¨Ã Â¥â‚¬Ã Â¤Â¯', 2, '2025-11-21 16:03:33'),
(4, 'Organic', 'Ã Â¤â€¦Ã Â¤Â°Ã Â¥ÂÃ Â¤â€”Ã Â¤Â¾Ã Â¤Â¨Ã Â¤Â¿Ã Â¤â€', 0, '2025-11-21 16:03:33'),
(5, 'Fresh', 'Ã Â¤Â¤Ã Â¤Â¾Ã Â¤Å“Ã Â¤Â¾', 4, '2025-11-21 16:03:33'),
(6, 'Frozen', 'Ã Â¤Å“Ã Â¤Â®Ã Â¤Â¾Ã Â¤ÂÃ Â¤â€¢Ã Â¥â€¹', 0, '2025-11-21 16:03:33'),
(7, 'Discount', 'Ã Â¤â€ºÃ Â¥ÂÃ Â¤Å¸', 0, '2025-11-21 16:03:33'),
(8, 'Premium', 'Ã Â¤ÂªÃ Â¥ÂÃ Â¤Â°Ã Â¤Â¿Ã Â¤Â®Ã Â¤Â¿Ã Â¤Â¯Ã Â¤Â®', 1, '2025-11-21 16:03:33'),
(9, 'New', 'Ã Â¤Â¨Ã Â¤Â¯Ã Â¤Â¾Ã Â¤Â', 0, '2025-11-21 16:03:33'),
(10, 'Popular', 'Ã Â¤Â²Ã Â¥â€¹Ã Â¤â€¢Ã Â¤ÂªÃ Â¥ÂÃ Â¤Â°Ã Â¤Â¿Ã Â¤Â¯', 0, '2025-11-21 16:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('contributor','admin') NOT NULL DEFAULT 'contributor',
  `status` enum('active','suspended','inactive') NOT NULL DEFAULT 'active',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `full_name`, `role`, `status`, `phone`, `created_at`, `updated_at`, `last_login`, `created_by`) VALUES
(1, 'admin', 'admin@sastomahango.com', '$2a$12$zL3kKQ7sYJzQx8ceVNhkT.gTv6uGd91izRFTlG20ky6c55CCIP.FO', 'Master Admin', 'admin', 'active', NULL, '2025-11-21 16:03:33', '2025-12-15 01:26:49', '2025-12-15 01:26:49', NULL),
(2, 'manual_test_1763744561', 'manual@test.com', '$2y$10$z4MYRts1lCfvO5ZoWw/ihuM07G2yDgHPdfAP1OpNtFD.ALkjoY8Ty', 'Manual Test User', 'contributor', 'active', NULL, '2025-11-21 17:02:41', '2025-11-21 17:02:41', NULL, NULL),
(3, 'seq_test_1763744628', 'seq@test.com', '$2y$10$6b8aJ92YsMr8G.KTdr1DveOwDnGePTpQOrUJTwVlQKtUtmXF9CPCW', 'Seq Test User', 'contributor', 'active', NULL, '2025-11-21 17:03:48', '2025-11-21 17:03:48', NULL, NULL),
(4, 'log_test_user', 'log@test.com', '$2y$10$bocnACxdHk9JzYrKxHDIeemzIY8UYTLOPdYe4TZ7QYUE8bTBPQzWu', 'Log Test User', 'contributor', 'active', NULL, '2025-11-21 17:05:45', '2025-11-21 17:05:45', '2025-11-21 17:05:45', NULL),
(5, 'Bhu Pu Sainik', 'sakshyamxeetri@gmail.com', '$2y$10$Eh/trm8jcgJwVAfKZ4mofuwm6eI6rKpBQG./0.KBOyEGotCe38lhC', 'Sakshyam Bastakoti', 'contributor', 'active', NULL, '2025-11-21 17:08:44', '2025-12-13 17:31:32', '2025-12-13 17:31:32', NULL),
(7, 'contributor1', 'contributor@sastomahango.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sample Contributor', 'contributor', 'active', NULL, '2025-11-25 14:38:55', '2025-11-25 14:38:55', NULL, 1),
(8, 'sakshyam Bastakoti', 'sakchyambastakoti36@gmail.com', '$2y$10$TekhJ.THTSolbxqZSHePF.GLJGAuw2fHHDRyWFOvvURzcNEP1fOsy', 'Sakshyam Bastakoti', 'contributor', 'active', NULL, '2025-12-13 18:25:52', '2025-12-15 01:25:10', '2025-12-15 01:25:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `validation_queue`
--

CREATE TABLE `validation_queue` (
  `queue_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED DEFAULT NULL,
  `action_type` enum('new_item','price_update') NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `market_location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `source` varchar(200) DEFAULT NULL,
  `submitted_by` int(10) UNSIGNED NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `validated_by` int(10) UNSIGNED DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `validation_queue`
--

INSERT INTO `validation_queue` (`queue_id`, `item_id`, `action_type`, `old_price`, `new_price`, `item_name`, `category_id`, `unit`, `market_location`, `description`, `image_path`, `source`, `submitted_by`, `submitted_at`, `status`, `validated_by`, `validated_at`, `rejection_reason`) VALUES
(1, NULL, 'new_item', NULL, 150.00, 'Test Momo', NULL, 'plate', 'Test Market', 'Delicious test momo', NULL, NULL, 4, '2025-11-21 17:50:09', 'approved', 1, '2025-11-21 18:04:16', NULL),
(2, NULL, 'new_item', NULL, 15.03, 'momo', NULL, '10', 'kalimati', 'idjnf (Nepali Name: momom)', NULL, NULL, 5, '2025-11-21 17:52:20', 'approved', 1, '2025-11-21 18:04:20', NULL),
(3, NULL, 'new_item', NULL, 545.00, 'se', NULL, 'sd', 'sd', 'sds', NULL, NULL, 5, '2025-11-25 07:22:49', 'rejected', 1, '2025-11-25 07:24:08', 'dsd'),
(4, NULL, 'new_item', NULL, 44.00, 'f', NULL, 'ghg', 'ghg', 'gh (Nepali Name: gfg)', NULL, NULL, 5, '2025-11-25 11:41:23', 'approved', 1, '2025-11-25 11:42:14', NULL),
(9, NULL, 'new_item', NULL, 320.00, 'Apple', NULL, 'kg', 'gurjodhara', 'it too good', NULL, NULL, 5, '2025-11-25 13:30:36', 'approved', 1, '2025-11-25 13:31:38', NULL),
(15, NULL, 'new_item', NULL, 1445.00, 'cx', 11, '252', '32', '53 (Nepali Name: xc)', NULL, NULL, 5, '2025-11-25 17:18:34', 'approved', 1, '2025-11-25 17:19:02', NULL),
(16, NULL, 'new_item', NULL, 536.00, 'turi', 10, '536', '5365', '24', NULL, NULL, 5, '2025-11-25 17:22:28', 'approved', 1, '2025-11-25 17:22:54', NULL),
(17, NULL, 'new_item', NULL, 5620.00, 'momo', 4, '56', '44', 'gvcfgvbh (Nepali Name: Apple)', NULL, NULL, 5, '2025-11-25 17:29:00', 'approved', 1, '2025-11-25 17:29:31', NULL),
(18, NULL, 'new_item', NULL, 1520.00, 'Apple', 11, 'kg', 'kalimati', 'in the course', 'item_6925ea19109a62.22981853_e645dd6b2f8cc732.jpeg', NULL, 5, '2025-11-25 17:40:41', 'approved', 1, '2025-11-25 17:41:11', NULL),
(19, NULL, 'new_item', NULL, 54455445.00, 'Air Freshener', 3, '6554', '55', '545445', 'item_6925eaf9a45524.60092466_f0c3eb3e11be9fc3.jpg', NULL, 5, '2025-11-25 17:44:25', 'approved', 1, '2025-11-25 17:44:46', NULL),
(20, NULL, 'new_item', NULL, 155.00, 'turi', 11, '44', 'kalimati', '5545', 'item_6925ee6624e817.16934620_1dbd5183564dbfec.png', NULL, 5, '2025-11-25 17:59:02', 'approved', 1, '2025-11-25 18:01:19', NULL),
(21, NULL, 'new_item', NULL, 554.00, 'se', 12, '4554', 'dcv', 'dfvd', 'item_6925f01a327e38.86108597_0cddb22ee7c3dfb9.png', NULL, 5, '2025-11-25 18:06:18', 'approved', 1, '2025-11-25 18:06:31', NULL),
(22, 555, 'price_update', 54455445.00, 259.00, NULL, NULL, NULL, NULL, NULL, NULL, '55', 5, '2025-12-01 06:50:30', 'approved', 1, '2025-12-01 07:00:03', NULL),
(23, 555, 'price_update', 259.00, 8878.00, NULL, NULL, NULL, NULL, NULL, NULL, '55', 5, '2025-12-01 07:01:13', 'approved', 1, '2025-12-01 07:01:23', NULL),
(24, NULL, 'new_item', NULL, 110.00, 'red bull', 6, '1', 'Texas college', 'it tastes too', NULL, NULL, 5, '2025-12-13 13:03:07', 'approved', 1, '2025-12-13 13:03:37', NULL),
(25, NULL, 'new_item', NULL, 2.69, 'sakshyam', 5, 'night', 'Thamel', 'ok (Nepali Name: local maal)', 'item_693d9e10b86d64.83398779_f7500ea2547f35ee.jpg', NULL, 5, '2025-12-13 17:10:40', 'approved', 1, '2025-12-13 17:11:07', NULL),
(26, NULL, 'new_item', NULL, 95.00, 'cable', 9, '1', 'Ason , Bishal Bazar', 'Good product at reasonable price.', 'item_693db02c7d0605.09917644_6c94607ab8fa7041.jpg', NULL, 8, '2025-12-13 18:27:56', 'approved', 1, '2025-12-13 18:28:27', NULL),
(27, 555, '', NULL, 8878.00, 'Air Freshener', 3, 'kg', '55', '545445', 'item_693db192d8ccc3.16638894_6241f5e79b57cef4.jpg', '', 8, '2025-12-13 18:33:54', 'approved', 1, '2025-12-13 18:34:43', NULL),
(28, 560, 'price_update', 95.00, 154.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Ason , Bishal Bazar', 8, '2025-12-13 18:35:46', 'approved', 1, '2025-12-13 18:36:37', NULL),
(29, NULL, 'new_item', NULL, 550.00, 'Black Arabic Aura Watch', 8, 'pcs', 'Durbar Marg Rolex Store', 'it is a good watch (Nepali Name: कालो अरबिक अउरा घडी)', 'item_693dbf738f60c0.35091426_03c0704dae7008ed.jpeg', NULL, 8, '2025-12-13 19:33:07', 'approved', 1, '2025-12-13 19:38:37', NULL),
(30, NULL, 'new_item', NULL, 1200.00, 'Fantech Mouse', 12, 'pcs', 'Bishal Bazar', 'It is a good mouse (Nepali Name: फानटेक माउस)', 'item_693dbfc8a340e5.12749033_5eddf373c9692a97.jpeg', NULL, 8, '2025-12-13 19:34:32', 'approved', 1, '2025-12-13 19:38:41', NULL),
(31, NULL, 'new_item', NULL, 120.00, 'All in one cable', 12, 'pcs', 'Kailash Marg Nagarkot', 'it is very useful (Nepali Name: अल ईन वान केबल)', 'item_693dc077212431.80818999_6ce3eb6b2513a513.jpg', NULL, 8, '2025-12-13 19:37:27', 'approved', 1, '2025-12-13 19:38:44', NULL),
(32, NULL, 'new_item', NULL, 12.50, 'Muffin', 4, 'pcs', 'krishna pauroti, putalisadak', 'it is very tasty (Nepali Name: मफिन)', 'item_693dc0a7ed5af0.20449284_d71f339d240b5de0.jpeg', NULL, 8, '2025-12-13 19:38:15', 'approved', 1, '2025-12-13 19:38:47', NULL),
(33, NULL, 'new_item', NULL, 1200.00, 'White Hoodie', 10, 'pcs', 'ason', 'this is agood hoodie (Nepali Name: सेतो हुडी)', 'item_693dc65f8145d1.91216948_cea6c093298bf1c9.jpeg', NULL, 8, '2025-12-13 20:02:39', 'approved', 1, '2025-12-13 20:08:56', NULL),
(34, NULL, 'new_item', NULL, 110.00, 'Red Bull', 2, 'can', 'sifal', 'very tasty (Nepali Name: रातो साँडे)', 'item_693dc6aa458031.34229917_c3be888b8a9c1b06.jpeg', NULL, 8, '2025-12-13 20:03:54', 'approved', 1, '2025-12-13 20:08:59', NULL),
(35, NULL, 'new_item', NULL, 1200.00, 'Casio Calculator', 12, 'pcs', 'bhotahiti', 'very good scientific calculator (Nepali Name: कासियो काल्कुलेटर)', 'item_693dc72fcba2d7.35399646_4f0905db282f45ab.jpeg', NULL, 8, '2025-12-13 20:06:07', 'approved', 1, '2025-12-13 20:09:02', NULL),
(36, NULL, 'new_item', NULL, 1800.00, 'Black Hoodie', 10, 'pcs', 'basantapur', 'good quality (Nepali Name: कालो हुडी)', 'item_693dc7579860e4.75589938_62fbc884a3f6e3c9.jpeg', NULL, 8, '2025-12-13 20:06:47', 'approved', 1, '2025-12-13 20:09:05', NULL),
(37, NULL, 'new_item', NULL, 1100.00, 'Green Hoodie', 10, 'pcs', 'basundhara', 'not that great (Nepali Name: हरियो हुडी)', 'item_693dc77aa771c1.10029550_483bdfee9c369863.jpeg', NULL, 8, '2025-12-13 20:07:22', 'approved', 1, '2025-12-13 20:09:07', NULL),
(38, NULL, 'new_item', NULL, 20.00, 'Pocket Tissue', 10, 'pkt', 'chakrapath', 'this tissues comes handy (Nepali Name: गोजि रुमाल)', 'item_693dc7cb4210d4.50725305_07d00fb0c135f428.jpeg', NULL, 8, '2025-12-13 20:08:43', 'approved', 1, '2025-12-13 20:09:10', NULL),
(39, NULL, 'new_item', NULL, 110.00, 'all in one cable', 9, '1', 'Bangemuda shop no 32', 'it is a really amazing product cause i have all the cables that i need to work wth.', 'item_693deb6cb5b8a5.83872154_620cffddb01028bd.jpg', NULL, 8, '2025-12-13 22:40:44', 'approved', 1, '2025-12-13 22:41:17', NULL),
(40, NULL, 'new_item', NULL, 100.00, 'data cable', 9, '1', 'kamal pokhari , Pranish Store', 'Good service looks nice according to the prize', 'item_693decdfccece2.64227110_615b7ba3726de736.jpg', NULL, 8, '2025-12-13 22:46:55', 'approved', 1, '2025-12-13 22:47:16', NULL),
(41, NULL, 'new_item', NULL, 120.00, 'tomato', 1, 'kg', 'kalimati', 'it is fresh (Nepali Name: टोमाटर)', 'item_693e5da8dbb1f9.28836287_7ff4f8923dfb7f2b.png', NULL, 8, '2025-12-14 06:48:08', 'approved', 1, '2025-12-14 06:48:51', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_items`
-- (See below for the actual view)
--
CREATE TABLE `view_active_items` (
`item_id` int(10) unsigned
,`item_name` varchar(200)
,`item_name_nepali` varchar(200)
,`slug` varchar(200)
,`current_price` decimal(10,2)
,`unit` enum('kg','piece','liter','dozen','gram','pack','meter','sq_meter')
,`market_location` varchar(100)
,`image_path` varchar(255)
,`updated_at` timestamp
,`category_name` varchar(100)
,`category_slug` varchar(100)
,`contributor_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pending_validations`
-- (See below for the actual view)
--
CREATE TABLE `view_pending_validations` (
`queue_id` int(10) unsigned
,`action_type` enum('new_item','price_update')
,`item_name` varchar(200)
,`old_price` decimal(10,2)
,`new_price` decimal(10,2)
,`market_location` varchar(100)
,`source` varchar(200)
,`submitted_at` timestamp
,`submitted_by_username` varchar(50)
,`submitted_by_name` varchar(100)
,`category_name` varchar(100)
,`existing_item_name` varchar(200)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_price_trends`
-- (See below for the actual view)
--
CREATE TABLE `view_price_trends` (
`item_id` int(10) unsigned
,`item_name` varchar(200)
,`current_price` decimal(10,2)
,`old_price` decimal(10,2)
,`new_price` decimal(10,2)
,`price_change` decimal(10,2)
,`price_change_percent` decimal(5,2)
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `view_active_items`
--
DROP TABLE IF EXISTS `view_active_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_active_items`  AS SELECT `i`.`item_id` AS `item_id`, `i`.`item_name` AS `item_name`, `i`.`item_name_nepali` AS `item_name_nepali`, `i`.`slug` AS `slug`, `i`.`current_price` AS `current_price`, `i`.`unit` AS `unit`, `i`.`market_location` AS `market_location`, `i`.`image_path` AS `image_path`, `i`.`updated_at` AS `updated_at`, `c`.`category_name` AS `category_name`, `c`.`slug` AS `category_slug`, `u`.`full_name` AS `contributor_name` FROM ((`items` `i` join `categories` `c` on(`i`.`category_id` = `c`.`category_id`)) join `users` `u` on(`i`.`created_by` = `u`.`user_id`)) WHERE `i`.`status` = 'active' AND `c`.`is_active` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `view_pending_validations`
--
DROP TABLE IF EXISTS `view_pending_validations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pending_validations`  AS SELECT `vq`.`queue_id` AS `queue_id`, `vq`.`action_type` AS `action_type`, `vq`.`item_name` AS `item_name`, `vq`.`old_price` AS `old_price`, `vq`.`new_price` AS `new_price`, `vq`.`market_location` AS `market_location`, `vq`.`source` AS `source`, `vq`.`submitted_at` AS `submitted_at`, `u`.`username` AS `submitted_by_username`, `u`.`full_name` AS `submitted_by_name`, `c`.`category_name` AS `category_name`, `i`.`item_name` AS `existing_item_name` FROM (((`validation_queue` `vq` join `users` `u` on(`vq`.`submitted_by` = `u`.`user_id`)) left join `categories` `c` on(`vq`.`category_id` = `c`.`category_id`)) left join `items` `i` on(`vq`.`item_id` = `i`.`item_id`)) WHERE `vq`.`status` = 'pending' ORDER BY `vq`.`submitted_at` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `view_price_trends`
--
DROP TABLE IF EXISTS `view_price_trends`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_price_trends`  AS SELECT `i`.`item_id` AS `item_id`, `i`.`item_name` AS `item_name`, `i`.`current_price` AS `current_price`, `ph`.`old_price` AS `old_price`, `ph`.`new_price` AS `new_price`, `ph`.`price_change` AS `price_change`, `ph`.`price_change_percent` AS `price_change_percent`, `ph`.`updated_at` AS `updated_at` FROM (`items` `i` join `price_history` `ph` on(`i`.`item_id` = `ph`.`item_id`)) WHERE `ph`.`updated_at` >= current_timestamp() - interval 30 day ORDER BY `ph`.`updated_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_item_name` (`item_name`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `validated_by` (`validated_by`),
  ADD KEY `idx_items_status_category` (`status`,`category_id`),
  ADD KEY `idx_items_status_price` (`status`,`current_price`),
  ADD KEY `idx_items_status_updated` (`status`,`updated_at`),
  ADD KEY `idx_items_category_status_price` (`category_id`,`status`,`current_price`),
  ADD KEY `idx_items_deleted_at` (`deleted_at`);
ALTER TABLE `items` ADD FULLTEXT KEY `idx_fulltext_search` (`item_name`,`description`);

--
-- Indexes for table `item_tags`
--
ALTER TABLE `item_tags`
  ADD PRIMARY KEY (`item_id`,`tag_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Indexes for table `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_updated_by` (`updated_by`),
  ADD KEY `idx_updated_at` (`updated_at`),
  ADD KEY `idx_price_history_item_date` (`item_id`,`updated_at`),
  ADD KEY `idx_price_history_date` (`updated_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_logs_user_action_date` (`user_id`,`action_type`,`created_at`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `tag_name` (`tag_name`),
  ADD KEY `idx_tag_name` (`tag_name`),
  ADD KEY `idx_usage_count` (`usage_count`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `validation_queue`
--
ALTER TABLE `validation_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submitted_by` (`submitted_by`),
  ADD KEY `idx_submitted_at` (`submitted_at`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `validated_by` (`validated_by`),
  ADD KEY `idx_validation_status_submitted` (`status`,`submitted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=574;

--
-- AUTO_INCREMENT for table `price_history`
--
ALTER TABLE `price_history`
  MODIFY `history_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `validation_queue`
--
ALTER TABLE `validation_queue`
  MODIFY `queue_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `item_tags`
--
ALTER TABLE `item_tags`
  ADD CONSTRAINT `item_tags_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `price_history_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD CONSTRAINT `site_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `validation_queue`
--
ALTER TABLE `validation_queue`
  ADD CONSTRAINT `validation_queue_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validation_queue_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `validation_queue_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `validation_queue_ibfk_4` FOREIGN KEY (`validated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
