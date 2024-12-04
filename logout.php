<?php

require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (isset($_SESSION['user_id'])) {
    // Log the logout action for auditing
    logActivity($pdo, $_SESSION['user_id'], 'LOGOUT', 'User logged out');
    
    // Clear all session variables and destroy the session
    session_unset();
    session_destroy();

    // Redirect to the login page
    header('Location: login.php');
    exit();
} else {
    // If no session is found, redirect directly to the login page
    header('Location: login.php');
    exit();
}
