<?php
include '../../db_connect.php';

session_start();
if (isset($_SESSION['student_id'])) {
    echo "Student ID: " . $_SESSION['student_id'];
} else {
    header("Location: ../../login_student/login_stu.php"); // Redirect to login if not logged in
    exit();
  }
$course = $_SESSION['course']; // Assuming course is stored in session

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Design</title>
    <link rel="stylesheet" href="clfac.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                    <img src="../../photo/images.jpg" alt="Menu" class="img-fluid">
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
          <li class="nav-item dropdown">
            <a class="fa-solid fa-bars" id="hamburger-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>    
            <ul class="dropdown-menu dropdown-menu-lg-end">
            <li><a class="dropdown-item" href="../../logout_student.php">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h3 class="mb-4" style="color: white">Evaluated Instructors</h3>
    <div id="instructorList" class="list-group bg-white rounded">
        <!-- Instructors will be dynamically injected here -->
    </div>
</div>

<!-- Modal content -->
<div class="modal" id="editEvaluationModal" tabindex="-1" aria-labelledby="editEvaluationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editEvaluationModalLabel">Edit Evaluation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Success message placeholder -->
        <div id="success-message" class="alert alert-success" style="display: none;"></div>
        
        <!-- Evaluation form content -->
        <form id="edit-evaluation-form">
          <div id="evaluation-content"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save-evaluation-btn">Save changes</button>
      </div>
    </div>
  </div>
</div>



<!-- Offcanvas Drawer with Dynamic Course Name in Span -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasDrawer" aria-labelledby="offcanvasDrawerLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasDrawerLabel">RCT Faculty Evaluation System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="but">
            <ul class="dropdown" id="it">
                <li><a href="../<?php echo $course; ?>/admin.php" type="button" class="btn" id="cl">Dashboard</a></li>
                <li><a href="../<?php echo $course; ?>/clfac.php" class="btn" id="cl" role="button" aria-expanded="false">Instructor Lis</a></li>
                <li><a href="../<?php echo $course; ?>/history/clfac.php" type="button" class="btn" id="cl">History</a></li>
            </ul>
        </div>
    </div>
</div>


<!-- Menu "Pull-Tab" Button -->
<div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDrawer" aria-controls="offcanvasDrawer">
  <span>Menu</span>
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
    // Fetch evaluated instructors for the history page
    fetchEvaluatedInstructors();

function fetchEvaluatedInstructors() {
    fetch('fetch_evaluated_instructors.php')
        .then(response => response.json())
        .then(data => {
            const instructorList = document.getElementById('instructorList');
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
                            <button class="btn btn-warning edit-btn" data-prof-id="${instructor.prof_id}" 
                                ${instructor.can_edit === 0 ? 'disabled' : ''}>
                                Edit
                            </button>
                        </li>`;
                });
            } else {
                listHTML += '<p>No evaluated instructors found.</p>';
            }

            listHTML += '</ul>';
            instructorList.innerHTML = listHTML;

            // Attach event listeners to enabled "Edit" buttons
            attachEditButtonListeners();
        })
        .catch(error => {
            console.error('Error fetching instructors:', error);
            const instructorList = document.getElementById('instructorList');
            instructorList.innerHTML = '<p>Failed to load evaluated instructors. Please try again later.</p>';
        });
}


    // Function to attach event listeners to "Edit" buttons
    function attachEditButtonListeners() {
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const profId = this.getAttribute('data-prof-id');
                editEvaluation(profId);  // This will fetch the evaluation and open the modal
            });
        });
    }

    // Function to fetch evaluation and open modal
    function editEvaluation(profId) {
        // Open the modal
        const editModal = new bootstrap.Modal(document.getElementById('editEvaluationModal'));
        editModal.show();

        // Fetch the previous evaluation for the selected instructor
        fetch(`fetch_evaluation_data.php?prof_id=${profId}`)
            .then(response => response.json())
            .then(data => {
                let evaluationContent = '';
                let currentCategory = ''; // Track current category

                if (data.length > 0) {
                    data.forEach(evaluation => {
                        // Only display the category if it changes
                        if (evaluation.category !== currentCategory) {
                            evaluationContent += `
                                <div class="category-container mb-4">
                                    <h5 class="category-title"><strong>${evaluation.category}</strong></h5>
                                </div>`;
                            currentCategory = evaluation.category; // Update current category
                        }

                        evaluationContent += `
                            <div class="mb-3">
                                <p>${evaluation.question}</p>
                                <input type="hidden" name="ratings[${evaluation.question_id}]" value="${evaluation.rating}" id="rating-${evaluation.question_id}">
                                <div class="star-rating" data-question-id="${evaluation.question_id}">
                                    <i class="fa fa-star" data-rating="1"></i>
                                    <i class="fa fa-star" data-rating="2"></i>
                                    <i class="fa fa-star" data-rating="3"></i>
                                    <i class="fa fa-star" data-rating="4"></i>
                                    <i class="fa fa-star" data-rating="5"></i>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    evaluationContent = '<p>No evaluation data found.</p>';
                }

                // Inject the fetched content into the modal
                document.getElementById('evaluation-content').innerHTML = evaluationContent;

                // Highlight the existing ratings and attach click event listeners for stars
                highlightExistingRatings();
                attachStarClickListeners();
            })
            .catch(error => console.error('Error fetching evaluation data:', error));
    }

    // Function to highlight existing ratings
    function highlightExistingRatings() {
        const starRatings = document.querySelectorAll('.star-rating');
        starRatings.forEach(ratingDiv => {
            const questionId = ratingDiv.getAttribute('data-question-id');
            const currentRating = document.getElementById(`rating-${questionId}`).value;
            const stars = ratingDiv.querySelectorAll('i');
            stars.forEach((star, index) => {
                if (index < currentRating) {
                    star.classList.add('selected');
                }
            });
        });
    }

    // Function to handle star click events and update the rating
    function attachStarClickListeners() {
        const stars = document.querySelectorAll('.star-rating i');
        stars.forEach(star => {
            star.addEventListener('click', function () {
                const rating = parseInt(this.getAttribute('data-rating'));
                const questionId = this.closest('.star-rating').getAttribute('data-question-id');

                // Update the hidden input with the selected rating
                document.getElementById(`rating-${questionId}`).value = rating;

                // Highlight the selected stars
                const allStars = this.closest('.star-rating').querySelectorAll('i');
                allStars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            });
        });
    }
});

// Save evaluation with prof_id in FormData
document.getElementById('save-evaluation-btn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('edit-evaluation-form'));
    const profId = document.querySelector('.edit-btn[data-prof-id]').getAttribute('data-prof-id'); // Get prof_id from the button
    
    formData.append('prof_id', profId); // Add prof_id to the formData

    // Debug form data
    for (var pair of formData.entries()) {
        console.log(pair[0] + ', ' + pair[1]); // Log each form data entry to the console
    }

    fetch('update_evaluation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message in the modal
            const successMessage = document.getElementById('success-message');
            successMessage.innerText = 'Evaluation updated successfully!';
            successMessage.style.display = 'block';

            // Optionally, hide the success message after a delay
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
        } else {
            alert('Failed to update evaluation: ' + data.message); // Show detailed error message
        }
    })
    .catch(error => console.error('Error updating evaluation:', error));
});
</script>



</body>
</html>
