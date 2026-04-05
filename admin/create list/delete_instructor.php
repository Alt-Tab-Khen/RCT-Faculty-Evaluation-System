<?php
include '../../db_connect.php';  // Include your database connection file

if (isset($_POST['prof_id']) && isset($_POST['course'])) {
    $prof_id = $_POST['prof_id'];
    $course = $_POST['course'];

    // Delete the instructor from the course
    $stmt = $conn->prepare("DELETE FROM course_instructor_list WHERE prof_id = ? AND course = ?");
    $stmt->bind_param("ss", $prof_id, $course);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Send success response
        echo json_encode(['status' => 'success']);
    } else {
        // Send failure response if nothing was deleted
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete instructor.']);
    }
} else {
    // Invalid data
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
