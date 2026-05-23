<?php 
  // Get array with games from the model
  require_once '../models/GameModel.php';
  $games = getAllGames();
  
  // Include header
  include '../components/header.php'; 
?>

<link rel="stylesheet" href="styles/index_styles.css">

<section class="container">
    <h1 class="page-title">My Game Collection</h1>
    
    <div class="game-grid">
        <!-- Loop the array !-->
        <?php foreach ($games as $game): ?>
        <a href="game.php?id=<?php echo $game['id']; ?>" class="game-card">
            <div class="card-image">
                <img src="assets/game-controller.png" alt="<?php echo $game['title']; ?>">
            </div>
            <div class="card-content">
                <h3><?php echo $game['title']; ?></h3>
                <p class="genre"><?php echo $game['genre']; ?></p>
                <span class="rating"><?php echo $game['rating']; ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<?php 
  // Include footer
  include '../components/footer.php'; 
?>