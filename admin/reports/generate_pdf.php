<?php

require 'vendor/autoload.php'; // Make sure dompdf autoload is here
use Dompdf\Dompdf;

include '../../db_connect.php';

$prof_id = $_GET['prof_id'];
$course = $_GET['course'];

// Fetch the latest report data
$reportQuery = "
    SELECT e.avg_rating, e.num_responses, e.comments, e.evaluation_period, i.first_name, i.last_name
    FROM evaluation_archive_linked e
    JOIN instructor i ON e.prof_id = i.prof_id
    WHERE e.prof_id = ? AND e.course = ?
    ORDER BY e.archived_at DESC
    LIMIT 1";
$stmt = $conn->prepare($reportQuery);
$stmt->bind_param("ss", $prof_id, $course);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if ($report) {
    $prof_name = $report['first_name'] . ' ' . $report['last_name'];

    // HTML content for the PDF with styling and line dividers
    $html = "
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            h3 { text-align: center; font-size: 18px; color: #333; margin-bottom: 15px; }
            .report-item { font-size: 14px; margin: 5px 0; }
            .report-label { font-weight: bold; color: #555; }
            .report-value { color: #333; }
            hr { border: 0; border-top: 1px solid #ddd; margin: 10px 0; }
            .comments-section { font-size: 16px; margin-top: 20px; color: #333; }
            .comment { 
                margin: 5px 0; 
                padding: 8px; 
                background-color: #f1f1f1; 
                border-radius: 5px; 
                word-wrap: break-word; 
                overflow-wrap: break-word; 
                max-width: 100%; 
            }
        </style>

        <h3>Professor Report</h3>
        <hr>
        <p class='report-item'><span class='report-label'>Professor:</span> <span class='report-value'>" . htmlspecialchars($prof_name) . "</span></p>
        <hr>
        <p class='report-item'><span class='report-label'>Course:</span> <span class='report-value'>" . htmlspecialchars($course) . "</span></p>
        <hr>
        <p class='report-item'><span class='report-label'>Evaluation Period:</span> <span class='report-value'>" . htmlspecialchars($report['evaluation_period']) . "</span></p>
        <hr>
        <p class='report-item'><span class='report-label'>Overall Rating:</span> <span class='report-value'>" . round($report['avg_rating'], 2) . "</span></p>
        <hr>
        <p class='report-item'><span class='report-label'>Number of Responses:</span> <span class='report-value'>" . htmlspecialchars($report['num_responses']) . "</span></p>
        <hr>
        <h4 class='comments-section'>Comments:</h4>";

    // Display each comment as "Anonymous"
    $comments = explode("; ", $report['comments']);
    foreach ($comments as $comment) {
        $html .= "<div class='comment'><strong>Anonymous:</strong> " . nl2br(htmlspecialchars(trim($comment))) . "</div>";
    }

    // Initialize Dompdf and load the HTML
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF and output to browser
    $dompdf->render();
    $dompdf->stream("Professor_Report_$prof_id.pdf", ["Attachment" => false]);
} else {
    echo "No report data available.";
}
