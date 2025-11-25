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
     * Approve validation (uses stored procedure)
     */
    public function approveValidation($queueId) {
        try {
            $stmt = $this->pdo->prepare("CALL sp_approve_validation(:queue_id, :admin_id)");
            $result = $stmt->execute([
                'queue_id' => $queueId,
                'admin_id' => Auth::getUserId()
            ]);
            
            if ($result) {
                Logger::log(LOG_VALIDATE, 'validation_queue', $queueId, "Validation approved");
            }
            
            return $result;
            
        } catch (PDOException $e) {
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
