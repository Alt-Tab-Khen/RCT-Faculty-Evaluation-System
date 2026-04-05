<?php
include '../../db_connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the announcement content from the form
    $content = $_POST['announcement_content'];

    // Insert the announcement into the database with the current timestamp
    $stmt = $conn->prepare("INSERT INTO announcements (content, created_at) VALUES (?, NOW())");
    $stmt->bind_param('s', $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
