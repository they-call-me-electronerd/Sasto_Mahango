# URGENT: Price Update Fix

## Issue
Price history was not being recorded correctly, causing the 30-day statistics to not update.

## What Was Fixed
1. **Item.php**: Removed insertion of computed columns (price_change, price_change_percent) - these are auto-calculated by MySQL
2. **Stored Procedure**: Updated to properly insert price history with timestamps and update item's updated_at field

## Required Action

**You MUST re-run the migration to update the stored procedure:**

### Option 1: Using phpMyAdmin
1. Open phpMyAdmin
2. Select `mulyasuchi` database
3. Go to SQL tab
4. Copy and paste the contents of: `sql/migration_add_item_edit_support.sql`
5. Click "Go"

### Option 2: Using MySQL Command Line
```bash
mysql -u root -p mulyasuchi < sql/migration_add_item_edit_support.sql
```

### Option 3: Using XAMPP Shell
```bash
cd c:\xampp\mysql\bin
mysql -u root mulyasuchi < c:\xampp\htdocs\MulyaSuchi\sql\migration_add_item_edit_support.sql
```

## After Running Migration

Test that price updates work:
1. As contributor: Submit a price update
2. As admin: Approve it in validation queue
3. Visit the item page
4. Verify the 30-day low/high/average updates correctly

## What Changed
- Price history now correctly records timestamps
- Items' `updated_at` field is now updated when price changes
- Computed columns (price_change, price_change_percent) are no longer manually inserted
