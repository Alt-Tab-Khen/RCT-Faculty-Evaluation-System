<?php
include '../../db_connect.php';  // Include your database connection file

// Check if selected_instructors and course are sent via POST
if (isset($_POST['selected_instructors']) && isset($_POST['course'])) {
    $selectedInstructors = $_POST['selected_instructors'];  // Array of instructor IDs
    $course = $_POST['course'];  // Selected course

    // Log the incoming data for debugging
    error_log(print_r($_POST, true));

    // Fetch existing instructors for the course
    $existingInstructors = [];
    $fetchStmt = $conn->prepare("SELECT prof_id FROM course_instructor_list WHERE course = ?");
    $fetchStmt->bind_param("s", $course);
    $fetchStmt->execute();
    $result = $fetchStmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $existingInstructors[] = $row['prof_id'];
    }

    // Only add new instructors that are not already in the list
    foreach ($selectedInstructors as $instructorId) {
        if (!in_array($instructorId, $existingInstructors)) {
            $stmt = $conn->prepare("INSERT INTO course_instructor_list (course, prof_id) VALUES (?, ?)");
            $stmt->bind_param("ss", $course, $instructorId);
            $stmt->execute();

            // Check for SQL errors during insertion
            if ($stmt->error) {
                error_log("Error inserting instructor: " . $stmt->error);  // Log SQL error
                echo json_encode(['status' => 'error', 'message' => 'Database error during insertion.']);
                exit;
            }
        }
    }

    // Send success response if all goes well
    echo json_encode(['status' => 'success']);
} else {
    // If data is missing, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
 