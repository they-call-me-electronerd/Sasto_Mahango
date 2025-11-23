# Products Page - User Guide

## Overview
The Products page provides an advanced search and filtering interface for browsing all products in the MulyaSuchi marketplace.

## Features

### 1. **Advanced Search**
- Search products by name (English or Nepali)
- Search in product descriptions
- Real-time filtering as you type

### 2. **Category Filter**
- Filter products by category
- Dropdown menu showing all active categories
- Quick category selection

### 3. **Price Range Filter**
- Set minimum price
- Set maximum price
- Filter products within specific price ranges
- Supports decimal values for precise filtering

### 4. **Sorting Options**
- **Name (A-Z)**: Alphabetical ascending
- **Name (Z-A)**: Alphabetical descending
- **Price (Low to High)**: Cheapest first
- **Price (High to Low)**: Most expensive first
- **Newest First**: Recently added products
- **Oldest First**: Older products first

### 5. **View Modes**
- **Grid View**: Card-based layout (default)
- **List View**: Extended horizontal layout
- Preference saved in browser localStorage

### 6. **Active Filters Display**
- See all currently applied filters
- Remove individual filters with one click
- Quick "Reset All" button

### 7. **Product Cards**
Each product card displays:
- Product image (or placeholder)
- Product name (English & Nepali if available)
- Category badge
- Price change indicator (↑ or ↓ with percentage)
- Current price
- Base price (if different)
- Unit of measurement (kg, piece, liter, etc.)
- Market location
- "View Details" button

### 8. **Pagination**
- Navigate through multiple pages
- Shows current page and total pages
- First, Previous, Next, Last navigation
- Page numbers with current page highlighted

## URL Parameters

The page uses URL query parameters for filtering:

```
products.php?search=tomato&category=1&min_price=10&max_price=100&sort=price_asc&page=1
```

### Parameters:
- `search`: Search query string
- `category`: Category ID (integer)
- `min_price`: Minimum price (decimal)
- `max_price`: Maximum price (decimal)
- `sort`: Sort option (name_asc, name_desc, price_asc, price_desc, newest, oldest)
- `page`: Current page number (integer)

## Database Methods

### Item Class Methods Added

#### `searchProductsAdvanced($filters)`
Performs advanced product search with multiple filters.

**Parameters:**
```php
$filters = [
    'search' => 'product name',
    'category_id' => 1,
    'min_price' => 10.00,
    'max_price' => 100.00,
    'sort_by' => 'price_asc',
    'limit' => 12,
    'offset' => 0
];
```

**Returns:** Array of products matching filters

#### `countProductsAdvanced($filters)`
Counts total products matching the filters (for pagination).

**Parameters:** Same as `searchProductsAdvanced` (except limit/offset)

**Returns:** Integer count

## Usage Examples

### 1. Search for "Tomato"
```
products.php?search=tomato
```

### 2. Filter by Vegetables Category
```
products.php?category=1
```

### 3. Products between NPR 50-150
```
products.php?min_price=50&max_price=150
```

### 4. Cheapest Vegetables
```
products.php?category=1&sort=price_asc
```

### 5. Combined Filters
```
products.php?search=potato&category=1&min_price=20&max_price=80&sort=price_asc
```

## Responsive Design

The page is fully responsive:

- **Desktop (1024px+)**: Sidebar + Grid layout
- **Tablet (768px-1024px)**: Stacked filters + Grid
- **Mobile (<768px)**: Single column layout

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Uses CSS Grid and Flexbox
- JavaScript for view toggle and form interactions
- localStorage for view preference

## File Structure

```
public/
  └── products.php         # Main products page

assets/
  └── css/
      └── products.css     # Dedicated stylesheet

classes/
  └── Item.php            # Updated with advanced search methods
```

## Future Enhancements

Potential improvements:
1. AJAX-based filtering (no page reload)
2. More filter options (stock status, ratings, etc.)
3. Save filter presets
4. Export search results
5. Compare products feature
6. Wishlist/favorites
7. Product recommendations
8. Advanced analytics dashboard

## Tips

1. **Reset Filters**: Click individual × or use "Reset" button
2. **Combine Filters**: Mix search, category, and price for precise results
3. **Sort First**: Apply filters, then sort for best results
4. **Bookmark Searches**: Copy URL to save specific filter combinations
5. **Mobile Use**: Filters collapse on mobile for better UX

---

**Created:** November 23, 2025  
**Version:** 1.0.0  
**Part of:** MulyaSuchi - Market Intelligence Platform
