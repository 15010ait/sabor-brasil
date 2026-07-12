<?php
session_start();
require 'config/db.php';

// Redirect to login if nobody is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user's info
$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

// Check that the user exists
if (!$user) {
    die("User not found.");
}

$stmt->close();
?>

<?php include 'includes/header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo-saborbrasil.png" alt="Sabor Brasil" height="32">
            Sabor Brasil
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Meals</a></li>
                <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="profile.php" class="list-group-item list-group-item-action active">Profile</a>
                <a href="#my-reviews" class="list-group-item list-group-item-action">My Reviews</a>
                <a href="#my-favourites" class="list-group-item list-group-item-action">My Favourites</a>
                <a href="#" class="list-group-item list-group-item-action">Settings</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">

            <!-- Profile card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <button class="btn btn-outline-success">Edit Profile</button>
                    <!-- TODO (Francine): hook this up to an update-profile form (username/email/password) -->
                </div>
            </div>

            <!-- My Reviews -->
            <h4 id="my-reviews" class="mb-3">My Reviews</h4>
            <!-- TODO (Francine): loop through the reviews table WHERE user_id = $user['id'], with edit/delete buttons per review -->
            <p class="text-muted mb-4">No reviews yet.</p>

            <!-- My Favourites -->
            <h4 id="my-favourites" class="mb-3">My Favourites</h4>
            <div class="row">
                <!-- TODO (Francine): loop through the favourites table WHERE user_id = $user['id'], joined with meals -->
                <p class="text-muted">No favourites yet.</p>

                <!--
                Example of what each favourite card should look like once wired up:

                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/feijoada.jpg" class="card-img-top" alt="Feijoada">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Feijoada</h5>
                            <p class="text-muted">Main Course</p>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="meal.php?id=1" class="btn btn-sm btn-success">View Details</a>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                -->
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>