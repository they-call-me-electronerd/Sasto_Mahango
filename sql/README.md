# SQL Files Documentation

This directory contains all essential SQL scripts for the SastoMahango database system.

## Production Files (Execute in Order for Fresh Installation)

### 1. `schema.sql` (16 KB)
**Purpose:** Main database schema definition  
**Contains:**
- Database creation with proper charset (utf8mb4)
- All table structures (users, categories, items, price_history, validation_queue, etc.)
- Primary keys, foreign keys, and indexes
- Default values and constraints

**When to use:** Fresh database installation or schema recreation

**Execute:**
```bash
mysql -u root -p < schema.sql
```

---

### 2. `seed_data.sql` (6 KB)
**Purpose:** Initial required data for system operation  
**Contains:**
- Default admin user (username: admin, password: Admin@123)
- 12 product categories with Nepali names
- System settings
- Product tags
- Default roles and permissions data

**When to use:** After running schema.sql on fresh installation

**Execute:**
```bash
mysql -u root -p sastomahango_db < seed_data.sql
```

---

### 3. `fresh_150_products.sql` (18 KB)
**Purpose:** Production-ready product dataset  
**Contains:**
- 150 realistic products across 7 categories
- Nepali names and locations
- Market-realistic pricing
- Multiple contributor IDs
- Mix of active/inactive status

**When to use:** To populate database with sample/production data

**Execute:**
```bash
mysql -u root -p sastomahango_db < fresh_150_products.sql
```

---

## Optimization & Migration Files

### 4. `database_optimizations.sql` (5 KB)
**Purpose:** Performance optimization  
**Contains:**
- Composite indexes for common queries
- Full-text search indexes
- Query optimization settings
- Performance tuning parameters

**When to use:** After initial setup or when experiencing slow queries

**Execute:**
```bash
mysql -u root -p sastomahango_db < database_optimizations.sql
```

---

### 5. `migration_add_item_edit_support.sql` (5 KB)
**Purpose:** Add item editing support to validation system  
**Contains:**
- Updated stored procedure `sp_approve_validation`
- Support for 'item_edit' action type
- Price history tracking for edits

**When to use:** When deploying item edit feature

**Execute:**
```bash
mysql -u root -p sastomahango_db < migration_add_item_edit_support.sql
```

---

## Complete Installation Guide

### Fresh Installation (New Database)

```bash
# Step 1: Create database and schema
mysql -u root -p < schema.sql

# Step 2: Add initial data (admin, categories, settings)
mysql -u root -p sastomahango_db < seed_data.sql

# Step 3: Add product data
mysql -u root -p sastomahango_db < fresh_150_products.sql

# Step 4: Optimize database
mysql -u root -p sastomahango_db < database_optimizations.sql

# Step 5: Add item edit support
mysql -u root -p sastomahango_db < migration_add_item_edit_support.sql
```

### Using XAMPP on Windows

```powershell
# Navigate to SQL directory
cd c:\xampp\htdocs\SastoMahango\sql

# Execute all in order
c:\xampp\mysql\bin\mysql.exe -u root < schema.sql
c:\xampp\mysql\bin\mysql.exe -u root sastomahango_db < seed_data.sql
c:\xampp\mysql\bin\mysql.exe -u root sastomahango_db < fresh_150_products.sql
c:\xampp\mysql\bin\mysql.exe -u root sastomahango_db < database_optimizations.sql
c:\xampp\mysql\bin\mysql.exe -u root sastomahango_db < migration_add_item_edit_support.sql
```

---

## File Maintenance

### Deleted Files (No longer needed)
- ❌ `add_new_categories.sql` - Duplicate of categories in seed_data.sql
- ❌ `add_sample_products.sql` - Replaced by fresh_150_products.sql
- ❌ `check_products.sql` - Debug query file

### Backup Policy
- Keep all 5 production files under version control
- Do NOT delete schema.sql or seed_data.sql
- Product data files (fresh_150_products.sql) can be regenerated as needed

---

## Default Credentials

**Admin User:**
- Username: `admin`
- Email: `admin@sastomahango.com`
- Password: `Admin@123`

⚠️ **IMPORTANT:** Change the default admin password immediately after first login in production!

---

## Database Information

- **Database Name:** sastomahango_db
- **Character Set:** utf8mb4
- **Collation:** utf8mb4_unicode_ci
- **Engine:** InnoDB
- **MySQL Version:** 8.0+

---

## Troubleshooting

### Foreign Key Constraint Errors
If you encounter foreign key errors when running fresh_150_products.sql:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Run your SQL
SET FOREIGN_KEY_CHECKS = 1;
```
(This is already included in fresh_150_products.sql)

### Schema Already Exists
To drop and recreate:
```sql
DROP DATABASE IF EXISTS sastomahango_db;
-- Then run schema.sql
```

### Duplicate Entry Errors
The seed_data.sql uses `INSERT IGNORE` for safety. If you need to force replace:
```sql
TRUNCATE TABLE categories;
TRUNCATE TABLE tags;
-- Then run seed_data.sql
```

---

**Last Updated:** November 25, 2025  
**Total Files:** 5 essential SQL scripts  
**Status:** Production Ready ✅
