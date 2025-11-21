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
                $uploadResult = uploadImage($_FILES['item_image'], UPLOAD_DIR);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['error'];
                }
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

include __DIR__ . '/../includes/header.php';
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
}

.form-group label .required {
    color: var(--danger);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 1rem;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.file-upload-box {
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-base);
}

.file-upload-box:hover {
    border-color: var(--primary-color);
    background: var(--bg-lighter);
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
}

.btn-submit {
    flex: 1;
    padding: var(--spacing-md);
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-cancel {
    padding: var(--spacing-md) var(--spacing-xl);
    background: var(--bg-lighter);
    color: var(--text-primary);
    border: none;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 600;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<nav class="contributor-nav">
    <div class="container">
        <h2>Add New Item</h2>
        <div>
            <a href="dashboard.php">← Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<main class="form-container">
    <div class="form-card">
        <h1>Submit New Item for Validation</h1>
        <p style="color: var(--text-secondary); margin-bottom: var(--spacing-xl);">
            All submissions are reviewed by admins before appearing on the site.
        </p>
        
        <?php if ($error): ?>
            <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="item_name">Item Name (English) <span class="required">*</span></label>
                    <input type="text" id="item_name" name="item_name" required
                           value="<?php echo htmlspecialchars($_POST['item_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="item_name_nepali">Item Name (Nepali)</label>
                    <input type="text" id="item_name_nepali" name="item_name_nepali"
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
                           value="<?php echo htmlspecialchars($_POST['base_price'] ?? ''); ?>">
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
                    <div class="file-upload-box">
                        <input type="file" id="item_image" name="item_image" accept="image/*" 
                               style="display: none;" onchange="updateFileName(this)">
                        <label for="item_image" style="cursor: pointer; margin: 0;">
                            <div id="file-name">📁 Click to upload image (Max 5MB)</div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit for Review</button>
            </div>
        </form>
    </div>
</main>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Click to upload image';
    document.getElementById('file-name').textContent = '📁 ' + fileName;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
