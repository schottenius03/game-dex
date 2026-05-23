<?php 
  // Include header
  include '../components/header.php'; 
?>

<link rel="stylesheet" href="styles/game_styles.css">

<section class="game-detail-container">
    
    <div class="game-sidebar">
        <div class="game-cover">
            <img src="assets/game-controller.png" alt="Game Cover">
        </div>
        <div class="game-meta">
            <p><strong>Genre: </strong>woho</p>
            <p><strong>Rating: </strong> <span class="rating-value">4.5</span></p>
        </div>
    </div>

    <div class="game-main-info">
        <h1 class="game-title">Title</h1>
        
        <div class="game-description">
            <h2>About the Game</h2>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
                exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
        </div>

        <div class="reviews-section">
            <h2>User Reviews</h2>
            
            <div class="review-card">
                <p class="review-author">User123 <span class="review-rating">5</span></p>
                <p>Leaving a review here!</p>
            </div>

            <div class="add-review-box">
                <h3>Leave a review</h3>
                <form action="#review-left" method="POST" class="review-form">
                    
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
    </div>

</section>

<?php 
  // Include footer
  include '../components/footer.php'; 
?>