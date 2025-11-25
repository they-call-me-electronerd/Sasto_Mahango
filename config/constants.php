<?php
/**
 * System Constants
 * 
 * Centralized constant definitions for the Mulyasuchi platform
 */

// Note: MULYASUCHI_APP is defined by each entry point (index.php, login.php, etc.)
// to prevent direct access to this file

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CONTRIBUTOR', 'contributor');

// User Status
define('STATUS_ACTIVE', 'active');
define('STATUS_SUSPENDED', 'suspended');
define('STATUS_INACTIVE', 'inactive');

// Item Status
define('ITEM_STATUS_ACTIVE', 'active');
define('ITEM_STATUS_PENDING', 'pending');
define('ITEM_STATUS_INACTIVE', 'inactive');

// Validation Queue Status
define('VALIDATION_PENDING', 'pending');
define('VALIDATION_APPROVED', 'approved');
define('VALIDATION_REJECTED', 'rejected');

// Action Types
define('ACTION_NEW_ITEM', 'new_item');
define('ACTION_PRICE_UPDATE', 'price_update');
define('ACTION_ITEM_EDIT', 'item_edit');

// Log Action Types
define('LOG_LOGIN', 'login');
define('LOG_LOGOUT', 'logout');
define('LOG_CREATE', 'create');
define('LOG_UPDATE', 'update');
define('LOG_DELETE', 'delete');
define('LOG_VALIDATE', 'validate');
define('LOG_REJECT', 'reject');

// Price Change Alert Threshold
define('PRICE_CHANGE_THRESHOLD', 20); // Percentage

// Maximum Tags Per Item
define('MAX_TAGS_PER_ITEM', 10);

?>
