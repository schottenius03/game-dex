<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $pdo;

    public function __construct() {
        // Initialize the database connection via Singleton
        $db = DataBase::getInstance();
        $this->pdo = $db->getConnection();
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
     * @return bool
     */
    public function registerUser($username, $email, $password) {
        try {
            // Hash the password using PHP's secure password_hash
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $hash
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
}