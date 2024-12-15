<?php
require_once 'config.php';
header('Content-Type: application/json'); //set response type to JSOn

//Check if ID is in the URL
if (!isset($_GET['issue_id'])) {
    echo json_encode(['success' => false, 'error' => 'No issue ID provided.']);
    exit();
}

$issueId = $_GET['issue_id'];

try {
    //Prepare and execute SQL query for issue details based on ID
    $stmt = $pdo->prepare("
        SELECT Issues.id, Issues.title, Issues.description, Issues.type, Issues.priority, 
               Issues.status, Issues.created_at, Issues.updated_at, 
               CONCAT(asigned.firstname, ' ', asigned.lastname) AS assigned_to, 
               CONCAT(creator.firstname, ' ', creator.lastname) AS created_by
        FROM Issues
        JOIN Users asigned ON Issues.assigned_to = asigned.id
        JOIN Users creator ON Issues.created_by = creator.id
        WHERE Issues.id = ?
    ");
    $stmt->execute([$issueId]);

    //fetch results of the query
    $issue = $stmt->fetch(PDO::FETCH_ASSOC);

    //check if fetching results was successful
    if ($issue) {
        echo json_encode(['success' => true, 'issue' => $issue]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Issue not found.']);
    }

} catch (PDOException $e) {
    //Handle errors
    error_log("Error fetching issue details: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while fetching issue details.']);
}
?>
