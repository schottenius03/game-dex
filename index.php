<?php 
  // Include header
  include 'components/header.php'; 
?>

<main class="container">
    <h1 class="page-title">My Game Collection</h1>
    
    <div class="game-grid">
        
        <a href="#1" class="game-card">
            <div class="card-image">
                <img src="assets/game-controller.png" alt="Video game 1">
            </div>
            <div class="card-content">
                <h3>Title</h3>
                <p class="genre">Genre</p>
                <span class="rating">Rating 4.5</span>
            </div>
        </a>

        <a href="#2" class="game-card">
            <div class="card-image">
                <img src="assets/game-controller.png" alt="Video game 2">
            </div>
            <div class="card-content">
                <h3>Title</h3>
                <p class="genre">Genre</p>
                <span class="rating">Rating 3.8</span>
            </div>
        </a>

    </div>
</main>

<?php 
  // Include footer
  include 'components/footer.php'; 
?>