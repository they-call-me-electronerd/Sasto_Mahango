<?php
/**
 * Logout Script (Admin)
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

$auth = new Auth();
$auth->logout();

redirect(SITE_URL . '/public/index.php');
?>
