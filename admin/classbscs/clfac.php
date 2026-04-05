<?php

session_start();  // Start the session at the beginning of the file

include '../../db_connect.php';
include '../../student edit/delete_rejected_student.php';

// Assuming the admin's ID is stored in the session as 'id'
if (!isset($_SESSION['id'])) {
    die("Error: admin not logged in.");
}

// Rest of your code here...

// Initialize variables
$errors = [];
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $student_id = trim($_POST['student_id']);  // Student ID added
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $section = trim($_POST['section']);
    $year = trim($_POST['year']);
    $course = $_POST['course'];
    $status = "pending"; // Set the status as pending

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Define the image upload directory
        $upload_dir = realpath(__DIR__ . "/../../photo/") . '/';
        
        // Ensure the upload directory exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
        }

        // Generate a unique file name
        $image_name = uniqid() . "-" . basename($_FILES['profile_image']['name']);
        $image_path = $upload_dir . $image_name;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
            $errors[] = "Failed to upload profile picture.";
        }
    } else {
        // If no image is uploaded, use the default image
        $image_name = "default.jpg";
    }

    // Validation
    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    }
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($section)) {
        $errors[] = "Section is required.";
    }
    if (empty($year)) {
        $errors[] = "Year is required.";
    }
    if (empty($course)) {
        $errors[] = "Course is required.";
    }

    // Check if student ID or email already exists
    $email_check_query = "SELECT * FROM student WHERE email = ? OR student_id = ? LIMIT 1";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("ss", $email, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_user = $result->fetch_assoc();

    if ($existing_user) {
        $errors[] = "Email or Student ID already exists. Please use different credentials.";
    }

    // If no errors, proceed to insert into database
    if (count($errors) == 0) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the student into the database along with the profile picture filename and status
        $stmt = $conn->prepare("INSERT INTO student (student_id, first_name, last_name, username, email, password, section, year, course, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $student_id, $first_name, $last_name, $username, $email, $hashed_password, $section, $year, $course, $image_name, $status);
       

        if ($stmt->execute()) {
            $success = "Registration successful! Your account is pending admin approval.";
            echo json_encode(['success' => true, 'message' => $success]);
        } else {
            // Log the error to the server's error log
            error_log("SQL Error: " . $stmt->error); 
            $errors[] = "Error occurred while adding the student. Please try again.";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSCS Class</title>
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
    <a href="#" id="notificationBell" class="nav-link position-relative" data-bs-toggle="modal" data-bs-target="#validationModal">
    <i class="fa fa-bell"></i>
    <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        0
    </span>
        </a>

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
            <li><a class="dropdown-item" href="../../logout_admin.php">Log Out</a></li>
              </ul>
          </li>
        </ul>
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
    <h2 style="color: rgb(255,255,255)">RCT Students</h2>

    <!-- Search Bar -->
    <form method="GET" action="">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search by Student ID, First Name, or Last Name" name="search_term" aria-label="Search" aria-describedby="button-addon2">
        <button class="btn btn-primary" type="submit" id="button-addon2">Search</button>
    </div>
</form>

    <?php
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search_term']);
    
    // Modify query to search by student_id, first_name, or last_name
    $query = "
    SELECT student_id, first_name, last_name, year, section, course, image 
    FROM student 
    WHERE student_id LIKE '%$search_term%' 
    OR first_name LIKE '%$search_term%' 
    OR last_name LIKE '%$search_term%'
    OR course LIKE '%$search_term%'
    OR year LIKE '%$search_term%'
    OR section LIKE '%$search_term%'
";
    $result = mysqli_query($conn, $query);

    // Check if any student data was found
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $profile_image_path = "../../photo/" . htmlspecialchars($row['image']);

            echo "<div class='card mt-4'>
                    <div class='card-body d-flex align-items-start'>
                        <div class='profile-pic-container'>";

            // Display the profile picture if exists, else use the default image
            if (file_exists($profile_image_path)) {
                echo "<img src='" . $profile_image_path . "' alt='Profile Picture' class='profile-picture' style='width: 150px; height: 150px; border-radius: 50%;'>";
            } else {
                echo "<img src='../../photo/default.jpg' alt='Profile Picture' class='profile-picture' style='width: 150px; height: 150px; border-radius: 50%;'>";
            }

            echo "</div>
                  <div class='student-details ms-4'>";

            // Display the student details
            echo "<p class='card-text'><strong>Student ID:</strong> " . htmlspecialchars($row['student_id']) . "</p>
                  <p class='card-text'><strong>Name:</strong> " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</p>
                  <p class='card-text'><strong>Course:</strong> " . htmlspecialchars($row['course']) . "</p>
                  <p class='card-text'><strong>Year:</strong> " . htmlspecialchars($row['year']) . "</p>
                  <p class='card-text'><strong>Section:</strong> " . htmlspecialchars($row['section']) . "</p>";

            // Display Edit and Delete buttons
            echo "<div class='d-flex justify-content-start mt-4'>
                     <a href='#' class='btn btn-warning me-2' onclick=\"openEditModal('" . addslashes($row['student_id']) . "', '" . addslashes($row['first_name']) . "', '" . addslashes($row['last_name']) . "', '" . addslashes($row['course']) . "', '" . addslashes($row['year']) . "', '" . addslashes($row['section']) . "')\">Edit</a>
                  <a href='#' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-student-id='" . $row['student_id'] . "'>Delete</a>
                  </div>
                </div>
            </div>
        </div>";
        }
    } else {
        echo "<p id='error-message' class='mt-4' style='background-color: red; color: white; padding: 10px; border-radius: 5px;'>No student found with that information.</p>";
    }
}

?>


<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="deleteConfirmationText">Are you sure you want to delete this student?</p>

        <!-- Success message inside the modal -->
        <div id="successMessage" style="display: none; color: green; padding: 10px; border: 1px solid green; border-radius: 5px;">
          Student deleted successfully!
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validationModalLabel">Pending Student Registrations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pendingList">
                    <!-- List of pending students will be loaded here via JavaScript -->
                </div>
                    <!-- Success/Error message placeholder -->
            <div id="validationMessage" class="alert d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

                <!-- Success message placeholder -->
                <div id="edit-success-message" class="alert alert-success d-none" role="alert">
                    Student updated successfully!
                </div>

                <!-- Error message placeholder -->
                <div id="edit-error-message" class="alert alert-danger d-none" role="alert">
                    Failed to update student.
                </div>

                <!-- Edit Student Form -->
                <form id="editStudentForm" method="POST" action="../../student edit/update_student.php">
                    <input type="hidden" id="edit_student_id" name="student_id">

                <div class="modal-body">
                <!-- Profile Picture Section -->
                <div class="profile-pic-container text-center mb-3">
                    <!-- The profile picture preview -->
                    <img class="profile-picture" id="editProfilePicturePreview" src="../../photo/default.jpg" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;" onclick="document.getElementById('editProfilePictureInput').click();">
                    <label>Edit Profile Picture</label>
                    <!-- Hidden file input -->
                    <input type="file" id="editProfilePictureInput" name="profile_image" accept="image/*" style="display: none;" onchange="previewEditProfilePicture(event)">
                </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" placeholder="First Name" required>
                        <label for="edit_first_name">First Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" placeholder="Last Name" required>
                        <label for="edit_last_name">Last Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="edit_course" name="course" required> 
                            <option value="" selected disabled>Course</option>
                            <option value="bscs">BSCS</option>
                            <option value="act">ACT</option>
                            <option value="bsba">BSBA</option>
                            <option value="bscrim">BSCRIM</option>
                            <option value="beed">BEED</option>
                            <option value="bsed">BSED</option>
                            <option value="bsa">BSA</option>
                        </select>
                        <label for="edit_course">Course</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="edit_year" name="year" placeholder="Year" min="1" max="4" required>
                        <label for="edit_year">Year</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit_section" name="section" placeholder="Section" required>
                        <label for="edit_section">Section</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Add Student Button (on the main page) -->
<button type="button" class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Success message placeholder -->
                <div id="success-message" class="alert alert-success d-none" role="alert">
                    Registration Successful Please Approve!
                </div>

                <!-- Error message placeholder -->
                <div id="error-message" class="alert alert-danger d-none" role="alert">
                    Failed to add student!
                </div>

                <!-- Add Student Form with Image Upload -->
                <form id="addStudentForm" method="POST" enctype="multipart/form-data">

                <!-- Profile Picture Section -->
                <div class="profile-pic-container text-center mb-3">
                    <!-- The profile picture preview -->
                    <img class="profile-picture" id="profilePicturePreview" src="../../photo/default.jpg" alt="Default Profile" style="width: 150px; height: 150px; border-radius: 50%;" onclick="document.getElementById('profilePictureInput').click();">
                <label>Add Profile</label>
                    <!-- Hidden file input -->
                    <input type="file" id="profilePictureInput" name="profile_image" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
                </div>


                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Student ID" required>
                        <label for="student_id">Student ID</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                        <label for="first_name">First Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                        <label for="last_name">Last Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        <label for="username">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                        <label for="email">Email Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <span class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                            <select class="form-select" id="course" name="course" required> 
                                <option value="" selected disabled>Course</option>
                                <option value="bscs">BSCS</option>
                                <option value="act">ACT</option>
                                <option value="bsba">BSBA</option>
                                <option value="bscrim">BSCRIM</option>
                                <option value="beed">BEED</option>
                                <option value="bsed">BSED</option>
                                <option value="bsa">BSA</option>
                                </select>
                                <label for="course">Course</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="year" name="year" placeholder="Year" min="1" max="4" required>
                                <label for="year">Year</label>
                            </div>
                        </div>
                    </div>  
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="section" name="section" placeholder="Section" required>
                        <label for="section">Section</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Add Student</button>
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
    
    <script>
function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var toggleIcon = document.getElementById("toggleIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>
<script>
document.getElementById('addStudentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent traditional form submission

    const formData = new FormData(this);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('success-message').classList.remove('d-none');
            document.getElementById('error-message').classList.add('d-none');

            // Optionally, clear the form fields after success
            this.reset();
        } else {
            // Show error message and keep modal open
            const errorMessage = data.errors.join("<br>");
            document.getElementById('error-message').innerHTML = errorMessage;
            document.getElementById('error-message').classList.remove('d-none');
            document.getElementById('success-message').classList.add('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message
        document.getElementById('error-message').classList.remove('d-none');
        document.getElementById('success-message').classList.add('d-none');
    });
});
</script>
<script>
function previewProfilePicture(event) {
    const input = event.target;
    const reader = new FileReader();

    reader.onload = function() {
        const preview = document.getElementById('profilePicturePreview');
        preview.src = reader.result; // Set the image source to the uploaded file's content
    };

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

// Add event listener to trigger image preview after selecting the file
document.getElementById('profilePictureInput').addEventListener('change', previewProfilePicture);

function previewProfilePicture(event) {
    const input = event.target;
    const reader = new FileReader();

    reader.onload = function() {
        const preview = document.getElementById('profilePicturePreview');
        preview.src = reader.result;
    }

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('profile_pic').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    
    reader.onload = function(e) {
        document.getElementById('profile_pic_preview').src = e.target.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    } else {
        // If no file is selected, display the default image
        document.getElementById('profile_pic_preview').src = "../../photo/default.jpg";
    }
});
</script>
<script>
    // Wait for the DOM to fully load
    document.addEventListener("DOMContentLoaded", function() {
        // Select the error message by ID
        var errorMessage = document.getElementById('error-message');

        // Check if the element exists
        if (errorMessage) {
            // Set a timer to hide the message after 5 seconds (5000 milliseconds)
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 5000); // 5000 milliseconds = 5 seconds
        }
    });
</script>
<script>
  var deleteModal = document.getElementById('deleteModal');
  var confirmDeleteButton = document.getElementById('confirmDelete');
  var successMessage = document.getElementById('successMessage');
  var deleteConfirmationText = document.getElementById('deleteConfirmationText');
  var studentIdToDelete = null;

  // Event listener for showing the modal and capturing student ID
  deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    studentIdToDelete = button.getAttribute('data-student-id');
    successMessage.style.display = 'none'; // Hide success message initially
    deleteConfirmationText.style.display = 'block'; // Show the confirmation text
  });

  // Add click listener for the confirm delete button
  confirmDeleteButton.addEventListener('click', function () {
    if (studentIdToDelete) {
      // Perform the deletion via AJAX or fetch
      fetch('../../student edit/delete_student.php?student_id=' + studentIdToDelete)
        .then(response => {
          if (response.ok) {
            // Hide the confirmation text
            deleteConfirmationText.style.display = 'none';

            // Show the success message
            successMessage.style.display = 'block';

            // Hide the delete and cancel buttons
            confirmDeleteButton.style.display = 'none';
            document.querySelector('.btn-secondary').style.display = 'none';

            // After 3 seconds, close the modal and reload the page
            setTimeout(function () {
              var modal = bootstrap.Modal.getInstance(deleteModal);
              modal.hide(); // Close the modal

              // Optionally reload the page after closing the modal
              location.reload(); // Reload the page after deletion
            }, 3000);
          } else {
            alert('Error: Unable to delete the student.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred.');
        });
    }
  });
</script>
<script>
// Function to load pending students with photos
function loadPendingValidations() {
    fetch('../../student edit/get_pending_students.php')
        .then(response => response.json())
        .then(data => {
            const pendingList = document.getElementById('pendingList');
            const notificationCount = document.getElementById('notificationCount');

            if (data.students.length > 0) {
                notificationCount.innerText = data.students.length;
                pendingList.innerHTML = data.students.map(student => `
                    <div class="student-entry d-flex align-items-center">
                        <img src="../../photo/${student.image}" alt="Profile Picture" class="profile-pic" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                        <div>
                            <p><strong>${student.first_name} ${student.last_name}</strong> (${student.student_id})</p>
                            <p><strong>Course:</strong> ${student.course} | <strong>Year:</strong> ${student.year} | <strong>Section:</strong> ${student.section}</p>
                        </div>
                        <div style="margin-left:auto;">
                            <button class="btn btn-success" onclick="validateStudent(${student.student_id})">Validate</button>
                            <button class="btn btn-danger" onclick="rejectStudent(${student.student_id})">Reject</button>
                        </div>
                    </div>
                    <hr>
                `).join('');
            } else {
                pendingList.innerHTML = '<p>No pending registrations.</p>';
                notificationCount.innerText = '0';
            }
        });
}

// Call the function when the page loads
window.onload = loadPendingValidations;
</script>

<script>
    // Function to display messages in the modal
function displayMessage(message, type) {
    const messageDiv = document.getElementById('validationMessage');
    messageDiv.innerText = message;

    // Add Bootstrap alert classes based on the message type
    if (type === 'success') {
        messageDiv.classList.remove('d-none', 'alert-danger');
        messageDiv.classList.add('alert-success');
    } else if (type === 'error') {
        messageDiv.classList.remove('d-none', 'alert-success');
        messageDiv.classList.add('alert-danger');
    }

    // Hide the message after 3 seconds
    setTimeout(() => {
        messageDiv.classList.add('d-none');
    }, 3000);
}

// Function for validation
function validateStudent(studentId) {
    fetch(`../../student edit/validate_or_reject_student.php?student_id=${studentId}&action=validate`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessage('Student validated successfully!', 'success');
            } else {
                displayMessage('Error validating student.', 'error');
            }
            loadPendingValidations();
        })
        .catch(error => {
            displayMessage('Error processing request.', 'error');
        });
}

// Function for rejection
function rejectStudent(studentId) {
    fetch(`../../student edit/validate_or_reject_student.php?student_id=${studentId}&action=reject`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessage('Student rejected successfully!', 'success');
            } else {
                displayMessage('Error rejecting student.', 'error');
            }
            loadPendingValidations();
        })
        .catch(error => {
            displayMessage('Error processing request.', 'error');
        });
}

</script>
<script>
function openEditModal(studentId) {
    fetch(`../../student edit/get_student_details.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal fields with the student data
            document.getElementById('edit_student_id').value = data.student_id;
            document.getElementById('edit_first_name').value = data.first_name;
            document.getElementById('edit_last_name').value = data.last_name;
            document.getElementById('edit_year').value = data.year;
            document.getElementById('edit_section').value = data.section;
            document.getElementById('edit_course').value = data.course;

            // Set the profile picture preview
            const profilePicturePreview = document.getElementById('editProfilePicturePreview');
            profilePicturePreview.src = "../../photo/" + (data.image ? data.image : "default.jpg");

            // Show the modal
            var editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
        });
}

</script>
<script>
    function previewEditProfilePicture(event) {
    const input = event.target;
    const reader = new FileReader();

    reader.onload = function() {
        const preview = document.getElementById('editProfilePicturePreview');
        preview.src = reader.result;
    };

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

</script>
<script>
    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);

    fetch('../../student edit/update_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show the success message inside the modal
            document.getElementById('edit-success-message').classList.remove('d-none');
            document.getElementById('edit-error-message').classList.add('d-none');

            // Optionally clear the form fields after success or reset the form
            setTimeout(function() {
                // You can hide the modal or do something else after showing the success message
                var editModal = bootstrap.Modal.getInstance(document.getElementById('editStudentModal'));
                editModal.hide();

                // Optionally refresh the page or just update the student info on the page dynamically
                location.reload();
            }, 2000); // Adjust the delay as necessary
        } else {
            // Show error message and keep modal open
            const errorMessage = data.errors.join("<br>");
            document.getElementById('edit-error-message').innerHTML = errorMessage;
            document.getElementById('edit-error-message').classList.remove('d-none');
            document.getElementById('edit-success-message').classList.add('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('edit-error-message').classList.remove('d-none');
        document.getElementById('edit-success-message').classList.add('d-none');
    });
});

</script>

</body>
</html>

