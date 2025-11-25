<?php
/**
 * Add New Item - Contributor Panel
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../includes/functions.php';

// Require contributor login
Auth::requireRole(ROLE_CONTRIBUTOR, SITE_URL . '/contributor/login.php');

$pageTitle = 'Add New Item';
$metaDescription = 'Add new products and commodities to Mulyasuchi marketplace database.';
$additionalCSS = ['pages/contributor-dashboard.css', 'pages/auth-admin.css'];
$error = '';
$success = '';

// Get categories
$categoryObj = new Category();
$categories = $categoryObj->getActiveCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $itemName = sanitizeInput($_POST['item_name'] ?? '');
        $itemNameNepali = sanitizeInput($_POST['item_name_nepali'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $basePrice = floatval($_POST['base_price'] ?? 0);
        $unit = sanitizeInput($_POST['unit'] ?? '');
        $marketLocation = sanitizeInput($_POST['market_location'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        
        // Validation
        if (empty($itemName) || $categoryId <= 0 || $basePrice <= 0 || empty($unit) || empty($marketLocation)) {
            $error = 'Please fill all required fields.';
        } else {
            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['item_image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['filename'];
                    // Log successful upload
                    error_log("Image uploaded successfully: " . $imagePath);
                    error_log("Full path: " . UPLOAD_DIR . $imagePath);
                    error_log("File exists: " . (file_exists(UPLOAD_DIR . $imagePath) ? 'YES' : 'NO'));
                } else {
                    $error = $uploadResult['error'];
                    error_log("Image upload failed: " . $uploadResult['error']);
                }
            } elseif (isset($_FILES['item_image'])) {
                error_log("File upload error code: " . $_FILES['item_image']['error']);
            }
            
            if (!$error) {
                try {
                    $validationObj = new Validation();
                    $result = $validationObj->submitNewItem([
                        'item_name' => $itemName,
                        'category_id' => $categoryId,
                        'price' => $basePrice,
                        'unit' => $unit,
                        'market_location' => $marketLocation,
                        'description' => $description . ($itemNameNepali ? " (Nepali Name: $itemNameNepali)" : ""),
                        'image_path' => $imagePath
                    ]);
                    
                    if ($result) {
                        setFlashMessage('Item submitted successfully! Waiting for admin approval.', 'success');
                        redirect(SITE_URL . '/contributor/dashboard.php');
                    } else {
                        $error = 'Failed to submit item. Please try again.';
                    }
                } catch (Exception $e) {
                    error_log("Add item error: " . $e->getMessage());
                    $error = 'An error occurred. Please try again.';
                }
            }
        }
    }
}

include __DIR__ . '/../includes/header_professional.php';

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
.contributor-nav {
    background: var(--gradient-success);
    color: white;
    padding: var(--spacing-md);
}

.contributor-nav .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.contributor-nav a {
    color: white;
    margin-left: var(--spacing-md);
}

.form-container {
    max-width: 800px;
    margin: var(--spacing-2xl) auto;
    padding: 0 var(--spacing-md);
}

.form-card {
    background: white;
    padding: var(--spacing-2xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-md);
}

.form-group-full {
    grid-column: 1 / -1;
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-group label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.9rem;
    letter-spacing: 0.01em;
}

.form-group label .required {
    color: #ef4444;
    font-weight: 700;
    margin-left: 2px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
    transform: translateY(-2px);
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.file-upload-box {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: var(--spacing-2xl);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bg-secondary);
}

.file-upload-box:hover {
    border-color: var(--brand-primary);
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, var(--bg-secondary) 100%);
    transform: scale(1.02);
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
}

.btn-submit {
    flex: 1;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    letter-spacing: 0.025em;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
}

.btn-cancel {
    padding: 1rem 2rem;
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel:hover {
    background: var(--bg-tertiary);
    border-color: var(--brand-primary);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .contributor-dashboard {
        padding-top: calc(var(--navbar-height) + 1rem);
    }
    
    .form-card {
        padding: var(--spacing-lg) !important;
    }
}
</style>

<main class="contributor-dashboard">
    <div class="dashboard-container">
        <div class="form-card" style="background: var(--bg-primary); border-radius: var(--radius-2xl); padding: var(--spacing-2xl); box-shadow: var(--shadow-md); border: 1px solid var(--border-light); max-width: 900px; margin: 0 auto;">
        <h1 style="font-family: var(--font-heading); font-size: var(--font-size-3xl); font-weight: var(--font-weight-extrabold); color: var(--text-primary); margin-bottom: var(--spacing-md);">Submit New Item for Validation</h1>
        <p style="color: var(--text-secondary); margin-bottom: var(--spacing-xl);">
            All submissions are reviewed by admins before appearing on the site.
        </p>
        
        <?php if ($error): ?>
            <div class="flash-message flash-error" style="margin-bottom: var(--spacing-xl); padding: var(--spacing-lg); border-radius: var(--radius-lg); background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #ef4444; color: #991b1b; font-weight: 600; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 1.5rem;"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="flash-message flash-success" style="margin-bottom: var(--spacing-xl); padding: var(--spacing-lg); border-radius: var(--radius-lg); background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #10b981; color: #065f46; font-weight: 600; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="item_name">Item Name (English) <span class="required">*</span></label>
                    <input type="text" id="item_name" name="item_name" required
                           placeholder="e.g., Tomato, Potato, Apple..."
                           value="<?php echo htmlspecialchars($_POST['item_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="item_name_nepali">Item Name (Nepali)</label>
                    <input type="text" id="item_name_nepali" name="item_name_nepali"
                           placeholder="e.g., गोलभेडा, आलु, स्याउ..."
                           value="<?php echo htmlspecialchars($_POST['item_name_nepali'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>"
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="base_price">Current Price <span class="required">*</span></label>
                    <input type="number" id="base_price" name="base_price" step="0.01" min="0" required
                           placeholder="e.g., 150.00"
                           value="<?php echo htmlspecialchars($_POST['base_price'] ?? ''); ?>">
                    <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem; display: block;">Enter price in NPR (Nepali Rupees)</small>
                </div>
                
                <div class="form-group">
                    <label for="unit">Unit <span class="required">*</span></label>
                    <input type="text" id="unit" name="unit" placeholder="e.g., kg, piece, liter" required
                           value="<?php echo htmlspecialchars($_POST['unit'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="market_location">Market Location <span class="required">*</span></label>
                    <input type="text" id="market_location" name="market_location" 
                           placeholder="e.g., Kalimati Vegetable Market" required
                           value="<?php echo htmlspecialchars($_POST['market_location'] ?? ''); ?>">
                </div>
                
                <div class="form-group form-group-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Provide details about the item..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group form-group-full">
                    <label for="item_image">Item Image (Optional)</label>
                    <div class="file-upload-box" id="upload-box">
                        <input type="file" id="item_image" name="item_image" accept="image/*" 
                               style="display: none;" onchange="previewImage(this)">
                        <label for="item_image" style="cursor: pointer; margin: 0;">
                            <div id="file-name" style="font-weight: 600; color: var(--text-primary);">
                                <i class="bi bi-cloud-upload" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: var(--brand-primary);"></i>
                                Click to upload image (Max 5MB)
                            </div>
                        </label>
                    </div>
                    <div id="image-preview" style="margin-top: 1rem; display: none; text-align: center;">
                        <p style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="bi bi-eye"></i> Image Preview
                        </p>
                        <div style="display: inline-block; position: relative;">
                            <img id="preview-img" src="" alt="Preview" style="max-width: 300px; max-height: 300px; border-radius: 12px; border: 3px solid var(--brand-primary); object-fit: contain; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); background: white; padding: 4px;">
                            <button type="button" onclick="clearImage()" style="position: absolute; top: -10px; right: -10px; width: 36px; height: 36px; padding: 0; background: #ef4444; color: white; border: 3px solid white; border-radius: 50%; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <p id="file-info" style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);"></p>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit for Review</button>
            </div>
        </form>
    </div>
    </div>
</main>

<script>
function previewImage(input) {
    const fileNameDisplay = document.getElementById('file-name');
    const uploadBox = document.getElementById('upload-box');
    const previewContainer = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const fileInfo = document.getElementById('file-info');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file!');
            clearImage();
            return;
        }
        
        // Update filename display
        fileNameDisplay.innerHTML = `<i class="bi bi-check-circle" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: #10b981;"></i>Image selected: ${file.name}`;
        uploadBox.style.borderColor = '#10b981';
        uploadBox.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, var(--bg-secondary) 100%)';
        
        // Show file info
        const fileSizeKB = (file.size / 1024).toFixed(2);
        const fileSizeMB = (file.size / 1048576).toFixed(2);
        const sizeText = file.size > 1048576 ? `${fileSizeMB} MB` : `${fileSizeKB} KB`;
        fileInfo.textContent = `File: ${file.name} | Size: ${sizeText} | Type: ${file.type}`;
        
        // Check file size
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit!');
            clearImage();
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.onerror = function(e) {
            alert('Error reading file. Please try again.');
        };
        
        reader.readAsDataURL(file);
    }
}

function clearImage() {
    const input = document.getElementById('item_image');
    const fileNameDisplay = document.getElementById('file-name');
    const uploadBox = document.getElementById('upload-box');
    const previewContainer = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    input.value = '';
    fileNameDisplay.innerHTML = '<i class="bi bi-cloud-upload" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: var(--brand-primary);"></i>Click to upload image (Max 5MB)';
    uploadBox.style.borderColor = '';
    uploadBox.style.background = '';
    previewContainer.style.display = 'none';
    previewImg.src = '';
}
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
