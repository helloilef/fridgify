<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost:3309;dbname=fridgify", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sample elements for non-logged-in users (admin's ingredients)
$sampleItems = [];
$stmt = $pdo->prepare("
    SELECT * 
    FROM ingredients 
    WHERE user_id = (SELECT id FROM users WHERE username = 'admin')
");
$stmt->execute();
$sampleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user-specific ingredients if logged in
$userIngredients = [];
if (isset($_SESSION['username'])) {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM ingredients 
        WHERE user_id = (SELECT id FROM users WHERE username = :username)
    ");
    $stmt->execute(['username' => $_SESSION['username']]);
    $userIngredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle Add Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-item'])) {
    $title = $_POST['title'];
    $quantity = $_POST['quantity'];
    $rating = $_POST['rating'];
    $price = $_POST['price'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = 'assets/'; // Ensure the trailing slash is included
        $imagePath = $uploadDir . $imageName; // Correctly concatenate the path
    
        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        // Move the uploaded file
        move_uploaded_file($imageTmpPath, $imagePath);
    
        // Save the relative path (e.g., "assets/imagename") in the database
        $imageFullPath = $imagePath;
    } else {
        $imageFullPath = null; // Handle cases where no image is uploaded
    }

    // Get the user ID for the logged-in user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $userId = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        INSERT INTO ingredients (user_id, name, quantity, image, rating, price, created_at) 
        VALUES (:user_id, :name, :quantity, :image, :rating, :price, NOW())
    ");
    $stmt->execute([
        'user_id' => $userId,
        'name' => $title,
        'quantity' => $quantity,
        'image' => $imageFullPath,
        'rating' => $rating,
        'price' => $price,
    ]);
    header("Location: fridge.php");
    exit();
}

// Handle Update Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-item'])) {
    $id = $_POST['id'];
    $newTitle = $_POST['newtitle'];
    $newQuantity = $_POST['newquantity'];
    $newRating = $_POST['newrating'];
    $newPrice = $_POST['newprice'];

    // Handle file upload
    if (isset($_FILES['newimage']) && $_FILES['newimage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['newimage']['tmp_name'];
        $imageName = basename($_FILES['newimage']['name']);
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . $imageName;

        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        move_uploaded_file($imageTmpPath, $imagePath);

        // Get the full URL path
        $imageFullPath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $imagePath;

        // Update the item with the new image
        $stmt = $pdo->prepare("
            UPDATE ingredients 
            SET name = :name, quantity = :quantity, image = :image, rating = :rating, price = :price 
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $newTitle,
            'quantity' => $newQuantity,
            'image' => $imageFullPath,
            'rating' => $newRating,
            'price' => $newPrice,
            'id' => $id,
        ]);
    } else {
        // Update the item without changing the image
        $stmt = $pdo->prepare("
            UPDATE ingredients 
            SET name = :name, quantity = :quantity, rating = :rating, price = :price 
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $newTitle,
            'quantity' => $newQuantity,
            'rating' => $newRating,
            'price' => $newPrice,
            'id' => $id,
        ]);
    }

    header("Location: fridge.php");
    exit();
}

// Handle Delete Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-item'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: fridge.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fridgify</title>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="fridge.php">Fridge</a></li>
                <li><a href="recipes.php">Recipes</a></li>
                <li><a href="index.php#about-us">About Us</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="profile-icon">
                    <div class="profile-dropdown">
  <img
    src="assets/sushi-12.png"
    alt="Profile Icon"
    title="<?= htmlspecialchars($_SESSION['username']) ?>"
    id="profile-icon"
    style="cursor: pointer;"
  />
  <div class="dropdown-menu hidden" id="dropdown-menu">
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-button">Logout</button>
    </form>
  </div>
</div>
</div>
                    </li>
                <?php else: ?>
                    <li><a href="#" id="login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="fridge-inventory1" id="fridge">
    <h2 class="fridge-inventory__title">Fridge Inventory</h2>
    <?php if (isset($_SESSION['username'])): ?>
        <button class="fridge-inventory__button">
            <a href="#" id="add-item">Add item</a>
        </button>
    <?php endif; ?>
    <div class="fridge-inventory__catalogue1">
        <?php if (isset($_SESSION['username'])): ?>
            <?php if (empty($userIngredients)): ?>
                <p>Your fridge is empty. Start by adding an ingredient using the "Add Item" button!</p>
            <?php else: ?>
                <?php foreach ($userIngredients as $ingredient): ?>
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

        <div class="fridge-inventory__card-actions">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $ingredient['id'] ?>">
                <button type="submit" name="delete-item" style="
                width: fit-content;

  padding: 12px 13px;
  margin: 18px auto 0;

  font-weight: 350;
  font-size: 12px;
  line-height: 12px;
  font-family: var(--plus-jakarta-sans);
  color:rgb(250, 250, 250);

  border: none;
  outline: none;
  background:rgb(179, 33, 0);
  border-radius: 64px;
  cursor: pointer;">Delete</button>
            </form>
            <button
            class="update-button"
                style="
                width: fit-content;

  padding: 12px 13px;
  margin: 18px auto 0;

  font-weight: 350;
  font-size: 12px;
  line-height: 12px;
  font-family: var(--plus-jakarta-sans);
  color:rgb(250, 250, 250);

  border: none;
  outline: none;
  background:rgb(0, 179, 42);
  border-radius: 64px;
  cursor: pointer;"
                type="button"
                data-id="<?= $ingredient['id'] ?>"
    data-name="<?= htmlspecialchars($ingredient['name']) ?>"
    data-image="<?= htmlspecialchars($ingredient['image']) ?>"
    data-quantity="<?= htmlspecialchars($ingredient['quantity']) ?>"
    data-rating="<?= htmlspecialchars($ingredient['rating']) ?>"
    data-price="<?= htmlspecialchars($ingredient['price']) ?>"

            >
                Update
            </button>
        </div>
    </article>
<?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($sampleItems as $item): ?>
    <article class="fridge-inventory__card">
        <img
            class="fridge-inventory__card-image"
            src="<?= htmlspecialchars($item['image'] ?? 'assets/default-image.png') ?>"
            alt="<?= htmlspecialchars($item['name'] ?? 'Unknown Name') ?>"
        />
        <h4 class="fridge-inventory__card-title"><?= htmlspecialchars($item['name'] ?? 'Unknown Name') ?></h4>
        <div class="fridge-inventory__card-details flex-between">
            <p>Quantity: <?= htmlspecialchars($item['quantity'] ?? 'Unknown Quantity') ?></p>
            <div class="fridge-inventory__card-rating">
    <img src="assets/star.svg" alt="star" />
    <p><?= htmlspecialchars($ingredient['rating'] ?? '0') ?></p>
</div>
        </div>
    </article>
<?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Add Item Form -->
<div id="add-item-form" class="hidden">
    <div class="login-container">
        <span class="close-btn">&times;</span>
        <h2>Add Item</h2>
        <form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="add-item" value="1">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required />
    <label for="image">Image:</label>
    <input type="file" name="image" id="image" accept="image/*" required />
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" required />
    <label for="rating">Rating:</label>
    <input type="number" name="rating" id="rating" step="0.1" min="0" max="5" required />
    <label for="price">Price:</label>
    <input type="number" name="price" id="price" step="0.01" required />
    <button type="submit">Add Item</button>
</form>
    </div>
</div>

<!-- Update Item Form -->
<div id="update-item-form" class="hidden">
  <div class="login-container">
    <span class="close-btn-up">&times;</span>
    <h2>Update Item</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="update-item" value="1">
      <input type="hidden" name="id" id="update-id" />
      <label for="newtitle">New Title:</label>
      <input type="text" name="newtitle" id="newtitle" required />
      <label for="newimage">New Image:</label>
      <input type="file" name="newimage" id="newimage" accept="image/*" />
      <label for="newquantity">New Quantity:</label>
      <input type="number" name="newquantity" id="newquantity" required />
      <label for="newrating">New Rating:</label>
      <input type="number" name="newrating" id="newrating" step="0.1" min="0" max="5" required />
      <label for="newprice">New Price:</label>
      <input type="number" name="newprice" id="newprice" step="0.01" required />
      <button type="submit">Update Item</button>
    </form>
  </div>
</div>
</div>

    <footer class="footer flex-between">
        <h3 class="footer__logo"><span>Fridg</span>ify</h3>
        <ul class="footer__nav">
            <li><a href="index.html">Home</a></li>
            <li><a href="index.html/#recipes">Recipes</a></li>
            <li><a href="index.html/#about-us">About Us</a></li>
            <li><a href="#">Up</a></li>
        </ul>
        <ul class="footer__social">
            <li class="flex-center"><img src="assets/facebook.svg" alt="facebook" /></li>
            <li class="flex-center"><img src="assets/instagram.svg" alt="instagram" /></li>
            <li class="flex-center"><img src="assets/twitter.svg" alt="twitter" /></li>
        </ul>
    </footer>
    <script src="js/fridge.js" type="module"></script>
</body>
</html>