<?php
/**
 * Update Item Price - Contributor Panel
 */

define('MULYASUCHI_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../includes/functions.php';

// Require contributor login
Auth::requireRole(ROLE_CONTRIBUTOR, SITE_URL . '/contributor/login.php');

$pageTitle = 'Update Price';
$metaDescription = 'Submit current market prices for existing items in Nepal.';
$additionalCSS = ['pages/contributor-dashboard.css', 'pages/auth-admin.css'];
$error = '';
$success = '';
$selectedItem = null;

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = sanitizeInput($_GET['search']);
    $itemObj = new Item();
    $searchResults = $itemObj->searchItems($searchTerm, null, 1, 50);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $newPrice = floatval($_POST['new_price'] ?? 0);
        $marketLocation = sanitizeInput($_POST['market_location'] ?? '');
        $remarks = sanitizeInput($_POST['remarks'] ?? '');
        
        if ($itemId <= 0 || $newPrice <= 0 || empty($marketLocation)) {
            $error = 'Please fill all required fields.';
        } else {
            try {
                $validationObj = new Validation();
                $result = $validationObj->submitPriceUpdate($itemId, $newPrice, $marketLocation, $remarks);
                
                if ($result) {
                    setFlashMessage('Price update submitted! Waiting for admin approval.', 'success');
                    redirect(SITE_URL . '/contributor/dashboard.php');
                } else {
                    $error = 'Failed to submit price update.';
                }
            } catch (Exception $e) {
                error_log("Update price error: " . $e->getMessage());
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}

// Load item details if ID provided
if (isset($_GET['item_id']) && !empty($_GET['item_id'])) {
    $itemObj = new Item();
    $selectedItem = $itemObj->getItemById((int)$_GET['item_id']);
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

.search-box {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-xl);
}

.search-box input {
    flex: 1;
    padding: 1rem 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
}

.search-box button {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.search-box button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
}

.search-results {
    margin-bottom: var(--spacing-xl);
}

.item-result {
    padding: var(--spacing-lg);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    margin-bottom: var(--spacing-md);
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bg-primary);
}

.item-result:hover {
    border-color: var(--brand-primary);
    background: var(--bg-secondary);
    transform: translateX(8px);
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.15);
}

.selected-item {
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, var(--bg-secondary) 100%);
    padding: var(--spacing-xl);
    border-radius: 12px;
    margin-bottom: var(--spacing-xl);
    border-left: 4px solid var(--brand-primary);
    box-shadow: var(--shadow-md);
}

.selected-item h3 {
    margin-bottom: var(--spacing-sm);
}

.current-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: var(--spacing-sm) 0;
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

.form-group input,
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
.form-group textarea:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
    transform: translateY(-2px);
}

.form-group textarea {
    min-height: 80px;
    resize: vertical;
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
    .contributor-dashboard {
        padding-top: calc(var(--navbar-height) + 1rem);
    }
    
    .form-card {
        padding: var(--spacing-lg) !important;
    }
    
    .search-box {
        flex-direction: column;
    }
    
    .search-box button {
        width: 100%;
    }
}
</style>

<main class="contributor-dashboard">
    <div class="dashboard-container">
        <div class="form-card" style="background: var(--bg-primary); border-radius: var(--radius-2xl); padding: var(--spacing-2xl); box-shadow: var(--shadow-md); border: 1px solid var(--border-light); max-width: 900px; margin: 0 auto;">
        <h1 style="font-family: var(--font-heading); font-size: var(--font-size-3xl); font-weight: var(--font-weight-extrabold); color: var(--text-primary); margin-bottom: var(--spacing-md);">Update Item Price</h1>
        <p style="color: var(--text-secondary); margin-bottom: var(--spacing-xl);">
            Search for an item to update its price. All price updates require admin validation.
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
        
        <!-- Search Box -->
        <form method="GET" action="" class="search-box">
            <input type="text" name="search" placeholder="Search for item name..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" required>
            <button type="submit">Search</button>
        </form>
        
        <!-- Search Results -->
        <?php if (isset($searchResults) && !empty($searchResults['items'])): ?>
            <div class="search-results">
                <h3 style="font-size: var(--font-size-xl); font-weight: var(--font-weight-bold); color: var(--text-primary); margin-bottom: var(--spacing-lg); display: flex; align-items: center; gap: var(--spacing-sm);">
                    <i class="bi bi-search" style="color: var(--brand-primary);"></i>
                    Search Results (<?php echo count($searchResults['items']); ?> found)
                </h3>
                <?php foreach ($searchResults['items'] as $item): ?>
                    <div class="item-result" onclick="selectItem(<?php echo $item['item_id']; ?>)">
                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                        <?php if (!empty($item['item_name_nepali'])): ?>
                            <span style="color: var(--text-secondary);"> (<?php echo htmlspecialchars($item['item_name_nepali']); ?>)</span>
                        <?php endif; ?>
                        <div style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 4px;">
                            Current: NPR <?php echo number_format($item['current_price'], 2); ?> / <?php echo htmlspecialchars($item['unit']); ?>
                            | <?php echo htmlspecialchars($item['market_location']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($searchResults)): ?>
            <div class="flash-message flash-error">No items found for "<?php echo htmlspecialchars($_GET['search']); ?>"</div>
        <?php endif; ?>
        
        <!-- Update Form -->
        <?php if ($selectedItem): ?>
            <div class="selected-item">
                <h3><?php echo htmlspecialchars($selectedItem['item_name']); ?></h3>
                <div style="color: var(--text-secondary); margin-bottom: var(--spacing-xs);">
                    Category: <?php echo htmlspecialchars($selectedItem['category_name']); ?>
                </div>
                <div class="current-price">
                    Current Price: NPR <?php echo number_format($selectedItem['current_price'], 2); ?> / <?php echo htmlspecialchars($selectedItem['unit']); ?>
                </div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">
                    Last updated: <?php echo date('M d, Y', strtotime($selectedItem['updated_at'])); ?>
                </div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                <input type="hidden" name="item_id" value="<?php echo $selectedItem['item_id']; ?>">
                
                <div class="form-group">
                    <label for="new_price">New Price (NPR) <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="new_price" name="new_price" step="0.01" min="0" required
                           placeholder="Enter new price">
                    <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem; display: block;">
                        Current price: NPR <?php echo number_format($selectedItem['current_price'], 2); ?>
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="market_location">Market Location <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="market_location" name="market_location" required
                           placeholder="e.g., Kalimati Vegetable Market"
                           value="<?php echo htmlspecialchars($selectedItem['market_location']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="remarks">Remarks (Optional)</label>
                    <textarea id="remarks" name="remarks" 
                              placeholder="Any additional notes about this price change..."></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Submit for Review</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    </div>
</main>

<script>
function selectItem(itemId) {
    window.location.href = '?item_id=' + itemId + '<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>';
}
</script>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>
