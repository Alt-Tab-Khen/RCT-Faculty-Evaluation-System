<?php
session_start();  // Start the session at the beginning of the file

include '../../db_connect.php';

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
                  <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Create Instructor List</span>
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
            <a class="nav-link active" aria-current="page" href="../dashboard/admin.html" id="n2" >Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="fa-solid fa-bars" id="hamburger-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>    
            <ul class="dropdown-menu dropdown-menu-lg-end">
                <li><a class="dropdown-item" href="#">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
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
  <div class="container mt-5">
    <h2 class="mb-4" style="color: white">Faculty Management</h2>

    <!-- Select Course Dropdown and Add Instructor Button -->
    <div class="row">
      <div class="col-2">
        <select class="form-select form-select-lg" id="selectCourseDropdown">
          <option selected disabled>Select Course</option>
          <option value="bscs">BSCS</option>
          <option value="bsa">BSA</option>
          <option value="bsba">BSBA</option>
          <option value="bsed">BSED</option>
          <option value="beed">BEED</option>
          <option value="act">ACT</option>
          <option value="bscrim">BSCRIM</option>
        </select>
      </div>

      <!-- Add Instructor Button -->
      <button type="button" class="btn btn-success col-9" data-bs-toggle="modal" data-bs-target="#addInstructorModal">Add Instructor</button>

    <!-- Trashcan Button for Delete Mode -->
    <button type="button" class="btn btn-danger ms-2" style="width: 80px;" id="toggleDeleteMode">
      <i class="fas fa-trash"></i> <!-- Trashcan icon -->
    </button>
    </div>

    <!-- Add Instructor Modal -->
    <div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addInstructorModalLabel">Add Instructors to List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <!-- Warnings for add modal -->
            <div id="modalWarning" class="alert alert-danger d-none"></div>
            <div id="modalSuccess" class="alert alert-success d-none"></div>



            <!-- Faculty Dropdown -->
            <div class="form-floating mb-3">
              <select class="form-select" id="facultyDropdown" name="faculty" required onchange="fetchInstructorsByFaculty()">
                <option value="" disabled selected>Select Faculty</option>
                <option value="cpus">CPUS</option>
                <option value="cba">CBA</option>
                <option value="educ">EDUC</option>
                <option value="crim">CRIM</option>
              </select>
              <label for="facultyDropdown">Select Faculty</label>
            </div>

            <!-- Instructor List -->
            <div id="instructorList"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="addSelectedInstructors">Add Selected Instructors</button>
          </div>
        </div>
      </div>
    </div>

<!-- Container for Selected Instructors (Styled with white background) -->
<div class="container mt-4">
    <h3 class="mb-4" style="color: white">Selected Instructors for the Course</h3>
    <div id="selectedInstructorsList" class="list-group bg-white rounded" style="background-color: white;">
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
<script>
// Function to toggle instructor selection
let selectedInstructors = []; // Array to store selected instructor IDs

function fetchInstructorsByFaculty() {
    var faculty = document.getElementById('facultyDropdown').value;
    var selectedCourse = document.getElementById('selectCourseDropdown').value; // Get the selected course

    if (faculty) {
        fetch(`fetch_instructors.php?faculty=${faculty}&course=${selectedCourse}`)  // Add course to the request
            .then(response => response.json())
            .then(data => {
                var instructorList = document.getElementById('instructorList');
                if (data.length > 0) {
                    let listHTML = '<ul class="list-group">';
                    data.forEach(instructor => {
                        const imagePath = instructor.profile_picture ? `../../photo/${instructor.profile_picture}` : "../../photo/default.jpg";
                        
                        // If instructor is already part of the course, disable the checkbox and make it checked
                        const isChecked = instructor.is_in_course ? 'checked' : (selectedInstructors.includes(instructor.prof_id) ? 'checked' : '');
                        const isDisabled = instructor.is_in_course ? 'disabled' : ''; // Grayed out if already in course

                        listHTML += `
                          <li class="list-group-item d-flex align-items-center" style="padding: 10px 15px;">
                              <input type="checkbox" id="instructor_${instructor.prof_id}" value="${instructor.prof_id}" ${isChecked} ${isDisabled} onchange="toggleInstructorSelection('${instructor.prof_id}')"
                                    style="width: 25px; height: 25px; margin-right: 15px;"> <!-- Increased size and added margin -->
                              <img src="${imagePath}" alt="Profile Picture" class="profile-pic" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                              <label for="instructor_${instructor.prof_id}">
                                  <strong>(${instructor.prof_id})</strong> ${instructor.first_name} ${instructor.last_name} - <em>${instructor.faculty}</em>
                              </label>
                          </li>`;
                    });
                    listHTML += '</ul>';
                    instructorList.innerHTML = listHTML;
                } else {
                    instructorList.innerHTML = '<p>No instructors found for the selected faculty.</p>';
                }
            })
            .catch(error => console.error('Error fetching instructors:', error));
    }
}

// Fetch instructors for a selected course
function fetchInstructorsForSelectedCourse(selectedCourse) {
    fetch(`fetch_instructors_for_course.php?course=${selectedCourse}`)
        .then(response => response.json())
        .then(data => {
            var selectedInstructorsList = document.getElementById('selectedInstructorsList');
            let listHTML = '<ul class="list-group">';

            if (data.length > 0) {
                data.forEach(instructor => {
                    const imagePath = instructor.profile_picture ? `../../photo/${instructor.profile_picture}` : "../../photo/default.jpg";
                    listHTML += `
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="${imagePath}" alt="Profile Picture" class="profile-pic" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                                <label for="instructor_${instructor.prof_id}">${instructor.first_name} ${instructor.last_name} - <em>${instructor.faculty}</em></label>
                            </div>
                            <!-- Delete Button (hidden by default, will be shown in delete mode) -->
                            <button type="button" class="btn btn-danger btn-sm d-none delete-btn" onclick="deleteInstructor('${instructor.prof_id}', '${selectedCourse}')">
                                <i class="fas fa-trash-alt"></i> <!-- Trash icon -->
                            </button>
                        </li>`;
                });
            } else {
                listHTML += '<p>No instructors found for the selected course.</p>';
            }

            listHTML += '</ul>';
            selectedInstructorsList.innerHTML = listHTML;
        })
        .catch(error => console.error('Error fetching instructors for the course:', error));
}


// Function to toggle instructor selection and store it in the array
function toggleInstructorSelection(prof_id) {
    const index = selectedInstructors.indexOf(prof_id);

    if (index > -1) {
        // Instructor is already selected, remove them
        selectedInstructors.splice(index, 1);
    } else {
        // Instructor is not selected, add them
        selectedInstructors.push(prof_id);
    }
}


// Add selected instructors
// Add selected instructors
document.getElementById('addSelectedInstructors').addEventListener('click', function () {
    var selectedCourse = document.getElementById('selectCourseDropdown').value;
    var modalWarning = document.getElementById('modalWarning');
    var modalSuccess = document.getElementById('modalSuccess');

    // Clear previous warnings and success messages
    modalWarning.classList.add('d-none');  // This will hide any previous warnings
    modalSuccess.classList.add('d-none');  // This will hide any previous success messages

// Check if a course is selected
if (!selectedCourse || selectedCourse === 'Select Course') {
    console.log('Course not selected'); // Debugging line
    modalWarning.classList.remove('d-none');
    modalWarning.textContent = 'Please select a course first.';
    return;
}

if (selectedInstructors.length === 0) {
    console.log('No instructors selected'); // Debugging line
    modalWarning.classList.remove('d-none');
    modalWarning.textContent = 'Please select at least one instructor.';
    return;
}


    let formData = new URLSearchParams();
    formData.append('course', selectedCourse);
    selectedInstructors.forEach(prof_id => formData.append('selected_instructors[]', prof_id));

    fetch('save_instructors.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            modalSuccess.classList.remove('d-none');  // Show success message
            modalSuccess.textContent = 'Instructors added successfully!';
            
            // After adding, fetch the updated instructor list for the selected course
            fetchInstructorsForSelectedCourse(selectedCourse);
        } else {
            modalWarning.classList.remove('d-none');  // Show error message
            modalWarning.textContent = 'Error: ' + data.message;
        }
    })
    .catch(error => {
        modalWarning.classList.remove('d-none');  // Show fetch error
        modalWarning.textContent = 'An error occurred: ' + error.message;
    });
});

// Event listener for course dropdown to fetch instructors when the course is selected
document.getElementById('selectCourseDropdown').addEventListener('change', function () {
    var selectedCourse = this.value;

    // Clear previously selected instructors
    selectedInstructors = [];

    // Fetch and display instructors for the selected course
    fetchInstructorsForSelectedCourse(selectedCourse);
});

// Toggle delete mode to show or hide delete buttons
document.getElementById('toggleDeleteMode').addEventListener('click', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.classList.toggle('d-none');  // Show or hide delete buttons
    });
});

// Function to delete an instructor from the course
function deleteInstructor(prof_id, course) {
    if (confirm('Are you sure you want to delete this instructor?')) {
        // Send a request to delete the instructor
        let formData = new URLSearchParams();
        formData.append('course', course);
        formData.append('prof_id', prof_id);

        fetch('delete_instructor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Refresh the list after successful deletion
                fetchInstructorsForSelectedCourse(course);
            } else {
                alert('Error deleting instructor: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting instructor:', error));
    }
}

</script>






</body>
</html>
