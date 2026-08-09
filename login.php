<?php
session_start();
require 'config/db.php';

// Message shown when login fails or fields are empty
$message = "";

// Keep the entered email so the user does not need to type it again after an error
$enteredEmail = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and clean the form values
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $enteredEmail = $email;

    // Check that both fields are filled in
    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        // Find the user by email
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // If exactly one user is found, check the password
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the entered password against the stored hash
            if (password_verify($password, $user["password"])) {
                // Store user information in the session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                // Redirect to the homepage after successful login
                header("Location: index.php");
                exit;
            } else {
                $message = "Invalid email or password.";
            }
        } else {
            $message = "Invalid email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sabor Brasil</title>

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

        <!-- Left side: hero banner -->
        <div class="col-12 col-md-6 d-flex text-white flex-column justify-content-end p-4 p-md-5 login-hero" style="min-height: 200px;">
            <h2 class="fw-bold">Discover the Soul of Brazilian Food</h2>
            <p>
                Explore authentic recipes, discover hidden gems, and connect with a community that celebrates Brazil's rich culinary heritage.
            </p>
        </div>

        <!-- Right side: login form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
            <div class="login-form-box">

                <h3 class="fw-bold mb-1">Welcome Back</h3>
                <p class="text-muted mb-4">Log in to continue exploring the best of Brazilian cuisine.</p>

                <?php if ($message): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter email"
                            value="<?php echo htmlspecialchars($enteredEmail); ?>"
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

                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Don't have an account? <a href="register.php">Register</a>
                </p>

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