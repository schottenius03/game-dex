<?php
// includes/auth.php
require_once __DIR__ . '/../models/UserModel.php';

// Global session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatic login via remember-me cookie if session is not set
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $userModel = new UserModel();
    $userId = $userModel->validateRememberToken($_COOKIE['remember_me']);

    if ($userId) {
        $_SESSION['user_id'] = $userId;
        
        // Fetch username to ensure session consistency
        $stmt = $userModel->getPdo()->prepare("SELECT username FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['username'] = $user['username'] ?? 'User';
    } else {
        // Token invalid or expired: cleanup
        setcookie('remember_me', '', time() - 3600, '/');
    }
}