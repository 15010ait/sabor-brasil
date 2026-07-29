<?php
session_start();
require 'config/db.php';

// Get the meal id from the URL (e.g. meal.php?id=3)
$mealId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the meal from the database
$stmt = $conn->prepare("SELECT * FROM meals WHERE id = ?");
$stmt->bind_param("i", $mealId);
$stmt->execute();
$result = $stmt->get_result();
$meal = $result->fetch_assoc();
$stmt->close();

// Fetch this meal's gallery images
$galleryStmt = $conn->prepare("SELECT image FROM meal_images WHERE meal_id = ?");
$galleryStmt->bind_param("i", $mealId);
$galleryStmt->execute();
$galleryResult = $galleryStmt->get_result();
$galleryStmt->close();

// Fetch reviews for this meal
$reviewsStmt = $conn->prepare("
    SELECT reviews.id, reviews.rating, reviews.comment, reviews.created_at, users.username
    FROM reviews
    INNER JOIN users ON reviews.user_id = users.id
    WHERE reviews.meal_id = ?
    ORDER BY reviews.created_at DESC
");
$reviewsStmt->bind_param("i", $mealId);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviewsStmt->close();
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

                <?php if (isset($_SESSION["user_id"])): ?>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <?php if (!$meal): ?>

        <div class="alert alert-warning mt-4">
            Meal not found. <a href="index.php">Back to home</a>
        </div>

    <?php else: ?>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><?php echo htmlspecialchars($meal['category']); ?></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($meal['title']); ?></li>
            </ol>
        </nav>

        <div class="row mt-3">

            <div class="col-md-7 mb-4">
                <img
                    src="assets/images/<?php echo htmlspecialchars($meal['image']); ?>"
                    class="img-fluid rounded shadow-sm"
                    alt="<?php echo htmlspecialchars($meal['title']); ?>"
                >

                <div class="row g-2 mt-2">
                    <?php while ($img = $galleryResult->fetch_assoc()): ?>
                        <div class="col-2">
                            <img src="assets/images/<?php echo htmlspecialchars($img['image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($meal['title']); ?>">
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-md-5">
                <h1 class="fw-bold"><?php echo htmlspecialchars($meal['title']); ?></h1>

                <span class="badge bg-warning text-dark mb-2"><?php echo htmlspecialchars($meal['category']); ?></span>

                <p class="mb-3">⭐⭐⭐⭐⭐ <strong>4.8</strong> (reviews pending)</p>

                <p><?php echo htmlspecialchars($meal['description']); ?></p>

                <button class="btn btn-success w-100 mt-2 mb-4">
                    ♥ Add to Favourites
                </button>

                <hr>

                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <strong>Category</strong><br>
                        <?php echo htmlspecialchars($meal['category']); ?>
                    </div>
                    <div class="col-6 mb-3">
                        <strong>Origin</strong><br>
                        Brazil
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <h3 class="mb-3">Reviews</h3>

        <?php if ($reviewsResult && $reviewsResult->num_rows > 0): ?>
            <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                            <span class="text-warning">
                                <?php echo str_repeat("★", (int)$review['rating']); ?>
                            </span>
                        </div>

                        <p class="mb-0"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">No reviews yet.</p>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>