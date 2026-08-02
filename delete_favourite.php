<?php
session_start();
require 'config/db.php';

// Only logged-in users can remove favourites
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: profile.php");
    exit;
}

// Get the favourite id from the form
$favouriteId = isset($_POST["favourite_id"]) ? intval($_POST["favourite_id"]) : 0;

// Make sure the favourite belongs to the logged-in user
$check = $conn->prepare("
    SELECT id
    FROM favourites
    WHERE id = ? AND user_id = ?
");
$check->bind_param("ii", $favouriteId, $_SESSION["user_id"]);
$check->execute();
$result = $check->get_result();
$favourite = $result->fetch_assoc();
$check->close();

// Stop if the favourite was not found
if (!$favourite) {
    $_SESSION["favourite_message"] = "Favourite not found or access denied.";
    header("Location: profile.php#my-favourites");
    exit;
}

// Delete the favourite
$delete = $conn->prepare("DELETE FROM favourites WHERE id = ? AND user_id = ?");
$delete->bind_param("ii", $favouriteId, $_SESSION["user_id"]);

if ($delete->execute()) {
    $_SESSION["favourite_message"] = "Favourite removed.";
} else {
    $_SESSION["favourite_message"] = "Could not remove favourite.";
}

$delete->close();

// Go back to the favourites section
header("Location: profile.php#my-favourites");
exit;