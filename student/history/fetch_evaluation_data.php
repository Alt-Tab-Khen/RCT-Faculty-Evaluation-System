<?php
include '../../db_connect.php';

session_start();
$student_id = $_SESSION['student_id'];
$prof_id = $_GET['prof_id']; // Get prof_id from the request

// Fetch the student's existing evaluation for this professor
$query = "
    SELECT e.question_id, e.rating, q.question, e.category
    FROM evaluations e
    JOIN questionnaires q ON e.question_id = q.id
    WHERE e.student_id = ? AND e.prof_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('is', $student_id, $prof_id);
$stmt->execute();
$result = $stmt->get_result();

$evaluations = [];
while ($row = $result->fetch_assoc()) {
    $evaluations[] = $row;
}

// Return the evaluations as JSON
echo json_encode($evaluations);
?>
