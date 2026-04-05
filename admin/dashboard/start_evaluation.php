<?php
include '../../db_connect.php'; // Your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $minutes = intval($_POST['minutes']);
    
    // Get the current timestamp and calculate the end time
    $startTime = new DateTime(); // Current time
    $endTime = clone $startTime;
    $endTime->modify("+$minutes minutes"); // Add the selected period (in minutes)

    // Format the dates and times for database insertion
    $startDateFormatted = $startTime->format('Y-m-d');
    $startTimeFormatted = $startTime->format('Y-m-d H:i:s');
    $endDateFormatted = $endTime->format('Y-m-d');
    $endTimeFormatted = $endTime->format('Y-m-d H:i:s');

    // Insert the evaluation period into the database
    $query = "INSERT INTO evaluation_periods (start_date, end_date, start_time, end_time, status) 
              VALUES (?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $startDateFormatted, $endDateFormatted, $startTimeFormatted, $endTimeFormatted);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'startTime' => $startTimeFormatted,
            'endTime' => $endTimeFormatted
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
