<?php
session_start();

// Set response type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, return an error response
    echo json_encode([
        'authenticated' => false,
        'error' => 'User is not logged in.'
    ]);
    exit;
}

// If logged in, return the user's details
echo json_encode([
    'authenticated' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'firstname' => $_SESSION['firstname'],
        'lastname' => $_SESSION['lastname'],
        'email' => $_SESSION['email'],
        'is_admin' => $_SESSION['is_admin'], // Check if user is admin
    ]
]);
?>
