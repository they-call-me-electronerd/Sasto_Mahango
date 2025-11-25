# ===========================================================================
# DATABASE OPTIMIZATION SCRIPTS
# ===========================================================================
# Run these SQL statements to optimize database performance
# Execute: mysql -u root -p mulyasuchi_db < database_optimizations.sql
# ===========================================================================

USE mulyasuchi_db;

-- ===========================================================================
-- ADD COMPOSITE INDEXES FOR COMMON QUERIES
-- ===========================================================================

-- Items table - frequently used combinations
CREATE INDEX IF NOT EXISTS idx_items_status_category ON items(status, category_id);
CREATE INDEX IF NOT EXISTS idx_items_status_price ON items(status, current_price);
CREATE INDEX IF NOT EXISTS idx_items_status_updated ON items(status, updated_at);
CREATE INDEX IF NOT EXISTS idx_items_category_status_price ON items(category_id, status, current_price);

-- Price history - time-based queries
CREATE INDEX IF NOT EXISTS idx_price_history_item_date ON price_history(item_id, updated_at DESC);
CREATE INDEX IF NOT EXISTS idx_price_history_date ON price_history(updated_at DESC);

-- Validation queue - status and time queries
CREATE INDEX IF NOT EXISTS idx_validation_status_submitted ON validation_queue(status, submitted_at DESC);

-- System logs - performance monitoring
CREATE INDEX IF NOT EXISTS idx_logs_user_action_date ON system_logs(user_id, action_type, created_at DESC);

-- ===========================================================================
-- ADD CHECK CONSTRAINTS FOR DATA INTEGRITY
-- ===========================================================================

-- Ensure prices are positive
ALTER TABLE items
ADD CONSTRAINT chk_items_base_price_positive 
CHECK (base_price >= 0);

ALTER TABLE items
ADD CONSTRAINT chk_items_current_price_positive 
CHECK (current_price >= 0);

ALTER TABLE price_history
ADD CONSTRAINT chk_price_history_old_price_positive 
CHECK (old_price >= 0);

ALTER TABLE price_history
ADD CONSTRAINT chk_price_history_new_price_positive 
CHECK (new_price >= 0);

-- ===========================================================================
-- ADD SOFT DELETE TIMESTAMP
-- ===========================================================================

-- Add deleted_at column for soft deletes
ALTER TABLE items 
ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL,
ADD INDEX idx_items_deleted_at (deleted_at);

-- ===========================================================================
-- PARTITIONING FOR PRICE HISTORY (Optional - for large datasets)
-- ===========================================================================

-- Partition by year for better query performance on historical data
-- Uncomment and adjust years as needed:

/*
ALTER TABLE price_history
PARTITION BY RANGE (YEAR(updated_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
*/

-- ===========================================================================
-- DATABASE CONFIGURATION OPTIMIZATION
-- ===========================================================================

-- Increase InnoDB buffer pool (adjust based on available RAM)
-- SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB

-- Enable query cache (if MySQL < 8.0)
-- SET GLOBAL query_cache_size = 67108864; -- 64MB
-- SET GLOBAL query_cache_type = 1;

-- ===========================================================================
-- ANALYZE TABLES FOR QUERY OPTIMIZER
-- ===========================================================================

ANALYZE TABLE users;
ANALYZE TABLE categories;
ANALYZE TABLE items;
ANALYZE TABLE price_history;
ANALYZE TABLE validation_queue;
ANALYZE TABLE system_logs;

-- ===========================================================================
-- CLEANUP OLD DATA (Run periodically)
-- ===========================================================================

-- Delete rejected validations older than 90 days
-- DELETE FROM validation_queue 
-- WHERE status = 'rejected' 
-- AND validated_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Archive old system logs (older than 1 year)
-- Consider moving to archive table before deleting
-- DELETE FROM system_logs 
-- WHERE created_at < DATE_SUB(NOW(), INTERVAL 365 DAY);

-- ===========================================================================
-- SHOW TABLE SIZES AND INDEX USAGE
-- ===========================================================================

SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.TABLES
WHERE table_schema = 'mulyasuchi_db'
ORDER BY (data_length + index_length) DESC;

-- ===========================================================================
-- COMPLETED
-- ===========================================================================
