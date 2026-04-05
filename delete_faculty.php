<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete from instructor table
    $query = "DELETE FROM instructor WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?message=Deleted successfully");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
