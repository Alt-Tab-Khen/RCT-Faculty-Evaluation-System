<?php
include '../../db_connect.php';

if (isset($_GET['prof_id'])) {
    $prof_id = $_GET['prof_id'];
    
    // Fetch historical ratings from the evaluation_archive_linked table
    $query = "SELECT DATE(archived_at) as evaluation_date, AVG(avg_rating) as avg_rating 
              FROM evaluation_archive_linked
              WHERE prof_id = ? OR prof_id = ?
              GROUP BY DATE(archived_at)
              ORDER BY evaluation_date ASC";
    $stmt = $conn->prepare($query);
    $formatted_prof_id = 'prof_' . $prof_id;
    $stmt->bind_param("ss", $prof_id, $formatted_prof_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[$row['evaluation_date']] = [
            'evaluation_date' => $row['evaluation_date'],
            'avg_rating' => $row['avg_rating']
        ];
    }

    // Fetch current ratings from the evaluations table
    $query = "SELECT DATE(created_at) as evaluation_date, AVG(rating) as avg_rating 
              FROM evaluations 
              WHERE prof_id = ? OR prof_id = ? 
              GROUP BY DATE(created_at)
              ORDER BY evaluation_date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $prof_id, $formatted_prof_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $date = $row['evaluation_date'];
        if (isset($data[$date])) {
            // Average the existing value with the new one from current evaluations
            $data[$date]['avg_rating'] = ($data[$date]['avg_rating'] + $row['avg_rating']) / 2;
        } else {
            $data[$date] = [
                'evaluation_date' => $date,
                'avg_rating' => $row['avg_rating']
            ];
        }
    }

    // Sort data by date in ascending order
    usort($data, function($a, $b) {
        return strcmp($a['evaluation_date'], $b['evaluation_date']);
    });

    // Return combined data as JSON
    echo json_encode(array_values($data));
} else {
    echo json_encode(['error' => 'Missing prof_id']);
}
?>

