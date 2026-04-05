<?php

session_start();  // Start the session at the beginning of the file

include '../../db_connect.php';
include '../../instructor edit/delete_rejected_instructor.php';

// Assuming the admin's ID is stored in the session as 'id'
if (!isset($_SESSION['id'])) {
    die("Error: admin not logged in.");
}

// Initialize variables
$errors = [];
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $faculty = $_POST['faculty'];
    $status = "pending"; // Set the status as pending

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif']; // Allowed file types
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_types)) {
            $upload_dir = realpath(__DIR__ . "/../../photo/") . '/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
            }
            $image_name = uniqid() . "-" . basename($_FILES['profile_image']['name']);
            $image_path = $upload_dir . $image_name;
            if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                $errors[] = "Failed to upload profile picture.";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $image_name = "default.jpg"; // Fallback to default image
    }

    // Validation
    if (empty($first_name)) { $errors[] = "First name is required."; }
    if (empty($last_name)) { $errors[] = "Last name is required."; }
    if (empty($username)) { $errors[] = "Username is required."; }
    if (empty($email)) { $errors[] = "Email is required."; } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Invalid email format."; }
    if (empty($password)) { $errors[] = "Password is required."; }
    if (empty($faculty)) { $errors[] = "Faculty is required."; }

    // Check if email or username already exists
    $email_check_query = "SELECT * FROM instructor WHERE email = ? OR username = ? LIMIT 1";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_user = $result->fetch_assoc();
    if ($existing_user) {
        $errors[] = "Email or Username already exists. Please use different credentials.";
    }

    // If no errors, proceed to insert into database
    if (count($errors) == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert instructor into the database with status
        $stmt = $conn->prepare("INSERT INTO instructor (username, email, password, first_name, last_name, faculty, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $username, $email, $hashed_password, $first_name, $last_name, $faculty, $image_name, $status);

        if ($stmt->execute()) {
            // Get the last inserted ID
            $last_id = $conn->insert_id;

            // Generate prof_id based on the inserted ID
            $prof_id = 'prof_' . str_pad($last_id, 2, '0', STR_PAD_LEFT); // Example: prof_01, prof_02, etc.

            // Update the instructor record with the generated prof_id
            $update_stmt = $conn->prepare("UPDATE instructor SET prof_id = ? WHERE id = ?");
            $update_stmt->bind_param("si", $prof_id, $last_id);
            $update_stmt->execute();

            // Send success response
            echo json_encode(['success' => true, 'message' => "Registration successful! Your account is pending admin approval."]);
        } else {
            // Log the error to the server's error log
            error_log("SQL Error: " . $stmt->error); 
            $errors[] = "Error occurred while adding the instructor. Please try again.";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }

        $stmt->close();
    } else {
        // Return validation errors
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
    $conn->close();
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
    <h2 style="color: rgb(255,255,255)">Instructors</h2>

    <!-- Search Bar -->
    <form method="GET" action="">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search by Instructor ID, First Name, or Last Name" name="search_term" aria-label="Search by Instructor ID, First Name, or Last Name" aria-describedby="button-addon2">
        <button class="btn btn-primary" type="submit" id="button-addon2">Search</button>
    </div>
</form>


    <?php
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search_term']);
    
    // Modify query to search by prof_id, first_name, or last_name
    $query = "
        SELECT prof_id, first_name, last_name, faculty, profile_picture 
        FROM instructor 
        WHERE prof_id LIKE '%$search_term%' 
        OR first_name LIKE '%$search_term%' 
        OR last_name LIKE '%$search_term%'
        OR faculty LIKE '%$search_term%'
    ";
    $result = mysqli_query($conn, $query);

    // Check if any instructor data was found
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $profile_image_path = "../../photo/" . htmlspecialchars($row['profile_picture']);

            echo "<div class='card mt-4'>
                    <div class='card-body d-flex align-items-start'>
                        <div class='profile-pic-container'>";

            // Display the profile picture if it exists, else use the default image
            if (file_exists($profile_image_path)) {
                echo "<img src='" . $profile_image_path . "' alt='Profile Picture' class='profile-picture' style='width: 150px; height: 150px; border-radius: 50%;'>";
            } else {
                echo "<img src='../../photo/default.jpg' alt='Profile Picture' class='profile-picture' style='width: 150px; height: 150px; border-radius: 50%;'>";
            }

            echo "</div>
                  <div class='instructor-details ms-4'>"; // Added margin to the left (ms-4)

            // Display the instructor details
            echo "<p class='card-text'><strong>Instructor ID:</strong> " . htmlspecialchars($row['prof_id']) . "</p>
                  <p class='card-text'><strong>Name:</strong> " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</p>
                  <p class='card-text'><strong>Faculty:</strong> " . htmlspecialchars($row['faculty']) . "</p>";

            // Display Edit and Delete buttons
            echo "<div class='d-flex justify-content-start mt-4'>
                     <a href='#' class='btn btn-warning me-2' onclick=\"openEditModal('" . addslashes($row['prof_id']) . "', '" . addslashes($row['first_name']) . "', '" . addslashes($row['last_name']) . "', '" . addslashes($row['faculty']) . "')\">Edit</a>
                  <a href='#' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-prof-id='" . $row['prof_id'] . "'>Delete</a>
                  </div>
                </div>
            </div>
        </div>";
        }
    } else {
        echo "<p id='error-message' class='mt-4' style='background-color: red; color: white; padding: 10px; border-radius: 5px;'>No instructor found with that information.</p>";
    }
}

?>

  <!-- Floating Create List Button -->
  <a href="../create list/clfac.php" class="create-list-btn">
    Create List
</a>





<!-- Delete Instructor Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="deleteConfirmationText">Are you sure you want to delete this instructor?</p>

        <!-- Success message inside the modal -->
        <div id="successMessage" style="display: none; color: green; padding: 10px; border: 1px solid green; border-radius: 5px;">
          Instructor deleted successfully!
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Validation Modal for Instructors -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validationModalLabel">Pending Instructor Registrations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pendingInstructorList">
                    <!-- List of pending instructors will be loaded here via JavaScript -->
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

<!-- Edit Instructor Modal -->
<div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInstructorModalLabel">Edit Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Success message placeholder -->
                <div id="edit-success-message" class="alert alert-success d-none" role="alert">Instructor updated successfully!</div>

                <!-- Error message placeholder -->
                <div id="edit-error-message" class="alert alert-danger d-none" role="alert">Failed to update instructor.</div>

                <!-- Edit Instructor Form -->
                <form id="editInstructorForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_prof_id" name="prof_id">

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
                        <select class="form-select" id="edit_faculty" name="faculty" required> 
                            <option value="" selected disabled>Faculty</option>
                            <option value="cpus">CPUS</option>
                            <option value="cba">CBA</option>
                            <option value="educ">EDUC</option>
                            <option value="crim">CRIM</option>
                        </select>
                        <label for="faculty">Faculty</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





<!-- Add Instructor Button (on the main page) -->
<button type="button" class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#addInstructorModal">Add Instructor</button>

<!-- Add Instructor Modal -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstructorModalLabel">Add New Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Success message placeholder -->
                <div id="success-message" class="alert alert-success d-none" role="alert">
                    Registration Successful! Please Approve!
                </div>

                <!-- Error message placeholder -->
                <div id="error-message" class="alert alert-danger d-none" role="alert">
                    Failed to add instructor!
                </div>

                <!-- Add Instructor Form with Image Upload -->
                <form id="addInstructorForm" method="POST" enctype="multipart/form-data">

                    <!-- Profile Picture Section -->
                    <div class="profile-pic-container text-center mb-3">
                        <!-- The profile picture preview -->
                        <img class="profile-picture" id="profilePicturePreview" src="../../photo/default.jpg" alt="Default Profile" style="width: 150px; height: 150px; border-radius: 50%;" onclick="document.getElementById('profilePictureInput').click();">
                        <label>Add Profile</label>
                        <!-- Hidden file input -->
                        <input type="file" id="profilePictureInput" name="profile_image" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
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
                    <div class="form-floating mb-3">
                        <select class="form-select" id="faculty" name="faculty" required> 
                            <option value="" selected disabled>Faculty</option>
                            <option value="cpus">CPUS</option>
                            <option value="cba">CBA</option>
                            <option value="educ">EDUC</option>
                            <option value="crim">CRIM</option>
                        </select>
                        <label for="faculty">Faculty</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Add Instructor</button>
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
document.getElementById('addInstructorForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent traditional form submission

    const formData = new FormData(this);

    fetch('', { // Update the path to your PHP file
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('success-message').classList.remove('d-none');
            document.getElementById('error-message').classList.add('d-none');

            // Optionally, reset the form fields after success
            document.getElementById('addInstructorForm').reset();
            
            // Keep the modal open
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
var profIdToDelete = null; // Updated to prof_id

// Event listener for showing the modal and capturing prof_id
deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    profIdToDelete = button.getAttribute('data-prof-id'); // Updated to capture prof_id
    successMessage.style.display = 'none'; // Hide success message initially
    deleteConfirmationText.style.display = 'block'; // Show the confirmation text
});

// Add click listener for the confirm delete button
confirmDeleteButton.addEventListener('click', function () {
    if (profIdToDelete) {
        // Perform the deletion via AJAX or fetch
        fetch('../../instructor edit/delete_instructor.php?prof_id=' + profIdToDelete) // Updated endpoint and prof_id
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
                    alert('Error: Unable to delete the instructor.');
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
// Function to load pending instructors with photos
function loadPendingValidations() {
    fetch('../../instructor edit/get_pending_instructor.php')  // Ensure the path is correct
        .then(response => response.json())
        .then(data => {
            const pendingList = document.getElementById('pendingInstructorList');
            const notificationCount = document.getElementById('notificationCount');

            if (data.instructors && data.instructors.length > 0) {
                notificationCount.innerText = data.instructors.length;
                pendingList.innerHTML = data.instructors.map(instructor => `
                    <div class="instructor-entry d-flex align-items-center">
                        <img src="../../photo/${instructor.profile_picture}" alt="Profile Picture" class="profile-pic" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 15px;">
                        <div>
                            <p><strong>${instructor.first_name} ${instructor.last_name}</strong> (${instructor.prof_id})</p>
                            <p><strong>Faculty:</strong> ${instructor.faculty}</p>
                        </div>
                        <div style="margin-left:auto;">
                            <button class="btn btn-success" onclick="validateInstructor('${instructor.prof_id}')">Validate</button>
                            <button class="btn btn-danger" onclick="rejectInstructor('${instructor.prof_id}')">Reject</button>
                        </div>
                    </div>
                    <hr>
                `).join('');
            } else {
                pendingList.innerHTML = '<p>No pending registrations.</p>';
                notificationCount.innerText = '0';
            }
        })
        .catch(error => {
            console.error('Error fetching instructors:', error);
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

// Function for instructor validation
function validateInstructor(profId) {
    fetch(`../../instructor edit/validate_or_reject_instructor.php?prof_id=${profId}&action=validate`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessage('Instructor validated successfully!', 'success');
            } else {
                displayMessage('Error validating instructor.', 'error');
            }
            loadPendingValidations();
        })
        .catch(error => {
            displayMessage('Error processing request.', 'error');
        });
}

// Function for instructor rejection
function rejectInstructor(profId) {
    fetch(`../../instructor edit/validate_or_reject_instructor.php?prof_id=${profId}&action=reject`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessage('Instructor rejected successfully!', 'success');
            } else {
                displayMessage('Error rejecting instructor.', 'error');
            }
            loadPendingValidations();
        })
        .catch(error => {
            displayMessage('Error processing request.', 'error');
        });
}

// Replace loadPendingValidations with a version that works for instructors

</script>
<script>
function openEditModal(prof_id) {
    fetch(`../../instructor edit/get_instructor_details.php?prof_id=${prof_id}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal fields with the instructor data
            document.getElementById('edit_prof_id').value = data.prof_id;
            document.getElementById('edit_first_name').value = data.first_name;
            document.getElementById('edit_last_name').value = data.last_name;
            document.getElementById('edit_faculty').value = data.faculty;

            // Set the profile picture preview
            const profilePicturePreview = document.getElementById('editProfilePicturePreview');
            profilePicturePreview.src = "../../photo/" + (data.profile_picture ? data.profile_picture : "default.jpg");

            // Show the modal
            var editModal = new bootstrap.Modal(document.getElementById('editInstructorModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error fetching instructor data:', error);
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
document.getElementById('editInstructorForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);

    fetch('../../instructor edit/update_instructor.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit-success-message').classList.remove('d-none');
            document.getElementById('edit-error-message').classList.add('d-none');

            setTimeout(function() {
                var editModal = bootstrap.Modal.getInstance(document.getElementById('editInstructorModal'));
                editModal.hide();
                location.reload(); // Optionally refresh the page to reflect changes
            }, 2000);
        } else {
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

