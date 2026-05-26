<?php
// Include the GameModel
require_once __DIR__ . '/../models/GameModel.php';

// Initialize the model
$gameModel = new GameModel();

// Get the search query from the URL, default to empty string if not provided
$query = $_GET['q'] ?? '';

// Fetch the filtered games
$games = $gameModel->searchGames($query);

// Set the header to JSON and output the data
header('Content-Type: application/json');
echo json_encode($games);