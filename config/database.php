<?php
// Database configuration
define('DB_HOST', getenv("DB_HOST") ?: 'localhost');
define('DB_USER', getenv("DB_USER") ?: 'phonestore_user');
define('DB_PASSWORD', getenv("DB_PASSWORD") ?: 'phonestore_password');
define('DB_NAME', getenv("DB_NAME") ?: 'phone_store');

// Create connection
function getDatabaseConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

function formatPrice($price) {
    return number_format($price, 2);
}
?>
