<?php 
  require_once '../models/GameModel.php';
  require_once '../models/WishlistModel.php'; 
  
  $gameModel = new GameModel();
  $wishlistModel = new WishlistModel(); 
  
  $games = $gameModel->getAllGames(); 
  $platforms = $gameModel->getAllPlatforms();
  $genres = $gameModel->getAllGenres();
  
  // Get ID for logged in user
  $wishlistIds = [];
  if (isset($_SESSION['user_id'])) {
      $wishlistIds = $wishlistModel->getUserWishlistIds($_SESSION['user_id']);
  }
  
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

            <div class="filter-group">
                <div class="dropdown">
                    <button class="dropbtn" data-default="Platform">Platform <span class="arrow">&#9663;</span></button>
                    <div class="dropdown-content">
                        <a href="#" class="platform-filter" data-id="">All Platforms</a>
                        
                        <?php foreach ($platforms as $platform): ?>
                            <a href="#" class="platform-filter" data-id="<?php echo htmlspecialchars($platform['id']); ?>">
                                <?php echo htmlspecialchars($platform['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn" data-default="Genre">Genre <span class="arrow">&#9663;</span></button>
                <div class="dropdown-content">
                    <a href="#" class="genre-filter" data-id="">All Genres</a>
                    <?php foreach ($genres as $genre): ?>
                        <a href="#" class="genre-filter" data-id="<?php echo htmlspecialchars($genre['id']); ?>">
                            <?php echo htmlspecialchars($genre['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" id="resetFilters" class="dropbtn reset-btn">Reset</button>

        </div>
    </div>

    <div class="game-grid" id="gameGrid">
        <?php foreach ($games as $game): ?>
        <?php 
            // Kontrollera om spelet finns i användarens önskelista
            $isWishlisted = in_array($game['id'], $wishlistIds);
            $activeClass = $isWishlisted ? 'active' : '';
        ?>
        <div class="game-card">
            <button class="wishlist-btn <?php echo $activeClass; ?>" data-id="<?php echo htmlspecialchars($game['id']); ?>">
                <span class="wishlist-icon-outline">♡</span>
                <span class="wishlist-icon-filled">♥</span>
            </button>

            <a href="game.php?id=<?php echo htmlspecialchars($game['id']); ?>" class="card-link">
                <div class="card-image">
                    <img src="<?php echo htmlspecialchars($game['image_url'] ?? 'assets/game-controller.png'); ?>" 
                        alt="<?php echo htmlspecialchars($game['title']); ?>">
                </div>
                <div class="card-content">
                    <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                    
                    <div class="card-meta">
                        <div class="game-card-elements">
                            <?php if (!empty($game['platforms'])): ?>
                                <?php foreach ($game['platforms'] as $platform): ?>
                                    <span class="badge badge-platform"><?php echo htmlspecialchars($platform['name']); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge badge-platform">N/A</span>
                            <?php endif; ?>
                        </div>
                        
                        <span class="rating">
                            <?php echo number_format((float)$game['rating_data']['avg'], 1); ?> 
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

</section>

<?php include '../components/footer.php'; ?>