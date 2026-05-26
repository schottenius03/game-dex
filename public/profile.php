<?php
  // Include global session handling
  require_once __DIR__ . '/../includes/auth.php';
  // Include the UserModel class
  require_once __DIR__ . '/../models/UserModel.php';

  // Security check: Redirect to login page if user is not signed in
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit;
  }

  $userModel = new UserModel();
  $userId = $_SESSION['user_id'];

  // Fetch data to populate the profile page dynamically
  $userData = $userModel->getUserData($userId);
  $allPlatforms = $userModel->getAllPlatforms();
  $allGenres = $userModel->getAllGenres();
  
  $userPlatforms = $userModel->getUserPlatforms($userId);
  $userGenres = $userModel->getUserGenres($userId);

  // Handle Profile Update Request
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
      $currency = $_POST['currency'];
      $email = !empty($_POST['email']) ? trim($_POST['email']) : $userData['email'];
      $selectedPlatforms = isset($_POST['platforms']) ? $_POST['platforms'] : [];
      $selectedGenres = isset($_POST['genres']) ? $_POST['genres'] : [];

      // Update profile including the new email address
      if ($userModel->updateProfile($userId, $currency, $selectedPlatforms, $selectedGenres, $email)) {
          $success = "Profile updated successfully!";

          // Refresh fresh data immediately for the DOM values
          $userData = $userModel->getUserData($userId);
          $userPlatforms = $userModel->getUserPlatforms($userId);
          $userGenres = $userModel->getUserGenres($userId);
      } else {
          $error = "Something went wrong. Could not save preferences.";
      }
  }

  // Handle Delete Account Request
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
      if ($userModel->deleteUser($userId)) {
          // Destroy session and log out completely
          session_destroy();
          header("Location: login.php?account_deleted=true");
          exit;
      } else {
          $error = "Could not delete account. Please try again.";
      }
  }

  // Include header component
  include '../components/header.php'; 
?>

<main>
    <div style="max-width: 700px; margin: 4rem auto; padding: 0 1rem;">
        
        <div class="form-box" style="padding: 3rem 2.5rem;">
            
            <?php if(isset($success)) echo "<p style='color:#2ecc71; margin-bottom: 1.5rem; font-weight: 500; text-align: center;'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p style='color:#e74c3c; margin-bottom: 1.5rem; font-weight: 500; text-align: center;'>$error</p>"; ?>

            <div class="reviews-section" style="margin-bottom: 2.5rem; text-align: center;">
                <h2>G'day, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Welcome to your GameDex profile.</p>
            </div>

            <form id="profileForm" method="POST" style="text-align: left;">
                
                <div style="margin-bottom: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; text-align: center;">
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">
                            Account Status: <span style="color: #2ecc71; font-weight: bold;">Active</span>
                        </p>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="email" style="display:block; margin-bottom:0.5rem; font-weight:bold; font-size: 0.95rem;">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" 
                               style="width:100%; padding:0.8rem; background:#1e1e24; color:#fff; border:1px solid #333; border-radius:4px; transition: border-color 0.2s;"
                               onfocus="this.select(); this.style.borderColor='#e7783c';" onblur="this.style.borderColor='#333'">
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 2.5rem;">
                    <label for="currency" style="display:block; margin-bottom:0.5rem; font-weight:bold; font-size: 0.95rem;">Preferred Currency</label>
                    <select id="currency" name="currency" style="width:100%; padding:0.8rem; background:#1e1e24; color:#fff; border:1px solid #333; border-radius:4px;" required>
                        <option value="EUR" <?php echo ($userData['preferred_currency'] ?? 'EUR') == 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                        <option value="USD" <?php echo ($userData['preferred_currency'] ?? '') == 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                        <option value="SEK" <?php echo ($userData['preferred_currency'] ?? '') == 'SEK' ? 'selected' : ''; ?>>SEK (kr)</option>
                        <option value="AUD" <?php echo ($userData['preferred_currency'] ?? '') == 'AUD' ? 'selected' : ''; ?>>AUD ($)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 2.5rem;">
                    <label style="display:block; margin-bottom:0.8rem; font-weight:bold; font-size: 0.95rem;">Your Platforms</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem;">
                        <?php foreach ($allPlatforms as $platform): ?>
                            <?php $isPlatformChecked = in_array($platform['id'], $userPlatforms); ?>
                            <label style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.95rem; user-select: none; color: #fff;">
                                <input type="checkbox" name="platforms[]" value="<?php echo $platform['id']; ?>"
                                    <?php echo $isPlatformChecked ? 'checked' : ''; ?>
                                    style="accent-color: #e7783c; margin: 0; width: 16px; height: 16px;">
                                <?php echo htmlspecialchars($platform['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 3rem;">
                    <label style="display:block; margin-bottom:0.8rem; font-weight:bold; font-size: 0.95rem;">Favorite Genres</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem;">
                        <?php foreach ($allGenres as $genre): ?>
                            <?php $isGenreChecked = in_array($genre['id'], $userGenres); ?>
                            <label style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.95rem; user-select: none; color: #fff;">
                                <input type="checkbox" name="genres[]" value="<?php echo $genre['id']; ?>"
                                    <?php echo $isGenreChecked ? 'checked' : ''; ?>
                                    style="accent-color: #e7783c; margin: 0; width: 16px; height: 16px;">
                                <?php echo htmlspecialchars($genre['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="submit" id="saveBtn" name="update_profile" class="btn-submit" disabled 
                        style="width: auto; min-width: 200px; background-color: #2c2c35; color: rgba(255,255,255,0.4); font-weight: bold; padding: 0.8rem 2.5rem; display: inline-block; border: 1px solid #444; cursor: not-allowed; transition: all 0.2s ease;">
                        Save Settings
                    </button>
                </div>
            </form>

            <div style="margin-top: 2.5rem; border-top: 1px solid #333; padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <a href="logout.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">Log out</a>
                
                <button type="button" id="deleteTriggerBtn" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 0.95rem; font-weight: 500; padding: 0; transition: color 0.2s;" onmouseover="this.style.color='#c0392b'" onmouseout="this.style.color='#e74c3c'">Delete Account</button>
            </div>

        </div>
    </div>
</main>

<div id="deleteModalOverlay" class="custom-modal-overlay">
    <div class="custom-modal">
        <h3 id="modalTitle">Are you sure you want to delete your account?</h3>
        <div id="modalBtnContainer" class="modal-btn-container">
        </div>
    </div>
</div>

<form id="hiddenDeleteForm" method="POST" style="display:none;">
    <input type="hidden" name="delete_account" value="1">
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Detect changes in main form
    const form = document.getElementById('profileForm');
    const saveBtn = document.getElementById('saveBtn');

    // Helper function to capture current state of the form fields
    function getFormState() {
        const state = {};
        const inputs = form.querySelectorAll('input[type="checkbox"], select, input[type="email"]');
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                state[input.name + '_' + input.value] = input.checked;
            } else {
                state[input.name] = input.value;
            }
        });
        return JSON.stringify(state);
    }

    // Save the snapshot of the database values on page load
    const initialState = getFormState();

    // Enable or disable save button based on changes made to the preferences form
    form.addEventListener('input', function() {
        if (getFormState() !== initialState) {
            saveBtn.disabled = false;
            saveBtn.style.backgroundColor = '#e7783c';
            saveBtn.style.color = '#ffffff';
            saveBtn.style.border = '1px solid #e7783c';
            saveBtn.style.cursor = 'pointer';
        } else {
            saveBtn.disabled = true;
            saveBtn.style.backgroundColor = '#2c2c35';
            saveBtn.style.color = 'rgba(255,255,255,0.4)';
            saveBtn.style.border = '1px solid #444';
            saveBtn.style.cursor = 'not-allowed';
        }
    });

    // Two-step account deletion modal logic
    const deleteTriggerBtn = document.getElementById('deleteTriggerBtn');
    const modalOverlay = document.getElementById('deleteModalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const btnContainer = document.getElementById('modalBtnContainer');
    const hiddenDeleteForm = document.getElementById('hiddenDeleteForm');

    // Open the confirmation modal on initial deletion trigger click
    deleteTriggerBtn.addEventListener('click', function() {
        showStepOne();
        modalOverlay.style.display = 'flex';
    });

    // Render the first confirmation prompt with standard button layout
    function showStepOne() {
        modalTitle.innerText = "Are you sure you want to delete your account?";
        
        // Setup initial alignment: Cancel on the left, Delete on the right
        btnContainer.innerHTML = `
            <button type="button" class="modal-btn btn-cancel-action" id="modalCancelBtn">Cancel</button>
            <button type="button" class="modal-btn btn-danger-action" id="modalDeleteBtn">Delete</button>
        `;

        document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
        document.getElementById('modalDeleteBtn').addEventListener('click', showStepTwo);
    }

    // Render the final confirmation prompt and switch button alignment to prevent muscle-memory clicks
    function showStepTwo() {
        modalTitle.innerText = "Are you REALLY sure?";

        // Switch alignment: Delete on the left, Cancel on the right
        btnContainer.innerHTML = `
            <button type="button" class="modal-btn btn-danger-action" id="modalDeleteBtn">Delete</button>
            <button type="button" class="modal-btn btn-cancel-action" id="modalCancelBtn">Cancel</button>
        `;

        document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
        document.getElementById('modalDeleteBtn').addEventListener('click', function() {
            hiddenDeleteForm.submit(); // Submit hidden form to trigger backend deletion
        });
    }

    // Close the custom modal and reset its visibility status
    function closeModal() {
        modalOverlay.style.display = 'none';
    }

    // Close modal if user clicks anywhere on the dark overlay background
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) closeModal();
    });
});
</script>

<?php 
  // Include footer component
  include '../components/footer.php'; 
?>