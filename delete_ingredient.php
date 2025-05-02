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

    // Delete the ingredient
    $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: admin_dashboard.php");
    exit();
} else {
    die("Invalid request.");
}
?>