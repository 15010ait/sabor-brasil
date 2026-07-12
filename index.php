<?php
session_start();
require 'config/db.php';

$featuredMeals = $conn->query("SELECT * FROM meals ORDER BY RAND() LIMIT 3");
?>

<?php include 'includes/header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/images/logo-saborbrasil.png" alt="Sabor Brasil" height="32" class="me-2">
            Sabor Brasil
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Meals</a>
                </li>

                <?php if (isset($_SESSION["user_id"])): ?>
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

<div class="container mt-4">
    <?php if (isset($_SESSION["username"])): ?>
        <p class="text-end">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!
        </p>
    <?php else: ?>
        <p class="text-end">
            <a href="login.php">Login</a> or <a href="register.php">Register</a>
        </p>
    <?php endif; ?>

    <div class="text-center py-5 rounded mb-4 text-white home-hero">
        <h1 class="display-5 fw-bold">Welcome to Sabor Brasil</h1>
        <p class="lead mb-0">
            Discover, review, and save your favourite Brazilian meals.
        </p>
    </div>

    <h2 class="mb-4">Featured Brazilian Meals</h2>

    <div class="row">
        <?php if ($featuredMeals && $featuredMeals->num_rows > 0): ?>
            <?php while ($meal = $featuredMeals->fetch_assoc()): ?>
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
                            <p class="mb-3"><strong>Category:</strong> <?php echo htmlspecialchars($meal['category']); ?></p>
                            <a href="meal.php?id=<?php echo $meal['id']; ?>" class="btn btn-success mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No meals found in the database.</p>
        <?php endif; ?>
    </div>

    <!-- Categories -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <h2 class="mb-0">Categories</h2>
        <a href="#" class="text-success fw-bold text-decoration-none">View all &raquo;</a>
    </div>

    <div class="row text-center g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🍽️</div>
                    <div class="mt-2 fw-semibold">Main Courses</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🍰</div>
                    <div class="mt-2 fw-semibold">Desserts</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🥟</div>
                    <div class="mt-2 fw-semibold">Snacks</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🥤</div>
                    <div class="mt-2 fw-semibold">Drinks</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🥗</div>
                    <div class="mt-2 fw-semibold">Vegetarian</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none text-dark">
                <div class="border rounded p-3 h-100">
                    <div style="font-size: 2rem;">🦐</div>
                    <div class="mt-2 fw-semibold">Seafood</div>
                </div>
            </a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>