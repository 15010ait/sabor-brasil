<?php
session_start();
require 'config/db.php';

// Get the search term from the URL, if the user typed something
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Hold search results
$searchResults = null;
$searchCount = 0;

// Only run the query if the user entered a search term
if ($query !== '') {
    $like = '%' . $query . '%';

    // Search meals by title, category, or description
    $searchStmt = $conn->prepare("
        SELECT id, title, category, description, image
        FROM meals
        WHERE title LIKE ? OR category LIKE ? OR description LIKE ?
        ORDER BY title ASC
    ");
    $searchStmt->bind_param("sss", $like, $like, $like);
    $searchStmt->execute();
    $searchResults = $searchStmt->get_result();
    $searchCount = $searchResults->num_rows;
    $searchStmt->close();
}
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
                <li class="nav-item"><a class="nav-link active" href="search.php">Search</a></li>

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
    <h1 class="mb-4">Search Meals</h1>

    <!-- Search form -->
    <form method="GET" action="search.php" class="row g-2 mb-4">
        <div class="col-md-9">
            <input
                type="text"
                name="q"
                class="form-control form-control-lg"
                placeholder="Search by title, category, or description"
                value="<?php echo htmlspecialchars($query); ?>"
            >
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-success btn-lg">Search</button>
        </div>
    </form>

    <?php if ($query === ''): ?>
        <p class="text-muted">Type a keyword to find meals quickly.</p>
    <?php else: ?>
        <p class="text-muted">
            Showing <?php echo $searchCount; ?> result<?php echo $searchCount === 1 ? '' : 's'; ?>
            for "<?php echo htmlspecialchars($query); ?>"
        </p>

        <div class="row">
            <?php if ($searchCount > 0): ?>
                <?php while ($meal = $searchResults->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img
                                src="assets/images/<?php echo htmlspecialchars($meal['image']); ?>"
                                class="card-img-top"
                                alt="<?php echo htmlspecialchars($meal['title']); ?>"
                            >
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($meal['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($meal['description']); ?></p>
                                <p class="mb-3">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($meal['category']); ?>
                                </p>
                                <a href="meal.php?id=<?php echo $meal['id']; ?>" class="btn btn-success mt-auto">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No meals found for that search.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>