<?php
include '../../db_connect.php'; // Include your database connection

session_start();
$student_id = $_SESSION['student_id'];

// Check if prof_id and ratings are set
if (!isset($_POST['prof_id']) || !isset($_POST['ratings'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit();
}

$prof_id = $_POST['prof_id'];
$ratings = $_POST['ratings']; // This is an array of question_id => rating

// Loop through each question and update its rating
foreach ($ratings as $question_id => $rating) {
    // Prepare and execute the update query
    $stmt = $conn->prepare("
        UPDATE evaluations
        SET rating = ?
        WHERE student_id = ? AND prof_id = ? AND question_id = ?
    ");

    if ($stmt === false) {
        // Debug query preparation errors
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('isis', $rating, $student_id, $prof_id, $question_id);

    if (!$stmt->execute()) {
        // Debug query execution errors
        echo json_encode(['success' => false, 'message' => 'Query execution failed: ' . $stmt->error]);
        exit();
    }
}

echo json_encode(['success' => true]);
?>
