<?php
// models/WishlistModel.php

// Ensure the configuration file exists before loading
require_once __DIR__ . '/../config/database.php';

class WishlistModel {
    private PDO $pdo; 

    public function __construct() {
        $db = DataBase::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Toggles the wishlist status for a game.
     * Returns 'added' if inserted, 'removed' if deleted.
     */
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

    /**
     * Checks if a game is already in the user's wishlist.
     */
    public function isWishlisted(int $userId, int $gameId): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND game_id = ?");
        $stmt->execute([$userId, $gameId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all games associated with a user's wishlist.
     */
    public function getUserWishlist(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT g.* FROM games g 
            JOIN wishlist w ON g.id = w.game_id 
            WHERE w.user_id = ?
        ");
        $stmt->execute([$userId]);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure GameModel is loaded correctly
        require_once __DIR__ . '/GameModel.php';
        $gameModel = new GameModel();
        
        foreach ($games as &$game) {
            $game['platforms'] = $gameModel->getGamePlatforms($game['id']);
            $game['rating_data'] = $gameModel->getGameRating($game['id']);
        }
        
        return $games;
    }
}