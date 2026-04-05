<?php
include '../../db_connect.php'; // Database connection

// Check if the evaluation period has ended based on end_time
$query = "SELECT * FROM evaluation_periods WHERE status = 'active' AND end_time <= NOW()";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo json_encode(['shouldEnd' => true]);
} else {
    echo json_encode(['shouldEnd' => false]);
}
?>
