<?php
// models/WishlistModel.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/GameModel.php';

class WishlistModel {
    private PDO $pdo; 

    public function __construct() {
        $db = DataBase::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Toggles the wishlist record for a specific user and game
    public function toggleWishlist(int $userId, int $gameId): string {
        if ($this->isWishlisted($userId, $gameId)) {
            $stmt = $this->pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND game_id = ?");
            $stmt->execute([$userId, $gameId]);
            return 'removed';
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO wishlist (user_id, game_id) VALUES (?, ?)");
            $stmt->execute([$userId, $gameId]);
            return 'added';
        }
    }

    // Checks if a record exists for the given user and game
    public function isWishlisted(int $userId, int $gameId): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND game_id = ?");
        $stmt->execute([$userId, $gameId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetches all game IDs associated with the user for quick UI checks
    public function getUserWishlistIds(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT game_id FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Fetches full game data including platforms, rating and cover image for the wishlist page
    public function getUserWishlist(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT g.* FROM games g 
            JOIN wishlist w ON g.id = w.game_id 
            WHERE w.user_id = ?
        ");
        $stmt->execute([$userId]);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $gameModel = new GameModel();
        
        foreach ($games as &$game) {
            $game['platforms'] = $gameModel->getGamePlatforms($game['id']);
            $game['rating_data'] = $gameModel->getGameRating($game['id']);
            $game['image_url'] = $gameModel->getGameImage($game['id']);
        }
        
        return $games;
    }
}