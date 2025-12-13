<?php
/**
 * Category Class
 * 
 * Handles all category-related operations
 */

class Category {
    private $pdo;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }
    
    /**
     * Get all active categories
     */
    public function getActiveCategories() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories
                WHERE is_active = 1
                ORDER BY display_order ASC, category_name ASC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get active categories error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get category by ID
     */
    public function getCategoryById($categoryId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = :category_id");
            $stmt->execute(['category_id' => $categoryId]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Get category by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = :slug AND is_active = 1");
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Get category by slug error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get categories with item counts
     */
    public function getCategoriesWithItemCounts() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, COUNT(i.item_id) as item_count
                FROM categories c
                LEFT JOIN items i ON c.category_id = i.category_id AND i.status = :status
                WHERE c.is_active = 1
                GROUP BY c.category_id
                ORDER BY c.display_order ASC, c.category_name ASC
            ");
            
            $stmt->execute(['status' => ITEM_STATUS_ACTIVE]);
            $categories = $stmt->fetchAll();
            
            // Add icon and color mappings
            $iconMap = [
                'Vegetables' => ['icon' => 'bi bi-basket2-fill', 'gradient_start' => '#10b981', 'gradient_end' => '#059669', 'badge_color' => '#10b981', 'description' => 'Fresh vegetables and produce'],
                'Fruits' => ['icon' => 'bi bi-apple', 'gradient_start' => '#ef4444', 'gradient_end' => '#dc2626', 'badge_color' => '#ef4444', 'description' => 'Fresh and seasonal fruits'],
                'Groceries' => ['icon' => 'bi bi-cart-fill', 'gradient_start' => '#eab308', 'gradient_end' => '#ca8a04', 'badge_color' => '#eab308', 'description' => 'Essential grocery items'],
                'Dairy Products' => ['icon' => 'bi bi-cup-straw', 'gradient_start' => '#3b82f6', 'gradient_end' => '#2563eb', 'badge_color' => '#3b82f6', 'description' => 'Milk and dairy items'],
                'Meat & Fish' => ['icon' => 'bi bi-egg-fried', 'gradient_start' => '#ec4899', 'gradient_end' => '#db2777', 'badge_color' => '#ec4899', 'description' => 'Fresh meat and seafood'],
                'Spices' => ['icon' => 'bi bi-fire', 'gradient_start' => '#f97316', 'gradient_end' => '#ea580c', 'badge_color' => '#f97316', 'description' => 'Herbs and spices'],
                'Kitchen Appliances' => ['icon' => 'bi bi-egg-fill', 'gradient_start' => '#f59e0b', 'gradient_end' => '#d97706', 'badge_color' => '#f59e0b', 'description' => 'Kitchen tools and appliances'],
                'Household Items' => ['icon' => 'bi bi-house-fill', 'gradient_start' => '#f97316', 'gradient_end' => '#ea580c', 'badge_color' => '#f97316', 'description' => 'Home care products'],
                'Electrical Appliances' => ['icon' => 'bi bi-lightbulb-fill', 'gradient_start' => '#8b5cf6', 'gradient_end' => '#7c3aed', 'badge_color' => '#8b5cf6', 'description' => 'Electronic appliances'],
                'Clothing' => ['icon' => 'bi bi-bag-fill', 'gradient_start' => '#ec4899', 'gradient_end' => '#db2777', 'badge_color' => '#ec4899', 'description' => 'Garments and accessories'],
                'Study Material' => ['icon' => 'bi bi-book-fill', 'gradient_start' => '#3b82f6', 'gradient_end' => '#2563eb', 'badge_color' => '#3b82f6', 'description' => 'Books and stationery'],
                'Tools & Hardware' => ['icon' => 'bi bi-tools', 'gradient_start' => '#f97316', 'gradient_end' => '#ea580c', 'badge_color' => '#f97316', 'description' => 'Hardware and tools'],
            ];
            
            // Merge icon and color data with categories
            foreach ($categories as &$category) {
                $name = $category['category_name'];
                if (isset($iconMap[$name])) {
                    $category = array_merge($category, $iconMap[$name]);
                    $category['name'] = $name; // Add a 'name' key for convenience
                } else {
                    // Default values
                    $category['icon'] = 'bi bi-circle-fill';
                    $category['gradient_start'] = '#6b7280';
                    $category['gradient_end'] = '#4b5563';
                    $category['badge_color'] = '#6b7280';
                    $category['description'] = 'Browse products';
                    $category['name'] = $name;
                }
            }
            
            return $categories;
            
        } catch (PDOException $e) {
            error_log("Get categories with item counts error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get items by category with limit
     */
    public function getItemsByCategory($categoryId, $limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT i.*, c.category_name
                FROM items i
                JOIN categories c ON i.category_id = c.category_id
                WHERE i.category_id = :category_id AND i.status = :status
                ORDER BY i.updated_at DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':status', ITEM_STATUS_ACTIVE, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get items by category error: " . $e->getMessage());
            return [];
        }
    }
}

?>
