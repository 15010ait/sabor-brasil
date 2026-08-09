<?php
session_start();
require 'config/db.php';

// Only logged-in users can access this page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Message shown if validation fails or update does not work
$message = "";

// Load the current user's details, including the stored password hash
$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Stop if the user record cannot be found
if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form values
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $currentPassword = trim($_POST["current_password"]);
    $newPassword = trim($_POST["new_password"]);
    $confirmNewPassword = trim($_POST["confirm_new_password"]);

    // Username and email are required
    if (empty($username) || empty($email)) {
        $message = "Username and email are required.";
    }
    // Check that the email is in a valid format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        // Check whether the email is already used by another account
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $_SESSION["user_id"]);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already in use.";
        } else {
            // Check whether the user is trying to change the password
            $passwordChangeRequested = !empty($currentPassword) || !empty($newPassword) || !empty($confirmNewPassword);

            if ($passwordChangeRequested) {
                // If changing password, all password fields must be filled
                if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
                    $message = "Please fill in the current password, new password, and confirm password fields.";
                }
                // Make sure the current password matches the stored one
                elseif (!password_verify($currentPassword, $user['password'])) {
                    $message = "Current password is incorrect.";
                }
                // Make sure the new password and confirm password match
                elseif ($newPassword !== $confirmNewPassword) {
                    $message = "New passwords do not match.";
                }
                // Make sure the new password is strong enough
                elseif (
                    strlen($newPassword) < 8 ||
                    !preg_match('/[A-Z]/', $newPassword) ||
                    !preg_match('/[a-z]/', $newPassword) ||
                    !preg_match('/[0-9]/', $newPassword)
                ) {
                    $message = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a number.";
                } else {
                    // Hash the new password before saving it
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update username, email, and password
                    $update = $conn->prepare("
                        UPDATE users
                        SET username = ?, email = ?, password = ?
                        WHERE id = ?
                    ");
                    $update->bind_param("sssi", $username, $email, $hashedPassword, $_SESSION["user_id"]);

                    if ($update->execute()) {
                        // Update the session username so the navbar shows the new name
                        $_SESSION["username"] = $username;

                        // Store a success message to show on the profile page
                        $_SESSION["profile_message"] = "Your profile has been updated successfully.";

                        // Return to profile page after saving
                        header("Location: profile.php");
                        exit;
                    } else {
                        $message = "Could not update profile.";
                    }

                    $update->close();
                }
            } else {
                // If password fields are empty, update only username and email
                $update = $conn->prepare("
                    UPDATE users
                    SET username = ?, email = ?
                    WHERE id = ?
                ");
                $update->bind_param("ssi", $username, $email, $_SESSION["user_id"]);

                if ($update->execute()) {
                    // Update the session username so the navbar shows the new name
                    $_SESSION["username"] = $username;

                    // Store a success message to show on the profile page
                    $_SESSION["profile_message"] = "Your profile has been updated successfully.";

                    // Return to profile page after saving
                    header("Location: profile.php");
                    exit;
                } else {
                    $message = "Could not update profile.";
                }

                $update->close();
            }
        }

        $check->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
    <h2 class="mb-4">Edit Profile</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-warning">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="">

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? $user['username']); ?>"
                        required
                    >
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email']); ?>"
                        required
                    >
                </div>

                <hr>

                <!-- Current password -->
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            class="form-control"
                            placeholder="Enter current password if changing your password"
                        >
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('current_password', this)"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- New password -->
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            name="new_password"
                            id="new_password"
                            class="form-control"
                            placeholder="Leave blank if you do not want to change it"
                        >
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('new_password', this)"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm new password -->
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            name="confirm_new_password"
                            id="confirm_new_password"
                            class="form-control"
                            placeholder="Repeat new password"
                        >
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('confirm_new_password', this)"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="profile.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility for any password field on the page
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>

<?php include 'includes/footer.php'; ?>