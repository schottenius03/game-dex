<?php
  // Include global session handling
  require_once __DIR__ . '/../includes/auth.php';
  // Include the new UserModel class
  require_once __DIR__ . '/../models/UserModel.php';

  // Process registration form
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = trim($_POST['username']);
      $email = trim($_POST['email']);
      $password = $_POST['password'];
      $repeat_password = $_POST['repeat_password'];

      // Instantiate the UserModel
      $userModel = new UserModel();

      // Validate email format using industry standard PHP filter
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $error = "Please enter a valid email address (e.g., name@domain.com).";
      }
      
      // Validate password strength (min 8 chars, at least one uppercase letter, at least one number)
      elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
          $error = "Password must be at least 8 characters long, contain at least one uppercase letter and one number.";
      } 
      
      // Validate password match
      elseif ($password !== $repeat_password) {
          $error = "Passwords do not match.";
      } 
      
      // Check if username or email is already taken using the model
      elseif ($userModel->isUserExisting($username, $email)) {
          $error = "Username or Email is already registered.";
      } 
      
      // Register the user if everything is OK using the model
      else {
        if ($userModel->registerUser($username, $email, $password)) {
            // Redirect to login page with a success flag in the URL
            header("Location: login.php?registered=true");
            exit;
        } else {
            $error = "Something went wrong. Please try again.";
        }
      }
  }

  // Include header component
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