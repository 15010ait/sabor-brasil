<?php
session_start();
require 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- Left: photo + brand message (hidden on small screens) -->
        <div class="col-md-6 d-none d-md-flex text-white flex-column justify-content-end p-5"
             style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('assets/images/login-image.png') center/cover no-repeat;">
            <h2 class="fw-bold">Discover the Soul of Brazilian Food</h2>
            <p>Explore authentic recipes, discover hidden gems, and connect with a community that celebrates Brazil's rich culinary heritage.</p>
        </div>

        <!-- Right: login form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
            <div style="max-width: 380px; width: 100%;">

                <h3 class="fw-bold mb-1">Welcome Back</h3>
                <p class="text-muted mb-4">Log in to continue exploring the best of Brazilian cuisine.</p>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>