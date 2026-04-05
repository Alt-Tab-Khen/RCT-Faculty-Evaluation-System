<?php
include '../../db_connect.php';
session_start();

if (!isset($_SESSION['prof_id'])) {
    die(json_encode(['error' => 'Professor not logged in.']));
}

$prof_id = $_SESSION['prof_id'];

if (strpos($prof_id, 'prof_') === false) {
    $prof_id = 'prof_' . $prof_id;
}

// Function to detect and censor harmful words
function containsBadWords($text) {
    $badWordsFile = 'badwords.txt'; 
    $badWords = file_exists($badWordsFile) ? file($badWordsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : ['bobo', 'tanga', 'inutil'];

    foreach ($badWords as $word) {
        if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
            return true;
        }
    }
    return false;
}

// Fetch comments
$stmt = $conn->prepare("SELECT comment, student_id FROM comments WHERE prof_id = ?");
$stmt->bind_param("s", $prof_id);
$stmt->execute();
$result = $stmt->get_result();
$comments = [];

while ($row = $result->fetch_assoc()) {
    $isBlurred = containsBadWords($row['comment']);
    $comments[] = [
        'comment' => $row['comment'],
        'blurred' => $isBlurred,
        'author' => 'Anonymous' . substr(md5($row['student_id']), 0, 6)
    ];
}
$stmt->close();

echo json_encode($comments);
