<?php
session_start();
require 'config/db.php';

// Make sure the user is logged in before allowing review editing
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the review ID from the URL
$reviewId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Message shown if something goes wrong
$message = "";

// Fetch the review only if it belongs to the logged-in user
$reviewStmt = $conn->prepare("
    SELECT id, user_id, meal_id, rating, comment
    FROM reviews
    WHERE id = ? AND user_id = ?
");
$reviewStmt->bind_param("ii", $reviewId, $_SESSION['user_id']);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();
$review = $reviewResult->fetch_assoc();
$reviewStmt->close();

// If the review does not exist or does not belong to this user, stop here
if (!$review) {
    die("Review not found or access denied.");
}

// If the form is submitted, update the review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = trim($_POST['comment']);

    // Basic validation
    if ($rating < 1 || $rating > 5) {
        $message = "Please choose a rating between 1 and 5.";
    } elseif (empty($comment)) {
        $message = "Please write a comment.";
    } else {
        // Update the review in the database
        $updateStmt = $conn->prepare("
            UPDATE reviews
            SET rating = ?, comment = ?
            WHERE id = ? AND user_id = ?
        ");
        $updateStmt->bind_param("isii", $rating, $comment, $reviewId, $_SESSION['user_id']);

        if ($updateStmt->execute()) {
            // Go back to the meal page after saving
            header("Location: meal.php?id=" . $review['meal_id']);
            exit;
        } else {
            $message = "Could not update review.";
        }

        $updateStmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
    <h2 class="mb-4">Edit Review</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-warning">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="">
                <!-- Keep the current rating -->
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Choose a rating</option>
                        <option value="5" <?php echo ($review['rating'] == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
                        <option value="4" <?php echo ($review['rating'] == 4) ? 'selected' : ''; ?>>4 - Very Good</option>
                        <option value="3" <?php echo ($review['rating'] == 3) ? 'selected' : ''; ?>>3 - Good</option>
                        <option value="2" <?php echo ($review['rating'] == 2) ? 'selected' : ''; ?>>2 - Fair</option>
                        <option value="1" <?php echo ($review['rating'] == 1) ? 'selected' : ''; ?>>1 - Poor</option>
                    </select>
                </div>

                <!-- Keep the current comment -->
                <div class="mb-3">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="4" required><?php echo htmlspecialchars($review['comment']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="meal.php?id=<?php echo $review['meal_id']; ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>