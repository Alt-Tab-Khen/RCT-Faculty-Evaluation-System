<?php
include '../../db_connect.php';
session_start();
if (!isset($_SESSION['prof_id'])) {
    die("Error: Professor not logged in.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments Section</title>
    <link rel="stylesheet" href="clfac.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        .blurred {
            filter: blur(8px);
            transition: filter 0.3s ease;
        }
        .show-comment {
            filter: none;
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
    <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
        <div class="container-fluid">        
            <div class="row">
                <div class="col-12">
                    <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                        <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Comments</span>
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
                    <a class="nav-link active" aria-current="page" href="#" id="n2">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="fa-solid fa-bars" id="hamburger-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>    
                    <ul class="dropdown-menu dropdown-menu-lg-end">
                    <li><a class="dropdown-item" href="logout_instructor.php">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Comments Section -->
    <div class="container mt-4">
        <h3 class="mb-4" style="color: white;">Comments Section</h3>
        <div class="bg-white p-4 rounded" id="commentList">
            <p>Loading comments...</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            function fetchComments() {
                fetch('fetch_comments.php') // Fetching from the separate endpoint
                    .then(response => response.json())
                    .then(data => {
                        const commentList = document.getElementById('commentList');
                        let commentsHTML = '';

                        if (data.length > 0) {
                            data.forEach(commentData => {
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
                            button.addEventListener('click', function() {
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
                        document.getElementById('commentList').innerHTML = '<p>Error loading comments.</p>';
                        console.error('Error fetching comments:', error);
                    });
            }

            // Fetch comments every 5 seconds
            setInterval(fetchComments, 5000);
            
            // Initial fetch
            fetchComments();
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
