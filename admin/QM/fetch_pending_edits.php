<?php
include '../../db_connect.php';

// Query to get count of pending edits
$query = "SELECT COUNT(*) AS pending_count FROM questionnaire_edits WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Return the count as JSON
echo json_encode(['pending_count' => $row['pending_count']]);
?>
