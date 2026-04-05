<?php
include '../db_connect.php';

header('Content-Type: application/json'); // Always return JSON

try {
    if (isset($_GET['student_id'])) {
        $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);

        // First, retrieve the student's profile picture filename
        $query = "SELECT image FROM student WHERE student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        if ($student) {
            // Get the profile picture path
            $profile_image = $student['image'];

            // Check if the image is not the default image
            if ($profile_image !== 'default_profile.png') {
                // Define the path to the image file
                $image_path = realpath(__DIR__ . "/../photo/" . $profile_image);

                // If the image file exists, delete it
                if (file_exists($image_path)) {
                    unlink($image_path); // Deletes the file
                }
            }

            // Now proceed to delete the student record from the database
            $delete_query = "DELETE FROM student WHERE student_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("s", $student_id);

            if ($delete_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting student: ' . $delete_stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Student not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
exit();
