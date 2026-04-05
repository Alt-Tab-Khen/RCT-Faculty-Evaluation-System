<?php
session_start(); // Make sure the session is started to access `student_id`
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../db_connect.php'; // Include your database connection

// Get student_id from session
$studentId = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;

if ($studentId) {
    // Fetch announcements and join with the `announcement_reads` to check read status
    $query = "
        SELECT a.id, a.content, a.created_at, 
               IF(ar.is_read IS NULL, 0, 1) AS read_status
        FROM announcements a
        LEFT JOIN announcement_reads ar 
        ON a.id = ar.announcement_id 
        AND ar.student_id = ?
        ORDER BY a.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $studentId); // Bind student ID
    $stmt->execute();
    $result = $stmt->get_result();

    $announcements = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }
    }

    // Return as JSON
    header('Content-Type: application/json');
    echo json_encode($announcements);
} else {
    // Return an error if the student ID is not available
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Student ID not found']);
}
?>
