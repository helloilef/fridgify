<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the ingredient details
    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ingredient) {
        die("Ingredient not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $image = $_FILES['image']['name'];

        // Handle image upload
        if (!empty($image)) {
            $targetDir = "assets/";
            $targetFile = $targetDir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        } else {
            $targetFile = $ingredient['image']; // Keep the existing image if no new image is uploaded
        }

        // Update the ingredient
        $stmt = $pdo->prepare("UPDATE ingredients SET name = :name, quantity = :quantity, image = :image WHERE id = :id");
        $stmt->execute(['name' => $name, 'quantity' => $quantity, 'image' => $targetFile, 'id' => $id]);

        header("Location: admin_dashboard.php");
        exit();
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ingredient</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Edit Ingredient</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($ingredient['name']) ?>" required>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($ingredient['quantity']) ?>" required>
            <label for="image">Image:</label>
            <input type="file" name="image" id="image" accept="image/*">
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>