<?php
include '../../db_connect.php';

session_start(); // Ensure session is started

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id']; // Get student ID from session

    // Fetch instructors that the student has already evaluated along with a `can_edit` flag
    $stmt = $conn->prepare("
        SELECT i.prof_id, i.first_name, i.last_name, i.profile_picture, i.faculty, e.created_at,
            IF(TIMESTAMPDIFF(HOUR, e.created_at, NOW()) < 12, 1, 0) AS can_edit
        FROM instructor i
        JOIN evaluations e ON i.prof_id = e.prof_id
        JOIN course_instructor_list cil ON i.prof_id = cil.prof_id
        WHERE e.student_id = ?
        GROUP BY i.prof_id
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }

    echo json_encode($instructors);
}
?>
