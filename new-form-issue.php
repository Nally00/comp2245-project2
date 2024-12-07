<?php
$query = "SELECT id, firstname, lastname FROM users";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row['id'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "<option>";
}

include 'db_connection.php'; //database.php here

if ($SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $POST['type'];
    $priority = $POST['priority'];
    $assigned_to = $POST['assigned_to'];
    $created_by = 1;
    $typecurrent_time = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO issue (title, description, type, priority, status, assigned_to, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, 'Open', ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiss", $title, $description, $type, $priority, $assigned_to, $created_by, $typecurrent_time, $typecurrent_time);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating issue."]);
    }

    $stmt->close();
    $conn->close();
}
?>