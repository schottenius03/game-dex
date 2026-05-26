<?php
  // Include global session handling
  require_once __DIR__ . '/../includes/auth.php';

  // Security check: Redirect to login page if user is not signed in
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit;
  }

  // Include header component
  include '../components/header.php'; 
?>

<main>
    <div style="max-width: 500px; margin: 4rem auto; text-align: center;">
        
        <div class="form-box" style="padding: 3rem 2rem;">
            <div class="reviews-section" style="margin-bottom: 2rem;">
                <h2>G'day, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Welcome to your GameDex profile.</p>
            </div>

            <div style="margin: 2rem 0; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                <p style="font-size: 0.9rem; color: var(--text-muted);">
                    Account Status: <span style="color: #2ecc71; font-weight: bold;">Active</span>
                </p>
            </div>

            <div style="margin-top: 3rem;">
                <a href="logout.php" class="btn-submit" style="display: inline-block; text-decoration: none; background-color: #e7783c; width: auto; padding: 0.8rem 2rem;">
                    Log out
                </a>
            </div>
        </div>

    </div>
</main>

<?php 
  // Include footer component
  include '../components/footer.php'; 
?>