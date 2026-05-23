<?php

/**
 * Fetches all games from the data source.
 *
 * @return array A collection of game records, each containing id, title, genre, rating, and description.
 */
function getAllGames() {
    return [
        ['id' => 1, 'title' => 'Title1', 'genre' => 'Genre', 'rating' => 5, 'image' => 'assets/game-controller.png', 'description' => 'This is a description of the game.'],
        ['id' => 2, 'title' => 'Title2', 'genre' => 'Genre', 'rating' => 5, 'image' => 'assets/game-controller.png', 'description' => 'This is a description of the game.'],
        ['id' => 3, 'title' => 'Title4', 'genre' => 'Genre', 'rating' => 5, 'image' => 'assets/game-controller.png', 'description' => 'This is a description of the game.']
    ];
}

/**
 * Retrieves game based on ID.
 *
 * @param  [type] $id
 * @return void
 */
function getGameById($id) {
    $games = getAllGames();
    foreach ($games as $game) {
        if ($game['id'] == (int)$id) {
            return $game;
        }
    }
    return null;
}
?>