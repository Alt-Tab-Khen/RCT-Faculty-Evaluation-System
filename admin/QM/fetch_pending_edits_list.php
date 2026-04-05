<?php
include '../../db_connect.php';

// Query to get all pending edits with their details
$query = "
    SELECT e.id AS edit_id, e.updated_question, q.question AS original_question, q.category
    FROM questionnaire_edits e
    JOIN questionnaires q ON e.question_id = q.id
    WHERE e.status = 'pending'
";
$result = mysqli_query($conn, $query);
$edits = [];

while ($row = mysqli_fetch_assoc($result)) {
    $edits[] = $row;
}

// Return the list of edits as JSON
echo json_encode(['edits' => $edits]);
?>
