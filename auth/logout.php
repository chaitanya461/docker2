<?php
// logout.php
require_once 'config/init.php';

// Only proceed if user is logged in
if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

// Clear all session data
session_unset();
session_destroy();

// Expire the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page with success message
$_SESSION['logout_success'] = "You have been successfully logged out.";
redirect('/auth/login.php');
exit;
?>
