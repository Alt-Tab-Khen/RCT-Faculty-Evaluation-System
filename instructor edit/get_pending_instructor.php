<?php
// Example PHP to return pending instructor details including the image
include '../db_connect.php';

$query = "SELECT prof_id, first_name, last_name, faculty, profile_picture FROM instructor WHERE status = 'pending'";
$result = $conn->query($query);

$instructors = [];
while ($row = $result->fetch_assoc()) {
    $instructors[] = [
        'prof_id' => $row['prof_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'faculty' => $row['faculty'],
        'profile_picture' => $row['profile_picture'] // The image filename
    ];
}

header('Content-Type: application/json');
echo json_encode(['instructors' => $instructors]);
?>
