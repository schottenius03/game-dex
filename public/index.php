<?php 
  require_once '../models/GameModel.php';
  
  $gameModel = new GameModel();
  $games = $gameModel->getAllGames(); 
  
  include '../components/header.php'; 
?>

<link rel="stylesheet" href="styles/index_styles.css">

<section class="container">
    
    <div class="search-filter-section">
        <form class="search-form">
            <div class="form-group" style="width: 100%;">
                <input type="text" id="searchBar" name="q" autocomplete="off" placeholder="Search games...">
            </div>
        </form>

        <div class="filter-group">
            <div class="dropdown">
                <button class="dropbtn">Platform <span class="arrow">&#9663;</span></button>
                <div class="dropdown-content">
                    <a href="#">Placeholder</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Genre <span class="arrow">&#9663;</span></button>
                <div class="dropdown-content">
                    <a href="#">Placeholder</a>
                </div>
            </div>
        </div>

    </div>

    <div class="game-grid" id="gameGrid">
        <?php foreach ($games as $game): ?>
        <a href="game.php?id=<?php echo htmlspecialchars($game['id']); ?>" class="game-card">
            <div class="card-image">
                <img src="<?php echo htmlspecialchars($game['image_url'] ?? 'assets/game-controller.png'); ?>" 
                     alt="<?php echo htmlspecialchars($game['title']); ?>">
            </div>
            <div class="card-content">
                <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                
                <div class="card-meta">
                    <p class="platform">
                        <?php 
                        $platformNames = array_map(function($p) { return $p['name']; }, $game['platforms']);
                        echo !empty($platformNames) ? htmlspecialchars(implode(', ', $platformNames)) : 'N/A';
                        ?>
                    </p>
                    
                    <span class="rating">
                        <?php echo number_format((float)$game['rating_data']['avg'], 1); ?> 
                    </span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../components/footer.php'; ?>