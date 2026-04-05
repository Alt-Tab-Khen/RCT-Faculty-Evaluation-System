<?php
include '../../db_connect.php'; // Your database connection

// Fetch the active evaluation period
$query = "SELECT start_time, end_time FROM evaluation_periods WHERE status = 'active' LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $evaluation = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'startTime' => $evaluation['start_time'], // Return start_time
        'endTime' => $evaluation['end_time'] // Return end_time in correct format
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'No active evaluation found']);
}
?>
