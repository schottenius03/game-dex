<?php
// public/wishlist_toggle.php
require_once __DIR__ . '/../models/WishlistModel.php';
session_start();

// Set response header to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to manage your wishlist!']);
    exit;
}

// Validate game using game_id
$gameId = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if (!$gameId) {
    echo json_encode(['success' => false, 'message' => 'Invalid game ID.']);
    exit;
}

// Initialize WishlistModel and toggle the status
$wishlistModel = new WishlistModel();
$action = $wishlistModel->toggleWishlist($userId, $gameId);

// Return success response
echo json_encode(['success' => true, 'action' => $action]);