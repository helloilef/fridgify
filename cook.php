<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost:3309;dbname=fridgify", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if the logged-in user is an admin
$isAdmin = false;
if (isset($_SESSION['username'])) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = $user && $user['role'] === 'admin';
}

// Fetch ingredients based on role
$ingredients = [];
if ($isAdmin) {
    // Admin: Fetch all ingredients
    $stmt = $pdo->prepare("
        SELECT * FROM ingredients
    ");
    $stmt->execute();
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else if (isset($_SESSION['username'])) {
    // Normal user: Fetch only their own ingredients
    $stmt = $pdo->prepare("
        SELECT * 
        FROM ingredients 
        WHERE user_id = (SELECT id FROM users WHERE username = :username)
    ");
    $stmt->execute(['username' => $_SESSION['username']]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fridgify - Find Recipes</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="icon" type="image/png" href="assets/bibimbap.png" />
  </head>
  <body>
    <header>
      <nav class="header__nav">
        <div class="header__logo">
          <h4>Fridgify</h4>
          <div class="header__logo-overlay"></div>
        </div>
        <ul class="header__menu">
          <li><a href="index.php">Homepage</a></li>
          <li><a href="fridge.php">Fridge</a></li>
        </ul>
      </nav>
    </header>

    <!-- Recipe Search Section -->
    <section class="recipe-search">
      <h2>Find Recipes Based on Your Ingredients</h2>
      <form action="cook.php" method="POST" class="recipe-search-form">
        <h3>Select Ingredients:</h3>
        <div class="ingredient-list">
          <?php foreach ($ingredients as $ingredient): ?>
            <label>
              <input
                type="checkbox"
                name="selected_ingredients[]"
                value="<?= $ingredient['id'] ?>"
              />
              <?= htmlspecialchars($ingredient['name']) ?>
            </label><br />
          <?php endforeach; ?>
        </div>
        <button type="submit">Find Recipes</button>
      </form>
    </section>

    <!-- Recipe Results Section -->
    <section class="recipe-results">
      <h2>Recipes</h2>
      <div class="recipe-list">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_ingredients'])) {
            $selectedIngredients = $_POST['selected_ingredients'];

            // Fetch recipes that use the selected ingredients
            $placeholders = implode(',', array_fill(0, count($selectedIngredients), '?'));
            $stmt = $pdo->prepare("
                SELECT DISTINCT r.*
                FROM recipes r
                JOIN recipe_ingredients ri ON r.id = ri.recipe_id
                WHERE ri.ingredient_id IN ($placeholders)
            ");
            $stmt->execute($selectedIngredients);
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Display recipes
            if (!empty($recipes)) {
                foreach ($recipes as $recipe) {
                    echo "<div class='recipe-card'>";
                    echo "<h4>" . htmlspecialchars($recipe['title']) . "</h4>";
                    echo "<p>" . htmlspecialchars($recipe['description']) . "</p>";
                    if (!empty($recipe['image'])) {
                        echo "<img src='" . htmlspecialchars($recipe['image']) . "' alt='" . htmlspecialchars($recipe['title']) . "' />";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>No recipes found for the selected ingredients.</p>";
            }
        }
        ?>
      </div>
    </section>

    <footer class="footer flex-between">
      <h3 class="footer__logo"><span>Fridg</span>ify</h3>
      <ul class="footer__nav">
        <li><a href="index.php">Homepage</a></li>
        <li><a href="#">Up</a></li>
      </ul>
    </footer>
    <script src="js/script.js" type="module"></script>
  </body>
</html>