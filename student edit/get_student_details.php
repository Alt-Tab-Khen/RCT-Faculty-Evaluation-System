<?php
include '../db_connect.php';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $query = "SELECT student_id, first_name, last_name, username, email, year, section, course FROM student WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Student not found']);
    }
}
?>
