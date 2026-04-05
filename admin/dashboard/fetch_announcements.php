<?php
include '../../db_connect.php';  // Adjust the path to your database connection file

$query = "SELECT id, content, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);

$announcements = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

echo json_encode($announcements);
?>
