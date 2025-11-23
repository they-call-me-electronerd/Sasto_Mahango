<?php
// Debug Test File
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Test</h1>";

echo "<h2>Step 1: PHP Working</h2>";
echo "✓ PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br><br>";

echo "<h2>Step 2: Testing Constants</h2>";
define('MULYASUCHI_APP', true);
echo "✓ MULYASUCHI_APP defined<br><br>";

echo "<h2>Step 3: Loading Constants</h2>";
require_once __DIR__ . '/config/constants.php';
echo "✓ Constants loaded<br>";
echo "ROLE_ADMIN = " . ROLE_ADMIN . "<br><br>";

echo "<h2>Step 4: Loading Config</h2>";
require_once __DIR__ . '/config/config.php';
echo "✓ Config loaded<br>";
echo "SITE_NAME = " . SITE_NAME . "<br>";
echo "SITE_URL = " . SITE_URL . "<br><br>";

echo "<h2>Step 5: Testing Database Connection</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "✓ Database file loaded<br>";
    echo "✓ PDO connection established<br>";
    echo "Database: " . DB_NAME . "<br><br>";
} catch (Exception $e) {
    echo "✗ Database Error: " . $e->getMessage() . "<br><br>";
}

echo "<h2>Step 6: Testing Database Class</h2>";
try {
    require_once __DIR__ . '/classes/Database.php';
    $db = Database::getInstance();
    echo "✓ Database class loaded<br>";
    echo "✓ Database instance created<br><br>";
} catch (Exception $e) {
    echo "✗ Database Class Error: " . $e->getMessage() . "<br><br>";
}

echo "<h2>Step 7: Testing Functions</h2>";
try {
    require_once __DIR__ . '/includes/functions.php';
    echo "✓ Functions loaded<br>";
    echo "✓ sanitizeInput exists: " . (function_exists('sanitizeInput') ? 'Yes' : 'No') . "<br><br>";
} catch (Exception $e) {
    echo "✗ Functions Error: " . $e->getMessage() . "<br><br>";
}

echo "<h2>✅ All Tests Complete</h2>";
echo "If you see this, PHP is working correctly!";
?>
