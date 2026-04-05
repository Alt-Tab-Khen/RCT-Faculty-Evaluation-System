<?php
include '../../db_connect.php'; // Your database connection

// Query to get participants from active evaluations
$activeQuery = "
    SELECT DATE(created_at) as evaluation_date, COUNT(DISTINCT student_id) as num_responses
    FROM evaluations
    GROUP BY DATE(created_at)
";

// Modified query to get participants from completed evaluation periods using total_respondents in evaluation_periods
$archivedQuery = "
    SELECT CONCAT(start_date, ' to ', end_date) as evaluation_date, total_respondents as num_responses
    FROM evaluation_periods
    WHERE status = 'completed'
";

// Execute both queries
$activeResult = $conn->query($activeQuery);
$archivedResult = $conn->query($archivedQuery);

$data = [];

// Use an associative array to combine data by evaluation date and avoid duplication
$dateWiseData = [];

// Process active evaluations
if ($activeResult->num_rows > 0) {
    while ($row = $activeResult->fetch_assoc()) {
        $evaluationDate = $row['evaluation_date'];
        $numResponses = $row['num_responses'];

        if (!isset($dateWiseData[$evaluationDate])) {
            $dateWiseData[$evaluationDate] = 0;
        }
        $dateWiseData[$evaluationDate] += $numResponses;
    }
}

// Process archived evaluations from evaluation_periods
if ($archivedResult->num_rows > 0) {
    while ($row = $archivedResult->fetch_assoc()) {
        $evaluationDate = $row['evaluation_date'];
        $numResponses = $row['num_responses'];

        if (!isset($dateWiseData[$evaluationDate])) {
            $dateWiseData[$evaluationDate] = 0;
        }
        $dateWiseData[$evaluationDate] += $numResponses;
    }
}

// Convert associative array back to indexed array for JSON response
foreach ($dateWiseData as $evaluationDate => $numResponses) {
    $data[] = [
        'evaluation_date' => $evaluationDate,
        'num_responses' => $numResponses
    ];
}

header('Content-Type: application/json');
echo json_encode($data); // Return combined data for Chart.js
?>
