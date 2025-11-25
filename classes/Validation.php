<?php
/**
 * Validation Class
 * 
 * Handles validation queue operations
 */

class Validation {
    private $pdo;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }
    
    /**
     * Submit new item for validation
     */
    public function submitNewItem($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO validation_queue (
                    action_type, item_name, category_id, new_price, unit,
                    market_location, description, image_path, source,
                    submitted_by
                ) VALUES (
                    :action_type, :item_name, :category_id, :new_price, :unit,
                    :market_location, :description, :image_path, :source,
                    :submitted_by
                )
            ");
            
            $result = $stmt->execute([
                'action_type' => ACTION_NEW_ITEM,
                'item_name' => $data['item_name'],
                'category_id' => $data['category_id'],
                'new_price' => $data['price'],
                'unit' => $data['unit'],
                'market_location' => $data['market_location'] ?? null,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'source' => $data['source'] ?? null,
                'submitted_by' => Auth::getUserId()
            ]);
            
            if ($result) {
                $queueId = $this->pdo->lastInsertId();
                Logger::log(LOG_CREATE, 'validation_queue', $queueId, "New item submitted for validation: {$data['item_name']}");
                return $queueId;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Submit new item error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Submit price update for validation
     */
    public function submitPriceUpdate($itemId, $newPrice, $source = null) {
        try {
            // Get current price
            $itemStmt = $this->pdo->prepare("SELECT current_price FROM items WHERE item_id = :item_id");
            $itemStmt->execute(['item_id' => $itemId]);
            $currentPrice = $itemStmt->fetchColumn();
            
            if (!$currentPrice) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("
                INSERT INTO validation_queue (
                    action_type, item_id, old_price, new_price, source, submitted_by
                ) VALUES (
                    :action_type, :item_id, :old_price, :new_price, :source, :submitted_by
                )
            ");
            
            $result = $stmt->execute([
                'action_type' => ACTION_PRICE_UPDATE,
                'item_id' => $itemId,
                'old_price' => $currentPrice,
                'new_price' => $newPrice,
                'source' => $source,
                'submitted_by' => Auth::getUserId()
            ]);
            
            if ($result) {
                $queueId = $this->pdo->lastInsertId();
                Logger::log(LOG_CREATE, 'validation_queue', $queueId, "Price update submitted for item ID: {$itemId}");
                return $queueId;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Submit price update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Submit item edit for validation
     */
    public function submitItemEdit($itemId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO validation_queue (
                    action_type, item_id, item_name, category_id, new_price, unit,
                    market_location, description, image_path, source, submitted_by
                ) VALUES (
                    :action_type, :item_id, :item_name, :category_id, :new_price, :unit,
                    :market_location, :description, :image_path, :source, :submitted_by
                )
            ");
            
            $result = $stmt->execute([
                'action_type' => ACTION_ITEM_EDIT,
                'item_id' => $itemId,
                'item_name' => $data['item_name'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'new_price' => $data['price'] ?? null,
                'unit' => $data['unit'] ?? null,
                'market_location' => $data['market_location'] ?? null,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'source' => $data['source'] ?? null,
                'submitted_by' => Auth::getUserId()
            ]);
            
            if ($result) {
                $queueId = $this->pdo->lastInsertId();
                Logger::log(LOG_CREATE, 'validation_queue', $queueId, "Item edit submitted for item ID: {$itemId}");
                return $queueId;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Submit item edit error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get pending validations
     */
    public function getPendingValidations() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT vq.*, u.username, u.full_name,
                       c.category_name, i.item_name as existing_item_name
                FROM validation_queue vq
                JOIN users u ON vq.submitted_by = u.user_id
                LEFT JOIN categories c ON vq.category_id = c.category_id
                LEFT JOIN items i ON vq.item_id = i.item_id
                WHERE vq.status = :status
                ORDER BY vq.submitted_at ASC
            ");
            
            $stmt->execute(['status' => VALIDATION_PENDING]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get pending validations error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get validation by ID
     */
    public function getValidationById($queueId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT vq.*, u.username, u.full_name,
                       c.category_name, i.item_name as existing_item_name
                FROM validation_queue vq
                JOIN users u ON vq.submitted_by = u.user_id
                LEFT JOIN categories c ON vq.category_id = c.category_id
                LEFT JOIN items i ON vq.item_id = i.item_id
                WHERE vq.queue_id = :queue_id
            ");
            
            $stmt->execute(['queue_id' => $queueId]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Get validation by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Approve validation (PHP implementation replacing stored procedure)
     */
    public function approveValidation($queueId) {
        try {
            $this->pdo->beginTransaction();

            // Get validation details
            $stmt = $this->pdo->prepare("
                SELECT * FROM validation_queue 
                WHERE queue_id = :queue_id AND status = :status
                FOR UPDATE
            ");
            $stmt->execute([
                'queue_id' => $queueId,
                'status' => VALIDATION_PENDING
            ]);
            $validation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$validation) {
                $this->pdo->rollBack();
                return false;
            }

            $adminId = Auth::getUserId();
            $itemId = $validation['item_id'];
            $actionType = $validation['action_type'];

            if ($actionType === ACTION_NEW_ITEM) {
                // Generate slug
                $slug = strtolower(str_replace([' ', ','], ['-', ''], $validation['item_name']));
                
                // Check for duplicate slug
                $checkSlug = $this->pdo->prepare("SELECT COUNT(*) FROM items WHERE slug = :slug");
                $checkSlug->execute(['slug' => $slug]);
                if ($checkSlug->fetchColumn() > 0) {
                    $slug .= '-' . uniqid();
                }

                // Create new item
                // Note: Using distinct parameter names to avoid PDO issues with some drivers
                $insertItem = $this->pdo->prepare("
                    INSERT INTO items (
                        item_name, item_name_nepali, slug, category_id, base_price, current_price, unit,
                        market_location, description, image_path, status,
                        created_by, validated_by, validated_at
                    ) VALUES (
                        :item_name, :item_name_nepali, :slug, :category_id, :base_price, :current_price, :unit,
                        :market_location, :description, :image_path, :status,
                        :created_by, :validated_by, NOW()
                    )
                ");
                
                $insertItem->execute([
                    'item_name' => $validation['item_name'],
                    'item_name_nepali' => null, // validation_queue doesn't have nepali name yet
                    'slug' => $slug,
                    'category_id' => $validation['category_id'],
                    'base_price' => $validation['new_price'],
                    'current_price' => $validation['new_price'],
                    'unit' => $validation['unit'],
                    'market_location' => $validation['market_location'],
                    'description' => $validation['description'],
                    'image_path' => $validation['image_path'],
                    'status' => ITEM_STATUS_ACTIVE,
                    'created_by' => $validation['submitted_by'],
                    'validated_by' => $adminId
                ]);
                
                $itemId = $this->pdo->lastInsertId();

            } elseif ($actionType === ACTION_PRICE_UPDATE) {
                // Update existing item price
                $updateItem = $this->pdo->prepare("
                    UPDATE items
                    SET current_price = :new_price,
                        validated_by = :validated_by,
                        validated_at = NOW(),
                        updated_at = NOW()
                    WHERE item_id = :item_id
                ");
                
                $updateItem->execute([
                    'new_price' => $validation['new_price'],
                    'validated_by' => $adminId,
                    'item_id' => $itemId
                ]);
                
                // Record price history
                $insertHistory = $this->pdo->prepare("
                    INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
                    VALUES (:item_id, :old_price, :new_price, :updated_by, NOW())
                ");
                
                $insertHistory->execute([
                    'item_id' => $itemId,
                    'old_price' => $validation['old_price'],
                    'new_price' => $validation['new_price'],
                    'updated_by' => $adminId
                ]);

            } elseif ($actionType === ACTION_ITEM_EDIT) {
                // Get current item details
                $currentItemStmt = $this->pdo->prepare("SELECT * FROM items WHERE item_id = :item_id");
                $currentItemStmt->execute(['item_id' => $itemId]);
                $currentItem = $currentItemStmt->fetch(PDO::FETCH_ASSOC);

                if ($currentItem) {
                    // Prepare update data, using COALESCE logic (use new value if not null, else keep old)
                    // Note: In PHP we check if value is not null in $validation
                    
                    $newItemName = $validation['item_name'] ?? $currentItem['item_name'];
                    $newCategoryId = $validation['category_id'] ?? $currentItem['category_id'];
                    $newPrice = $validation['new_price'] ?? $currentItem['current_price'];
                    $newUnit = $validation['unit'] ?? $currentItem['unit'];
                    $newLocation = $validation['market_location'] ?? $currentItem['market_location'];
                    $newDescription = $validation['description'] ?? $currentItem['description'];
                    $newImagePath = $validation['image_path'] ?? $currentItem['image_path'];
                    
                    // Generate new slug if name changed
                    $newSlug = $currentItem['slug'];
                    if (!empty($validation['item_name']) && $validation['item_name'] !== $currentItem['item_name']) {
                        $newSlug = strtolower(str_replace([' ', ','], ['-', ''], $newItemName));
                        // Check duplicate
                         $checkSlug = $this->pdo->prepare("SELECT COUNT(*) FROM items WHERE slug = :slug AND item_id != :item_id");
                        $checkSlug->execute(['slug' => $newSlug, 'item_id' => $itemId]);
                        if ($checkSlug->fetchColumn() > 0) {
                            $newSlug .= '-' . uniqid();
                        }
                    }

                    $updateItem = $this->pdo->prepare("
                        UPDATE items
                        SET item_name = :item_name,
                            category_id = :category_id,
                            current_price = :current_price,
                            unit = :unit,
                            market_location = :market_location,
                            description = :description,
                            image_path = :image_path,
                            slug = :slug,
                            validated_by = :validated_by,
                            validated_at = NOW(),
                            updated_at = NOW()
                        WHERE item_id = :item_id
                    ");

                    $updateItem->execute([
                        'item_name' => $newItemName,
                        'category_id' => $newCategoryId,
                        'current_price' => $newPrice,
                        'unit' => $newUnit,
                        'market_location' => $newLocation,
                        'description' => $newDescription,
                        'image_path' => $newImagePath,
                        'slug' => $newSlug,
                        'validated_by' => $adminId,
                        'item_id' => $itemId
                    ]);

                    // Record price history if changed
                    if ($newPrice != $currentItem['current_price']) {
                        $insertHistory = $this->pdo->prepare("
                            INSERT INTO price_history (item_id, old_price, new_price, updated_by, updated_at)
                            VALUES (:item_id, :old_price, :new_price, :updated_by, NOW())
                        ");
                        $insertHistory->execute([
                            'item_id' => $itemId,
                            'old_price' => $currentItem['current_price'],
                            'new_price' => $newPrice,
                            'updated_by' => $adminId
                        ]);
                    }
                }
            }

            // Update validation queue status
            $updateQueue = $this->pdo->prepare("
                UPDATE validation_queue 
                SET status = :status, 
                    validated_by = :validated_by, 
                    validated_at = NOW() 
                WHERE queue_id = :queue_id
            ");
            
            $updateQueue->execute([
                'status' => VALIDATION_APPROVED,
                'validated_by' => $adminId,
                'queue_id' => $queueId
            ]);

            $this->pdo->commit();
            
            Logger::log(LOG_VALIDATE, 'validation_queue', $queueId, "Validation approved");
            return true;
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Approve validation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reject validation (uses stored procedure)
     */
    public function rejectValidation($queueId, $reason) {
        try {
            $stmt = $this->pdo->prepare("CALL sp_reject_validation(:queue_id, :admin_id, :reason)");
            $result = $stmt->execute([
                'queue_id' => $queueId,
                'admin_id' => Auth::getUserId(),
                'reason' => $reason
            ]);
            
            if ($result) {
                Logger::log(LOG_REJECT, 'validation_queue', $queueId, "Validation rejected: {$reason}");
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Reject validation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get contributor's submission history
     */
    public function getContributorHistory($userId, $limit = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT vq.*, c.category_name, i.item_name as existing_item_name,
                       u_validator.full_name as validated_by_name
                FROM validation_queue vq
                LEFT JOIN categories c ON vq.category_id = c.category_id
                LEFT JOIN items i ON vq.item_id = i.item_id
                LEFT JOIN users u_validator ON vq.validated_by = u_validator.user_id
                WHERE vq.submitted_by = :user_id
                ORDER BY vq.submitted_at DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get contributor history error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count pending validations
     */
    public function countPendingValidations() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM validation_queue WHERE status = :status
            ");
            $stmt->execute(['status' => VALIDATION_PENDING]);
            return $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("Count pending validations error: " . $e->getMessage());
            return 0;
        }
    }
}

?>
