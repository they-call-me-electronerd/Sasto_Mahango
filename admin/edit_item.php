<?php
/**
 * Admin Edit Item
 * Edit existing product details
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin login
Auth::requireRole(ROLE_ADMIN, SITE_URL . '/admin/login.php');

$itemObj = new Item();
$categoryObj = new Category();

// Get item ID from URL
$itemId = (int)($_GET['id'] ?? 0);

if ($itemId <= 0) {
    setFlashMessage('Invalid item ID', 'error');
    redirect(SITE_URL . '/public/products.php');
}

// Get item details
$item = $itemObj->getItemById($itemId);

if (!$item) {
    setFlashMessage('Item not found', 'error');
    redirect(SITE_URL . '/public/products.php');
}

// Get all categories for dropdown
$categories = $categoryObj->getActiveCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('Invalid security token', 'error');
    } else {
        // Validate inputs
        $itemName = sanitizeInput($_POST['item_name'] ?? '');
        $itemNameNepali = sanitizeInput($_POST['item_name_nepali'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $currentPrice = sanitizeInput($_POST['current_price'] ?? '');
        $unit = sanitizeInput($_POST['unit'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $marketLocation = sanitizeInput($_POST['market_location'] ?? '');
        
        $errors = [];
        
        // Debug logging
        Logger::log('item_edit_attempt', 'Editing item #' . $itemId . ' - Files received: ' . json_encode($_FILES));
        
        if (empty($itemName)) {
            $errors[] = 'Item name is required';
        }
        
        if ($categoryId <= 0) {
            $errors[] = 'Please select a valid category';
        }
        
        if (empty($currentPrice) || !is_numeric($currentPrice) || $currentPrice < 0) {
            $errors[] = 'Please enter a valid price';
        }
        
        if (empty($unit)) {
            $errors[] = 'Unit is required';
        }
        
        if (empty($errors)) {
            // Handle image upload if new image is provided
            $imagePath = $item['image_path']; // Keep existing image by default
            
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/items/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Validate file size (5MB limit)
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                if ($_FILES['item_image']['size'] > $maxFileSize) {
                    $errors[] = 'Image size exceeds 5MB limit';
                } else {
                    // Validate MIME type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    $fileType = $_FILES['item_image']['type'];
                    $fileMime = mime_content_type($_FILES['item_image']['tmp_name']);
                    
                    if (in_array($fileType, $allowedTypes) && in_array($fileMime, $allowedTypes)) {
                        $extension = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
                        $filename = 'item_' . time() . '_' . uniqid() . '.' . $extension;
                        $uploadPath = $uploadDir . $filename;
                        
                        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $uploadPath)) {
                            $imagePath = $filename;
                            Logger::log('item_image_upload', 'Item #' . $itemId . ' - New image uploaded: ' . $filename);
                            
                            // Delete old image if it exists and is different
                            if (!empty($item['image_path']) && $item['image_path'] !== $imagePath) {
                                $oldImagePath = __DIR__ . '/../assets/uploads/items/' . $item['image_path'];
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                    Logger::log('item_image_delete', 'Item #' . $itemId . ' - Old image deleted: ' . $item['image_path']);
                                }
                            }
                        } else {
                            $errors[] = 'Failed to upload image';
                            Logger::log('item_image_error', 'Item #' . $itemId . ' - Failed to move uploaded file');
                        }
                    } else {
                        $errors[] = 'Invalid image type. Only JPG, PNG, and WEBP are allowed (detected: ' . $fileMime . ')';
                        Logger::log('item_image_error', 'Item #' . $itemId . ' - Invalid MIME type: ' . $fileMime);
                    }
                }
            } elseif (isset($_FILES['item_image']) && $_FILES['item_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle upload errors
                switch ($_FILES['item_image']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errors[] = 'Image file is too large';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errors[] = 'Image was only partially uploaded';
                        break;
                    default:
                        $errors[] = 'Error uploading image';
                        break;
                }
            }
            
            if (empty($errors)) {
                // Prepare data for update
                $data = [
                    'item_name' => $itemName,
                    'item_name_nepali' => $itemNameNepali,
                    'category_id' => $categoryId,
                    'current_price' => $currentPrice,
                    'unit' => $unit,
                    'description' => $description,
                    'market_location' => $marketLocation
                ];
                
                // Add image path if changed
                if ($imagePath !== $item['image_path']) {
                    $data['image_path'] = $imagePath;
                    Logger::log('item_image_update', 'Item #' . $itemId . ' - Image changed from "' . $item['image_path'] . '" to "' . $imagePath . '"');
                } else {
                    Logger::log('item_image_update', 'Item #' . $itemId . ' - Image unchanged: "' . $imagePath . '"');
                }
                
                // Update item
                $result = $itemObj->updateItem($itemId, $data, Auth::getUserId());
                
                if ($result) {
                    Logger::log('item_updated', 'Item #' . $itemId . ' updated by admin');
                    setFlashMessage('Item updated successfully!', 'success');
                    redirect(SITE_URL . '/public/products.php');
                } else {
                    $errors[] = 'Failed to update item. Please try again.';
                }
            }
        }
        
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
        }
    }
}

$pageTitle = 'Edit Item - ' . htmlspecialchars($item['item_name']);
$additionalCSS = ['pages/auth-admin.css', 'pages/contributor-dashboard.css'];
include __DIR__ . '/../includes/header_professional.php';
?>

<main class="dashboard-layout" style="padding-top: 100px;">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">
                    <i class="bi bi-pencil-square"></i> Edit Item
                </h1>
                <p class="dashboard-subtitle">Update product information</p>
            </div>
            <a href="<?php echo SITE_URL; ?>/public/products.php" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlashMessage()): ?>
            <?php $flash = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?>"></i>
                <span><?php echo $flash['message']; ?></span>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                
                <div class="form-grid">
                    
                    <!-- Item Name (English) -->
                    <div class="form-group">
                        <label for="item_name" class="form-label required">
                            <i class="bi bi-tag"></i> Item Name (English)
                        </label>
                        <input 
                            type="text" 
                            id="item_name" 
                            name="item_name" 
                            class="form-input"
                            value="<?php echo htmlspecialchars($item['item_name']); ?>"
                            required
                            placeholder="e.g., Tomato">
                    </div>

                    <!-- Item Name (Nepali) -->
                    <div class="form-group">
                        <label for="item_name_nepali" class="form-label">
                            <i class="bi bi-tag"></i> Item Name (Nepali)
                        </label>
                        <input 
                            type="text" 
                            id="item_name_nepali" 
                            name="item_name_nepali" 
                            class="form-input"
                            value="<?php echo htmlspecialchars($item['item_name_nepali'] ?? ''); ?>"
                            placeholder="e.g., गोलभेडा">
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category_id" class="form-label required">
                            <i class="bi bi-grid"></i> Category
                        </label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option 
                                    value="<?php echo $category['category_id']; ?>"
                                    <?php echo $item['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Current Price -->
                    <div class="form-group">
                        <label for="current_price" class="form-label required">
                            <i class="bi bi-currency-rupee"></i> Current Price (NPR)
                        </label>
                        <input 
                            type="number" 
                            id="current_price" 
                            name="current_price" 
                            class="form-input"
                            value="<?php echo htmlspecialchars($item['current_price']); ?>"
                            step="0.01"
                            min="0"
                            required
                            placeholder="e.g., 120.00">
                    </div>

                    <!-- Unit -->
                    <div class="form-group">
                        <label for="unit" class="form-label required">
                            <i class="bi bi-rulers"></i> Unit
                        </label>
                        <select id="unit" name="unit" class="form-select" required>
                            <option value="">Select Unit</option>
                            <option value="kg" <?php echo $item['unit'] === 'kg' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                            <option value="dozen" <?php echo $item['unit'] === 'dozen' ? 'selected' : ''; ?>>Dozen</option>
                            <option value="piece" <?php echo $item['unit'] === 'piece' ? 'selected' : ''; ?>>Piece</option>
                            <option value="liter" <?php echo $item['unit'] === 'liter' ? 'selected' : ''; ?>>Liter</option>
                            <option value="gram" <?php echo $item['unit'] === 'gram' ? 'selected' : ''; ?>>Gram</option>
                            <option value="bunch" <?php echo $item['unit'] === 'bunch' ? 'selected' : ''; ?>>Bunch</option>
                            <option value="bag" <?php echo $item['unit'] === 'bag' ? 'selected' : ''; ?>>Bag</option>
                        </select>
                    </div>

                    <!-- Market Location -->
                    <div class="form-group">
                        <label for="market_location" class="form-label">
                            <i class="bi bi-geo-alt"></i> Market Location
                        </label>
                        <input 
                            type="text" 
                            id="market_location" 
                            name="market_location" 
                            class="form-input"
                            value="<?php echo htmlspecialchars($item['market_location'] ?? ''); ?>"
                            placeholder="e.g., Kalimati Wholesale Market">
                    </div>

                    <!-- Description (Full Width) -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description" class="form-label">
                            <i class="bi bi-text-paragraph"></i> Description
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-textarea"
                            rows="4"
                            placeholder="Additional details about the item..."><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Current Image Display -->
                    <?php if (!empty($item['image_path'])): ?>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">
                            <i class="bi bi-image"></i> Current Image
                        </label>
                        <div class="current-image-preview">
                            <img 
                                src="<?php echo SITE_URL . '/' . htmlspecialchars($item['image_path']); ?>" 
                                alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid var(--border-color); object-fit: cover;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Item Image Upload -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="item_image" class="form-label">
                            <i class="bi bi-image"></i> Upload New Image (Optional)
                        </label>
                        <input 
                            type="file" 
                            id="item_image" 
                            name="item_image" 
                            class="form-input"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewNewImage(this)">
                        <small class="form-help">Accepted formats: JPG, PNG, WEBP. Max size: 5MB. Leave empty to keep current image.</small>
                        
                        <!-- New Image Preview -->
                        <div id="new-image-preview" style="margin-top: 1rem; display: none;">
                            <label class="form-label" style="margin-bottom: 0.5rem;">
                                <i class="bi bi-eye"></i> New Image Preview
                            </label>
                            <img id="new-preview-img" src="" alt="New Preview" style="max-width: 200px; max-height: 200px; border-radius: 12px; border: 2px solid var(--brand-primary); object-fit: cover; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
                            <button type="button" onclick="clearNewImage()" style="display: block; margin-top: 0.5rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem; font-weight: 600;">
                                <i class="bi bi-x-circle"></i> Remove New Image
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-circle"></i> Update Item
                    </button>
                    <a href="<?php echo SITE_URL; ?>/public/products.php" class="btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Item Information Card -->
        <div class="info-card" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--brand-primary);">
                <i class="bi bi-info-circle"></i> Item Information
            </h3>
            <div style="display: grid; gap: 0.5rem; font-size: 0.9rem; color: var(--text-secondary);">
                <div><strong>Item ID:</strong> #<?php echo $item['item_id']; ?></div>
                <div><strong>Status:</strong> 
                    <span class="badge badge-<?php echo $item['status'] === 'active' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                </div>
                <div><strong>Created By:</strong> <?php echo htmlspecialchars($item['created_by_name'] ?? 'Unknown'); ?></div>
                <div><strong>Created At:</strong> <?php echo date('M j, Y g:i A', strtotime($item['created_at'])); ?></div>
                <div><strong>Last Updated:</strong> <?php echo date('M j, Y g:i A', strtotime($item['updated_at'])); ?></div>
                <div><strong>Slug:</strong> <?php echo htmlspecialchars($item['slug']); ?></div>
            </div>
        </div>

    </div>
</main>

<style>
.form-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--input-bg, #fff);
    color: var(--text-primary);
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-textarea {
    resize: vertical;
    font-family: inherit;
}

.form-help {
    display: block;
    margin-top: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.info-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    border-left: 4px solid var(--brand-primary);
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions button,
    .form-actions a {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
// Form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    
    form.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('item_image');
        
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            console.log('Form submitting with file:', {
                name: file.name,
                size: file.size,
                type: file.type
            });
            
            // Validate file before submission
            if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/)) {
                e.preventDefault();
                alert('Please select a valid image file (JPG, PNG, or WEBP)');
                return false;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('File size must be less than 5MB');
                return false;
            }
        } else {
            console.log('Form submitting without new image - keeping existing image');
        }
    });
});

function previewNewImage(input) {
    console.log('previewNewImage called', input);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        console.log('File selected:', file.name, file.size, file.type);
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file!');
            clearNewImage();
            return;
        }
        
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit!');
            clearNewImage();
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            console.log('FileReader onload triggered');
            const previewContainer = document.getElementById('new-image-preview');
            const previewImg = document.getElementById('new-preview-img');
            
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.onerror = function(e) {
            console.error('FileReader error:', e);
            alert('Error reading file. Please try again.');
        };
        
        reader.readAsDataURL(file);
        console.log('FileReader.readAsDataURL called');
    } else {
        console.log('No file selected');
    }
}

function clearNewImage() {
    const input = document.getElementById('item_image');
    input.value = '';
    document.getElementById('new-image-preview').style.display = 'none';
    document.getElementById('new-preview-img').src = '';
}
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
