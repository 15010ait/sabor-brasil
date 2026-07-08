<?php
require 'config/db.php';

$sql = "SELECT * FROM meals";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Test</title>
</head>
<body>

<h1>Meals in Database</h1>

<?php
while ($meal = $result->fetch_assoc()) {
    echo "<h3>" . $meal['title'] . "</h3>";
    echo "<p>Category: " . $meal['category'] . "</p>";
    echo "<p>Description: " . $meal['description'] . "</p>";
    echo "<hr>";
}
?>

</body>
</html>