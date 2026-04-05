<?php
include '../../db_connect.php';

// Query to get the profile pictures of the students that will be deleted
$select_stmt = $conn->prepare("SELECT image FROM student WHERE status = 'rejected' AND rejected_at <= NOW() - INTERVAL 1 HOUR");
$select_stmt->execute();
$result = $select_stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $profile_picture = $row['image'];
    
    // Only delete the image if it's not the default image
    if ($profile_picture && $profile_picture !== 'default_profile.png') {
        $image_path = realpath(__DIR__ . "/../photo/" . $profile_picture);
        
        if (file_exists($image_path)) {
            unlink($image_path);  // Delete the file from the server
        }
    }
}

// After deleting the images, delete the students
$delete_stmt = $conn->prepare("DELETE FROM student WHERE status = 'rejected' AND rejected_at <= NOW() - INTERVAL 1 HOUR");

if ($delete_stmt->execute()) {
    // Success message removed for silent background process
} else {
    error_log("Error deleting rejected students: " . $delete_stmt->error);
}

?>
