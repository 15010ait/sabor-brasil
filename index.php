<?php
// Start the session so we can show different links for logged-in users
session_start();

// Connect to the database
require 'config/db.php';

// Get all meals in a random order for the homepage carousel
$featuredMealsResult = $conn->query("SELECT * FROM meals ORDER BY RAND()");
$allFeaturedMeals = $featuredMealsResult->fetch_all(MYSQLI_ASSOC);

// Split the meals into groups of 3 so each carousel slide shows 3 meals
$mealSlides = array_chunk($allFeaturedMeals, 3);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">

    <?php if (isset($_SESSION["username"])): ?>
        <!-- Show a welcome message if the user is logged in -->
        <p class="text-end">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!
        </p>
    <?php else: ?>
        <!-- Show login/register links if the user is not logged in -->
        <p class="text-end">
            <a href="login.php">Login</a> or <a href="register.php">Register</a>
        </p>
    <?php endif; ?>

    <!-- Homepage hero banner -->
    <div class="text-center py-5 rounded mb-4 text-white home-hero">
        <h1 class="display-5 fw-bold">Welcome to Sabor Brasil</h1>
        <p class="lead mb-0">
            Discover, review, and save your favourite Brazilian meals.
        </p>
    </div>

    <!-- Search form allowing users to search meals by keyword -->
    <form method="GET" action="search.php" class="row g-2 justify-content-center mb-4">
        <div class="col-md-8 col-lg-6">
            <input
                type="text"
                name="q"
                class="form-control form-control-lg"
                placeholder="Search Brazilian meals..."
            >
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-success btn-lg">Search</button>
        </div>
    </form>

    <h2 class="mb-4">Featured Brazilian Meals</h2>

    <!-- Bootstrap carousel for the featured meals -->
    <?php if (count($mealSlides) > 0): ?>
        <div id="mealsCarousel" class="carousel slide mb-4" data-bs-ride="carousel">

            <div class="carousel-inner">
                <?php foreach ($mealSlides as $index => $slide): ?>
                    <!-- Each slide contains up to 3 meals -->
                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                        <div class="row">
                            <?php foreach ($slide as $meal): ?>
                                <!-- Individual featured meal card -->
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
                                                <strong>Category:</strong>
                                                <?php echo htmlspecialchars($meal['category']); ?>
                                            </p>
                                            <a href="meal.php?id=<?php echo $meal['id']; ?>" class="btn btn-success mt-auto">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Previous slide button -->
            <button class="carousel-control-prev" type="button" data-bs-target="#mealsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1) grayscale(100);"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <!-- Next slide button -->
            <button class="carousel-control-next" type="button" data-bs-target="#mealsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1) grayscale(100);"></span>
                <span class="visually-hidden">Next</span>
            </button>

        </div>
    <?php else: ?>
        <p>No meals found in the database.</p>
    <?php endif; ?>

    <!-- Category shortcuts -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <h2 class="mb-0">Categories</h2>
        <a href="meals.php" class="text-success fw-bold text-decoration-none">
            View all &raquo;
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 text-center g-3 mb-4">

        <!-- Main Courses -->
        <div class="col">
            <a href="meals.php?category=Main%20Course" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-egg-fried"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Main Courses</div>
                </div>
            </a>
        </div>

        <!-- Desserts -->
        <div class="col">
            <a href="meals.php?category=Dessert" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-cake2"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Desserts</div>
                </div>
            </a>
        </div>

        <!-- Snacks -->
        <div class="col">
            <a href="meals.php?category=Snack" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-basket2"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Snacks</div>
                </div>
            </a>
        </div>

        <!-- Street Food -->
        <div class="col">
            <a href="meals.php?category=Street%20Food" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-cart3"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Street Food</div>
                </div>
            </a>
        </div>

        <!-- Drinks -->
        <div class="col">
            <a href="meals.php?category=Drink" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-cup-straw"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Drinks</div>
                </div>
            </a>
        </div>

        <!-- BBQ -->
        <div class="col">
            <a href="meals.php?category=BBQ" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">BBQ</div>
                </div>
            </a>
        </div>

        <!-- Vegetarian -->
        <div class="col">
            <a href="meals.php?category=Vegetarian" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-flower1"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Vegetarian</div>
                </div>
            </a>
        </div>

        <!-- Seafood -->
        <div class="col">
            <a href="meals.php?category=Seafood" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem; color: var(--sb-green);">
                        <i class="bi bi-water"></i>
                    </div>
                    <div class="mt-2 fw-semibold small">Seafood</div>
                </div>
            </a>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>