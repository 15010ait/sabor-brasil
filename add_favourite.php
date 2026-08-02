<?php
session_start();
require 'config/db.php';

// Only logged-in users can add favourites
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Make sure the request is coming from POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Get the meal ID from the form
$mealId = isset($_POST["meal_id"]) ? intval($_POST["meal_id"]) : 0;

// If the meal ID is invalid, send the user back
if ($mealId <= 0) {
    $_SESSION["favourite_message"] = "Invalid meal selected.";
    header("Location: index.php");
    exit;
}

// Check whether this meal is already in the user's favourites
$check = $conn->prepare("
    SELECT id
    FROM favourites
    WHERE user_id = ? AND meal_id = ?
");
$check->bind_param("ii", $_SESSION["user_id"], $mealId);
$check->execute();
$result = $check->get_result();

// If it already exists, do not add it again
if ($result->num_rows > 0) {
    $_SESSION["favourite_message"] = "This meal is already in your favourites.";
    $check->close();
    header("Location: meal.php?id=" . $mealId);
    exit;
}

$check->close();

// Insert the favourite into the database
$insert = $conn->prepare("
    INSERT INTO favourites (user_id, meal_id)
    VALUES (?, ?)
");
$insert->bind_param("ii", $_SESSION["user_id"], $mealId);

if ($insert->execute()) {
    $_SESSION["favourite_message"] = "Meal added to your favourites.";
} else {
    $_SESSION["favourite_message"] = "Could not add favourite.";
}

$insert->close();

// Return to the meal page
header("Location: meal.php?id=" . $mealId);
exit;