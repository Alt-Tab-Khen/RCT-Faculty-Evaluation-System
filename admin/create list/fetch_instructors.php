<?php
include '../../db_connect.php';

$faculty = $_GET['faculty'];
$course = $_GET['course'];  // Get the selected course from the request

if (!empty($faculty) && !empty($course)) {
    // Fetch instructors by faculty and check if they are already assigned to the selected course
    $stmt = $conn->prepare("
        SELECT i.prof_id, i.first_name, i.last_name, i.faculty, i.profile_picture,
        CASE 
            WHEN cil.prof_id IS NOT NULL THEN 1
            ELSE 0
        END AS is_in_course
        FROM instructor i
        LEFT JOIN course_instructor_list cil ON i.prof_id = cil.prof_id AND cil.course = ?
        WHERE i.faculty = ?
    ");
    $stmt->bind_param("ss", $course, $faculty);  // Bind both course and faculty parameters
    $stmt->execute();
    $result = $stmt->get_result();
    
    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }

    echo json_encode($instructors);
} else {
    echo json_encode([]);
}
?>
