<?php
include '../../db_connect.php';

session_start();  // Start the session at the beginning of the file

// Assuming the admin's ID is stored in the session as 'id'
if (!isset($_SESSION['id'])) {
    die("Error: admin not logged in.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Design</title>
    <link rel="stylesheet" href="../dashboard/admin.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<style>
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
.modal-body .student-course-container, .modal-body .instructor-faculty-container {
    white-space: nowrap;
    padding: 10px;
}

.student-course-container > div, .instructor-faculty-container > div {
    min-width: 200px;
    margin-right: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    text-align: center;
}
#categoryContainer {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Adjust 200px as needed */
    gap: 15px;
}

.category-box {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    background-color: #f8f9fa; /* Light background for better visibility */
}
.question-item {
    word-wrap: break-word;
    white-space: normal;
    margin-bottom: 10px; /* Optional: add space between questions */
}


</style>
<body>
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col">
            <!-- Button to trigger offcanvas -->
            <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                <img src="../../photo/images.jpg" alt="Menu" class="img-fluid">
            </button>
        </div>
        <div class="col-10 d-flex align-items-center justify-content-between">
            <!-- Countdown Timer Container -->
            <div class="countdown-container">
                <span id="countdown-timer" style="font-weight:bold;"></span>
            </div>
            <!-- Button to trigger modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startEvaluationModal">Start Evaluation</button>
            <button class="btn btn-danger" id="endEvaluationBtn">End Evaluation</button>
        </div>
        <div class="col-1">
            <i class="fa-solid fa-bars" id="hamburger-icon" data-bs-toggle="dropdown" aria-expanded="false"></i>    
            <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../../logout_admin.php">Log Out</a></li>
            </ul>
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
<div class="container-fluid">
    <div class="row align-items-start">
        <!-- Left Side: Total Students and Total Instructors -->
        <div class="col-md-4">
            <div class="card mb-2">
                <h3>Total Students</h3>
                <span id="student-count" class="clickable">0</span>
            </div>
            <div class="card mb-3">
                <h3>Total Instructors</h3>
                <span id="instructor-count" class="clickable">0</span>
            </div>
                        <!-- Add the div that will trigger the modal -->
            <div class="card mb-3 clickable" id="view-questionnaire">
                <h3>Questionnaire</h3>
                <span class="clickable"></span>
            </div>
            <div class="col-md-10 announcement-container">
            <h3>Announcements</h3>
            <div id="announcement-list" class="announcement-list">
                <!-- Announcements will be dynamically added here -->
            </div>
        </div>
        </div>
        <!-- Right Side: Pie Chart -->
        <div class="col-md-6 ">
            <div class="white_background">
                <canvas id="evaluationPieChart"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Modal for viewing categories and questions -->
<div class="modal fade" id="questionnaireModal" tabindex="-1" aria-labelledby="questionnaireModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionnaireModalLabel">View Questionnaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="categoryContainer" class="d-flex flex-wrap justify-content-start">
                        <!-- Categories, questions, and dividers will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal for viewing full announcement -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="announcement-content"></p>
                <p id="announcement-date" class="text-muted small"></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal for showing students per course -->
<div class="modal fade" id="studentCourseModal" tabindex="-1" aria-labelledby="studentCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentCourseModalLabel">Students Per Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="student-course-container d-flex flex-nowrap overflow-auto">
                    <!-- Dynamic content will be added here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for showing instructors per faculty -->
<div class="modal fade" id="instructorFacultyModal" tabindex="-1" aria-labelledby="instructorFacultyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructorFacultyModalLabel">Instructors Per Faculty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="instructor-faculty-container d-flex flex-nowrap overflow-auto">
                    <!-- Dynamic content will be added here -->
                </div>
            </div>
        </div>
    </div>
</div>



        <div class="chart-container">
    <canvas id="studentParticipationChart"></canvas>
</div>




<!-- Evaluation Activation Modal -->
<div class="modal fade" id="startEvaluationModal" tabindex="-1" aria-labelledby="startEvaluationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="startEvaluationLabel">Start Evaluation Period</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Notification Area -->
        <div id="notif-message" style="display: none;"></div>
        <!-- Form for evaluation period selection -->
        <form id="startEvaluationForm">
          <div class="mb-3">
            <label for="evaluationPeriod" class="form-label">Evaluation Period</label>
            <select class="form-select" id="evaluationPeriod" required>
              <option value="custom">Custom (enter number of days)</option>
              <option value="test">Test (enter number of minutes)</option> <!-- Added test option -->
            </select>
          </div>
          <!-- Custom Days Input -->
          <div class="mb-3" id="customPeriodInput" style="display: none;">
            <label for="customDays" class="form-label">Number of Days</label>
            <input type="number" class="form-control" id="customDays" min="1" placeholder="Enter number of days">
          </div>
            <!-- Custom Minutes Input for Test Evaluations -->
            <div class="mb-3" id="customMinutesInput" style="display: none;">
                <label for="customMinutes" class="form-label">Number of Minutes</label>
                <input type="number" class="form-control" id="customMinutes" min="1" placeholder="Enter number of minutes">
            </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <!-- Make this button part of the form submission process -->
        <button type="submit" form="startEvaluationForm" class="btn btn-primary" id="startEvaluationBtn">Start Evaluation</button>
      </div>
    </div>
  </div>
</div>



    

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
   
   <script>
      const hamburgerIcon = document.getElementById('hamburger-icon');

      hamburgerIcon.addEventListener('click', function () {
        this.classList.toggle('active');
      });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Elements for the chart and countdown timer
    const ctx = document.getElementById('studentParticipationChart') ? document.getElementById('studentParticipationChart').getContext('2d') : null;
    const countdownTimer = document.getElementById('countdown-timer');
    let chart, countdownInterval;

 // Function to fetch and update the student participation chart
 function fetchEvaluationData() {
        if (ctx) { // Ensure the chart element exists
            fetch('fetch_evaluation_data.php')
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => item.evaluation_date);
                    const values = data.map(item => item.num_responses);

                    if (chart) {
                        chart.data.labels = labels;
                        chart.data.datasets[0].data = values;
                        chart.update(); // Update the chart with new data
                    } else {
                        chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Number of Students',
                                    data: values,
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1,
                                    maxBarThickness: 50,  // Set max bar thickness to control the width of bars
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,  // Allow flexible height
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0 // Ensure integer steps
                                        }
                                    }
                                },
                                layout: {
                                    padding: {
                                        top: 20,
                                        right: 20,
                                        bottom: 20,
                                        left: 20
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(error => console.error('Error fetching evaluation data:', error));
        }
    }

    // Fetch chart data every 10 seconds
    if (ctx) setInterval(fetchEvaluationData, 10000);
    fetchEvaluationData(); // Initial chart fetch on page load

    // Function to handle ending the evaluation
    function endEvaluation() {
        fetch('end_evaluation.php', { method: 'GET' })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log('Evaluation ended and data archived/reset successfully.');
                } else {
                    console.error('Error ending evaluation: ' + result.message);
                }
            })
            .catch(error => console.error('Error triggering evaluation end:', error));
    }

    // Function to periodically check evaluation status
    function checkEvaluationStatus() {
        fetch('check_evaluation_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.shouldEnd) {
                    endEvaluation(); // Trigger evaluation end/reset if needed
                }
            })
            .catch(error => console.error('Error checking evaluation status:', error));
    }

    setInterval(checkEvaluationStatus, 300000); // Check evaluation status every 5 minutes
    checkEvaluationStatus(); // Initial check on page load

    // Countdown timer function
    function startCountdown(endTime) {
        if (countdownInterval) {
            clearInterval(countdownInterval); // Clear previous countdown
        }

        // Convert endTime to a Date object, if not already
        const endDateTime = new Date(endTime);

        countdownInterval = setInterval(function () {
            const now = new Date(); // Get current time
            const timeLeft = endDateTime - now; // Calculate the difference in milliseconds

            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                // Update the countdown timer text
                countdownTimer.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            } else {
                // If timeLeft is less than or equal to 0, the evaluation is ended
                clearInterval(countdownInterval);
                countdownTimer.textContent = "Evaluation has ended";
                endEvaluation(); // Trigger the evaluation end/reset
            }
        }, 1000); // Update countdown every second
    }

    // Fetch active evaluation period and start countdown
    fetch('fetch_active_evaluation.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const endTime = new Date(data.endTime); // Get end time
                startCountdown(endTime); // Start countdown for active evaluation
            } else {
                countdownTimer.textContent = "No active evaluation.";
            }
        })
        .catch(error => console.error('Error fetching active evaluation:', error));

    // Handle starting a new evaluation
    const startEvaluationForm = document.getElementById('startEvaluationForm');
    const evaluationPeriod = document.getElementById('evaluationPeriod');
    const customPeriodInput = document.getElementById('customPeriodInput');
    const customMinutesInput = document.getElementById('customMinutesInput');
    const customDays = document.getElementById('customDays');
    const customMinutes = document.getElementById('customMinutes');
    const notifMessage = document.getElementById('notif-message');

    // Show/hide custom inputs based on selected period
    evaluationPeriod.addEventListener('change', function () {
        if (evaluationPeriod.value === 'custom') {
            customPeriodInput.style.display = 'block';
            customMinutesInput.style.display = 'none';
            customDays.value = '';
        } else if (evaluationPeriod.value === 'test') {
            customPeriodInput.style.display = 'none';
            customMinutesInput.style.display = 'block';
            customMinutes.value = '';
        } else {
            customPeriodInput.style.display = 'none';
            customMinutesInput.style.display = 'none';
            customDays.value = '';
            customMinutes.value = '';
        }
    });

    // Handle the form submission for starting a new evaluation
    startEvaluationForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const period = evaluationPeriod.value;
        let duration = 0;

        if (period === 'monthly') {
            duration = 30 * 24 * 60; // 30 days in minutes
        } else if (period === 'semester') {
            duration = 120 * 24 * 60; // 120 days in minutes
        } else if (period === 'custom' && customDays.value) {
            duration = parseInt(customDays.value, 10) * 24 * 60; // Custom days in minutes
        } else if (period === 'test' && customMinutes.value) {
            duration = parseInt(customMinutes.value, 10); // Custom minutes for test
        }

        if (duration > 0) {
            // Send AJAX request to start the evaluation
            fetch('start_evaluation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ period: period, minutes: duration })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifMessage.style.display = 'block';
                    notifMessage.innerHTML = '<div class="alert alert-success">Evaluation started successfully!</div>';

                    const endTime = new Date(data.endTime);
                    startCountdown(endTime); // Start countdown

                    setTimeout(() => {
                        notifMessage.style.display = 'none'; // Hide notification
                        const modal = bootstrap.Modal.getInstance(document.getElementById('startEvaluationModal'));
                        modal.hide();
                    }, 3000);
                } else {
                    notifMessage.style.display = 'block';
                    notifMessage.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
                }
            })
            .catch(error => {
                notifMessage.style.display = 'block';
                notifMessage.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            });
        } else {
            notifMessage.style.display = 'block';
            notifMessage.innerHTML = '<div class="alert alert-danger">Please enter a valid number of days or minutes.</div>';
        }
    });
});
</script>

<script>
    document.getElementById('endEvaluationBtn').addEventListener('click', function () {
        if (confirm("Are you sure you want to end the current evaluation?")) {
            fetch('end_evaluation_manual.php', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Evaluation has been ended successfully.');
                        location.reload();  // Reload the page to reflect the changes
                    } else {
                        alert('Error ending evaluation: ' + data.message);
                    }
                })
                .catch(error => alert('Error: ' + error.message));
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to fetch student and instructor count and details
    function fetchData() {
        fetch('fetch_dashboard_data.php') // Adjust path
            .then(response => response.json())
            .then(data => {
                // Update total student count
                document.getElementById('student-count').textContent = data.total_students;

                // Update total instructor count
                document.getElementById('instructor-count').textContent = data.total_instructors;

                // Handle click to show students per course
                document.getElementById('student-count').addEventListener('click', function () {
                    const studentCourseContainer = document.querySelector('.student-course-container');
                    studentCourseContainer.innerHTML = ''; // Clear the list

                    // Populate modal with student count per course
                    data.courses.forEach(course => {
                        const courseDiv = document.createElement('div');
                        courseDiv.innerHTML = `<strong>${course.course} students</strong><br>${course.student_count}`;
                        studentCourseContainer.appendChild(courseDiv);
                    });

                    // Show the modal
                    const studentModal = new bootstrap.Modal(document.getElementById('studentCourseModal'));
                    studentModal.show();
                });

                // Handle click to show instructors per faculty
                document.getElementById('instructor-count').addEventListener('click', function () {
                    const instructorFacultyContainer = document.querySelector('.instructor-faculty-container');
                    instructorFacultyContainer.innerHTML = ''; // Clear the list

                    // Populate modal with instructor count per faculty
                    data.faculties.forEach(faculty => {
                        const facultyDiv = document.createElement('div');
                        facultyDiv.innerHTML = `<strong>${faculty.faculty} instructors</strong><br>${faculty.instructor_count}`;
                        instructorFacultyContainer.appendChild(facultyDiv);
                    });

                    // Show the modal
                    const instructorModal = new bootstrap.Modal(document.getElementById('instructorFacultyModal'));
                    instructorModal.show();
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Call the function to fetch and display the data
    fetchData();
});


</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxPie = document.getElementById('evaluationPieChart').getContext('2d');
    let pieChart = null; // Declare chart instance variable for reuse
    const pollInterval = 10000; // Poll every 10 seconds

    function fetchPieChartData() {
        fetch('fetch_evaluation_pie_data.php')
            .then(response => response.json())
            .then(data => {
                // If the chart already exists, update its data
                if (pieChart) {
                    pieChart.data.datasets[0].data = [data.studentsEvaluated, data.studentsNotEvaluated];
                    pieChart.update(); // Update the chart with new data
                } else {
                    // Create the chart for the first time
                    pieChart = new Chart(ctxPie, {
                        type: 'pie',
                        data: {
                            labels: ['Evaluated', 'Not Evaluated'],
                            datasets: [{
                                data: [data.studentsEvaluated, data.studentsNotEvaluated],
                                backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            maintainAspectRatio: true,
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching pie chart data:', error));
    }

    // Initial fetch when page loads
    fetchPieChartData();

    // Poll the pie chart data every 10 seconds for real-time updates
    setInterval(fetchPieChartData, pollInterval);
});

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to fetch categories and questions
    function fetchCategoriesAndQuestions() {
        fetch('fetch_categories_questions.php')  // Replace with your actual path
            .then(response => response.json())
            .then(data => {
                const categoryContainer = document.getElementById('categoryContainer');
                categoryContainer.innerHTML = ''; // Clear the container before appending new data

                data.forEach(category => {
                    // Create category box container
                    const categoryBox = document.createElement('div');
                    categoryBox.classList.add('category-box');

                    // Add category title
                    const categoryTitle = document.createElement('h5'); // Use h5 for category headings
                    categoryTitle.textContent = category.category_name;
                    categoryTitle.classList.add('category-title'); // Add custom class for styling
                    categoryBox.appendChild(categoryTitle);

                    // Create a list to hold the questions
                    const questionList = document.createElement('ul');
                    questionList.classList.add('question-list'); // Add custom class for questions styling
                    
                    category.questions.forEach(question => {
                        // Create list item for each question
                        const questionItem = document.createElement('li');
                        questionItem.textContent = question.question_text;
                        questionItem.classList.add('question-item'); // Add custom class for each question
                        questionList.appendChild(questionItem);
                    });

                    // Append the questions list to the category box
                    categoryBox.appendChild(questionList);

                    // Append category box to the main container
                    categoryContainer.appendChild(categoryBox);
                });
            })
            .catch(error => console.error('Error fetching categories and questions:', error));
    }

    // Trigger the modal when the div is clicked
    document.getElementById('view-questionnaire').addEventListener('click', function() {
        fetchCategoriesAndQuestions();  // Fetch categories and questions when clicked
        const modal = new bootstrap.Modal(document.getElementById('questionnaireModal'));
        modal.show();  // Show the modal
    });
});

</script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    function fetchAnnouncements() {
        fetch('fetch_announcements.php')  // Adjust the path to your PHP file
            .then(response => response.json())
            .then(data => {
                const announcementList = document.getElementById('announcement-list');
                announcementList.innerHTML = '';  // Clear the list

                // Loop through the announcements and add them to the list
                data.forEach(announcement => {
                    const announcementItem = document.createElement('div');
                    announcementItem.classList.add('announcement-item');
                    announcementItem.innerHTML = `
                        <div class="announcement-header">
                            <span>${announcement.content.slice(0, 100)}...</span> 
                            <small class="text-muted">${new Date(announcement.created_at).toLocaleDateString()}</small>
                        </div>
                    `;
                    
                    // Add click event to show full announcement in a modal
                    announcementItem.addEventListener('click', function () {
                        document.getElementById('announcement-content').textContent = announcement.content;
                        document.getElementById('announcement-date').textContent = new Date(announcement.created_at).toLocaleString();
                        const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
                        modal.show();
                    });

                    announcementList.appendChild(announcementItem);
                });
            })
            .catch(error => console.error('Error fetching announcements:', error));
    }

    fetchAnnouncements();  // Call the function to fetch announcements
});

</script>



</body>
</html>
