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

      // Attempt to log in using the model's method
      if ($userModel->loginUser($username, $password)) {
          header("Location: index.php");
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
            
            <?php if(isset($_GET['registered']) && $_GET['registered'] === 'true'): ?>
                <p style="color: #2ecc71; font-weight: bold; margin-bottom: 1rem;">Account created successfully!</p>
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