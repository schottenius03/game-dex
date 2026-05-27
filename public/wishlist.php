<?php

require_once __DIR__ . '/../models/WishlistModel.php';
require_once '../models/GameModel.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$wishlistModel = new WishlistModel();
$favoriteGames = $wishlistModel->getUserWishlist($_SESSION['user_id']);

include '../components/header.php';
?>

<link rel="stylesheet" href="styles/wishlist_styles.css">

<section class="container">
    <h2 class="page-title">My Favorites</h2>

    <?php if (empty($favoriteGames)): ?>
        <p>You haven't added any games to your favorites yet.</p>
    <?php else: ?>
        <div class="game-grid">
            <?php foreach ($favoriteGames as $game): ?>
            <div class="game-card">
                <button class="wishlist-btn active" data-id="<?php echo htmlspecialchars($game['id']); ?>">
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
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include '../components/footer.php'; ?>