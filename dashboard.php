<?php
include 'config.php';  
session_start();

// Check if the database connection exists
if (!isset($pdo)) {
    echo "<tr><td colspan='6'>Error: Unable to connect to the database.</td></tr>";
    exit;
}

// Check if user is logged on 
if (!isset($_SESSION['user_id'])) {
    echo "<tr><td colspan='6'>Error: User not logged in.</td></tr>";
    exit;
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Determine the filter based on the POST request
$filter = $_POST['filter'] ?? 'all';

try {
    // Prepare SQL query based on filter
    if ($filter === 'open') {
        $stmt = $pdo->prepare("SELECT * FROM Issues WHERE status = 'Open'");
        $stmt->execute();

    } elseif ($filter === 'my') {
        $stmt = $pdo->prepare("SELECT * FROM Issues WHERE assigned_to = :user_id");
        $stmt->execute(['user_id' => $user_id]);

    } else {
        $stmt = $pdo->prepare("SELECT * FROM Issues");
        $stmt->execute();
    }

    //Get all issues
    $issues = $stmt->fetchAll();

    // Get the table rows from database
    if ($issues) {
        foreach ($issues as $row) {
            echo "<tr data-issue-id='" . htmlspecialchars($row['id']) . "'>";
            echo "<td># " . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
            
            $statusClass = strtolower(str_replace(' ', '-', $row['status']));
            echo "<td class='status-{$statusClass}'>" . htmlspecialchars($row['status']) . "</td>";

            echo "<td>" . htmlspecialchars($row['assigned_to']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
    } else {
        //show error if no issues found
        echo "<tr><td colspan='6'>No issues found.</td></tr>";
    }

} catch (PDOException $e) {
    error_log("Error fetching issues: " . $e->getMessage());
    echo "<tr><td colspan='6'>Error: Unable to fetch issues. Please try again later.</td></tr>";
}
?>
