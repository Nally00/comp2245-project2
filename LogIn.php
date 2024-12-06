<?php
session_start();
//return JSON response
header('Content-Type: application/json');

require_once 'config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form input data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check if email and password fields are empty
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
        exit;
    }

    // Prepare a SQL statement to fetch user data from database
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and if password matches
    if ($user && password_verify($password, $user['password'])) {
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];

        // Check if user is the admin
        $_SESSION['is_admin'] = ($user['email'] === 'admin@project2.com'); 

        //return success reponse
        echo json_encode(['success' => true, 'message' => 'Login successful.']);
    } else {
        //return error response
        echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    }
} else {
    // If the request method is not POST return error
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
