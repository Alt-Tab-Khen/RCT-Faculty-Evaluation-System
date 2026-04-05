<?php
include '../../db_connect.php';
session_start();

// Assuming prof_id is stored in the session
if (!isset($_SESSION['prof_id'])) {
    die("Error: Professor not logged in.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link rel="stylesheet" href="../dashboard-tt/admin.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Add Custom Style for Circular Progress -->
    <style>
                .blurred {
            filter: blur(8px);
            transition: filter 0.3s ease;
        }
        .show-comment {
            filter: none;
        }
        /* Circle container */
        .circle-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }

        /* Base circle */
        .circle-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #f0f0f0;
        }

        /* The blue progress circle */
        .circle-progress {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: transparent;
            border: 10px solid #007bff;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            transform: rotate(-90deg);
        }

        /* Progress percentage */
        .circle-progress[data-progress="0"] {
            clip-path: polygon(50% 0%, 50% 0%, 50% 100%);
        }
        .circle-progress[data-progress="25"] {
            clip-path: polygon(50% 0%, 0% 50%, 50% 100%);
        }
        .circle-progress[data-progress="50"] {
            clip-path: polygon(50% 0%, 0% 100%, 50% 100%);
        }
        .circle-progress[data-progress="75"] {
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }
        .circle-progress[data-progress="100"] {
            clip-path: polygon(50% 0%, 50% 100%, 100% 100%, 0% 100%);
        }

        /* Overall Rating in the middle */
        .overall-rating-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            font-weight: bold;
            color: #333;
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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                    <img src="../dashboard-tt/images.jpg" alt="Menu" class="img-fluid">
                </button>
            </div>
        </div>
    </div>

<!-- Menu "Pull-Tab" Button -->
<div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
  <span>Menu</span>
</div>

<!-- Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">RCT Faculty Evaluation System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="but">
            <ul class="dropdown" id="it">
                <li><a href="../dashboard-tt/admin.php" type="button" class="btn" id="cl">Dashboard</a></li>
                <li><a href="../QS/clfac.php" type="button" class="btn" id="cl">Question Session</a></li>
                <li><a href="../comment/clfac.php" type="button" class="btn" id="cl">Comments</a></li>
                <li><a href="../Perfres/clfac.php" type="button" class="btn" id="cl">Performance Result</a></li>
            </ul>
        </div>
    </div>
</div>

    
    <div class="container mt-4">
        <div class="row">
            <!-- Total Evaluators Section -->
            <div class="col-lg-6 col-md-6 mb-3">
                <div id="total-evaluators" class="p-5 bg-light text-center border rounded">
                    <h5>Total Evaluators</h5>
                    <h2 id="total-evaluators-count">Loading...</h2>
                </div>
            </div>

            <!-- Overall Rating Section with Circular Progress -->
            <div class="col-lg-6 col-md-6 mb-3">
                <div id="overall-rating" class="p-7 bg-light text-center border rounded">
                    <h5>Overall Rating</h5>
                    <!-- Circular Progress Rating -->
                    <div class="circle-container">
                        <div class="circle-background"></div>
                        <div class="circle-progress" data-progress="0" id="circleProgress"></div>
                        <div class="overall-rating-text" id="overallRatingText">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="row">
            <div class="col-lg-12">
                <div id="comments-container" class="p-3 bg-light text-left border rounded">
                    <h5>Comments</h5>
                    <div id="commentList">
                        <!-- Comments will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dropdown">
      <i class="fa-solid fa-bars" id="hamburger-icon" data-bs-toggle="dropdown" aria-expanded="false"></i>    
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="../../logout_instructor.php">Log Out</a></li>
        </ul>
    </div>
    

    <!-- Optional: Add any required offcanvas or dropdown elements if needed -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
const menuButton = document.querySelector('.menu-button');
const offcanvasElement = document.getElementById('offcanvasScrolling');


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
    document.addEventListener('DOMContentLoaded', function () {
        const profId = '<?= $_SESSION['prof_id']; ?>';  // Pass the professor's ID from the session

        fetchDashboardData(profId);

        // Function to fetch total evaluators, overall rating, and comments
        function fetchDashboardData(profId) {
            fetch(`fetch_dashboard_data.php?prof_id=${profId}`)
                .then(response => response.json())
                .then(data => {
                    // Update total evaluators
                    document.getElementById('total-evaluators-count').textContent = data.total_evaluators || "No Evaluators Yet";

                    // Update overall rating with circular progress
                    const overallRating = data.overall_rating || 0;
                    document.getElementById('overallRatingText').textContent = overallRating;

                    // Calculate percentage progress (out of 100)
                    const progress = Math.round((overallRating / 5) * 100);

                    // Set the progress data attribute to visually fill the circle
                    const progressElement = document.getElementById('circleProgress');
                    if (progress <= 25) {
                        progressElement.setAttribute('data-progress', '25');
                    } else if (progress <= 50) {
                        progressElement.setAttribute('data-progress', '50');
                    } else if (progress <= 75) {
                        progressElement.setAttribute('data-progress', '75');
                    } else {
                        progressElement.setAttribute('data-progress', '100');
                    }

                    // Update comments section
                    const commentList = document.getElementById('commentList');
                    let commentsHTML = '';

                    if (data.comments.length > 0) {
                        data.comments.forEach(commentData => {
                            commentsHTML += `
                                <div class="comment-item mb-4 p-3 border rounded">
                                    <h5>From: ${commentData.author}</h5>
                                    <p class="${commentData.blurred ? 'blurred' : ''}">
                                        ${commentData.comment}
                                    </p>
                                    ${commentData.blurred ? '<button class="btn btn-secondary toggle-comment">Show Comment</button>' : ''}
                                </div>
                            `;
                        });
                    } else {
                        commentsHTML = '<p>No comments available.</p>';
                    }

                    commentList.innerHTML = commentsHTML;

                    // Attach click events to the toggle-comment buttons
                    document.querySelectorAll('.toggle-comment').forEach(button => {
                        button.addEventListener('click', function () {
                            const commentParagraph = this.previousElementSibling;
                            if (commentParagraph.classList.contains('blurred')) {
                                commentParagraph.classList.remove('blurred');
                                commentParagraph.classList.add('show-comment');
                                this.textContent = 'Hide Comment';
                            } else {
                                commentParagraph.classList.add('blurred');
                                commentParagraph.classList.remove('show-comment');
                                this.textContent = 'Show Comment';
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching dashboard data:', error);
                    document.getElementById('total-evaluators-count').textContent = 'Error loading data';
                    document.getElementById('overallRatingText').textContent = 'Error loading data';
                });
        }
    });
    </script>

</body>
</html>
