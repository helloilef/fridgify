<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirect non-admin users to the homepage
    exit();
}

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

// Fetch all users
$stmt = $pdo->query("SELECT id, username, role, created_at FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all ingredients (example table: `ingredients`)
$stmt = $pdo->query("SELECT * FROM ingredients");
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <h4>Admin Dashboard</h4>
          <div class="header__logo-overlay"></div>
        </div>

        <ul class="header__menu">
    <li><a href="index.php">Homepage</a></li>
    <?php if (isset($_SESSION['username'])): ?>
    <?php
    // Check if the logged-in user is an admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin'): ?>
        
    <?php endif; ?>
<?php endif; ?>
    
    
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


    <section>
    <h2>All Users</h2>
    <table class="styled-table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr >
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="action-link">Edit</a>
                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="action-link" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section>
    <h2>All Ingredients</h2>
    <table class="styled-table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ingredients as $ingredient): ?>
                <tr>
                    <td><?= htmlspecialchars($ingredient['id']) ?></td>
                    <td><?= htmlspecialchars($ingredient['name']) ?></td>
                    <td><?= htmlspecialchars($ingredient['quantity']) ?></td>
                    <td>
                        <a href="edit_ingredient.php?id=<?= $ingredient['id'] ?>" class="action-link">Edit</a>
                        <a href="delete_ingredient.php?id=<?= $ingredient['id'] ?>" class="action-link" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
    <script src="js/auth.js" type="module"></script>
</body>
</html>