<?php
include '../../db_connect.php'; // Include your database connection

// Start session to access student data
session_start();

// Check if student is logged in and session has the student_id
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Error: Student not logged in.']);
    exit();
}

// Check if prof_id, ratings, and feedback are set
if (!isset($_POST['prof_id']) || !isset($_POST['ratings']) || !is_array($_POST['ratings'])) {
    echo json_encode(['success' => false, 'message' => 'Error: Missing or invalid ratings data.']);
    exit();
}

// Get student ID and prof ID from session and POST data
$student_id = $_SESSION['student_id'];
$prof_id = $_POST['prof_id'];
$ratings = $_POST['ratings'];
$categories = $_POST['categories'];
$feedback = $_POST['feedback'];

// Prepare SQL insert statement for evaluations
$stmt = $conn->prepare("
    INSERT INTO evaluations (student_id, prof_id, question_id, rating, category) 
    VALUES (?, ?, ?, ?, ?)
");

foreach ($ratings as $question_id => $rating) {
    $category = $categories[$question_id]; // Get the corresponding category

    // Bind parameters and execute the query
    $stmt->bind_param('isiss', $student_id, $prof_id, $question_id, $rating, $category);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => "Error: " . $stmt->error]);
        exit();
    }
}

// Close the evaluation insert statement
$stmt->close();

// If feedback is provided, insert it into the comments table
if (!empty($feedback)) {
    $comment_stmt = $conn->prepare("
        INSERT INTO comments (prof_id, student_id, comment) 
        VALUES (?, ?, ?)
    ");
    $comment_stmt->bind_param('sis', $prof_id, $student_id, $feedback);
    if (!$comment_stmt->execute()) {
        echo json_encode(['success' => false, 'message' => "Error: " . $comment_stmt->error]);
        exit();
    }
    $comment_stmt->close();
}

// Close connection
$conn->close();

// Respond with a success message and course for redirection
$course = $_SESSION['course']; // Ensure this is set in the session
echo json_encode(['success' => true, 'redirect_url' => "../$course/clfac.php"]);
exit();
?>
