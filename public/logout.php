<?php
require_once __DIR__ . '/../models/UserModel.php';

session_start();

// Handle remember-me token cleanup
if (isset($_COOKIE['remember_me'])) {
    $userModel = new UserModel();
    
    // Remove token from database
    $tokenHash = hash('sha256', $_COOKIE['remember_me']);
    $stmt = $userModel->getPdo()->prepare("DELETE FROM user_tokens WHERE token_hash = ?");
    $stmt->execute([$tokenHash]);

    // Clear cookie from browser
    setcookie('remember_me', '', time() - 3600, '/');
}

// Clear all session variables
session_unset();
// Destroy the session
session_destroy();
// Redirect to login page
header("Location: login.php");
exit;