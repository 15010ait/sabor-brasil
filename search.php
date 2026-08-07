<?php
session_start();
require 'config/db.php';

// Get the search term from the URL, if the user typed something
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Hold search terms and results
$searchTerms = [];
$allResults = [];
$searchCount = 0;

// Build a list of search terms
if ($query !== '') {
    $searchTerms[] = $query;

    // If the search term ends with "s", also try the singular version
    // Example: "drinks" -> "drink"
    if (strlen($query) > 1 && strtolower(substr($query, -1)) === 's') {
        $singularTerm = substr($query, 0, -1);

        if ($singularTerm !== '') {
            $searchTerms[] = $singularTerm;
        }
    }

    // Search each term and remove duplicate results
    foreach ($searchTerms as $term) {
        $like = '%' . $term . '%';

        // Search meals by title, category, or description
        $searchStmt = $conn->prepare("
            SELECT id, title, category, description, image
            FROM meals
            WHERE title LIKE ? OR category LIKE ? OR description LIKE ?
            ORDER BY title ASC
        ");
        $searchStmt->bind_param("sss", $like, $like, $like);
        $searchStmt->execute();
        $result = $searchStmt->get_result();

        while ($meal = $result->fetch_assoc()) {
            // Use meal ID as the key so duplicates are overwritten
            $allResults[$meal['id']] = $meal;
        }

        $searchStmt->close();
    }

    // Sort results alphabetically by title after removing duplicates
    uasort($allResults, function ($a, $b) {
        return strcasecmp($a['title'], $b['title']);
    });

    $searchCount = count($allResults);
}
?>

<?php include 'includes/header.php'; ?>

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
                <?php foreach ($allResults as $meal): ?>
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
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No meals found for that search.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>