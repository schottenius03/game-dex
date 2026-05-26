<?php
// Include model and database configuration
require_once __DIR__ . '/../models/GameModel.php';

$gameModel = new GameModel();

// Get filter parameters from the request
$query = $_GET['q'] ?? '';
$platform_id = $_GET['platform_id'] ?? null;
$genre_id = $_GET['genre_id'] ?? null;

// Get the connection from the database instance
$db = DataBase::getInstance();
$pdo = $db->getConnection();

// Base SQL query
$sql = "SELECT DISTINCT g.* FROM games g";
$params = [];
$whereClauses = [];

// Apply platform filter if selected
if (!empty($platform_id)) {
    $sql .= " JOIN game_platforms gp ON g.id = gp.game_id";
    $whereClauses[] = "gp.platform_id = :pid";
    $params[':pid'] = $platform_id;
}

// Apply genre filter if selected
if (!empty($genre_id)) {
    $sql .= " JOIN game_genres gg ON g.id = gg.game_id";
    $whereClauses[] = "gg.genre_id = :gid";
    $params[':gid'] = $genre_id;
}

// Apply search query filter if provided
if (!empty($query)) {
    $whereClauses[] = "g.title LIKE :query";
    $params[':query'] = '%' . $query . '%';
}

// Combine where clauses
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Fetch the filtered games
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enrich each game with the image_url
foreach ($games as &$game) {
    // Fetch the cover image for each game
    $sqlImg = "SELECT url FROM game_images WHERE game_id = :id AND type = 'cover' LIMIT 1";
    $stmtImg = $pdo->prepare($sqlImg);
    $stmtImg->execute(['id' => $game['id']]);
    $imgResult = $stmtImg->fetch(PDO::FETCH_ASSOC);
    
    $game['image_url'] = $imgResult ? $imgResult['url'] : null;
}

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode($games);