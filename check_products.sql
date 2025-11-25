-- Check if items exist in the database
SELECT item_id, item_name, current_price, status FROM items LIMIT 10;

-- Check total count of items
SELECT COUNT(*) as total_items FROM items;

-- Check total count of active items  
SELECT COUNT(*) as active_items FROM items WHERE status = 'active';

-- Search for 'g' specifically
SELECT item_id, item_name, current_price, status 
FROM items 
WHERE status = 'active' 
AND (item_name LIKE '%g%' OR item_name_nepali LIKE '%g%' OR description LIKE '%g%')
LIMIT 10;
