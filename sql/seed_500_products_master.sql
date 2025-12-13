-- =============================================================================
-- SASTOMAHANGO: COMPLETE 500 PRODUCTS DATABASE SEED
-- =============================================================================
-- Master SQL File - Execute this to load all 500 products
-- This file sources all three parts in the correct order
-- =============================================================================
-- Created: 2025-11-25
-- Total Products: 500
-- Total Categories: 12
-- Author: SastoMahango Dev Team
-- =============================================================================

-- Execute Part 1: Base setup, categories 1-6 (Vegetables through Spices)
SOURCE c:/xampp/htdocs/SastoMahango/sql/fresh_500_products.sql;

-- Execute Part 2: Categories 7-9 (Kitchen Appliances through Electrical)
SOURCE c:/xampp/htdocs/SastoMahango/sql/fresh_500_products_part2.sql;

-- Execute Part 3: Categories 10-12 (Clothing through Tools & Hardware) + Verification
SOURCE c:/xampp/htdocs/SastoMahango/sql/fresh_500_products_part3.sql;

--
-- =============================================================================
-- ALTERNATIVE: Execute each part manually if SOURCE command doesn't work
-- =============================================================================
-- If the SOURCE command doesn't work in your MySQL client, execute each file
-- separately in this order:
-- 1. fresh_500_products.sql
-- 2. fresh_500_products_part2.sql
-- 3. fresh_500_products_part3.sql
-- =============================================================================
