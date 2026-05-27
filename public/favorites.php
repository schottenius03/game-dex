<?php
// favorites.php - API endpoint to handle wishlist toggle actions
require_once __DIR__ . '/../models/WishlistModel.php';

session_start();

// Ensure only POST requests are processed for security
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$gameId = $_POST['game_id'] ?? null;
if ($gameId) {
    $wishlistModel = new WishlistModel();
    // Toggle the status in the database
    $result = $wishlistModel->toggleWishlist($_SESSION['user_id'], $gameId);
    
    echo json_encode(['status' => 'success', 'action' => $result]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing game ID']);
}