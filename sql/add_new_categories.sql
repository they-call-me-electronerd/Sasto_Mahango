-- ===========================================================================
-- ADD NEW CATEGORIES TO SASTOMAHANGO
-- ===========================================================================
-- Description: Adds 5 new categories to the existing database
-- Categories: Household Items, Electrical Appliances, Clothing, 
--             Study Material, Tools & Hardware
-- ===========================================================================

USE sastomahango_db;

-- Insert new categories
INSERT INTO categories (category_name, category_name_nepali, slug, description, icon_class, display_order) VALUES
('Household Items', 'घरेलु सामानहरू', 'household-items', 'Home care products', 'icon-household', 10),
('Electrical Appliances', 'बिजुली उपकरणहरू', 'electrical-appliances', 'Electronic appliances', 'icon-electrical', 11),
('Clothing', 'लुगाफाटा', 'clothing', 'Garments and accessories', 'icon-clothing', 12),
('Study Material', 'अध्ययन सामग्री', 'study-material', 'Books and stationery', 'icon-study', 13),
('Tools & Hardware', 'औजार तथा हार्डवेयर', 'tools-hardware', 'Hardware and tools', 'icon-tools', 14);

-- Verify categories were added
SELECT category_id, category_name, category_name_nepali, slug, display_order 
FROM categories 
ORDER BY display_order;
