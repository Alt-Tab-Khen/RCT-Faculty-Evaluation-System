<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete from miscinstructor table
    $query = "DELETE FROM miscinstructor WHERE instructor_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Dynamically redirect to the previous faculty page
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=Deleted successfully");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
