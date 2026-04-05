<?php
session_start();
include '../../db_connect.php';

// Prepare the response
$response = ['success' => false, 'message' => 'Something went wrong'];

// Check if prof_id is in session
if (!isset($_SESSION['prof_id'])) {
    $response['message'] = 'Error: Professor not logged in.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
    $updated_question = mysqli_real_escape_string($conn, $_POST['updated_question']);
    $prof_id = $_SESSION['prof_id']; // Get prof_id from session

    // Check if the prof_id needs the 'prof_' prefix
    if (strpos($prof_id, 'prof_') === false) {
        $prof_id = 'prof_' . $prof_id;
    }

    // Insert the edit request into the questionnaire_edits table
    $query = "INSERT INTO questionnaire_edits (question_id, prof_id, updated_question, status) 
              VALUES ('$question_id', '$prof_id', '$updated_question', 'pending')";
    
    if (mysqli_query($conn, $query)) {
        $response['success'] = true;
        $response['message'] = 'Edit submitted for approval.';
    } else {
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
