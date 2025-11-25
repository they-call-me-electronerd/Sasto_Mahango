# Item Edit Feature - Implementation Guide

## Overview
Contributors can now submit item edit requests that require admin approval before being applied to the database.

## Database Migration Required

**IMPORTANT:** You must run the SQL migration to update the stored procedure.

### How to Apply Migration:

1. **Using phpMyAdmin:**
   - Open phpMyAdmin
   - Select your `mulyasuchi` database
   - Go to SQL tab
   - Open the file: `sql/migration_add_item_edit_support.sql`
   - Copy and paste the contents
   - Click "Go" to execute

2. **Using MySQL Command Line:**
   ```bash
   mysql -u root -p mulyasuchi < sql/migration_add_item_edit_support.sql
   ```

3. **Using XAMPP Shell:**
   ```bash
   cd c:\xampp\mysql\bin
   mysql -u root mulyasuchi < c:\xampp\htdocs\MulyaSuchi\sql\migration_add_item_edit_support.sql
   ```

## Features Added

### 1. New Action Type
- Added `ACTION_ITEM_EDIT` constant for tracking item edit requests

### 2. Contributor Features
- **Edit Item Page** (`contributor/edit_item.php`)
  - Contributors can request changes to existing items
  - All fields are editable: name, category, price, unit, location, description, image
  - Shows current item details for reference
  - Requires source/reference field for verification

- **Dashboard Integration**
  - New "Edit Item" card in Quick Actions
  - Links to products page where contributors can select items to edit

- **Products Page**
  - Edit button appears for contributors (pencil icon)
  - Different from admin's direct edit (pencil-square icon)
  - Clicking opens edit request form

### 3. Admin Features
- **Validation Queue Updates**
  - New stat card for "Item Edits"
  - Purple badge for item edit submissions
  - Displays all edited fields in review interface
  - Approve/Reject workflow same as other submissions

- **Stored Procedure**
  - Updated `sp_approve_validation` to handle item edits
  - Automatically updates item details when approved
  - Records price history if price changed
  - Updates slug if item name changed

### 4. Visual Indicators
- **Purple Badge** for item edit requests
- **Blue Badge** for new items
- **Green Badge** for price updates

## Workflow

### Contributor Side:
1. Navigate to Products page
2. Click edit icon (pencil) on any item
3. Fill out edit request form with changes
4. Optionally provide source/reference
5. Submit for admin approval
6. View request status in dashboard

### Admin Side:
1. See item edit count in Validation Queue
2. Review edit requests with purple badge
3. See current values vs proposed changes
4. Approve or reject with reason
5. On approval, item is automatically updated

## Security Features
- ✅ CSRF token protection
- ✅ Role-based access control
- ✅ Input sanitization
- ✅ File upload validation
- ✅ Admin approval required
- ✅ Audit logging

## Files Modified/Created

### New Files:
- `contributor/edit_item.php` - Contributor edit request page
- `sql/migration_add_item_edit_support.sql` - Database migration

### Modified Files:
- `config/constants.php` - Added ACTION_ITEM_EDIT constant
- `classes/Validation.php` - Added submitItemEdit() method
- `admin/validation_queue.php` - Updated to display item edits
- `assets/css/pages/auth-admin.css` - Added .badge-edit styling
- `public/products.php` - Added contributor edit button
- `contributor/dashboard.php` - Added Edit Item action card

## Testing Checklist

- [ ] Run SQL migration successfully
- [ ] Contributor can see edit button on products
- [ ] Contributor can submit edit request
- [ ] Edit request appears in admin validation queue with purple badge
- [ ] Admin can approve edit request
- [ ] Item is updated correctly after approval
- [ ] Price history is recorded if price changed
- [ ] Admin can reject edit request
- [ ] Contributor sees rejection reason in dashboard
- [ ] Image upload works correctly

## Support

If you encounter any issues:
1. Check that the SQL migration ran successfully
2. Verify database user has EXECUTE permission on stored procedures
3. Check error logs in XAMPP control panel
4. Ensure all files have proper permissions
