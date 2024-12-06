<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
        exit;
    }

    // Connect to database
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = ($user['email'] === 'admin@project2.com'); // Admin check

        echo json_encode(['success' => true, 'message' => 'Login successful.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
