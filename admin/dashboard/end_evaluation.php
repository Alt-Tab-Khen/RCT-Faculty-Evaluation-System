<?php
include '../../db_connect.php'; // Database connection

// Check if there is an active evaluation period
$query = "SELECT * FROM evaluation_periods WHERE status = 'active'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($evaluation = $result->fetch_assoc()) {
        // Define evaluation period
        $start_date = $evaluation['start_date'];
        $end_date = $evaluation['end_date'];
        $evaluation_period = $start_date . ' to ' . $end_date;
        $evaluation_period_id = $evaluation['id']; // Store the ID for updating total respondents later

        // Fetch the total number of unique respondents for the evaluation period
        $respondentsQuery = "
            SELECT COUNT(DISTINCT student_id) AS total_respondents
            FROM evaluations
            WHERE created_at BETWEEN ? AND ?";
        $respondentsStmt = $conn->prepare($respondentsQuery);
        $respondentsStmt->bind_param("ss", $start_date, $end_date);
        $respondentsStmt->execute();
        $respondentsResult = $respondentsStmt->get_result();
        $respondentsData = $respondentsResult->fetch_assoc();
        $totalRespondents = $respondentsData['total_respondents'];

        // Update the total respondents in the evaluation_periods table
        $updateTotalRespondentsQuery = "UPDATE evaluation_periods SET total_respondents = ? WHERE id = ?";
        $updateTotalRespondentsStmt = $conn->prepare($updateTotalRespondentsQuery);
        $updateTotalRespondentsStmt->bind_param("ii", $totalRespondents, $evaluation_period_id);
        $updateTotalRespondentsStmt->execute();

        // Now process each professor, course, and additional details as needed for archival
        $profQuery = "
            SELECT DISTINCT e.prof_id, i.first_name, i.last_name
            FROM evaluations e
            JOIN instructor i ON e.prof_id = i.prof_id
            WHERE e.created_at BETWEEN ? AND ?";
        $profStmt = $conn->prepare($profQuery);
        $profStmt->bind_param("ss", $start_date, $end_date);
        $profStmt->execute();
        $profResult = $profStmt->get_result();

        while ($professor = $profResult->fetch_assoc()) {
            $prof_id = $professor['prof_id'];
            $prof_name = $professor['first_name'] . ' ' . $professor['last_name'];

            // Fetch all distinct courses that the professor was evaluated for during this evaluation period
            $courseQuery = "
                SELECT DISTINCT s.course
                FROM evaluations e
                JOIN student s ON e.student_id = s.student_id
                WHERE e.prof_id = ? AND e.created_at BETWEEN ? AND ?";
            $courseStmt = $conn->prepare($courseQuery);
            $courseStmt->bind_param("sss", $prof_id, $start_date, $end_date);
            $courseStmt->execute();
            $courseResult = $courseStmt->get_result();

            while ($courseData = $courseResult->fetch_assoc()) {
                $course = $courseData['course'];

                // Calculate the average rating and number of unique student responses for this professor per course
                $avgRatingQuery = "
                    SELECT AVG(e.rating) AS avg_rating, COUNT(DISTINCT e.student_id) AS num_responses
                    FROM evaluations e
                    JOIN student s ON e.student_id = s.student_id
                    WHERE e.prof_id = ? AND s.course = ? AND e.created_at BETWEEN ? AND ?";
                $avgRatingStmt = $conn->prepare($avgRatingQuery);
                $avgRatingStmt->bind_param("ssss", $prof_id, $course, $start_date, $end_date);
                $avgRatingStmt->execute();
                $ratingResult = $avgRatingStmt->get_result();
                $ratingData = $ratingResult->fetch_assoc();

                // Fetch comments for each professor per course in this evaluation period
                $commentsQuery = "
                    SELECT GROUP_CONCAT(comment SEPARATOR '; ') AS all_comments 
                    FROM comments 
                    WHERE prof_id = ? AND created_at BETWEEN ? AND ?";
                $commentsStmt = $conn->prepare($commentsQuery);
                $commentsStmt->bind_param("sss", $prof_id, $start_date, $end_date);
                $commentsStmt->execute();
                $commentsResult = $commentsStmt->get_result();
                $commentsData = $commentsResult->fetch_assoc();
                $comments_string = $commentsData['all_comments'];

                // Insert data into the evaluation_archive_linked table, including comments and individual course data
                $archiveQuery = "
                    INSERT INTO evaluation_archive_linked (prof_id, evaluation_period, avg_rating, num_responses, course, comments) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                $archiveStmt = $conn->prepare($archiveQuery);
                $archiveStmt->bind_param("ssdiss", $prof_id, $evaluation_period, $ratingData['avg_rating'], $ratingData['num_responses'], $course, $comments_string);
                $archiveStmt->execute();
            }
        }

        // Now delete evaluations and comments for the archived period
        $deleteEvaluationsQuery = "DELETE FROM evaluations WHERE created_at BETWEEN ? AND ?";
        $deleteEvaluationsStmt = $conn->prepare($deleteEvaluationsQuery);
        $deleteEvaluationsStmt->bind_param("ss", $start_date, $end_date);
        $deleteEvaluationsStmt->execute();

        $deleteCommentsQuery = "DELETE FROM comments WHERE created_at BETWEEN ? AND ?";
        $deleteCommentsStmt = $conn->prepare($deleteCommentsQuery);
        $deleteCommentsStmt->bind_param("ss", $start_date, $end_date);
        $deleteCommentsStmt->execute();
    }

    // Update the evaluation period status to completed
    $updateQuery = "UPDATE evaluation_periods SET status = 'completed' WHERE status = 'active'";
    $conn->query($updateQuery);

    echo json_encode(['success' => true, 'message' => 'Evaluation has been ended successfully, data archived, and data deleted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No active evaluation found.']);
}
?>
