<?php
/**
 * Contributor Edit Item
 * Submit item edit requests for admin approval
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../includes/functions.php';

// Require contributor login
Auth::requireRole(ROLE_CONTRIBUTOR, SITE_URL . '/contributor/login.php');

$itemObj = new Item();
$categoryObj = new Category();
$validationObj = new Validation();

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
        $source = sanitizeInput($_POST['source'] ?? '');
        
        $errors = [];
        
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
            $imagePath = null;
            
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['item_image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['filename'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }
            
            if (empty($errors)) {
                // Prepare data for validation queue
                $data = [
                    'item_name' => $itemName,
                    'item_name_nepali' => $itemNameNepali,
                    'category_id' => $categoryId,
                    'price' => $currentPrice,
                    'unit' => $unit,
                    'description' => $description,
                    'market_location' => $marketLocation,
                    'source' => $source
                ];
                
                // Add image path if uploaded
                if ($imagePath) {
                    $data['image_path'] = $imagePath;
                }
                
                // Submit for validation
                $result = $validationObj->submitItemEdit($itemId, $data);
                
                if ($result) {
                    setFlashMessage('Item edit request submitted successfully! Waiting for admin approval.', 'success');
                    redirect(SITE_URL . '/contributor/dashboard.php');
                } else {
                    $errors[] = 'Failed to submit edit request. Please try again.';
                }
            }
        }
        
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
        }
    }
}

$pageTitle = 'Edit Item Request - ' . htmlspecialchars($item['item_name']);
$additionalCSS = ['pages/contributor-dashboard.css'];
include __DIR__ . '/../includes/header_professional.php';
?>

<main class="contributor-dashboard" style="padding-top: 100px;">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">
                    <i class="bi bi-pencil-square"></i> Request Item Edit
                </h1>
                <p class="dashboard-subtitle">Submit changes for admin approval</p>
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

        <!-- Info Alert -->
        <div class="alert alert-info" style="margin-bottom: 2rem;">
            <i class="bi bi-info-circle-fill"></i>
            <span><strong>Note:</strong> Your edit request will be reviewed by an admin before the changes are applied to the item.</span>
        </div>

        <!-- Edit Form -->
        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" class="contributor-form">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                
                <div class="form-grid">
                    
                    <!-- Current Item Info -->
                    <div class="current-item-info" style="grid-column: 1 / -1; padding: 1.5rem; background: var(--bg-secondary); border-radius: 12px; border-left: 4px solid var(--brand-primary); margin-bottom: 1rem; border: 1px solid var(--border-color);">
                        <h3 style="color: var(--brand-primary); margin-bottom: 0.5rem; font-size: 1.125rem; font-weight: 600;">
                            <i class="bi bi-box"></i> Current Item Details
                        </h3>
                        <div style="display: grid; gap: 0.5rem; font-size: 0.9rem; color: var(--text-primary);">
                            <div style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Name:</strong> <?php echo htmlspecialchars($item['item_name']); ?></div>
                            <div style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Category:</strong> <?php echo htmlspecialchars($item['category_name']); ?></div>
                            <div style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Price:</strong> NPR <?php echo number_format($item['current_price'], 2); ?> per <?php echo htmlspecialchars($item['unit']); ?></div>
                            <?php if (!empty($item['market_location'])): ?>
                                <div style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Market:</strong> <?php echo htmlspecialchars($item['market_location']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
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

                    <!-- Quick Price Update Shortcut -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(249, 115, 22, 0.05)); border: 2px solid #3b82f6; border-radius: 12px; padding: 1.25rem;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                                    <i class="bi bi-lightning-charge-fill" style="font-size: 1.5rem; color: white;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1rem; font-weight: 700; color: #1e40af; margin: 0 0 0.25rem 0;">
                                        Need to Update Price Only?
                                    </h4>
                                    <p style="font-size: 0.875rem; color: #4b5563; margin: 0;">
                                        If you only want to update the price, use our quick price update feature for faster approval.
                                    </p>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/contributor/update_price.php?item_id=<?php echo $itemId; ?>" 
                                   class="btn-primary" 
                                   style="padding: 0.75rem 1.5rem; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; border-radius: 8px;">
                                    <i class="bi bi-speedometer2"></i>
                                    Quick Update
                                </a>
                            </div>
                        </div>
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

                    <!-- Source -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="source" class="form-label">
                            <i class="bi bi-link-45deg"></i> Source / Reference
                        </label>
                        <input 
                            type="text" 
                            id="source" 
                            name="source" 
                            class="form-input"
                            placeholder="e.g., Official market website, vendor contact">
                        <small class="form-help">Provide a source for your edit to help with verification</small>
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
                                style="max-width: 200px; border-radius: 8px; border: 2px solid var(--border-color);">
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
                        <i class="bi bi-send"></i> Submit Edit Request
                    </button>
                    <a href="<?php echo SITE_URL; ?>/public/products.php" class="btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
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

.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 500;
}

.alert-info {
    background: var(--bg-secondary);
    border: 2px solid #3b82f6;
    color: var(--text-primary);
}

[data-theme="dark"] .alert-info {
    background: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
    color: #93c5fd;
}

.alert-success {
    background: var(--bg-secondary);
    border: 2px solid #10b981;
    color: var(--text-primary);
}

[data-theme="dark"] .alert-success {
    background: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
    color: #6ee7b7;
}

.alert-error {
    background: var(--bg-secondary);
    border: 2px solid #ef4444;
    color: var(--text-primary);
}

[data-theme="dark"] .alert-error {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
    color: #fca5a5;
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
function previewNewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
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
            const previewContainer = document.getElementById('new-image-preview');
            const previewImg = document.getElementById('new-preview-img');
            
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.onerror = function(e) {
            alert('Error reading file. Please try again.');
        };
        
        reader.readAsDataURL(file);
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
