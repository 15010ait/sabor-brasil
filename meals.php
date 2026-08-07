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

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img
                src="assets/images/logo-saborbrasil.png"
                alt="Sabor Brasil"
                height="50"
            >
            Sabor Brasil
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="meals.php">Meals</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>

        <?php if ($category !== ''): ?>

            <h1 class="fw-bold mb-1">
                <?php echo htmlspecialchars($category); ?> Meals
            </h1>

            <p class="text-muted mb-0">
                Explore Brazilian meals from this category.
            </p>

        <?php else: ?>

            <h1 class="fw-bold mb-1">
                All Brazilian Meals
            </h1>

            <p class="text-muted mb-0">
                Explore all meals available in Sabor Brasil.
            </p>

        <?php endif; ?>

    </div>
</div>

    <?php if ($mealsResult->num_rows > 0): ?>

        <div class="row g-4">

            <?php while ($meal = $mealsResult->fetch_assoc()): ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        <img
                            src="assets/images/<?php echo htmlspecialchars($meal['image']); ?>"
                            class="card-img-top"
                            alt="<?php echo htmlspecialchars($meal['title']); ?>"
                            style="height: 260px; object-fit: cover;"
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

                            <a
                                href="meal.php?id=<?php echo $meal['id']; ?>"
                                class="btn btn-success mt-auto"
                            >
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