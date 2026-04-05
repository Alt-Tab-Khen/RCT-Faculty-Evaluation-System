<?php
include '../db_connect.php';

$student_id = $_GET['student_id'];
$action = $_GET['action'];

$response = [];

if ($action == 'validate') {
    // Perform validation (e.g., update student status in the database)
    $stmt = $conn->prepare("UPDATE student SET status = 'validated' WHERE student_id = ?");
    $stmt->bind_param('s', $student_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Student validated successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to validate student.';
    }
    
} elseif ($action == 'reject') {
    // Perform rejection (e.g., delete or update student status)
    $stmt = $conn->prepare("UPDATE student SET status = 'rejected' WHERE student_id = ?");
    $stmt->bind_param('s', $student_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Student rejected successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to reject student.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid action.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
