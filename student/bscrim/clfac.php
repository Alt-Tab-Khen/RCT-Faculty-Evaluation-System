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
    <title>BSCRIM Faculty List</title>
    <link rel="stylesheet" href="clfac.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
      <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
        <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">BSCRIM</span>
      </button>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <div class="d-flex w-100 justify-content-end align-items-center">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#" id="n2">Home</a>
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
    </div>
  </nav>

  <div class="container mt-4">
    <h3 class="mb-4" style="color: white">Instructors for BSCRIM</h3>
    <div id="instructorList" class="list-group bg-white rounded" style="background-color: white;">
    <!-- Instructors will be displayed here -->
    </div>
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
        <li><a href="../bscrim/admin.php" type="button" class="btn" id="cl">Dashboard</a></li>
        <li><a href="../bscrim/clfac.php" type="button" class="btn" id="cl">Instructor List</a></li>
        <li><a href="../history/clfac.php" type="button" class="btn" id="cl">History</a></li>
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
    // Check if the evaluation is active
    fetch('../check_evaluation_active.php')
        .then(response => response.json())
        .then(data => {
            if (data.active) {
                // Evaluation is active, show the 'Evaluate' buttons
                fetchInstructorsForCourse(); // Fetch and display instructors
                setInterval(fetchInstructorsForCourse, 5000); // Auto-reload every 5 seconds
            } else {
                // Evaluation is not active, disable/hide the 'Evaluate' buttons
                const instructorList = document.getElementById('instructorList');
                instructorList.innerHTML = '<p>Evaluation is currently not active.</p>';
            }
        })
        .catch(error => {
            console.error('Error checking evaluation status:', error);
        });

    // Fetch and display instructors
    function fetchInstructorsForCourse() {
        fetch('../../admin/create list/fetch_instructors_for_course.php?course=bscrim')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);  // Debugging step: Check the fetched data in console
                const instructorList = document.getElementById('instructorList');
                let listHTML = '<ul class="list-group">';

                if (data.length > 0) {
                    data.forEach(instructor => {
                        const imagePath = instructor.profile_picture 
                            ? `../../photo/${instructor.profile_picture}` 
                            : '../../photo/default.png';

                        // Check if the instructor has already been evaluated by this student
                        const isDisabled = instructor.already_evaluated > 0 ? 'disabled' : '';
                        const buttonText = instructor.already_evaluated > 0 ? 'Evaluated' : 'Evaluate';

                        listHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="${imagePath}" alt="Profile Picture" class="profile-pic" 
                                         style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                                    <label>${instructor.first_name} ${instructor.last_name} - <em>${instructor.faculty}</em></label>
                                </div>
                                <button class="btn btn-primary evaluate-btn" data-prof-id="${instructor.prof_id}" ${isDisabled}>
                                    ${buttonText}
                                </button>
                            </li>`;
                    });
                } else {
                    listHTML += '<p>No instructors found for BSCRIM.</p>';
                }

                listHTML += '</ul>';
                instructorList.innerHTML = listHTML;

                // Attach click event listeners to the "Evaluate" buttons
                const evaluateButtons = document.querySelectorAll('.evaluate-btn');
                evaluateButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const profId = this.getAttribute('data-prof-id');
                        evaluateInstructor(profId);
                    });
                });
            })
            .catch(error => {
                console.error('Error fetching instructors:', error);
                const instructorList = document.getElementById('instructorList');
                instructorList.innerHTML = '<p>Failed to load instructors. Please try again later.</p>';
            });
    }

    // "Evaluate" button functionality
    function evaluateInstructor(profId) {
        console.log("Instructor ID: " + profId);  // Debugging step
        window.location.href = `../evaluation/eval.php?prof_id=${profId}`;
    }
});

</script>
</body>
</html>
