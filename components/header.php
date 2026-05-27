<?php
// Start session to track user login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the URL for the Account link
$accountUrl = isset($_SESSION['user_id']) ? 'profile.php' : 'login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameDex</title>
    <link rel="icon" type="image/png" href="assets/game-controller.png">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/form.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="assets/game-controller.png" alt="GameDex Logo" class="logo-img"> GameDex
        </div>
        <nav class="nav-menu">
            <a href="index.php">Home</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="<?php echo $accountUrl; ?>">Account</a>
        </nav>
    </div>
</header>

<main class="main-content">