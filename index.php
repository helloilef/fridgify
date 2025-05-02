<?php
session_start();
$host = 'localhost:3309';
$dbname = 'fridgify';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch ingredients based on role
$ingredients = [];
if (isset($_SESSION['username'])) {
    // Check if the logged-in user is an admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['role'] === 'admin') {
        // Admin: Fetch all ingredients
        $stmt = $pdo->prepare("
            SELECT ingredients.*, users.username 
            FROM ingredients 
            JOIN users ON ingredients.user_id = users.id
        ");
        $stmt->execute();
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Normal user: Fetch only their own ingredients
        $stmt = $pdo->prepare("
            SELECT * 
            FROM ingredients 
            WHERE user_id = (SELECT id FROM users WHERE username = :username)
        ");
        $stmt->execute(['username' => $_SESSION['username']]);
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Sample ingredients for non-logged-in users
$sampleIngredients = [
    [
        'name' => 'Chezu Sushi',
        'image' => 'assets/sushi-12.png',
        'rating' => 4.9,
        'price' => 21.00,
    ],
    [
        'name' => 'Original Sushi',
        'image' => 'assets/sushi-11.png',
        'rating' => 5.0,
        'price' => 19.00,
    ],
    [
        'name' => 'Ramen Legendo',
        'image' => 'assets/sushi-10.png',
        'rating' => 4.7,
        'price' => 13.00,
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fridgify</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="icon" type="image/png" href="assets/bibimbap.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  </head>
  <body>
    <header>
      <nav class="header__nav">
        <div class="header__logo">
          <h4>Fridgify</h4>
          <div class="header__logo-overlay"></div>
        </div>

        <ul class="header__menu">
    <li><a href="fridge.php">Fridge</a></li>
    <?php if (isset($_SESSION['username'])): ?>
    <?php
    // Check if the logged-in user is an admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin'): ?>
        <li><a href="admin_dashboard.php">Users</a></li>
    <?php endif; ?>
<?php endif; ?>
    <li><a href="recipes.php">Recipes</a></li>
    <li><a href="#about-us">About Us</a></li>
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

        <ul class="header__menu-mobile">
          <li><img src="assets/menu.svg" alt="menu" /></li>
        </ul>
      </nav>
    </header>
<header>
    


<!-- User Login -->
<div id="login-form" class="hidden">
  <div class="login-container">
    <span class="close-btn">&times;</span>
    <h2>Login</h2>
    <form id="loginForm" method="POST" action="login.php">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required />
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required />
      <button type="submit" id="login-btn">Login</button>
    </form>
    <p>Don't have an account? <a href="#" id="show-signup">Sign Up</a></p>
  </div>
</div>



<!-- User Sign-Up -->
<div id="signup-form" class="hidden">
  <div class="login-container">
    <span class="close-btn">&times;</span>
    <h2>Sign Up</h2>
    <form id="signupForm" method="POST" action="signup.php">
      <label for="signup-name">Name:</label>
      <input type="text" name="name" id="signup-name" required />
      <label for="signup-age">Age:</label>
      <input type="number" name="age" id="signup-age" required />
      <label for="signup-username">Username:</label>
      <input type="text" name="username" id="signup-username" required />
      <label for="signup-password">Password:</label>
      <input type="password" name="password" id="signup-password" required />
      <label for="signup-role">Role:</label>
      <select name="role" id="signup-role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit" id="signup-btn">Sign Up</button>
    </form>
    <p>Already have an account? <a href="#" id="show-login">Login</a></p>
  </div>
</div>
    <section class="hero">
      <div class="hero-image">
        <img
          src="assets/output-onlinepngtools.png"
          alt="food"
          data-aos="zoom-in"
        />

        <div class="hero-image__overlay"></div>
      </div>
      <div class="hero-content">
        <div class="hero-content-info" data-aos="fade-left">
          <h1>Your own online fridge</h1>
          <p>
            Fridgify is a web application that helps you manage your fridge
            contents and find recipes based on what you have.
          </p>
        </div>
        <div class="hero-content__buttons">
          <a href="cook.php"><button class="hero-content__cook-button">Cook Now</button></a>
        </div>
        <div class="hero-content__testimonial" data-aos="fade-up">
          <div class="hero-content__customer flex-center">
            <h4>24<span>k+</span></h4>
            <p>Happy Customers</p>
          </div>

          <div class="hero-content__review">
            <img src="assets/user.png" alt="user" />
            <p>
              "My meals are very diverse now! I can cook anything I want with
              the ingredients I have in my fridge. I love Fridgify!"
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="about-us" id="about-us">
      <div class="about-us__image">
        <div class="about-us__image-3">
          <img src="assets/sushi-3.png" alt="sushi" data-aos="fade-right" />
        </div>

        <button class="about-us__button">
          Learn More

          <img src="assets/arrow-up-right.svg" alt="learn more" />
        </button>

        <div class="about-us__image-sushi2">
          <img src="assets/sushi-2.png" alt="sushi" data-aos="fade-right" />
        </div>
      </div>

      <div class="about-us__content" data-aos="fade-left">
        <p class="food__subtitle">About Us</p>
        <h3 class="food__title">
          Our mission is to help you make the most of your fridge and reduce
          food waste.
        </h3>
        <p class="food__description">
          We will continue to provide the best experience for our customers.
        </p>
      </div>
    </section>

    <section class="fridge-inventory" id="fridge">
    <h2 class="fridge-inventory__title" data-aos="flip-up">Fridge Inventory</h2>

    <div class="fridge-inventory__catalogue" data-aos="fade-up">
        <?php if (isset($_SESSION['username'])): ?>
            <?php if (empty($ingredients)): ?>
                <p>Your fridge is empty. Add some ingredients!</p>
            <?php else: ?>
                <?php foreach ($ingredients as $ingredient): ?>
                    <article class="fridge-inventory__card">
                        <img
                            class="fridge-inventory__card-image"
                            src="<?= htmlspecialchars($ingredient['image']) ?>"
                            alt="<?= htmlspecialchars($ingredient['name']) ?>"
                        />
                        <h4 class="fridge-inventory__card-title"><?= htmlspecialchars($ingredient['name']) ?></h4>

                        <div class="fridge-inventory__card-details flex-between">
                            <p>Quantity: <?= htmlspecialchars($ingredient['quantity']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($sampleIngredients as $ingredient): ?>
                <article class="fridge-inventory__card">
                    <img
                        class="fridge-inventory__card-image"
                        src="<?= htmlspecialchars($ingredient['image']) ?>"
                        alt="<?= htmlspecialchars($ingredient['name']) ?>"
                    />
                    <h4 class="fridge-inventory__card-title"><?= htmlspecialchars($ingredient['name']) ?></h4>

                    <div class="fridge-inventory__card-details flex-between">
                        <div class="fridge-inventory__card-rating">
                            <img src="assets/star.svg" alt="star" />
                            <p><?= htmlspecialchars($ingredient['rating']) ?></p>
                        </div>

                        <p class="fridge-inventory__card-price">$<?= number_format($ingredient['price'], 2) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button class="fridge-inventory__button">
        <a href="fridge.php">Explore Inventory</a>
        <img src="assets/arrow-right.svg" alt="arrow-right" />
    </button>
</section>


    <section class="recipes" id="recipes">
      <section class="recipes-food">
        <div class="recipes__content" data-aos="fade-right">
          <p class="food__subtitle">food Recipes</p>

          <h3 class="food__title">Japanese Sushi</h3>
          <p class="food__description">
            Feel the taste of the most delicious Sushi.
          </p>

          <ul class="recipes__list flex-between">
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>
                Cook sushi rice and season it with rice vinegar, sugar, and
                salt.
              </p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>Prepare fillings like fresh fish, avocado, and cucumber.</p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>
                Roll ingredients in a seaweed sheet (nori) using a bamboo mat.
              </p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>
                Slice into bite-sized pieces and serve with soy sauce, wasabi,
                and ginger.
              </p>
            </li>
          </ul>
        </div>
        <div class="recipes__discover" data-aos="zoom-in">
        <p><a href="recipes.html" >Discover</a></p>
      </div>

        <div class="recipes__image flex-center">
          <img src="assets/sushi-5.png" alt="sushi-5" data-aos="fade-left" />

          <div class="recipes__arrow recipes__arrow-left">
            <img src="assets/arrow-vertical.svg" alt="arrow vertical" />
          </div>

          <div class="recipes__arrow recipes__arrow-bottom">
            <img src="assets/arrow-horizontal.svg" alt="arrow horizontal" />
          </div>
        </div>
      </section>

      <section class="recipes-drinks">
        <div class="recipes__image flex-center">
          <img src="assets/sushi-4.png" alt="sushi-4" data-aos="fade-right" />

          <div class="recipes__arrow recipes__arrow-top">
            <img src="assets/arrow-horizontal.svg" alt="arrow horizontal" />
          </div>

          <div class="recipes__arrow recipes__arrow-right">
            <img src="assets/arrow-vertical.svg" alt="arrow vertical" />
          </div>
        </div>

        <div class="recipes__content" data-aos="fade-left">
          <p class="food__subtitle">Drink Recipe</p>

          <h3 class="food__title">Japanese Drinks</h3>
          <p class="food__description">
            Feel the taste of the most delicious drinks.
          </p>

          <ul class="recipes__list flex-between">
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>Boil water and steep green tea leaves or sakura blossoms.</p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>Sweeten with honey or sugar if desired.</p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>Chill or serve hot, depending on preference.</p>
            </li>
            <li>
              <div class="recipes__icon flex-center">
                <img src="assets/check.svg" alt="check" />
              </div>
              <p>Enjoy with a slice of lemon or a few ice cubes.</p>
            </li>
          </ul>
        </div>
      </section>
    </section>

    <footer class="footer flex-between">
      <h3 class="footer__logo"><span>Fridg</span>ify</h3>

      <ul class="footer__nav">
        <li>
          <a href="#fridge">Fridge</a>
        </li>
        <li>
          <a href="#recipes">Recipes</a>
        </li>
        <li>
          <a href="#about-us">About Us</a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      offset: 100,
    });
  </script>
    <script src="js/auth.js" type="module"></script>

  </body>
</html>
