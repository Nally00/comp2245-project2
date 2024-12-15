<?php
session_start();
header('Content-Type: application/json');

// Include database configuration
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Get the logged-in user's ID from the session
$created_by = $_SESSION['user_id'];

try {
    // Validate and sanitize user input
    $title = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars(trim($_POST['type'] ?? ''), ENT_QUOTES, 'UTF-8');
    $priority = htmlspecialchars(trim($_POST['priority'] ?? ''), ENT_QUOTES, 'UTF-8');
    $assigned_to = intval($_POST['assigned_to'] ?? 0);

    // Validation checks for required fields
    if (empty($title)) {
        echo json_encode(['success' => false, 'error' => 'Title is required.']);
        exit();
    }

    if (empty($description)) {
        echo json_encode(['success' => false, 'error' => 'Description is required.']);
        exit();
    }

    if (empty($type) || !in_array($type, ['Bug', 'Proposal', 'Task'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid selection.']);
        exit();
    }

    if (empty($priority) || !in_array($priority, ['Minor', 'Medium', 'Major', 'Critical'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid selection.']);
        exit();
    }

    if ($assigned_to <= 0) {
        echo json_encode(['success' => false, 'error' => 'A user must be assigned to the issue.']);
        exit();
    }

    // Prepare SQL query to insert the new issue into the database
    $stmt = $pdo->prepare("
        INSERT INTO Issues (title, description, type, priority, status, assigned_to, created_by, created_at, updated_at)
        VALUES (:title, :description, :type, :priority, 'Open', :assigned_to, :created_by, NOW(), NOW())
    ");

    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':type' => $type,
        ':priority' => $priority,
        ':assigned_to' => $assigned_to,
        ':created_by' => $created_by,
    ]);

    echo json_encode(['success' => true, 'message' => 'Issue created successfully!']);
} catch (PDOException $e) {
    //Handle errors
    error_log("Error creating issue: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while creating the issue.']);
}
?>
