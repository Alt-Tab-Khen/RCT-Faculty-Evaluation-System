<?php
include '../../db_connect.php';

// Fetch active questions
$query = "SELECT id, question, category FROM questionnaires WHERE status = 'active' ORDER BY created_at";
$result = $conn->query($query);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

// Return the questions as a JSON response
echo json_encode($questions);
?>
