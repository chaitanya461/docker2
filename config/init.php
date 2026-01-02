<?php
// config/init.php
// NO WHITESPACE before <?php

// Start output buffering
if (!ob_get_level()) {
    ob_start();
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/database.php';
?>
