<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];

    // Fetch total evaluators for the selected date
    $totalEvaluatorsQuery = "SELECT SUM(num_responses) as total_evaluators 
                             FROM evaluation_archive_linked 
                             WHERE DATE(archived_at) = ?";
    $stmt = $conn->prepare($totalEvaluatorsQuery);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $totalEvaluatorsResult = $stmt->get_result()->fetch_assoc();
    $total_evaluators = $totalEvaluatorsResult['total_evaluators'] ?? 0;

    // Fetch course and professor data for the selected date
    $query = "SELECT e.prof_id, e.avg_rating, e.num_responses, e.course, i.first_name, i.last_name
              FROM evaluation_archive_linked e
              JOIN instructor i ON e.prof_id = i.prof_id
              WHERE DATE(e.archived_at) = ?
              ORDER BY e.course, e.prof_id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $course_data = [];
    while ($row = $result->fetch_assoc()) {
        $course = $row['course'];
        $prof_name = $row['first_name'] . ' ' . $row['last_name'];
        $avg_rating = $row['avg_rating'];
        $num_responses = $row['num_responses'];
        $prof_id = $row['prof_id'];

        if (!isset($course_data[$course])) {
            $course_data[$course] = [
                'professors' => [],
                'total_ratings_sum' => 0,
                'total_ratings_count' => 0
            ];
        }

        $course_data[$course]['professors'][] = [
            'prof_name' => $prof_name,
            'avg_rating' => $avg_rating,
            'num_responses' => $num_responses,
            'prof_id' => $prof_id
        ];

        $course_data[$course]['total_ratings_sum'] += $avg_rating;
        $course_data[$course]['total_ratings_count']++;
    }

    // Render the updated content
    ob_start();
    ?>
    <div class="total-summary">
        <p>Total Evaluators: <?= htmlspecialchars($total_evaluators) ?></p>
        <p>Total Average Rating: <?= htmlspecialchars(round($total_evaluators > 0 ? array_sum(array_column($course_data, 'total_ratings_sum')) / $total_evaluators : 0, 2)) ?></p>
    </div>

    <?php if (!empty($course_data)): ?>
        <?php foreach ($course_data as $course => $data): ?>
            <hr class="collapse-divider">
            <h2 class="course-title">Course: <?= htmlspecialchars($course) ?> | Overall Course Rating: <?= round($data['total_ratings_sum'] / $data['total_ratings_count'], 2) ?></h2>
            <ul>
                <?php foreach ($data['professors'] as $prof): ?>
                    <li class="professor-row">
                        <span><?= htmlspecialchars($prof['prof_name']) ?></span>
                        <button class="btn btn-info view-report-btn" 
                                data-prof-id="<?= $prof['prof_id'] ?>" 
                                data-course="<?= htmlspecialchars($course) ?>">
                            View Report
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No data available for courses or professors.</p>
    <?php endif; ?>
    <?php
    echo ob_get_clean();
}
?>
