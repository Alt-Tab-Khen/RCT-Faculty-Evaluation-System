<?php
include '../db_connect.php';

session_start(); // Ensure session is started

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch the student's course from the student table
    $stmt_course = $conn->prepare("SELECT course FROM student WHERE student_id = ?");
    $stmt_course->bind_param("i", $student_id);
    $stmt_course->execute();
    $result_course = $stmt_course->get_result();

    if ($row_course = $result_course->fetch_assoc()) {
        $course = $row_course['course'];

        // Fetch total instructors for the student's course
        $stmt = $conn->prepare("
            SELECT 
            (SELECT COUNT(DISTINCT prof_id) FROM course_instructor_list WHERE course = ?) AS total_instructors,
            (SELECT COUNT(DISTINCT e.prof_id) FROM evaluations e 
             JOIN course_instructor_list cil ON e.prof_id = cil.prof_id
             WHERE e.student_id = ? AND cil.course = ?) AS evaluated_instructors
        ");
        $stmt->bind_param("sis", $course, $student_id, $course);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $total_instructors = $row['total_instructors'];
            $evaluated_instructors = $row['evaluated_instructors'];
            $non_evaluated_instructors = $total_instructors - $evaluated_instructors;

            echo json_encode([
                'instructor_count' => $total_instructors,
                'evaluated_instructors' => $evaluated_instructors,
                'non_evaluated_instructors' => $non_evaluated_instructors
            ]);
        } else {
            echo json_encode(['error' => 'No instructors found for this course.']);
        }
    } else {
        echo json_encode(['error' => 'Course not found for this student.']);
    }
} else {
    echo json_encode(['error' => 'Student ID not set or session expired.']);
}

$conn->close();
?>
