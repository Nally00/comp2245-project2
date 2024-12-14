<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Database Connection
require 'config.php';

$servername = "localhost"; $username = "root"; $password = ""; $dbname = "bugme";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$issue_id = $_GET['issue_id'];

if (isset($_GET['issue_id'])) {
    $issue_id = intval($_GET['issue_id']);

    // Fetch issue details from the database
    $query = "SELECT 
                issues.id AS issue_number, 
                issues.title, 
                issues.description, 
                issues.type, 
                issues.priority, 
                issues.status, 
                users.name AS assigned_to, 
                issues.date_created, 
                issues.date_updated, 
                created_by.name AS created_by
              FROM issues
              INNER JOIN users ON issues.assigned_to = users.id
              INNER JOIN users AS created_by ON issues.created_by = created_by.id
              WHERE issues.id = ?";
    
    $issue_id = $_GET['issue_id'];
    $stmt = $conn->prepare("SELECT * FROM issues WHERE id = ?");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $issue_details = $result->fetch_assoc();
        echo json_encode($issue_details);
    } else {
        echo json_encode(["error" => "Issue not found."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request."]);
}

$conn->close();
?>
