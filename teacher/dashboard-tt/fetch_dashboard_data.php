<?php
include '../../db_connect.php';
session_start();

if (!isset($_GET['prof_id'])) {
    echo json_encode(['error' => 'Professor ID is missing']);
    exit;
}

$prof_id = $_GET['prof_id'];

$formatted_prof_id = 'prof_' . $prof_id;

// Initialize response data array
$response = [
    'total_evaluators' => 0,
    'overall_rating' => 0,
    'comments' => []
];

// Fetch total evaluators for the professor
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT student_id) AS total_evaluators 
    FROM evaluations 
    WHERE prof_id = ? OR prof_id = ?");
$stmt->bind_param("ss", $prof_id, $formatted_prof_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $response['total_evaluators'] = $row['total_evaluators'];
}
$stmt->close();

// Fetch overall rating for the professor
$stmt = $conn->prepare("
    SELECT AVG(rating) AS overall_rating 
    FROM evaluations 
    WHERE prof_id = ? OR prof_id = ?");
$stmt->bind_param("ss", $prof_id, $formatted_prof_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $response['overall_rating'] = round($row['overall_rating'], 2); // Round to 2 decimal places
}
$stmt->close();

// Fetch comments for the professor
function containsBadWords($text) {
    $badWordsFile = '../comment/badwords.txt'; // This file contains a list of harmful words
    $badWords = file_exists($badWordsFile) ? file($badWordsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : ['bobo', 'tanga', 'inutil'];

    foreach ($badWords as $word) {
        if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
            return true;
        }
    }
    return false;
}

$stmt = $conn->prepare("SELECT comment, student_id FROM comments WHERE prof_id = ? OR prof_id = ?");
$stmt->bind_param("ss", $prof_id, $formatted_prof_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $isBlurred = containsBadWords($row['comment']);
    $response['comments'][] = [
        'comment' => $row['comment'],
        'blurred' => $isBlurred,
        'author' => 'Anonymous' . substr(md5($row['student_id']), 0, 6) // Generate anonymous author names
    ];
}

$stmt->close();
$conn->close();

// Return response as JSON
echo json_encode($response);
?>
