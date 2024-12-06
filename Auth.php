<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['authenticated' => false]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'firstname' => $_SESSION['firstname'],
        'lastname' => $_SESSION['lastname'],
        'email' => $_SESSION['email'],
        'is_admin' => $_SESSION['is_admin'],
    ]
]);
?>
