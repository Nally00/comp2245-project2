<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Query to fetch users
    $stmt = $pdo->query("SELECT id, firstname, lastname FROM Users ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        echo json_encode(['success' => true, 'users' => $users]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No users found']);
    }
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to fetch user data']);
}
?>
