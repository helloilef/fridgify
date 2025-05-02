<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost:3309;dbname=fridgify", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch selected ingredients
$videos = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_ingredients'])) {
    $selectedIngredients = $_POST['selected_ingredients'];

    // Generate YouTube search links for each ingredient
    foreach ($selectedIngredients as $ingredient) {
        // Fetch the ingredient image from the database
        $stmt = $pdo->prepare("SELECT image FROM ingredients WHERE name = :name");
        $stmt->execute(['name' => $ingredient]);
        $ingredientData = $stmt->fetch(PDO::FETCH_ASSOC);

        $searchQuery = urlencode($ingredient . ' recipe');
        $youtubeUrl = "https://www.youtube.com/results?search_query={$searchQuery}";

        // Add the ingredient image to the video array
        $videos[] = [
            'title' => $ingredient . ' Recipe',
            'url' => $youtubeUrl,
            'ingredient' => $ingredient,
            'image' => $ingredientData['image'] ?? 'assets/default-image.png', // Use default image if none exists
        ];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fridgify - Recipe Videos</title>
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
          <?php if (isset($_SESSION['username'])): ?>
      <li class="profile-icon">
    <div class="profile-dropdown">
        <img src="assets/sushi-12.png" alt="Profile Icon" title="<?= htmlspecialchars($_SESSION['username']) ?>" id="profile-icon">
        <div class="dropdown-menu hidden" id="dropdown-menu">
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>
</li>
<?php else: ?>
        <li><a href="#" id="login">Login</a></li>
    <?php endif; ?>
        </ul>
      </nav>
    </header>

    <!-- Recipe Videos Section -->
    <section class="recipe-results" data-aos="fade-up" data-aos-duration="1000">
  <h2 class="fridge-inventory__title" style="color: black;">Recipe Videos</h2>
  <div class="fridge-inventory__catalogue">
    <?php if (!empty($videos)): ?>
      <?php foreach ($videos as $video): ?>
        <article 
          class="fridge-inventory__card"
          data-aos="zoom-in" 
          data-aos-delay="<?= $index * 100 ?>" 
          data-aos-duration="600"
        >
          <!-- Display Ingredient Image -->
          <img
            class="fridge-inventory__card-image"
            src="<?= htmlspecialchars($video['image']) ?>"
            alt="<?= htmlspecialchars($video['ingredient']) ?>"
          />
          <h4 class="fridge-inventory__card-title" style="color: black;"><?= htmlspecialchars($video['title']) ?></h4>
          <p class="fridge-inventory__card-details">
            Ingredient: <?= htmlspecialchars($video['ingredient']) ?>
          </p>
          <a
            href="<?= htmlspecialchars($video['url']) ?>"
            target="_blank"
            class="fridge-inventory__card-link styled-link"
          >
            🎥 Watch Recipe Video
          </a>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <p data-aos="fade-in" data-aos-duration="600">
        No ingredients selected. Please go back and select ingredients from your fridge.
      </p>
    <?php endif; ?>
  </div>
</section>
<section class="extra-lists" data-aos="fade-up" data-aos-duration="1000">
  <h2>Explore More</h2>

  <!-- Ordered List -->
  <div class="list-container">
    <h3>Top 5 Cooking Tips</h3>
    <ol>
      <li>Always taste as you cook.</li>
      <li>Use fresh ingredients for better flavor.</li>
      <li>Keep your knives sharp for easier prep.</li>
      <li>Don’t overcrowd the pan when cooking.</li>
      <li>Clean as you go to save time.</li>
    </ol>
  </div>

  <!-- Unordered List -->
  <div class="list-container">
    <h3>Essential Kitchen Tools</h3>
    <ul>
      <li>Chef’s knife</li>
      <li>Cutting board</li>
      <li>Measuring cups and spoons</li>
      <li>Mixing bowls</li>
      <li>Non-stick skillet</li>
    </ul>
  </div>

  <!-- Description List -->
  <div class="list-container">
    <h3>Fun Food Facts</h3>
    <dl>
      <dt>Honey</dt>
      <dd>Honey never spoils. Archaeologists have found pots of honey in ancient Egyptian tombs that are over 3,000 years old and still edible.</dd>
      <dt>Carrots</dt>
      <dd>Carrots were originally purple or white. The orange variety was developed in the Netherlands in the 17th century.</dd>
      <dt>Chocolate</dt>
      <dd>Chocolate was once used as currency by the Aztecs.</dd>
    </dl>
  </div>
</section>

    <footer class="footer flex-between">
      <h3 class="footer__logo"><span>Fridg</span>ify</h3>

      <ul class="footer__nav">
        <li>
          <a href="fridge.php">Fridge</a>
        </li>
        <li>
          <a href="recipes.html">Recipes</a>
        </li>
        <li>
          <a href="index.php#about-us">About Us</a>
        </li>
        <li>
          <a href="#">Up</a>
        </li>
      </ul>

      <ul class="footer__social">
        <li class="flex-center">
          <img src="assets/facebook.svg" alt="facebook" />
        </li>
        <li class="flex-center">
          <img src="assets/instagram.svg" alt="instagram" />
        </li>
        <li class="flex-center">
          <img src="assets/twitter.svg" alt="twitter" />
        </li>
      </ul>
    </footer>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
<script src="js/auth.js" type="module"></script>
  </body>
</html>