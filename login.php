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
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        error_log("Login Error: Missing fields. Input: " . json_encode($input));
        die(json_encode(["success" => false, "message" => "All fields are required."]));
    }

    try {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo json_encode(["success" => true, "redirect" => "index.php"]);
        } else {
            die(json_encode(["success" => false, "message" => "Invalid username or password."]));
        }
    } catch (PDOException $e) {
        error_log("Login Error: " . $e->getMessage());
        die(json_encode(["success" => false, "message" => "An error occurred during login."]));
    }
}