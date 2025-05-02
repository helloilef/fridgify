<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
    $stmt->execute(['username' => $username, 'email' => $email, 'role' => $role, 'id' => $id]);

    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Edit User</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            
            <label for="age">Age:</label>
            <input type="age" name="age" id="age" value="<?= htmlspecialchars($user['age']) ?>" required>
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>