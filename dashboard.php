<?php
//include 'db_connection.php';  data base not created yet
session_start();

// Placeholder for database connection (to be added later)
$conn = null; 

// Check if database connection exists
if (!$conn) {
    echo "<tr><td colspan='6'>Error: Database connection not set up.</td></tr>";
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
if ($filter === 'open') {
    $query = "SELECT * FROM Issues WHERE status = 'Open'";
} elseif ($filter === 'my') {
    $query = "SELECT * FROM Issues WHERE assigned_to = ?";
} else {
    $query = "SELECT * FROM Issues";
}

// Prepare the SQL query 
if ($conn) {
    $stmt = $conn->prepare($query);

    if ($filter === 'my') {
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Table rows to be shown 
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
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

    $stmt->close();
} else {
    echo "<tr><td colspan='6'>Error: Unable to fetch issues. Database connection not available.</td></tr>";
}

// Close the database connection
if ($conn) {
    $conn->close();
}
?>
