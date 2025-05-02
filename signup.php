<?php
// Start the session
session_start();

header('Content-Type: application/json');

try {
    // Database connection
    $pdo = new PDO("mysql:host=localhost:3309;dbname=fridgify", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $age = $input['age'] ?? '';
    $username = $input['username'] ?? '';
    $password = password_hash($input['password'] ?? '', PASSWORD_BCRYPT);
    $role = $input['role'] ?? 'user';

    // Validate input
    if (empty($name) || empty($age) || empty($username) || empty($password) || empty($role)) {
        error_log("Signup Error: Missing fields. Input: " . json_encode($input));
        die(json_encode(['success' => false, 'message' => 'All fields are required.']));
    }

    try {
        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, age, password, role, created_at) VALUES (:username, :age, :password, :role, NOW())");
        $stmt->execute([
            ':username' => $username,
            ':age' => $age,
            ':password' => $password,
            ':role' => $role,
        ]);
        echo json_encode(['success' => true, 'message' => 'Signup successful.']);
    } catch (PDOException $e) {
        error_log("Signup Error: " . $e->getMessage());
        die(json_encode(['success' => false, 'message' => 'Sign-up failed: ' . $e->getMessage()]));
    }
}