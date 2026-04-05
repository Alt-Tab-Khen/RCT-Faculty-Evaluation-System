<?php
include '../db_connect.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['prof_id'])) {
        $prof_id = mysqli_real_escape_string($conn, $_GET['prof_id']);

        // Retrieve the instructor's profile picture filename
        $query = "SELECT profile_picture FROM instructor WHERE prof_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $prof_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $instructor = $result->fetch_assoc();

        if ($instructor) {
            // Get the profile picture path
            $profile_image = $instructor['profile_picture'];

            // Check if the image is not the default image
            if ($profile_image !== 'default_profile.png') {
                // Define the path to the image file
                $image_path = realpath(__DIR__ . "/../photo/" . $profile_image);

                // If the image file exists, delete it
                if (file_exists($image_path)) {
                    unlink($image_path); // Deletes the file
                }
            }

            // Proceed to delete the instructor record from the database
            $delete_query = "DELETE FROM instructor WHERE prof_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("s", $prof_id);

            if ($delete_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Instructor deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting instructor: ' . $delete_stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Instructor not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
exit();
