<?php
// includes/auth.php
// Requires the database configuration from the config folder
require_once __DIR__ . '/../config/database.php';

// Function to register a new user
function registerUser($username, $email, $password) {
    $db = DataBase::getInstance()->getConnection();
    // Hash the password using PHP's secure password_hash
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare statement to prevent SQL injection
    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $email, $hash]);
}

// Function to handle user login
function loginUser($username, $password) {
    $db = DataBase::getInstance()->getConnection();
    
    // Select user by username
    $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify the provided password against the stored hash
    if ($user && password_verify($password, $user['password_hash'])) {
        // Start session and store user info
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}