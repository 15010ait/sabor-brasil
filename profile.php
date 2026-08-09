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
$stmt->close();

// Stop if the user does not exist
if (!$user) {
    die("User not found.");
}

// Fetch the user's reviews, joined with meals so we can show the meal title
$reviewsStmt = $conn->prepare("
    SELECT reviews.id, reviews.meal_id, reviews.rating, reviews.comment, reviews.created_at, meals.title AS meal_title
    FROM reviews
    INNER JOIN meals ON reviews.meal_id = meals.id
    WHERE reviews.user_id = ?
    ORDER BY reviews.created_at DESC
");
$reviewsStmt->bind_param("i", $_SESSION["user_id"]);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviewsStmt->close();

// Fetch the user's favourites, joined with meals so we can show meal details
$favouritesStmt = $conn->prepare("
    SELECT favourites.id AS favourite_id, meals.id AS meal_id, meals.title, meals.category, meals.image
    FROM favourites
    INNER JOIN meals ON favourites.meal_id = meals.id
    WHERE favourites.user_id = ?
    ORDER BY favourites.created_at DESC
");
$favouritesStmt->bind_param("i", $_SESSION["user_id"]);
$favouritesStmt->execute();
$favouritesResult = $favouritesStmt->get_result();
$favouritesStmt->close();
?>

<?php include 'includes/header.php'; ?>

<?php
// Show success message after updating the profile
$profileMessage = $_SESSION["profile_message"] ?? "";
unset($_SESSION["profile_message"]);

// Show favourite status message if one was set
$favouriteMessage = $_SESSION["favourite_message"] ?? "";
unset($_SESSION["favourite_message"]);
?>

<div class="container mt-4">

    <?php if (!empty($profileMessage)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($profileMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($favouriteMessage)): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($favouriteMessage); ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="profile.php" class="list-group-item list-group-item-action active">Profile</a>
                <a href="#my-reviews" class="list-group-item list-group-item-action">My Reviews</a>
                <a href="#my-favourites" class="list-group-item list-group-item-action">My Favourites</a>
                <a href="edit_profile.php" class="list-group-item list-group-item-action">Settings</a>
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
                    <a href="edit_profile.php" class="btn btn-outline-success">Edit Profile</a>
                </div>
            </div>

            <!-- My Reviews -->
            <h4 id="my-reviews" class="mb-3">My Reviews</h4>

            <?php if ($reviewsResult && $reviewsResult->num_rows > 0): ?>
                <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong><?php echo htmlspecialchars($review['meal_title']); ?></strong><br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($review['created_at']); ?>
                                    </small>
                                </div>
                                <span class="text-warning">
                                    <?php echo str_repeat("★", (int)$review['rating']); ?>
                                </span>
                            </div>

                            <p class="mb-0"><?php echo htmlspecialchars($review['comment']); ?></p>

                            <div class="mt-3">
                                <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="delete_review.php" method="POST" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?');">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted mb-4">No reviews yet.</p>
            <?php endif; ?>

            <!-- My Favourites -->
            <h4 id="my-favourites" class="mb-3">My Favourites</h4>

            <div class="row">
                <?php if ($favouritesResult && $favouritesResult->num_rows > 0): ?>
                    <?php while ($fav = $favouritesResult->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img
                                    src="assets/images/<?php echo htmlspecialchars($fav['image']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($fav['title']); ?>"
                                >
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($fav['title']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($fav['category']); ?></p>

                                    <!-- Show the correct favourites action depending on whether the meal is already saved -->
                                    <div class="mt-auto d-flex justify-content-between">
                                        <a href="meal.php?id=<?php echo $fav['meal_id']; ?>" class="btn btn-sm btn-success">View Details</a>

                                        <form action="delete_favourite.php" method="POST" class="d-inline">
                                            <input type="hidden" name="favourite_id" value="<?php echo $fav['favourite_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this favourite?');">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No favourites yet.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>