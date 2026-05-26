<?php
  // Include global session handling
  require_once __DIR__ . '/../includes/auth.php';
  // Include the user model for database operations
  require_once __DIR__ . '/../models/UserModel.php';

  // Process the registration form submission
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = trim($_POST['username']);
      $email = trim($_POST['email']);
      $password = $_POST['password'];
      $repeat_password = $_POST['repeat_password'];
      $currency = $_POST['currency'];

      $userModel = new UserModel();

      // Validate email format using built-in filter
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $error = "Please enter a valid email address.";
      } 
      // Validate password security requirements
      elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
          $error = "Password must be at least 8 characters long, contain at least one uppercase letter and one number.";
      } 
      // Ensure passwords match
      elseif ($password !== $repeat_password) {
          $error = "Passwords do not match.";
      } 
      // Check if username or email already exists in the database
      elseif ($userModel->isUserExisting($username, $email)) {
          $error = "Username or Email is already registered.";
      } 
      // Proceed with user registration
      else {
          if ($userModel->registerUser($username, $email, $password, $currency)) {
              header("Location: login.php?registered=true");
              exit;
          } else {
              $error = "Something went wrong. Please try again.";
          }
      }
  }

  include '../components/header.php'; 
?>

<main>
    <div style="max-width: 400px; margin: 3rem auto;">
        <div class="form-box">
            <?php if(isset($error)) echo "<p style='color:red; margin-bottom: 1rem;'>$error</p>"; ?>
            
            <form method="POST">
                <div class="reviews-section">
                    <h2>Sign up</h2>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label for="currency">Preferred Currency</label>
                    <select id="currency" name="currency" style="width: 100%; padding: 10px; background: #1e1e24; color: #fff; border: 1px solid #333; border-radius: 4px;" required>
                        <option value="EUR">EUR (€)</option>
                        <option value="USD">USD ($)</option>
                        <option value="SEK">SEK (kr)</option>
                        <option value="AUD">AUD ($)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" autocomplete="new-password" required>
                        <button type="button" class="toggle-password">&#128065;</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="repeat_password">Repeat password</label>
                    <div class="password-wrapper">
                        <input type="password" id="repeat_password" name="repeat_password" autocomplete="new-password" required>
                        <button type="button" class="toggle-password">&#128065;</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign up</button>
            </form>

        <p style="margin-top: 1.5rem; color: var(--text-muted); text-align: center;">
            Already have an account? <a href="login.php" style="color: var(--accent-orange);">Login here</a>
        </p>
    </div>
</main>

<?php include '../components/footer.php'; ?>