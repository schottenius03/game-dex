<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private PDO $pdo;

    public function __construct() {
        // Initialize the database connection via Singleton
        $db = DataBase::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Get the PDO database connection
     * @return PDO
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Check if username or email already exists
     * @param string $username
     * @param string $email
     * @return bool
     */
    public function isUserExisting($username, $email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error checking if user exists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a new user
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $preferred_currency
     * @return bool
     */
    public function registerUser($username, $email, $password, $preferred_currency = 'EUR') {
        try {
            // Hash the password using PHP's secure password_hash
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password_hash, preferred_currency) VALUES (:username, :email, :password_hash, :preferred_currency)");
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $hash,
                'preferred_currency' => $preferred_currency
            ]);
        } catch (PDOException $e) {
            error_log("Error registering user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle user login and session initialization
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function loginUser($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the provided password against the stored hash
            if ($user && password_verify($password, $user['password_hash'])) {
                // Ensure session is started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error during login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate and save a remember-me token for the user
     * @param int $userId
     * @return string
     */
    public function createRememberToken($userId) {
        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

        $stmt = $this->pdo->prepare("INSERT INTO user_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $tokenHash, $expiry]);

        return $rawToken;
    }

    /**
     * Validate a remember-me token and return the user ID
     * @param string $rawToken
     * @return int|false
     */
    public function validateRememberToken($rawToken) {
        $tokenHash = hash('sha256', $rawToken);
        
        $stmt = $this->pdo->prepare("SELECT user_id FROM user_tokens WHERE token_hash = ? AND expires_at > NOW()");
        $stmt->execute([$tokenHash]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['user_id'] : false;
    }

    /**
     * Get single user data by ID
     * @param int $userId
     * @return array|bool
     */
    public function getUserData($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT email, preferred_currency FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all available platforms
     * @return array
     */
    public function getAllPlatforms() {
        $stmt = $this->pdo->prepare("SELECT id, name FROM platforms ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all available genres
     * @return array
     */
    public function getAllGenres() {
        $stmt = $this->pdo->prepare("SELECT id, name FROM genres ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get platform IDs chosen by a specific user
     * @param int $userId
     * @return array
     */
    public function getUserPlatforms($userId) {
        $stmt = $this->pdo->prepare("SELECT platform_id FROM user_platforms WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get genre IDs chosen by a specific user
     * @param int $userId
     * @return array
     */
    public function getUserGenres($userId) {
        $stmt = $this->pdo->prepare("SELECT favorite_genre_id FROM user_preferences WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Update user profile preferences and email using database transaction
     * @param int $userId
     * @param string $currency
     * @param array $platformIds
     * @param array $genreIds
     * @param string|null $email
     * @return bool
     */
    public function updateProfile($userId, $currency, $platformIds, $genreIds, $email = null) {
        try {
            // Start transaction to secure all related updates
            $this->pdo->beginTransaction();

            // Update user core account details depending on email provision
            if ($email !== null) {
                $stmt = $this->pdo->prepare("UPDATE users SET preferred_currency = :currency, email = :email WHERE id = :id");
                $stmt->execute([
                    'currency' => $currency,
                    'email' => $email,
                    'id' => $userId
                ]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE users SET preferred_currency = :currency WHERE id = :id");
                $stmt->execute([
                    'currency' => $currency,
                    'id' => $userId
                ]);
            }

            // Clear old platform associations for this user
            $stmt = $this->pdo->prepare("DELETE FROM user_platforms WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);

            // Insert newly selected platform preferences
            if (!empty($platformIds)) {
                $stmt = $this->pdo->prepare("INSERT INTO user_platforms (user_id, platform_id) VALUES (:user_id, :platform_id)");
                foreach ($platformIds as $platformId) {
                    $stmt->execute(['user_id' => $userId, 'platform_id' => $platformId]);
                }
            }

            // Clear old genre preferences for this user
            $stmt = $this->pdo->prepare("DELETE FROM user_preferences WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);

            // Insert newly selected genre preferences
            if (!empty($genreIds)) {
                $stmt = $this->pdo->prepare("INSERT INTO user_preferences (user_id, favorite_genre_id) VALUES (:user_id, :favorite_genre_id)");
                foreach ($genreIds as $genreId) {
                    $stmt->execute(['user_id' => $userId, 'favorite_genre_id' => $genreId]);
                }
            }

            // Commit transaction if all queries succeed
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Roll back database changes if any query fails
            $this->pdo->rollBack();
            error_log("Error updating profile preferences: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user account from database (Foreign keys handle cascade deletions)
     * @param int $userId
     * @return bool
     */
    public function deleteUser($userId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute(['id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user account: " . $e->getMessage());
            return false;
        }
    }
}