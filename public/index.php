<?php 
  // Include header
  include '../components/header.php'; 
?>

<link rel="stylesheet" href="styles/index_styles.css">

<section class="container">
    <h1 class="page-title">My Game Collection</h1>
    
    <div class="game-grid">
        
        <a href="game.php" class="game-card">
            <div class="card-image">
                <img src="assets/game-controller.png" alt="Video game 1">
            </div>
            <div class="card-content">
                <h3>Title</h3>
                <p class="genre">Genre</p>
                <span class="rating">4.5</span>
            </div>
        </a>

        <a href="game.php" class="game-card">
            <div class="card-image">
                <img src="assets/game-controller.png" alt="Video game 2">
            </div>
            <div class="card-content">
                <h3>Title</h3>
                <p class="genre">Genre</p>
                <span class="rating">3.8</span>
            </div>
        </a>

    </div>
</section>

<?php 
  // Include footer
  include '../components/footer.php'; 
?>