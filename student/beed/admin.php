<?php
include '../../db_connect.php';
session_start();
if (isset($_SESSION['student_id'])) {
    echo "Student ID: " . $_SESSION['student_id'];
} else {
    header("Location: ../../login_student/login_stu.php"); // Redirect to login if not logged in
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Design</title>
    <link rel="stylesheet" href="../bscs/admin.css?v=<?php echo time(); ?>">
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
</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                    <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">BSEED</span>
                </button>
            </div>
        </div>
    </div>
<!-- Notification Bell -->
<div class="dropdown">
  <i class="fa-solid fa-bell" id="notif-bell" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 24px; cursor: pointer;"></i>
  <ul class="dropdown-menu dropdown-menu-end" id="notification-list">
    <li><span class="dropdown-item">No new announcements</span></li> <!-- Default if no announcements -->
  </ul>
</div>
<!-- Offcanvas Drawer with Bootstrap -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasDrawer" aria-labelledby="offcanvasDrawerLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasDrawerLabel">RCT Faculty Evaluation System</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="but">
      <ul class="dropdown" id="it">
        <li><a href="../beed/admin.php" type="button" class="btn" id="cl">Dashboard</a></li>
        <li><a href="../beed/clfac.php" type="button" class="btn" id="cl">Instructor List</a></li>
        <li><a href="../history/clfac.php" type="button" class="btn" id="cl">History</a></li>
      </ul>
    </div>
  </div>
</div>

<!-- Menu "Pull-Tab" Button -->
<div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDrawer" aria-controls="offcanvasDrawer">
  <span>Menu</span>
</div>

    <div class="container mt-4">
        <div class="row">
            <!-- Instructor Count Section (Clickable) -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div id="instructor-count" class="p-5 bg-light text-center border rounded" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#instructorListModal">
                    <h5>Total Instructors</h5>
                    <h2 id="total-instructors">Loading...</h2>
                </div>
            </div>

            <!-- Evaluation Progress Chart -->
            <div class="col-lg-8 col-md-6 mb-3">
                <div class="p-2 bg-light text-center border rounded">
                    <h5>Evaluation Progress</h5>
                    <canvas id="evaluationProgressChart" style="max-height: 150px;"></canvas> <!-- Chart.js Canvas -->
                </div>
            </div>
        </div>

        <!-- Scrollable History Container -->
        <div class="row">
            <div class="col-lg-12">
                <div id="history-container" class="p-3 bg-light text-left border rounded">
                    <h5>Evaluation History</h5>
                    <div id="historyList">
                        <!-- History items will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Modal for Instructor List -->
    <div class="modal fade" id="instructorListModal" tabindex="-1" aria-labelledby="instructorListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="instructorListModalLabel">Instructor List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="instructorList" class="row">
                        <!-- List of instructors will be dynamically injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for viewing the announcement -->
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewAnnouncementLabel">Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="announcement-details">
        <!-- Announcement content will be dynamically injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    <div class="dropdown">
      <i class="fa-solid fa-bars" id="hamburger-icon" data-bs-toggle="dropdown" aria-expanded="false"></i>    
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="../../logout_student.php">Log Out</a></li>
        </ul>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
const menuButton = document.querySelector('.menu-button');
const offcanvasElement = document.getElementById('offcanvasDrawer');

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
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const notifBell = document.getElementById('notif-bell');
    const pollInterval = 10000; // Poll every 10 seconds

    hamburgerIcon.addEventListener('click', function () {
        this.classList.toggle('active');
    });

    notifBell.addEventListener('click', function () {
        this.classList.toggle('active');
    });

    // Initial fetch when the page loads
    fetchAnnouncements();

    // Poll the server for new announcements every 10 seconds
    setInterval(fetchAnnouncements, pollInterval);

    function fetchAnnouncements() {
        fetch('../announcements/fetch_announcements.php')
            .then(response => response.json())
            .then(data => {
                const notificationList = document.getElementById('notification-list');
                notificationList.innerHTML = ''; // Clear existing notifications
                let unreadCount = 0;

                if (data.length > 0) {
                    data.forEach(announcement => {
                        const announcementItem = document.createElement('li');
                        announcementItem.classList.add('dropdown-item');
                        announcementItem.style.cursor = 'pointer';

                        // Check if the announcement was read from the backend
                        if (announcement.read_status === 0) {
                            // Add red dot for unread announcements
                            const redDot = document.createElement('span');
                            redDot.style.backgroundColor = 'red';
                            redDot.style.height = '10px';
                            redDot.style.width = '10px';
                            redDot.style.borderRadius = '50%';
                            redDot.style.display = 'inline-block';
                            redDot.style.marginRight = '10px';
                            announcementItem.appendChild(redDot);

                            // Increase unread count
                            unreadCount++;
                        }

                        // Announcement text
                        const announcementText = document.createElement('span');
                        announcementText.textContent = `Announcement: ${announcement.created_at}`;
                        announcementItem.appendChild(announcementText);

                        // Store announcement data in the element to access it later
                        announcementItem.dataset.announcementId = announcement.id;
                        announcementItem.dataset.announcementContent = announcement.content;

                        // Event listener for showing the modal on click
                        announcementItem.addEventListener('click', function () {
                            showAnnouncementModal(this.dataset.announcementContent, this.dataset.announcementId);
                        });

                        notificationList.appendChild(announcementItem);
                    });

                    // Add the red dot with number to the notification bell
                    if (unreadCount > 0) {
                        updateBellDot(unreadCount);
                    } else {
                        removeBellDot();
                    }
                } else {
                    const noAnnouncementItem = document.createElement('li');
                    noAnnouncementItem.classList.add('dropdown-item');
                    noAnnouncementItem.textContent = 'No new announcements';
                    notificationList.appendChild(noAnnouncementItem);
                }
            })
            .catch(error => console.error('Error fetching announcements:', error));
    }

    // Show the modal with announcement details
    function showAnnouncementModal(content, id) {
        const modalContent = document.getElementById('announcement-details');
        modalContent.textContent = content;

        const modal = new bootstrap.Modal(document.getElementById('viewAnnouncementModal'));
        modal.show();

        // Mark as read (send to backend)
        markAsRead(id);
    }

    // Mark announcement as read when clicked
    function markAsRead(id) {
        fetch('../announcements/mark_announcement_as_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the clicked item by its announcement ID
                    const clickedItem = document.querySelector(`[data-announcement-id="${id}"]`);
                    if (clickedItem) {
                        // Remove red dot if it exists
                        const redDot = clickedItem.querySelector('span[style*="background-color: red"]');
                        if (redDot) {
                            redDot.remove(); // Remove the red dot
                        }
                        clickedItem.style.fontWeight = 'normal'; // Set font weight to normal
                    }

                    // Decrease the unread count and update the bell dot
                    const bellDot = document.querySelector('.notification-bell-dot');
                    if (bellDot) {
                        let unreadCount = parseInt(bellDot.textContent) - 1;
                        if (unreadCount <= 0) {
                            removeBellDot(); // Remove the bell dot if no unread announcements remain
                        } else {
                            bellDot.textContent = unreadCount; // Update the unread count
                        }
                    }
                } else {
                    console.error('Error marking announcement as read:', data.error);
                }
            })
            .catch(error => console.error('Error marking announcement as read:', error));
    }

    // Function to update the bell dot
    function updateBellDot(unreadCount) {
        let bellDot = document.querySelector('.notification-bell-dot');
        if (!bellDot) {
            bellDot = document.createElement('span');
            bellDot.classList.add('notification-bell-dot');
            bellDot.style.backgroundColor = 'red';
            bellDot.style.color = 'white';
            bellDot.style.height = '20px';
            bellDot.style.width = '20px';
            bellDot.style.fontSize = '15px';
            bellDot.style.borderRadius = '50%';
            bellDot.style.display = 'flex';
            bellDot.style.alignItems = 'center';
            bellDot.style.justifyContent = 'center';
            bellDot.style.position = 'absolute';
            bellDot.style.top = '-5px';
            bellDot.style.right = '10px';
            notifBell.appendChild(bellDot);
        }
        bellDot.textContent = unreadCount; // Set the unread count as the text
    }

    // Function to remove the bell dot
    function removeBellDot() {
        const bellDot = document.querySelector('.notification-bell-dot');
        if (bellDot) {
            bellDot.remove(); // Remove the bell dot
        }
    }
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const instructorListContainer = document.getElementById('instructorList');
    const historyListContainer = document.getElementById('historyList');
    const countElement = document.getElementById('total-instructors');
    const ctx = document.getElementById('evaluationProgressChart').getContext('2d');
    let chart = null; // Store the chart instance globally
    const pollInterval = 10000; // 10 seconds for real-time update

    // Initial load of the dashboard sections
    fetchInstructorsForCourse();
    fetchEvaluationHistory();
    fetchInstructorCountAndProgress();

    // Poll the server every 10 seconds for updates
    setInterval(fetchInstructorsForCourse, pollInterval);
    setInterval(fetchEvaluationHistory, pollInterval);
    setInterval(fetchInstructorCountAndProgress, pollInterval);

    // Fetch and display instructors for the course
    function fetchInstructorsForCourse() {
        fetch('../../admin/create list/fetch_instructors_for_course.php?course=beed')
            .then(response => response.json())
            .then(data => {
                let listHTML = '<ul class="list-group">';
                if (data.length > 0) {
                    data.forEach(instructor => {
                        const imagePath = instructor.profile_picture 
                            ? `../../photo/${instructor.profile_picture}` 
                            : '../../photo/default.png';
                        listHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="${imagePath}" alt="Profile Picture" class="profile-pic" 
                                         style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                                    <label>${instructor.first_name} ${instructor.last_name} - <em>${instructor.faculty}</em></label>
                                </div>
                            </li>`;
                    });
                } else {
                    listHTML += '<p>No instructors found for BSCS.</p>';
                }
                listHTML += '</ul>';
                instructorListContainer.innerHTML = listHTML;
            })
            .catch(error => {
                instructorListContainer.innerHTML = '<p>Error loading instructors.</p>';
                console.error('Fetch Error:', error);
            });
    }

    // Fetch evaluation history and display it in the history container
    function fetchEvaluationHistory() {
        fetch('../history/fetch_evaluated_instructors.php')
            .then(response => response.json())
            .then(data => {
                let historyHTML = '<ul class="list-group">';
                if (data.length > 0) {
                    data.forEach(instructor => {
                        historyHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="../../photo/${instructor.profile_picture}" alt="Profile Picture" class="profile-pic" 
                                         style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                                    <label>${instructor.first_name} ${instructor.last_name} - <em>Evaluated on ${instructor.created_at}</em></label>
                                </div>
                            </li>`;
                    });
                } else {
                    historyHTML += '<p>No evaluation history available.</p>';
                }
                historyHTML += '</ul>';
                historyListContainer.innerHTML = historyHTML;
            })
            .catch(error => {
                historyListContainer.innerHTML = '<p>Error loading evaluation history.</p>';
                console.error('Fetch Error:', error);
            });
    }

    // Fetch the instructor count and evaluation data for the chart
    function fetchInstructorCountAndProgress() {
        fetch('../fetch_instructor_count.php') 
            .then(response => response.json())
            .then(data => {
                if (data.instructor_count) {
                    countElement.textContent = data.instructor_count; // Update total instructor count

                    const totalInstructors = data.instructor_count;
                    const evaluatedInstructors = data.evaluated_instructors;
                    const nonEvaluatedInstructors = data.non_evaluated_instructors;

                    if (chart) {
                        // Update existing chart with new data
                        chart.data.datasets[0].data = [evaluatedInstructors];
                        chart.data.datasets[1].data = [nonEvaluatedInstructors * -1];
                        chart.update();
                    } else {
                        // Create the chart for the first time
                        chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Instructors'],
                                datasets: [{
                                    label: 'Evaluated',
                                    data: [evaluatedInstructors],
                                    backgroundColor: '#0000ff', // Blue for evaluated
                                    stack: 'Stack 0',
                                }, {
                                    label: 'Not Evaluated',
                                    data: [nonEvaluatedInstructors * -1], // Negative value to grow left
                                    backgroundColor: '#ff0000', // Red for non-evaluated
                                    stack: 'Stack 0',
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Horizontal bars
                                scales: {
                                    x: {
                                        beginAtZero: true, 
                                        suggestedMin: -totalInstructors,
                                        suggestedMax: totalInstructors 
                                    }
                                },
                                plugins: {
                                    tooltip: {
                                        enabled: true,
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                if (tooltipItem.dataset.label === 'Evaluated') {
                                                    return `${evaluatedInstructors} professors were evaluated`;
                                                } else {
                                                    return `${nonEvaluatedInstructors} professors weren't evaluated`;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                } else if (data.error) {
                    countElement.textContent = 'Error: ' + data.error;
                } else {
                    countElement.textContent = 'No instructor count found.';
                }
            })
            .catch(error => {
                countElement.textContent = 'Error fetching instructor count';
                console.error('Fetch Error:', error);
            });
    }
});
</script>

</body>
</html>
