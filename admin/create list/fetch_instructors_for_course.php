<?php
include '../../db_connect.php';

session_start(); // Ensure session is started

if (isset($_GET['course']) && isset($_SESSION['student_id'])) {
    $course = $_GET['course'];
    $student_id = $_SESSION['student_id']; // Get student ID from session

    // Fetch instructors assigned to the selected course and also check if they have been evaluated
    $stmt = $conn->prepare("
    SELECT i.prof_id, i.first_name, i.last_name, i.profile_picture, i.faculty, 
    (SELECT COUNT(*) FROM evaluations e WHERE e.student_id = ? AND e.prof_id = i.prof_id) AS already_evaluated 
    FROM instructor i 
    JOIN course_instructor_list cil ON i.prof_id = cil.prof_id 
    WHERE cil.course = ?
");
$stmt->bind_param("is", $student_id, $course);
;

    $stmt->execute();
    $result = $stmt->get_result();

    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }

    echo json_encode($instructors);
}
?>
