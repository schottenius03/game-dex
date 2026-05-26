<?php
  // Include global session handling
  require_once __DIR__ . '/../includes/auth.php';
  // Include the new UserModel class
  require_once __DIR__ . '/../models/UserModel.php';

  // Process login form
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = trim($_POST['username']);
      $password = $_POST['password'];
      $remember = isset($_POST['remember']); 
      
      // Instantiate the UserModel
      $userModel = new UserModel();

      // Verify credentials and start session
      if ($userModel->loginUser($username, $password)) {
          
          // Handle persistent login if remember me is checked
          if ($remember) {
              $rawToken = $userModel->createRememberToken($_SESSION['user_id']);
              
              // Set secure cookie for 30 days
              setcookie('remember_me', $rawToken, [
                  'expires' => time() + (86400 * 30),
                  'path' => '/',
                  'secure' => true,
                  'httponly' => true,
                  'samesite' => 'Strict'
              ]);
          }

          header("Location: profile.php");
          exit;
      } else {
          $error = "Invalid username or password.";
      }
  }

  // Include header component
  include '../components/header.php'; 
?>

<main>
    <div style="max-width: 400px; margin: 3rem auto;">
        
        <div class="form-box">
            <?php if(isset($error)) echo "<p style='color:red; margin-bottom: 1rem;'>$error</p>"; ?>
            
            <?php if(isset($_GET['registered']) && $_GET['registered'] === 'true' && !isset($error)): ?>
                <p style="color: #2ecc71; font-weight: bold; margin-bottom: 1rem;">Account created successfully!</p>
            <?php endif; ?>

            <?php if(isset($_GET['account_deleted']) && $_GET['account_deleted'] === 'true' && !isset($error)): ?>
                <p style="color: #2ecc71; font-weight: bold; margin-bottom: 1rem;">Account has been successfully deleted.</p>
            <?php endif; ?>
            
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
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">&#128065;</button>
                    </div>
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

<script>
// Toggle visibility of password input
function togglePasswordVisibility(fieldId, button) {
    const passwordField = document.getElementById(fieldId);
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        button.classList.add('visible');
    } else {
        passwordField.type = 'password';
        button.classList.remove('visible');
    }
}
</script>

<?php 
  // Include footer component
  include '../components/footer.php'; 
?>