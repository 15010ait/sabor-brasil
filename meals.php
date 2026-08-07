<?php
session_start();
require 'config/db.php';

// Get category from the URL.
// Example: meals.php?category=Dessert
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// If a category was selected, show only meals from that category.
// Otherwise, show all meals.
if ($category !== '') {
    $stmt = $conn->prepare("
        SELECT id, title, category, description, image
        FROM meals
        WHERE category = ?
        ORDER BY title ASC
    ");

    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare("
        SELECT id, title, category, description, image
        FROM meals
        ORDER BY title ASC
    ");
}

$stmt->execute();
$mealsResult = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container my-5">

      <div class="text-center py-5 rounded mb-4 text-white meals-hero">

        <?php if ($category !== ''): ?>

            <h1 class="fw-bold display-6">
                <?php echo htmlspecialchars($category); ?> Meals
            </h1>

            <p class="lead mb-0">
                Explore Brazilian meals from this category.
            </p>

        <?php else: ?>

            <h1 class="fw-bold display-6">
                All Brazilian Meals
            </h1>

            <p class="lead mb-0">
                Explore all meals available in Sabor Brasil.
            </p>

        <?php endif; ?>

    </div>

    <form method="GET" action="meals.php" class="mb-4" style="max-width: 300px;">
        <label for="categorySelect" class="form-label fw-semibold">Filter by category</label>
        <select
            name="category"
            id="categorySelect"
            class="form-select"
            onchange="this.form.submit()"
        >
            <option value="" <?php echo ($category === '') ? 'selected' : ''; ?>>All Categories</option>
            <option value="Main Course" <?php echo ($category === 'Main Course') ? 'selected' : ''; ?>>Main Course</option>
            <option value="Dessert" <?php echo ($category === 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
            <option value="Snack" <?php echo ($category === 'Snack') ? 'selected' : ''; ?>>Snack</option>
            <option value="Street Food" <?php echo ($category === 'Street Food') ? 'selected' : ''; ?>>Street Food</option>
            <option value="Drink" <?php echo ($category === 'Drink') ? 'selected' : ''; ?>>Drink</option>
            <option value="BBQ" <?php echo ($category === 'BBQ') ? 'selected' : ''; ?>>BBQ</option>
        </select>
    </form>

    <?php if ($mealsResult->num_rows > 0): ?>

        <div class="row g-4">

            <?php while ($meal = $mealsResult->fetch_assoc()): ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        <img
                            src="assets/images/<?php echo htmlspecialchars($meal['image']); ?>"
                            class="card-img-top"
                            alt="<?php echo htmlspecialchars($meal['title']); ?>"
                      >

                        <div class="card-body d-flex flex-column">

                            <h2 class="h5 card-title">
                                <?php echo htmlspecialchars($meal['title']); ?>
                            </h2>

                            <p class="card-text">
                                <?php echo htmlspecialchars($meal['description']); ?>
                            </p>

                            <p class="mb-3">
                                <strong>Category:</strong>
                                <?php echo htmlspecialchars($meal['category']); ?>
                            </p>

                            <a href="meal.php?id=<?php echo $meal['id']; ?>" class="btn btn-success mt-auto">
                                View Details
                            </a>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            No meals were found.
        </div>

    <?php endif; ?>

</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>