# Item Edit with Automatic Price Statistics Update

## Overview
When you edit an item and change its price through the edit request system, the 30-day LOW, HIGH, and AVERAGE prices will automatically update after admin approval.

## How It Works

### 1. Contributor Submits Edit
- Go to any product page
- Click "Request Edit" button
- Modify the item details (including price)
- Submit for approval

### 2. Admin Approves Edit
- Admin reviews the edit in the Validation Queue
- When approved, the system automatically:
  - Updates the item's current price
  - Records the price change in `price_history` table with timestamp
  - Updates item's `updated_at` field

### 3. Statistics Auto-Update
The 30-day statistics (LOW, HIGH, AVERAGE) are calculated from:
- All price history records from the last 30 days
- The current price
- This means edits with price changes will immediately affect these stats

## IMPORTANT: Run Migration First

**The migration MUST be run to enable this functionality!**

### Step-by-Step Migration Process

#### Option 1: Using phpMyAdmin (Recommended)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on `mulyasuchi` database (left sidebar)
3. Click on the **SQL** tab at the top
4. Open the file: `c:\xampp\htdocs\MulyaSuchi\sql\migration_add_item_edit_support.sql`
5. Copy the entire contents
6. Paste into the SQL query box in phpMyAdmin
7. Click **Go** button
8. You should see: "Query executed successfully"

#### Option 2: Using MySQL Command Line
```powershell
# Open PowerShell in XAMPP MySQL bin directory
cd C:\xampp\mysql\bin

# Run the migration
.\mysql.exe -u root mulyasuchi < C:\xampp\htdocs\MulyaSuchi\sql\migration_add_item_edit_support.sql
```

#### Option 3: Using XAMPP Shell
1. Open XAMPP Control Panel
2. Click "Shell" button
3. Run these commands:
```bash
cd mysql/bin
mysql -u root mulyasuchi < ../../htdocs/MulyaSuchi/sql/migration_add_item_edit_support.sql
```

### Verify Migration Success
After running migration, check that the stored procedure was updated:

```sql
SHOW PROCEDURE STATUS WHERE Db = 'mulyasuchi' AND Name = 'sp_approve_validation';
```

You should see the procedure listed with today's date.

## What Was Changed

### 1. Updated Item.php
- `getPriceHistory()` now fetches records from last 30 **days** (not last 30 records)
- This ensures accurate time-based statistics

### 2. Migration File Updates sp_approve_validation
The stored procedure now handles three action types:

#### `new_item`
- Creates new item
- Sets initial price

#### `price_update`
- Updates item's current price
- Records in price_history with timestamp

#### `item_edit` (NEW)
- Updates all item fields (name, category, price, unit, location, description, image)
- If price changed: records in price_history
- Updates item's `updated_at` timestamp
- Auto-updates slug based on new name

## Testing the Feature

### Test Case 1: Edit Item Name Only
1. Request edit for an item (change only name)
2. Admin approves
3. Item name updates, price stats unchanged ✓

### Test Case 2: Edit Item Price
1. Request edit for an item (change price from 100 to 120)
2. Admin approves
3. Check item page:
   - Current price shows 120 ✓
   - Price history shows the change ✓
   - If new price is highest in 30 days: "30-Day High" updates to 120 ✓
   - If new price is lowest in 30 days: "30-Day Low" updates to 120 ✓
   - Average recalculates including new price ✓

### Test Case 3: Multiple Fields
1. Request edit changing: name + category + price + unit
2. Admin approves
3. All fields update ✓
4. Price statistics recalculate ✓

## Price Statistics Calculation

The item detail page (`public/item.php`) calculates:

```php
30-day LOW    = Minimum price from all history records in last 30 days + current price
30-day HIGH   = Maximum price from all history records in last 30 days + current price
Average Price = Average of all prices from last 30 days + current price
```

## Troubleshooting

### Issue: Stats Not Updating
**Solution:** 
1. Verify migration was run successfully
2. Check that price actually changed (not same value)
3. Clear browser cache and reload item page

### Issue: Migration Fails
**Error:** "Procedure already exists"
**Solution:** The migration file drops and recreates the procedure - this is normal

### Issue: No Price History
**Cause:** Item was created before migration
**Solution:** Submit a price update for the item to start building history

## Database Schema

### price_history Table
```sql
- history_id (PRIMARY KEY)
- item_id (FOREIGN KEY to items)
- old_price (previous price)
- new_price (updated price)
- price_change (AUTO COMPUTED: new_price - old_price)
- price_change_percent (AUTO COMPUTED: percentage change)
- updated_by (user who made the change)
- updated_at (timestamp of change)
```

### validation_queue Table
For item_edit action:
```sql
- action_type = 'item_edit'
- item_id = ID of item being edited
- item_name = new name (or NULL if unchanged)
- category_id = new category (or NULL if unchanged)
- new_price = new price (or NULL if unchanged)
- unit = new unit (or NULL if unchanged)
- market_location = new location (or NULL if unchanged)
- description = new description (or NULL if unchanged)
- image_path = new image (or NULL if unchanged)
```

## Summary

✅ **Fixed:** `getPriceHistory()` now gets records from last 30 days (not last 30 records)
✅ **Enhanced:** Migration handles item edits with automatic price history recording
✅ **Automatic:** Price statistics (LOW/HIGH/AVG) update immediately after edit approval
✅ **Smart:** Only changed fields are updated, NULL fields keep existing values
✅ **Accurate:** Timestamps ensure proper 30-day calculation

**Next Step:** Run the migration file, then test by editing an item's price!
