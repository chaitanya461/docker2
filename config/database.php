<?php
// config/database.php
// No session_start() here - it's in init.php

// Database configuration
define('DB_HOST', getenv("DB_HOST") ?: 'database');
define('DB_USER', getenv("DB_USER") ?: 'phonestore_user');
define('DB_PASSWORD', getenv("DB_PASSWORD") ?: 'phonestore_password');
define('DB_NAME', getenv("DB_NAME") ?: 'phone_store');

// Create connection
function getDatabaseConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Connection failed. Please try again later.");
        }
        
        // Set charset to UTF-8
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit();
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
        exit();
    }
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    
    $conn = getDatabaseConnection();
    if ($conn) {
        return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
    }
    
    return htmlspecialchars(trim($input));
}

function formatPrice($price) {
    return number_format((float)$price, 2, '.', ',');
}

function getCartCount() {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    $conn = getDatabaseConnection();
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'] ?: 0;
}
?>
