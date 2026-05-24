<?php 
  require_once '../models/GameModel.php';
  
  // Initialize the model class
  $gameModel = new GameModel();
  
  $id = $_GET['id'] ?? null; 
  // Fetch game by ID using the model instance
  $game = $gameModel->getGameById($id);

  // Include header
  include '../components/header.php'; 
?>

<link rel="stylesheet" href="styles/game_styles.css">

<div class="back-nav">
    <a href="index.php" class="back-button">
        <span class="back-arrow">&larr;</span> 
        <span class="back-text">Back to all games</span>
    </a>
</div>

<section class="game-detail-container">
    <?php if ($game): ?>
    
    <div class="game-sidebar">
        <div class="game-cover">
            <img src="<?php echo htmlspecialchars($game['image_url'] ?? 'assets/game-controller.png'); ?>" 
                 alt="<?php echo htmlspecialchars($game['title']); ?>">
        </div>
    </div>

    <!-- Title and rating !-->
    <div class="game-main-info">
        <div class="title-container">
            <h1 class="game-title"><?php echo htmlspecialchars($game['title']); ?></h1>
            <span class="header-rating">
                &#9733; <?php echo number_format($game['rating_data']['avg'], 1); ?> 
                (<?php echo $game['rating_data']['count']; ?>)
            </span>
        </div>
        
        <div class="game-description">
            <h2>About the Game</h2>
            <p>
                <?php echo nl2br(htmlspecialchars($game['synopsis'])); ?>
            </p>
        </div>

        <div class="game-meta-box">
            <div style="grid-column: span 4;">
                <strong>Genre:</strong> <span><?php echo htmlspecialchars(implode(', ', array_map(function($g) { return $g['name']; }, $game['genres']))); ?></span>
            </div>

            <strong>Platform:</strong> <span><?php echo !empty($game['platforms']) ? htmlspecialchars(implode(', ', array_map(function($p) { return $p['name']; }, $game['platforms']))) : 'N/A'; ?></span>
            <strong>Developer:</strong> <span><?php echo htmlspecialchars($game['developer'] ?? 'N/A'); ?></span> 
            <strong>Release date:</strong> <span>
                <?php 
                    if (!empty($game['release_date'])) {
                        $date = new DateTime($game['release_date']);
                        echo $date->format('j M Y'); 
                    } else {
                        echo 'N/A';
                    }
                ?>
            </span>
            <strong>Publisher:</strong> <span><?php echo htmlspecialchars($game['publisher'] ?? 'N/A'); ?></span>
        </div>
    </div>
    
    <?php else: ?>
        <h1 class="game-title">Game not found!</h1>
        <a href="index.php">Return to home</a>
    <?php endif; ?>
</section>

<?php if ($game): ?>
<section class="full-width-reviews">
    <div class="reviews-section">
        <h2>User Reviews</h2>
        
        <?php if (!empty($game['reviews'])): ?>
            <?php foreach ($game['reviews'] as $review): ?>
                <div class="review-card">
                    <p class="review-author">
                        User <span class="review-rating"><?php echo htmlspecialchars($review['rating']); ?></span>
                    </p>
                    <p><?php echo nl2br(htmlspecialchars($review['body'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-reviews-msg">No reviews have been posted for this game yet. Be the first to share your thoughts below!</p>
        <?php endif; ?>

        <div class="add-review-box">
            <form action="#review-left" method="POST" class="review-form">
                <h3>Leave a review</h3>
                
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select id="rating" name="rating" required>
                        <option value="5">&#9733; 5 - Excellent</option>
                        <option value="4">&#9733; 4 - Very Good</option>
                        <option value="3">&#9733; 3 - Average</option>
                        <option value="2">&#9733; 2 - Poor</option>
                        <option value="1">&#9733; 1 - Terrible</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="review_text">Review</label>
                    <textarea id="review_text" name="review_text" rows="4" required placeholder="What did you think of the game?"></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit review</button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>

<?php 
  // Include footer
  include '../components/footer.php'; 
?>