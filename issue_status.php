<?php
require_once 'config.php';
header('Content-Type: application/json');

//get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

//check if there is an ID and status in the data
if (!isset($data['issue_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data.']);
    exit();
}

$issueId = $data['issue_id'];
$status = $data['status'];

//valid status types 
$statusTypes = ['Closed', 'In Progress'];  


//check if status is valid type
if (!in_array($status, $statusTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status value.']);
    exit();
}

try {
    //prepare SQL query
    $stmt = $pdo->prepare("
        UPDATE Issues 
        SET status = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$status, $issueId]); //update issue status and update fields

    //Check if update was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Issue status updated.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No changes made.']);
    }

} catch (PDOException $e) {
    //Handle errors
    error_log("Error updating issue status: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while updating the issue status.']);
}
?>
