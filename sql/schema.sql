-- ===========================================================================
-- MULYASUCHI DATABASE SCHEMA
-- ===========================================================================
-- Project: Mulyasuchi (Market Intelligence Platform for Nepal)
-- Version: 1.0.0
-- Database: MySQL 8.0+
-- Character Set: utf8mb4_unicode_ci
-- ===========================================================================

-- ===========================================================================
-- DATABASE INITIALIZATION
-- ===========================================================================

CREATE DATABASE IF NOT EXISTS sastomahango_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sastomahango_db;

-- ===========================================================================
-- TABLE: users
-- Description: Stores all system users (Contributors and Admins)
-- ===========================================================================

CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('contributor', 'admin') NOT NULL DEFAULT 'contributor',
    status ENUM('active', 'suspended', 'inactive') NOT NULL DEFAULT 'active',
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: categories
-- Description: Item categories (Vegetables, Fruits, Tech, etc.)
-- ===========================================================================

CREATE TABLE categories (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_name_nepali VARCHAR(100) DEFAULT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    icon_class VARCHAR(50) DEFAULT NULL,
    display_order INT UNSIGNED DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: items
-- Description: Core items in the marketplace
-- ===========================================================================

CREATE TABLE items (
    item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(200) NOT NULL,
    item_name_nepali VARCHAR(200) DEFAULT NULL,
    slug VARCHAR(200) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    current_price DECIMAL(10, 2) NOT NULL,
    unit ENUM('kg', 'piece', 'liter', 'dozen', 'gram', 'pack', 'meter', 'sq_meter') NOT NULL DEFAULT 'piece',
    market_location VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'pending', 'inactive') NOT NULL DEFAULT 'pending',
    created_by INT UNSIGNED NOT NULL,
    validated_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    validated_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_item_name (item_name),
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by),
    FULLTEXT idx_fulltext_search (item_name, description),
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (validated_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: price_history
-- Description: Historical price tracking for trend analysis
-- ===========================================================================

CREATE TABLE price_history (
    history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNSIGNED NOT NULL,
    old_price DECIMAL(10, 2) NOT NULL,
    new_price DECIMAL(10, 2) NOT NULL,
    price_change DECIMAL(10, 2) AS (new_price - old_price) STORED,
    price_change_percent DECIMAL(5, 2) AS (((new_price - old_price) / old_price) * 100) STORED,
    market_location VARCHAR(100) DEFAULT NULL,
    source VARCHAR(200) DEFAULT NULL,
    updated_by INT UNSIGNED NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_item_id (item_id),
    INDEX idx_updated_by (updated_by),
    INDEX idx_updated_at (updated_at),
    
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: validation_queue
-- Description: Pending validations for price updates and new items
-- ===========================================================================

CREATE TABLE validation_queue (
    queue_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNSIGNED DEFAULT NULL,
    action_type ENUM('new_item', 'price_update') NOT NULL,
    old_price DECIMAL(10, 2) DEFAULT NULL,
    new_price DECIMAL(10, 2) NOT NULL,
    item_name VARCHAR(200) DEFAULT NULL,
    category_id INT UNSIGNED DEFAULT NULL,
    unit VARCHAR(20) DEFAULT NULL,
    market_location VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    source VARCHAR(200) DEFAULT NULL,
    submitted_by INT UNSIGNED NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    validated_by INT UNSIGNED DEFAULT NULL,
    validated_at TIMESTAMP NULL DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    
    INDEX idx_item_id (item_id),
    INDEX idx_status (status),
    INDEX idx_submitted_by (submitted_by),
    INDEX idx_submitted_at (submitted_at),
    
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (submitted_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (validated_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: tags
-- Description: Tags for enhanced search and categorization
-- ===========================================================================

CREATE TABLE tags (
    tag_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(50) NOT NULL UNIQUE,
    tag_name_nepali VARCHAR(50) DEFAULT NULL,
    usage_count INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_tag_name (tag_name),
    INDEX idx_usage_count (usage_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: item_tags
-- Description: Many-to-many relationship between items and tags
-- ===========================================================================

CREATE TABLE item_tags (
    item_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (item_id, tag_id),
    INDEX idx_tag_id (tag_id),
    
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: system_logs
-- Description: Comprehensive audit trail
-- ===========================================================================

CREATE TABLE system_logs (
    log_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action_type VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id INT UNSIGNED DEFAULT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: site_settings
-- Description: Configurable site-wide settings
-- ===========================================================================

CREATE TABLE site_settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description VARCHAR(255) DEFAULT NULL,
    updated_by INT UNSIGNED DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key),
    
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- TABLE: sessions
-- Description: User session management
-- ===========================================================================

CREATE TABLE sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- VIEWS FOR COMMON QUERIES
-- ===========================================================================

-- Active Items with Category Info
CREATE VIEW view_active_items AS
SELECT 
    i.item_id,
    i.item_name,
    i.item_name_nepali,
    i.slug,
    i.current_price,
    i.unit,
    i.market_location,
    i.image_path,
    i.updated_at,
    c.category_name,
    c.slug AS category_slug,
    u.full_name AS contributor_name
FROM items i
JOIN categories c ON i.category_id = c.category_id
JOIN users u ON i.created_by = u.user_id
WHERE i.status = 'active' AND c.is_active = 1;

-- Pending Validations with Details
CREATE VIEW view_pending_validations AS
SELECT 
    vq.queue_id,
    vq.action_type,
    vq.item_name,
    vq.old_price,
    vq.new_price,
    vq.market_location,
    vq.source,
    vq.submitted_at,
    u.username AS submitted_by_username,
    u.full_name AS submitted_by_name,
    c.category_name,
    i.item_name AS existing_item_name
FROM validation_queue vq
JOIN users u ON vq.submitted_by = u.user_id
LEFT JOIN categories c ON vq.category_id = c.category_id
LEFT JOIN items i ON vq.item_id = i.item_id
WHERE vq.status = 'pending'
ORDER BY vq.submitted_at ASC;

-- Price Trends (Last 30 Days)
CREATE VIEW view_price_trends AS
SELECT 
    i.item_id,
    i.item_name,
    i.current_price,
    ph.old_price,
    ph.new_price,
    ph.price_change,
    ph.price_change_percent,
    ph.updated_at
FROM items i
JOIN price_history ph ON i.item_id = ph.item_id
WHERE ph.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY ph.updated_at DESC;

-- ===========================================================================
-- STORED PROCEDURES
-- ===========================================================================

-- Procedure: Approve Validation
DELIMITER $$

CREATE PROCEDURE sp_approve_validation(
    IN p_queue_id INT UNSIGNED,
    IN p_admin_id INT UNSIGNED
)
BEGIN
    DECLARE v_item_id INT UNSIGNED;
    DECLARE v_action_type VARCHAR(20);
    DECLARE v_old_price DECIMAL(10,2);
    DECLARE v_new_price DECIMAL(10,2);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error approving validation';
    END;
    
    START TRANSACTION;
    
    -- Get validation details
    SELECT item_id, action_type, old_price, new_price
    INTO v_item_id, v_action_type, v_old_price, v_new_price
    FROM validation_queue
    WHERE queue_id = p_queue_id AND status = 'pending';
    
    IF v_action_type = 'new_item' THEN
        -- Create new item from queue data
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
        -- Update existing item price
        UPDATE items
        SET current_price = v_new_price,
            validated_by = p_admin_id,
            validated_at = NOW()
        WHERE item_id = v_item_id;
        
        -- Record price history
        INSERT INTO price_history (item_id, old_price, new_price, updated_by)
        VALUES (v_item_id, v_old_price, v_new_price, p_admin_id);
    END IF;
    
    -- Update validation queue
    UPDATE validation_queue
    SET status = 'approved',
        validated_by = p_admin_id,
        validated_at = NOW()
    WHERE queue_id = p_queue_id;
    
    COMMIT;
END$$

DELIMITER ;

-- Procedure: Reject Validation
DELIMITER $$

CREATE PROCEDURE sp_reject_validation(
    IN p_queue_id INT UNSIGNED,
    IN p_admin_id INT UNSIGNED,
    IN p_reason TEXT
)
BEGIN
    UPDATE validation_queue
    SET status = 'rejected',
        validated_by = p_admin_id,
        validated_at = NOW(),
        rejection_reason = p_reason
    WHERE queue_id = p_queue_id AND status = 'pending';
END$$

DELIMITER ;

-- ===========================================================================
-- TRIGGERS
-- ===========================================================================

-- Trigger: Update tag usage count when item_tags is inserted
DELIMITER $$

CREATE TRIGGER trg_after_insert_item_tags
AFTER INSERT ON item_tags
FOR EACH ROW
BEGIN
    UPDATE tags
    SET usage_count = usage_count + 1
    WHERE tag_id = NEW.tag_id;
END$$

DELIMITER ;

-- Trigger: Update tag usage count when item_tags is deleted
DELIMITER $$

CREATE TRIGGER trg_after_delete_item_tags
AFTER DELETE ON item_tags
FOR EACH ROW
BEGIN
    UPDATE tags
    SET usage_count = usage_count - 1
    WHERE tag_id = OLD.tag_id;
END$$

DELIMITER ;
