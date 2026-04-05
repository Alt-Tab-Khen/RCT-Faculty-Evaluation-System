<?php
include '../../db_connect.php'; // Your database connection file

// Fetch categories and their associated questions from the same table
$query = "
    SELECT 
        category, 
        question
    FROM questionnaires 
    WHERE status = 'active'
    ORDER BY category"; // Fetch only active questionnaires, ordered by category

$result = $conn->query($query);

$categories = [];
$current_category = null;

// Loop through the results and group questions by category
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];

        // If the category doesn't exist, create a new one
        if (!isset($categories[$category])) {
            $categories[$category] = [
                'category_name' => $category,
                'questions' => []
            ];
        }

        // Add the question under the current category
        $categories[$category]['questions'][] = [
            'question_text' => $row['question']
        ];
    }
}

// Return the categories and questions as JSON
header('Content-Type: application/json');
echo json_encode(array_values($categories)); // Return as JSON for the frontend
?>
