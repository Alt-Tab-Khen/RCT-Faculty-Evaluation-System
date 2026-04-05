<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update the edit status
    $query = "UPDATE questionnaire_edits SET status = '$status' WHERE id = $edit_id";
    
    if (mysqli_query($conn, $query)) {
        // If approved, update the original questionnaire with the new question
        if ($status === 'approved') {
            $editQuery = "
                SELECT updated_question, question_id
                FROM questionnaire_edits
                WHERE id = $edit_id
            ";
            $editResult = mysqli_query($conn, $editQuery);
            $editRow = mysqli_fetch_assoc($editResult);
            $updatedQuestion = mysqli_real_escape_string($conn, $editRow['updated_question']);
            $questionId = $editRow['question_id'];

            // Update the question in the questionnaire
            $updateQuery = "UPDATE questionnaires SET question = '$updatedQuestion' WHERE id = $questionId";
            mysqli_query($conn, $updateQuery);
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
?>
