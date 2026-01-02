<?php
// config/init.php
// This must be the FIRST file included in every page

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
if (!ob_get_level()) {
    ob_start();
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    }
}

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Include database configuration
require_once __DIR__ . '/database.php';
?>
