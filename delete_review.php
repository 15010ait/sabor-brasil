<?php
session_start();
require 'config/db.php';

// Only logged-in users can delete reviews
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Make sure the request is coming from POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Get the review ID from the form
$reviewId = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;

// Find the review and make sure it belongs to the logged-in user
$reviewStmt = $conn->prepare("
    SELECT id, user_id, meal_id
    FROM reviews
    WHERE id = ? AND user_id = ?
");
$reviewStmt->bind_param("ii", $reviewId, $_SESSION['user_id']);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();
$review = $reviewResult->fetch_assoc();
$reviewStmt->close();

// If the review does not belong to this user, stop
if (!$review) {
    die("Review not found or access denied.");
}

// Delete the review
$deleteStmt = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
$deleteStmt->bind_param("ii", $reviewId, $_SESSION['user_id']);
$deleteStmt->execute();
$deleteStmt->close();

// Go back to the meal page after deleting
header("Location: meal.php?id=" . $review['meal_id']);
exit;