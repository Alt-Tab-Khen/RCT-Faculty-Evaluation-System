<?php
include '../db_connect.php';

$prof_id = $_GET['prof_id'];
$action = $_GET['action'];

$response = [];

if ($action == 'validate') {
    // Perform validation (e.g., update instructor status in the database)
    $stmt = $conn->prepare("UPDATE instructor SET status = 'validated', rejected_at = NULL WHERE prof_id = ?");
    $stmt->bind_param('s', $prof_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Instructor validated successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to validate instructor.';
    }

} elseif ($action == 'reject') {
    // Perform rejection (update status and set rejected_at timestamp)
    $stmt = $conn->prepare("UPDATE instructor SET status = 'rejected', rejected_at = NOW() WHERE prof_id = ?");
    $stmt->bind_param('s', $prof_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Instructor rejected successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to reject instructor.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid action.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
