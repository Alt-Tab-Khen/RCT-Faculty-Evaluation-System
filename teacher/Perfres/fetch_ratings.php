<?php
include '../../db_connect.php';

if (isset($_GET['prof_id'])) {
    $prof_id = $_GET['prof_id'];

    // Fetch overall rating for the specific professor
    $overallQuery = "SELECT AVG(rating) as overall_rating FROM evaluations WHERE prof_id = ? OR prof_id = ?";
    $stmt = $conn->prepare($overallQuery);
    $formatted_prof_id = 'prof_' . $prof_id; // Adding this line to handle different formats
    $stmt->bind_param("ss", $prof_id, $formatted_prof_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $overallRating = $result->fetch_assoc()['overall_rating'];

    // Fetch category ratings for the specific professor
    $categoryQuery = "SELECT category, AVG(rating) as category_rating FROM evaluations WHERE prof_id = ? OR prof_id = ? GROUP BY category";
    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param("ss", $prof_id, $formatted_prof_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'name' => $row['category'],
            'rating' => $row['category_rating']
        ];
    }

    // Return data as JSON
    echo json_encode([
        'overall_rating' => $overallRating,
        'categories' => $categories
    ]);
} else {
    echo json_encode(['error' => 'Missing prof_id']);
}

?>
