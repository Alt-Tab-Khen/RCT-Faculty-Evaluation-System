<?php
include '../../db_connect.php'; // Include your database connection

// Use POST to receive the ID and check session
session_start();
if (isset($_POST['id']) && isset($_SESSION['student_id'])) {
    $announcementId = intval($_POST['id']); // Ensure the ID is an integer
    $studentId = $_SESSION['student_id']; // Student ID from session

    // Insert or update the 'read' status in the `announcement_reads` table
    $query = "INSERT INTO announcement_reads (student_id, announcement_id, is_read, read_at) 
              VALUES (?, ?, 1, NOW()) 
              ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $studentId, $announcementId); // Use 'ii' for both integers

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No ID or student_id provided']);
}
?>
