-- ============================================================================
-- DATABASE REBRANDING SCRIPT: Mulyasuchi -> SastoMahango
-- ============================================================================
-- This script renames the database and updates all data references
-- Execute this if you already have an existing mulyasuchi_db database
-- ============================================================================

-- Step 1: Create new database
CREATE DATABASE IF NOT EXISTS sastomahango_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Step 2: Copy all data from old database to new
-- Note: You can use mysqldump for this
-- mysqldump -u root -p mulyasuchi_db | mysql -u root -p sastomahango_db

-- Step 3: Update site settings in new database
USE sastomahango_db;

UPDATE site_settings SET setting_value = 'SastoMahango' WHERE setting_key = 'site_name';
UPDATE site_settings SET setting_value = 'contact@sastomahango.com' WHERE setting_key = 'contact_email';

-- Step 4: Update user emails
UPDATE users SET email = 'admin@sastomahango.com' WHERE email = 'admin@mulyasuchi.com';
UPDATE users SET email = 'contributor@sastomahango.com' WHERE email = 'contributor@mulyasuchi.com';
UPDATE users SET email = REPLACE(email, '@mulyasuchi.com', '@sastomahango.com') WHERE email LIKE '%@mulyasuchi.com';

-- Step 5: Verify the changes
SELECT * FROM site_settings WHERE setting_key IN ('site_name', 'contact_email');
SELECT username, email, role FROM users WHERE role IN ('admin', 'contributor');

-- ============================================================================
-- MANUAL STEPS TO COMPLETE:
-- ============================================================================
-- 1. Export old database:
--    mysqldump -u root -p mulyasuchi_db > mulyasuchi_backup.sql
--
-- 2. Import to new database:
--    mysql -u root -p sastomahango_db < mulyasuchi_backup.sql
--
-- 3. Run this script to update references:
--    mysql -u root -p < rebrand_database.sql
--
-- 4. Update .env file with new database name:
--    DB_NAME=sastomahango_db
--
-- 5. Test the application thoroughly
--
-- 6. Once confirmed working, optionally drop old database:
--    DROP DATABASE IF EXISTS mulyasuchi_db;
-- ============================================================================
