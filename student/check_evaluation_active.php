<?php
include '../db_connect.php'; // Database connection

// Ensure `start_time` and `end_time` are compared with `NOW()`, including both date and time
$query = "SELECT * FROM evaluation_periods 
          WHERE status = 'active' 
          AND NOW() >= start_time 
          AND NOW() <= end_time";  // Check if NOW is between start_time and end_time

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo json_encode(['active' => true]);  // Evaluation is active
} else {
    echo json_encode(['active' => false]);  // No active evaluation
}
?>
