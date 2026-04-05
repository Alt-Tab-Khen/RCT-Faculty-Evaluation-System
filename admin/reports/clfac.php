<?php

session_start();
include '../../db_connect.php';

if (!isset($_SESSION['id'])) {
    die("Error: admin not logged in.");
}

// Fetch total evaluators from completed evaluation periods
$totalEvaluatorsQuery = "SELECT SUM(total_respondents) as total_evaluators FROM evaluation_periods WHERE status = 'completed'";
$totalEvaluatorsResult = $conn->query($totalEvaluatorsQuery);
$totalEvaluatorsData = $totalEvaluatorsResult->fetch_assoc();
$total_evaluators = $totalEvaluatorsData['total_evaluators'] ?? 0; // Use ?? 0 in case there's no data

// Fetch individual instructor data
$query = "SELECT e.prof_id, e.avg_rating, e.num_responses, e.course, i.first_name, i.last_name
          FROM evaluation_archive_linked e
          JOIN instructor i ON e.prof_id = i.prof_id
          WHERE e.archived_at = (SELECT MAX(archived_at) FROM evaluation_archive_linked WHERE prof_id = e.prof_id AND course = e.course)
          ORDER BY e.archived_at DESC";
$result = $conn->query($query);

$course_data = [];
$total_ratings_sum = 0;
$total_ratings_count = 0;

while ($row = $result->fetch_assoc()) {
    $course = $row['course'];
    $prof_name = $row['first_name'] . ' ' . $row['last_name'];
    $avg_rating = $row['avg_rating'];
    $num_responses = $row['num_responses'];
    $prof_id = $row['prof_id'];

    $total_ratings_sum += $avg_rating;
    $total_ratings_count++;

    if (!isset($course_data[$course])) {
        $course_data[$course] = [
            'professors' => [],
            'total_ratings_sum' => 0,
            'total_ratings_count' => 0
        ];
    }

    // Avoid duplicate professors under the same course
    $professor_exists = false;
    foreach ($course_data[$course]['professors'] as $professor) {
        if ($professor['prof_id'] === $prof_id) {
            $professor_exists = true;
            break;
        }
    }

    if (!$professor_exists) {
        $course_data[$course]['professors'][] = [
            'prof_name' => $prof_name,
            'avg_rating' => $avg_rating,
            'prof_id' => $prof_id,
            'course' => $course
        ];
    }

    $course_data[$course]['total_ratings_sum'] += $avg_rating;
    $course_data[$course]['total_ratings_count']++;
}

$total_avg_rating = ($total_ratings_count > 0) ? $total_ratings_sum / $total_ratings_count : 0;

?>




<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_report'])) {
    $prof_id = $_POST['prof_id'];
    $course = $_POST['course'];

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

        echo "<div class='report-container'>";
        echo "<h3 class='report-title'>Professor Report</h3>";
        
        echo "<div class='report-item'><span class='report-label'>Professor:</span><span class='report-value'>" . htmlspecialchars($prof_name) . "</span></div>";
        echo "<div class='report-item'><span class='report-label'>Course:</span><span class='report-value'>" . htmlspecialchars($course) . "</span></div>";
        echo "<div class='report-item'><span class='report-label'>Evaluation Period:</span><span class='report-value'>" . htmlspecialchars($report['evaluation_period']) . "</span></div>";
        echo "<div class='report-item'><span class='report-label'>Overall Rating:</span><span class='report-value'>" . round($report['avg_rating'], 2) . "</span></div>";
        echo "<div class='report-item'><span class='report-label'>Number of Responses:</span><span class='report-value'>" . htmlspecialchars($report['num_responses']) . "</span></div>";

        echo "<h4 class='comments-section'>Comments:</h4>";
        $comments = explode("; ", $report['comments']);
        foreach ($comments as $comment) {
            echo "<div class='comment'><strong>Anonymous:</strong> " . nl2br(htmlspecialchars(trim($comment))) . "</div>";
        }

        echo '<button id="generatePdfBtn" class="btn-generate-pdf">Generate PDF</button>';
        echo "</div>";
    } else {
        echo "<p>No report data available for this professor.</p>";
    }

    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Design</title>
    <link rel="stylesheet" href="clfac.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<style>

    /* General styling */
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .title {
        text-align: center;
        margin-bottom: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #333;
    }

    .course-title {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border-radius: 5px;
        font-size: 18px;
    }

    .professor-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 15px;
        border-bottom: 1px solid #ddd;
        font-size: 16px;
    }

    .professor-row:last-child {
        border-bottom: none;
    }

    .professor-row span {
        font-weight: bold;
    }

    .total-summary {
        margin-top: 30px;
        padding: 15px;
        background-color: #e9ecef;
        border-radius: 10px;
        text-align: center;
    }

    /* Report modal styling */
    .report-container {
        padding: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .report-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    .report-item {
        font-size: 16px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
    }

    .report-item:last-child {
        border-bottom: none;
    }

    .report-label {
        font-weight: bold;
        color: #555;
    }

    .report-value {
        color: #333;
    }

    /* Comments section */
    .comments-section {
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .comment {
        margin-bottom: 15px;
        padding: 10px;
        border-left: 4px solid #007bff;
        background-color: #f1f1f1;
        border-radius: 4px;
    }

    /* Generate PDF button styling */
    .btn-generate-pdf {
        display: block;
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    .btn-generate-pdf:hover {
        background-color: #0056b3;
    }
    .comment {
    background-color: #f8f9fa; /* Light background for each comment */
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 5px;
    word-wrap: break-word; /* Ensures long text wraps */
    white-space: normal; /* Prevents text overflow */
}

/* Pull-tab button styling */
.menu-button {
    position: fixed;
    top: 50%;
    left: 0;
    transform: translateY(-50%); /* Lock vertical position at center */
    background-color: blue;
    color: white;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1050;
    border-radius: 0px 10px 10px 0px; /* Optional: rounded corners on the right */
    writing-mode: vertical-rl; /* Makes the text vertical */
    text-orientation: upright; /* Keeps text orientation upright */
    font-size: 20px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: left 0.28s ease-in-out; /* Only apply horizontal transition */
}

/* Offcanvas adjustments */
.offcanvas-start {
    width: 400px;
    transition: transform 0.3s ease-in-out;
}

/* When the drawer is open, move the button horizontally */
.drawer-open .menu-button {
    left: 450px; /* Move button horizontally without affecting vertical alignment */
}


    </style>
<body>
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                  <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Report</span>
                </button>
            </div>
        </div>
    </div>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#" id="n2" >Home</a>
          </li>   
          <li class="nav-item">          
            <a class="nav-link active btn"  data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
           Create Announcement
          </a>
        </li> 
          

      <!-- Create Announcement Button -->

          <li class="nav-item dropdown">
            <a class="fa-solid fa-bars" id="hamburger-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>    
            <ul class="dropdown-menu dropdown-menu-lg-end">
            <li><a class="dropdown-item" href="../../logout_admin.php">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>

    </div>
  </nav>

  <div class="container">
    <h1 class="title">RCT Evaluation System Result</h1>

    <!-- Dropdown for Date Filter -->
    <div class="date-filter-container mb-3">
        <select id="dateDropdown" class="form-select">
            <option value="" disabled selected>Select a date</option>
            <?php
            // Fetch unique dates from `evaluation_archive_linked`
            $dateQuery = "SELECT DISTINCT DATE(archived_at) as archived_date FROM evaluation_archive_linked ORDER BY archived_date DESC";
            $dateResult = $conn->query($dateQuery);

            if ($dateResult->num_rows > 0) {
                while ($row = $dateResult->fetch_assoc()) {
                    $archived_date = $row['archived_date'];
                    echo "<option value='$archived_date'>" . htmlspecialchars($archived_date) . "</option>";
                }
            }
            ?>
        </select>
    </div>

    <!-- Container for dynamic content -->
    <div id="evaluationResults">
        <div class="total-summary">
            <p>Total Evaluators: <?= $total_evaluators ?></p>
            <p>Total Average Rating: <?= round($total_avg_rating, 2) ?></p>
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
    </div>
</div>


<!-- Modal for Displaying Detailed Report -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Detailed Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reportContent">
                <!-- Report content will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>




  <!-- Menu "Pull-Tab" Button -->
  <div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" aria-controls="adminOffcanvas">
  <span>Menu</span>
</div>

<!-- Offcanvas Drawer for Admin -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminOffcanvasLabel">RCT Faculty Evaluation System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="but">
            <ul class="dropdown" id="it">
                <li><a type="button" class="btn" id="cl" href="../dashboard/admin.php">Dashboard</a></li>
                <li><a class="btn" id="cl" href="../classbscs/clfac.php" role="button">Class Management</a></li>
                <li><a class="btn" id="cl" href="../faculty/clfac.php" role="button">Faculty Management</a></li>
                <li><a href="../QM/clfac.php" type="button" class="btn" id="cl">Questionnaire Management</a></li>
                <li><a href="../reports/clfac.php" type="button" class="btn" id="cl">Reports</a></li>
            </ul>
        </div>
    </div>
</div>


<!-- Modal for Creating Announcements -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createAnnouncementLabel">Create Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Success/Error message handler -->
        <div id="announcementMessage" style="display: none;" class="alert" role="alert"></div>

        <form id="announcementForm">
          <div class="mb-3">
            <label for="announcement-content" class="form-label">Announcement</label>
            <textarea class="form-control" id="announcement-content" name="announcement_content" rows="4" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Announcement</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <script>
const menuButton = document.querySelector('.menu-button');
const offcanvasElement = document.getElementById('adminOffcanvas');

// Add 'drawer-open' class to the body when the drawer opens
offcanvasElement.addEventListener('show.bs.offcanvas', function () {
    document.body.classList.add('drawer-open');
});

// Remove 'drawer-open' class from the body when the drawer closes
offcanvasElement.addEventListener('hide.bs.offcanvas', function () {
    document.body.classList.remove('drawer-open');
});
</script>
  <!-- Script for Handling Announcement Submission -->
  <script>
document.getElementById('announcementForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent normal form submission

    const formData = new FormData(this);

    // Send the AJAX request
    fetch('save_announcement.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('announcementMessage');

        if (data.success) {
            // Display the success message inside the modal
            messageDiv.className = 'alert alert-success'; // Style as success
            messageDiv.innerText = 'Announcement created successfully!';
            messageDiv.style.display = 'block'; // Make the message visible

            // Automatically hide the modal after 3-5 seconds
            setTimeout(() => {
                const announcementModal = bootstrap.Modal.getInstance(document.getElementById('createAnnouncementModal'));
                announcementModal.hide();
                messageDiv.style.display = 'none'; // Hide the message after closing the modal
                document.getElementById('announcementForm').reset(); // Reset the form
            }, 3000); // Adjust this timing if needed
        } else {
            // Display an error message inside the modal
            messageDiv.className = 'alert alert-danger'; // Style as error
            messageDiv.innerText = 'Failed to create announcement. Please try again.';
            messageDiv.style.display = 'block'; // Make the message visible

            // Optionally hide the error message after 5 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none'; // Hide the message after delay
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const messageDiv = document.getElementById('announcementMessage');
        messageDiv.className = 'alert alert-danger';
        messageDiv.innerText = 'An error occurred. Please try again later.';
        messageDiv.style.display = 'block';
        
        // Hide the error message after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    });
});


</script>
<script>
// Event listener for "View Report" buttons
document.querySelectorAll('.view-report-btn').forEach(button => {
    button.addEventListener('click', function () {
        const profId = this.getAttribute('data-prof-id');
        const course = this.getAttribute('data-course');

        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                fetch_report: true,
                prof_id: profId,
                course: course
            })
        })
        .then(response => response.text())
        .then(data => {
            // Update the modal content with the fetched report data
            document.getElementById('reportContent').innerHTML = data;

            // Update the "Generate PDF" button attributes dynamically
            const generatePdfBtn = document.getElementById('generatePdfBtn');
            if (generatePdfBtn) {
                generatePdfBtn.setAttribute('data-prof-id', profId);
                generatePdfBtn.setAttribute('data-course', course);
            }

            // Show the modal
            new bootstrap.Modal(document.getElementById('reportModal')).show();
        })
        .catch(error => console.error('Error:', error));
    });
});

// Event listener for the "Generate PDF" button inside the modal
document.getElementById('reportModal').addEventListener('click', function (event) {
    if (event.target.id === 'generatePdfBtn') {
        const profId = event.target.getAttribute('data-prof-id');
        const course = event.target.getAttribute('data-course');

        if (profId && course) {
            // Open the PDF generation link in a new tab
            window.open(`generate_pdf.php?prof_id=${profId}&course=${encodeURIComponent(course)}`, '_blank');
        } else {
            console.error('Missing prof_id or course for PDF generation.');
        }
    }
});

// Dropdown date filter logic
document.getElementById('dateDropdown').addEventListener('change', function () {
    const selectedDate = this.value;

    // Send AJAX request to fetch data for the selected date
    fetch('fetch_records_by_date.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ date: selectedDate })
    })
    .then(response => response.text())
    .then(data => {
        // Replace only the dynamic content within the evaluationResults container
        document.getElementById('evaluationResults').innerHTML = data;

        // Reinitialize event listeners for newly loaded "View Report" buttons
        initializeViewReportButtons();
    })
    .catch(error => console.error('Error:', error));
});

// Function to reinitialize event listeners for dynamically loaded "View Report" buttons
function initializeViewReportButtons() {
    document.querySelectorAll('.view-report-btn').forEach(button => {
        button.addEventListener('click', function () {
            const profId = this.getAttribute('data-prof-id');
            const course = this.getAttribute('data-course');

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    fetch_report: true,
                    prof_id: profId,
                    course: course
                })
            })
            .then(response => response.text())
            .then(data => {
                // Update the modal content with the fetched report data
                document.getElementById('reportContent').innerHTML = data;

                // Update the "Generate PDF" button attributes dynamically
                const generatePdfBtn = document.getElementById('generatePdfBtn');
                if (generatePdfBtn) {
                    generatePdfBtn.setAttribute('data-prof-id', profId);
                    generatePdfBtn.setAttribute('data-course', course);
                }

                // Show the modal
                new bootstrap.Modal(document.getElementById('reportModal')).show();
            })
            .catch(error => console.error('Error:', error));
        });
    });
}

// Initialize the event listeners for the first load
initializeViewReportButtons();

</script>

</body>
</html>
