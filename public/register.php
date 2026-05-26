<?php
  // Include authentication logic
  require_once __DIR__ . '/../includes/auth.php';

  // Process login form
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                    <h2>Sign up</h2>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">&#128065;</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="repeat_password">Repeat password</label>
                    <div class="password-wrapper">
                        <input type="password" id="repeat_password" name="repeat_password" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('repeat_password', this)">&#128065;</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign up</button>
            </form>
        </div>

        <p style="margin-top: 1.5rem; color: var(--text-muted); text-align: center;">
            Already have an account? <a href="login.php" style="color: var(--accent-orange);">Login here</a>
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