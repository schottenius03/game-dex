<?php
  // Include authentication logic
  require_once __DIR__ . '/../includes/auth.php';

  // Process login form
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Vi lägger till en kontroll för checkboxen sen när vi bygger logiken imorgon
      $remember = isset($_POST['remember']); 
      
      if (loginUser($_POST['username'], $_POST['password'])) {
          header("Location: index.php");
          exit;
      } else {
          $error = "Invalid username or password.";
      }
  }

  // Include header component (Ensure CSS is linked in header.php)
  include '../components/header.php'; 
?>

<main>
    <div style="max-width: 400px; margin: 3rem auto;">
        
        <div class="form-box">
            <?php if(isset($error)) echo "<p style='color:red; margin-bottom: 1rem;'>$error</p>"; ?>
            
            <form method="POST" class="review-form">
                <div class="reviews-section">
                    <h2>Login</h2>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="remember-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn-submit">Sign in</button>
            </form>
        </div>

        <p style="margin-top: 1.5rem; color: var(--text-muted); text-align: center;">
            Don't have an account? <a href="register.php" style="color: var(--accent-orange);">Register here</a>
        </p>
    </div>
</main>

<?php 
  // Include footer component
  include '../components/footer.php'; 
?>