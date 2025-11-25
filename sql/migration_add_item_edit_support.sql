-- ============================================================================
-- Migration: Add Item Edit Support to Validation System
-- Description: Updates sp_approve_validation to handle item_edit action type
-- Date: 2025-11-25
-- ============================================================================

-- Drop existing procedure
DROP PROCEDURE IF EXISTS sp_approve_validation;

-- Recreate with item_edit support
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
    
    -- Get validation details
    SELECT item_id, action_type, old_price, new_price, item_name, category_id, 
           unit, market_location, description, image_path
    INTO v_item_id, v_action_type, v_old_price, v_new_price, v_item_name, 
         v_category_id, v_unit, v_market_location, v_description, v_image_path
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
            validated_at = NOW(),
            updated_at = NOW()
        WHERE item_id = v_item_id;
        
        -- Record price history with timestamp
        INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
        VALUES (v_item_id, v_old_price, v_new_price, p_admin_id, NOW());
        
    ELSEIF v_action_type = 'item_edit' THEN
        -- Get current price before update
        SELECT current_price INTO v_current_price
        FROM items
        WHERE item_id = v_item_id;
        
        -- Update item with new details
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
        
        -- If price changed, record in price history with timestamp
        IF v_new_price IS NOT NULL AND v_new_price != v_current_price THEN
            INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
            VALUES (v_item_id, v_current_price, v_new_price, p_admin_id, NOW());
        END IF;
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

-- ============================================================================
-- Verification Query (Optional - run to test)
-- ============================================================================
-- SELECT 'Migration completed successfully' AS status;
