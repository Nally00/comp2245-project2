<?php
include 'config.php';  
session_start();

// Check if the database connection exists
if (!isset($pdo) || !$pdo) {
    error_log("Database connection is not set up."); // Log the error for debugging
    echo "<tr><td colspan='6'>Error: Unable to connect to the database. Please contact the administrator.</td></tr>";
    exit;
}

// Check if user is logged in 
if (!isset($_SESSION['user_id'])) {
    echo "<tr><td colspan='6'>Error: User not logged in.</td></tr>";
    exit;
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Determine the filter from the POST request
$filter = $_POST['filter'] ?? 'all';

// SQL query based selected filter
try {
    // Build the SQL query based on the selected filter
    if ($filter === 'open') {
        $query = "SELECT * FROM Issues WHERE status = 'Open'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    } elseif ($filter === 'my') {
        $query = "SELECT * FROM Issues WHERE assigned_to = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
    } else {
        $query = "SELECT * FROM Issues";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }

    $result = $stmt->fetchAll();

    // Generate the table rows dynamically
    if ($result) {
        foreach ($result as $row) {
            echo "<tr data-issue-id='" . htmlspecialchars($row['id']) . "'>";
            echo "<td># " . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['assigned_to']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No issues found.</td></tr>";
    }
} catch (PDOException $e) {
    // Log the error for debugging purposes
    error_log("Database query error: " . $e->getMessage());
    echo "<tr><td colspan='6'>Error: Unable to fetch issues. Please try again later.</td></tr>";
}
?>