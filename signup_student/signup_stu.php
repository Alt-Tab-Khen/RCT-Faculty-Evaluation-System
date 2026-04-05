<?php

include '../db_connect.php';

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
        $upload_dir = realpath(__DIR__ . "/../photo/") . '/';
        
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
            // Show a message that registration is pending approval
            $success = "Registration successful! Your account is pending admin approval.";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
<style>
        img {
            height: 80px;
            width: auto;
            border-radius: 50%;
            margin-right: 10px;
        }
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.60), rgba(117, 117, 117, 0.75)), url(../photo/12.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        .rct {
            font-size: 25px;
            font-weight: bold;
        }
        .n1 {
            font-size: 20px;
            font-weight: 500;
            margin-right: 20px;
        }
        .lg {
            font-size: 20px;
            font-weight: 500;
        }
        .fa-house {
            display: inline-block;
            align-self: center;
            margin-right: 5px;
        }
        .cont {
            width: 90%;
            max-width: 400px; /* Adjust as needed */
            background: linear-gradient(to top, rgba(0, 0, 0, 0.75) 50%, rgba(0, 0, 0, 0.75) 50%);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: for a subtle shadow effect */
        }
        .log {
            padding-bottom: 20px;
        }
        /* Style for the container div */
        .tre {
            display: flex; /* Align child elements horizontally */
            align-items: center; /* Vertically center the items */
            gap: 10px; /* Space between the text and the 'Log In' link */
        }
        /* Style for the 'text' span */
        .text {
            color: white; /* Text color */
        }
        /* Style for the 'but' link */
        .but {
            color: #3b9ce2; /* Link color */
            text-decoration: none; /* Remove underline from link */
        }
        .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1.2em;
        }

        /* Adjust the margin and padding to reduce spacing */
.form-floating {
    margin-bottom: 0.5rem; /* Decrease the space between fields */
}

.form-floating input, 
.form-floating select {
    padding: 0.75rem; /* Adjust input padding for smaller fields */
}

/* Align the 'Already have an account?' to be closer to the form */
.text-center {
    margin-top: 1rem; /* Reduce the space above this message */
}

.cont {
        width: 90%;
        max-width: 400px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.75) 50%, rgba(0, 0, 0, 0.75) 50%);
        border-radius: 30px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-top: 150px;
}
.profile-pic-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }
    .profile-pic-container img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        cursor: pointer;
        object-fit: cover;
    }

    </style>

<nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../photo/images.jpg" alt=""><span class="rct">RCT Faculty Evaluation System</span>
        </a>
    </div>
</nav>

<div style="display: flex; justify-content: center; align-items: center; height: calc(100vh - 60px);">
    <div class="cont">
    <form action="signup_stu.php" method="POST" enctype="multipart/form-data">
        <h1 class="log" id="exampleModalToggleLabel2" style="color: white; text-align: center;">Create an Account</h1>

        <!-- Display errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- Display success message if registration is successful -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <div class="profile-pic-container text-center mb-3">
                <!-- The profile picture preview -->
                <img class="profile-picture" id="profilePicturePreview" src="../photo/default.jpg" alt="Default Profile" style="width: 150px; height: 150px; border-radius: 50%;" onclick="document.getElementById('profilePictureInput').click();">
                <label style="color: white;">Add Profile Picture</label>
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
                <button class="btn btn-lg btn-primary" type="submit" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">Register</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p class="text-white">Already have an account? <a href="../login_student/login_stu.php" class="text-primary">Login</a></p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    function previewProfilePicture(event) {
    const input = event.target;
    const reader = new FileReader();

    reader.onload = function() {
        const preview = document.getElementById('profilePicturePreview');
        preview.src = reader.result;
    };

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        // If no file is selected, reset to default image
        document.getElementById('profilePicturePreview').src = "../photo/default.jpg";
    }
}


</script>

</body>
</html>
