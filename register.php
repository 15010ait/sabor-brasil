<?php
session_start();
require 'config/db.php';

// Show any message stored in the session
$message = $_SESSION['register_message'] ?? '';
unset($_SESSION['register_message']);

// Keep entered values so the form does not reset after a validation error
$enteredUsername = '';
$enteredEmail = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and clean the form values
    $enteredUsername = trim($_POST["username"]);
    $enteredEmail = trim($_POST["email"]);
    $password = $_POST["password"];

    $username = $enteredUsername;
    $email = $enteredEmail;

    // Check that all fields are filled in
    if (empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    }
    // Check that the email is in a valid format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }
    // Check password strength
    elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $message = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a number.";
    } else {
        // Check if the email is already registered
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['register_message'] = 'This email is already registered. <a href="login.php">Click here to log in.</a>';
            header("Location: register.php");
            exit;
        } else {
            // Hash the password before saving it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['register_message'] = "Registration successful! Please log in.";
                header("Location: register.php");
                exit;
            } else {
                $message = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sabor Brasil</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons for the eye button -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom styling -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- Left: photo + brand message -->
        <div class="col-12 col-md-6 d-flex text-white flex-column justify-content-end p-4 p-md-5 register-hero" style="min-height: 200px;">
            <h2 class="fw-bold">Join the Sabor Brasil Community</h2>
            <p>Create your account to start reviewing, rating, and saving your favourite Brazilian dishes.</p>
        </div>

        <!-- Right: register form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
            <div class="register-form-box">

                <h3 class="fw-bold mb-1">Create an Account</h3>
                <p class="text-muted mb-4">Join us and start exploring Brazilian cuisine.</p>

                <?php if ($message): ?>
                    <div class="alert alert-warning">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" autocomplete="off">
                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            placeholder="Enter username"
                            value="<?php echo htmlspecialchars($enteredUsername); ?>"
                            autocomplete="username"
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
                            placeholder="Enter email"
                            value="<?php echo htmlspecialchars($enteredEmail); ?>"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Enter password"
                                autocomplete="new-password"
                                required
                            >
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                onclick="togglePassword('password', this)"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Register</button>

                    <p class="text-center mt-3 mb-0">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </form>

            </div>
        </div>

    </div>
</div>

<!-- Toggle password visibility -->
<script>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>