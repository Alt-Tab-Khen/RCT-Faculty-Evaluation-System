<?php
include '../../db_connect.php'; // Your database connection

// Total number of students
$studentCountQuery = "SELECT COUNT(*) as total_students FROM student";
$studentCountResult = $conn->query($studentCountQuery);
$totalStudents = $studentCountResult->fetch_assoc()['total_students'];

// Number of students who have evaluated
$evaluatedCountQuery = "SELECT COUNT(DISTINCT student_id) as students_evaluated FROM evaluations";
$evaluatedCountResult = $conn->query($evaluatedCountQuery);
$studentsEvaluated = $evaluatedCountResult->fetch_assoc()['students_evaluated'];

// Calculate students who have not evaluated
$studentsNotEvaluated = $totalStudents - $studentsEvaluated;

// Return JSON data for the chart
echo json_encode([
    'studentsEvaluated' => $studentsEvaluated,
    'studentsNotEvaluated' => $studentsNotEvaluated
]);
?>
