<?php
// Example PHP to return pending student details including the image
include '../db_connect.php';

$query = "SELECT student_id, first_name, last_name, course, year, section, image FROM student WHERE status = 'pending'";
$result = $conn->query($query);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        'student_id' => $row['student_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'course' => $row['course'],
        'year' => $row['year'],
        'section' => $row['section'],
        'image' => $row['image'] // The image filename
    ];
}

header('Content-Type: application/json');
echo json_encode(['students' => $students]);
?>
