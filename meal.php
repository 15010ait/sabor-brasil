<?php
session_start();
require 'config/db.php';

// Get the meal id from the URL (e.g. meal.php?id=3)
$mealId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle review submission from the form at the bottom of the page
$reviewMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Make sure only logged-in users can submit reviews
    if (!isset($_SESSION['user_id'])) {
        $reviewMessage = "Please log in to write a review.";
    } else {
        // Get the rating and comment from the form
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $comment = trim($_POST['comment']);

        // Validate the rating value
        if ($rating < 1 || $rating > 5) {
            $reviewMessage = "Please choose a rating between 1 and 5.";
        } elseif (empty($comment)) {
            $reviewMessage = "Please write a comment.";
        } else {
            // Insert the new review into the reviews table
            $insertStmt = $conn->prepare("
                INSERT INTO reviews (user_id, meal_id, rating, comment)
                VALUES (?, ?, ?, ?)
            ");
            $insertStmt->bind_param("iiis", $_SESSION['user_id'], $mealId, $rating, $comment);

            // If saved successfully, reload the page so the new review appears
            if ($insertStmt->execute()) {
                header("Location: meal.php?id=" . $mealId);
                exit;
            } else {
                $reviewMessage = "Could not save review.";
            }

            $insertStmt->close();
        }
    }
}

// Fetch the meal details from the meals table
$stmt = $conn->prepare("SELECT * FROM meals WHERE id = ?");
$stmt->bind_param("i", $mealId);
$stmt->execute();
$result = $stmt->get_result();
$meal = $result->fetch_assoc();
$stmt->close();

// Check whether the logged-in user already favourited this meal
$isFavourited = false;
$favouriteId = null;

if (isset($_SESSION["user_id"])) {
    $favCheckStmt = $conn->prepare("
        SELECT id
        FROM favourites
        WHERE user_id = ? AND meal_id = ?
    ");
    $favCheckStmt->bind_param("ii", $_SESSION["user_id"], $mealId);
    $favCheckStmt->execute();
    $favCheckResult = $favCheckStmt->get_result();

    if ($favCheckResult->num_rows > 0) {
        $isFavourited = true;
        $favouriteId = $favCheckResult->fetch_assoc()['id'];
    }

    $favCheckStmt->close();
}

// Fetch gallery images for this meal from meal_images
$galleryStmt = $conn->prepare("SELECT image FROM meal_images WHERE meal_id = ?");
$galleryStmt->bind_param("i", $mealId);
$galleryStmt->execute();
$galleryResult = $galleryStmt->get_result();
$galleryStmt->close();

// Fetch reviews for this meal, joined with users so we can show usernames
$reviewsStmt = $conn->prepare("
    SELECT reviews.id, reviews.user_id, reviews.rating, reviews.comment, reviews.created_at, users.username
    FROM reviews
    INNER JOIN users ON reviews.user_id = users.id
    WHERE reviews.meal_id = ?
    ORDER BY reviews.created_at DESC
");
$reviewsStmt->bind_param("i", $mealId);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviewsStmt->close();


// Fetch the average rating and total number of reviews for the current meal
$ratingStmt = $conn->prepare("
    SELECT
        AVG(rating) AS average_rating,
        COUNT(id) AS total_reviews
    FROM reviews
    WHERE meal_id = ?
");
$ratingStmt->bind_param("i", $mealId);
$ratingStmt->execute();
$ratingResult = $ratingStmt->get_result();
$ratingData = $ratingResult->fetch_assoc();
$ratingStmt->close();

// Prepare the average rating and total review count for display
$averageRating = isset($ratingData['average_rating']) ? round((float)$ratingData['average_rating'], 1) : 0;
$totalReviews = isset($ratingData['total_reviews']) ? (int)$ratingData['total_reviews'] : 0;
?>

<?php include 'includes/header.php'; ?>

<?php
// Show favourite status message if one was set
$favouriteMessage = $_SESSION["favourite_message"] ?? "";
unset($_SESSION["favourite_message"]);
?>

<div class="container mt-4">

<?php if (!empty($favouriteMessage)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($favouriteMessage); ?>
    </div>
<?php endif; ?>

    <?php if (!$meal): ?>

        <!-- Show this if the meal ID does not exist in the database -->
        <div class="alert alert-warning mt-4">
            Meal not found. <a href="index.php">Back to home</a>
        </div>

    <?php else: ?>

        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><?php echo htmlspecialchars($meal['category']); ?></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($meal['title']); ?></li>
            </ol>
        </nav>

        <div class="row mt-3">

            <!-- Left side: main image and gallery thumbnails -->
            <div class="col-md-7 mb-4">
                <!-- Main meal image -->
                <img
                    src="assets/images/<?php echo htmlspecialchars($meal['image']); ?>"
                    class="img-fluid rounded shadow-sm meal-main-image"
                    alt="<?php echo htmlspecialchars($meal['title']); ?>"
                >

                <!-- Gallery images for this meal -->
                <div class="row g-2 mt-2">
                    <?php while ($img = $galleryResult->fetch_assoc()): ?>
                        <div class="col-2">
                            <img
                                src="assets/images/<?php echo htmlspecialchars($img['image']); ?>"
                                class="img-fluid rounded meal-thumbnail"
                                alt="<?php echo htmlspecialchars($meal['title']); ?>"
                            >
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Right side: meal information -->
            <div class="col-md-5">
                <h1 class="fw-bold"><?php echo htmlspecialchars($meal['title']); ?></h1>

                <!-- Category label -->
                <span class="badge bg-warning text-dark mb-2">
                    <?php echo htmlspecialchars($meal['category']); ?>
                </span>

                <?php if ($totalReviews > 0): ?>
                   <p class="mb-3">
                      <?php echo str_repeat("⭐", (int)round($averageRating)); ?>
                      <strong><?php echo number_format($averageRating, 1); ?></strong>
                      (<?php echo $totalReviews; ?> review<?php echo $totalReviews === 1 ? '' : 's'; ?>)
                  </p>
                <?php else: ?>
                  <p class="mb-3 text-muted">No ratings yet</p>
                <?php endif; ?>

                <!-- Meal description -->
                <p><?php echo htmlspecialchars($meal['description']); ?></p>

                <!-- Favourites button -->
                <?php if (isset($_SESSION["user_id"])): ?>
                    <?php if ($isFavourited): ?>
                        <form action="delete_favourite.php" method="POST" class="mb-4">
                            <input type="hidden" name="favourite_id" value="<?php echo $favouriteId; ?>">
                            <button type="submit" class="btn btn-outline-danger w-100 mt-2">
                                ♥ Remove from Favourites
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="add_favourite.php" method="POST" class="mb-4">
                            <input type="hidden" name="meal_id" value="<?php echo $mealId; ?>">
                            <button type="submit" class="btn btn-success w-100 mt-2">
                                ♥ Add to Favourites
                            </button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-success w-100 mt-2 mb-4">
                        ♥ Log in to add favourites
                    </a>
                <?php endif; ?>

                <hr>

                <!-- Extra metadata section -->
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

        <!-- Reviews section -->
        <h3 class="mb-3">Reviews</h3>

        <?php if ($reviewsResult && $reviewsResult->num_rows > 0): ?>
            <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <!-- Reviewer username -->
                            <strong><?php echo htmlspecialchars($review['username']); ?></strong>

                            <!-- Star rating display -->
                            <span class="text-warning">
                                <?php echo str_repeat("★", (int)$review['rating']); ?>
                            </span>
                        </div>

                        <!-- Review comment -->
                        <p class="mb-0"><?php echo htmlspecialchars($review['comment']); ?></p>

                        <!-- Only show edit/delete for the logged-in user's own review -->
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <div class="mt-3">
                                <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                <form action="delete_review.php" method="POST" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?');">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Message shown when there are no reviews yet -->
            <p class="text-muted">No reviews yet.</p>
        <?php endif; ?>

        <!-- Review form for logged-in users -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-3">Write a Review</h4>

                    <!-- Validation / status message -->
                    <?php if (!empty($reviewMessage)): ?>
                        <div class="alert alert-warning">
                            <?php echo htmlspecialchars($reviewMessage); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Review submission form -->
                    <form method="POST" action="">
                        <input type="hidden" name="submit_review" value="1">

                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Choose a rating</option>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Good</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" rows="4" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Submit Review</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Prompt shown to users who are not logged in -->
            <p class="text-muted mt-4">
                Please <a href="login.php">log in</a> to write a review.
            </p>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>