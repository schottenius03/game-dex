<?php
require_once __DIR__ . '/../config/database.php';

class GameModel {
    private PDO $pdo;

    public function __construct() {
        // Initialize the database connection via Singleton
        $db = DataBase::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Fetch all games from the database
     * @return array
     */
    public function getAllGames() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM games");
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Enrich each game with genres, ratings and the cover image
            foreach ($games as &$game) {
                $game['genres'] = $this->getGameGenres($game['id']);
                $game['platforms'] = $this->getGamePlatforms($game['id']);
                // Now returns array with 'avg' and 'count'
                $game['rating_data'] = $this->getGameRating($game['id']);
                $game['image_url'] = $this->getGameImage($game['id']);
            }
            return $games;
        } catch (PDOException $e) {
            error_log("Error fetching all games: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a single game by ID and enrich it with metadata
     * @param int $id
     * @return array|null
     */
    public function getGameById($id) {
        try {
            $sql = "SELECT * FROM games WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $game = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($game) {
                // Enrich the game with genres, ratings and the cover image
                $game['genres'] = $this->getGameGenres($game['id']);
                $game['platforms'] = $this->getGamePlatforms($game['id']);
                // Now returns array with 'avg' and 'count'
                $game['rating_data'] = $this->getGameRating($game['id']);
                $game['image_url'] = $this->getGameImage($game['id']);
                $game['reviews'] = $this->getGameReviews($game['id']);
                return $game;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error fetching game by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch the cover image for a game
     * @param int $game_id
     * @return string|null
     */
    private function getGameImage($game_id) {
        $sql = "SELECT url FROM game_images WHERE game_id = :id AND type = 'cover' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $game_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['url'] : null;
    }

    /**
     * Fetch genres for a specific game
     * @param int $game_id
     * @return array
     */
    private function getGameGenres($game_id) {
        $sql = "SELECT ge.name FROM genres ge 
                JOIN game_genres gg ON ge.id = gg.genre_id 
                WHERE gg.game_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $game_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch platforms for a specific game
     * @param int $game_id
     * @return array
     */
    private function getGamePlatforms($game_id) {
        $sql = "SELECT pl.name FROM platforms pl 
                JOIN game_platforms gp ON pl.id = gp.platform_id 
                WHERE gp.game_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $game_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch average rating and count from reviews table
     * @param int $game_id
     * @return array
     */
    private function getGameRating($game_id) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as count 
                FROM reviews 
                WHERE game_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $game_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['count'] > 0) {
            return [
                'avg' => (float)$result['avg_rating'],
                'count' => (int)$result['count']
            ];
        }
        return ['avg' => 0.0, 'count' => 0];
    }

    /**
     * Fetch reviews for a specific game
     * @param int $game_id
     * @return array
     */
    private function getGameReviews($game_id) {
        $sql = "SELECT r.rating, r.body, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.game_id = :id 
                ORDER BY r.created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $game_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new review into the database
     * @param int $game_id
     * @param int $user_id
     * @param int $rating
     * @param string $body
     * @return bool
     */
    public function addReview($game_id, $user_id, $rating, $body) {
        try {
            $sql = "INSERT INTO reviews (game_id, user_id, rating, body, created_at) 
                    VALUES (:game_id, :user_id, :rating, :body, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'game_id' => $game_id,
                'user_id' => $user_id,
                'rating'  => $rating,
                'body'    => $body
            ]);
        } catch (PDOException $e) {
            error_log("Error inserting review: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all platforms from the database
     * @return array
     */
    public function getAllPlatforms() {
        $stmt = $this->pdo->query("SELECT * FROM platforms ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all genres from the database
     * @return array
     */
    public function getAllGenres() {
        $stmt = $this->pdo->query("SELECT * FROM genres ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}