<?php

include '../db_connect.php';

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
            $upload_dir = realpath(__DIR__ . "/../photo/") . '/';
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

            // Success message
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
    <title>Signup Instructor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <style>
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
        .navbar-brand img {
            width: 150px; /* Set a default size */
            height: auto;
        }
        @media (max-width: 767px) {
            .navbar-brand img {
                width: 100px; /* Resize the logo on smaller screens */
            }
        }
        .cont {
            width: 100%;
            max-width: 600px; /* Adjust the container width */
            background: linear-gradient(to top, rgba(0, 0, 0, 0.75) 50%, rgba(0, 0, 0, 0.75) 50%);
            border-radius: 30px;
            padding: 30px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        @media (max-width: 767px) {
            .cont {
                padding: 20px;
                max-width: 95%;
            }
            .form-floating .form-control {
                padding: 0.75rem 0.75rem;
            }
        }
        .tre {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .text {
            color: white;
        }
        .but {
            color: #3b9ce2;
            text-decoration: none;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2em;
        }

        .navbar-brand img {
            width: 100px;   /* Default size for the logo */
            height: 100px;  /* Ensures the logo stays a perfect circle */
            border-radius: 50%;  /* Makes the logo round */
            object-fit: cover;   /* Ensures the image fits within the circular container */
        }

        @media (max-width: 767px) {
            .navbar-brand img {
                width: 100px;  /* Smaller size for mobile */
                height: 100px;  /* Maintain the circular aspect on mobile */
            }
        }
        .container {
            max-width: 400px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }

        .profile-picture-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #ddd;
            display: inline-block;
            position: relative;
        }

        .profile-picture-label {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #666;
            cursor: pointer;
        }

        .profile-picture-input {
            display: none;
        }

        .btn-primary {
            background: linear-gradient(90deg, rgba(9, 165, 255, 1) 0%, rgba(0, 225, 255, 1) 100%);
            border: none;
        }
        /* Align the 'Already have an account?' to be closer to the form */
.text-center {
    margin-top: 1rem; /* Reduce the space above this message */
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
                <img src="../photo/images.jpg" alt="RCT Logo"><span class="rct">RCT Faculty Evaluation System</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">
                            <i class="fa-solid fa-house"></i><span class="n1">Home</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flex container to center .cont -->
    <div style="display: flex; justify-content: center; align-items: center; height: calc(90vh - 60px);">
        <div class="cont">
            <div class="modal-header">
                <h1 class="log" id="exampleModalToggleLabel2" style="color: white;">Create an Account</h1>
            </div>
            <div class="px-2 modal-body">
                <form class="px-1 py-3" action="signup_ins.php" method="post" enctype="multipart/form-data">

                <!-- Display errors if any, inside the form container -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Display success message if registration is successful, inside the form container -->
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
                <!-- First and Last Name -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                <label for="first_name">First Name</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                                <label for="last_name">Last Name</label>
                            </div>
                        </div>
                    </div>

                    <!-- Username and Email side by side -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                <label for="email">Email Address</label>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <span class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>

                    <!-- Faculty -->
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
                        <button class="btn btn-lg btn-primary" type="submit" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">Register</button>
                    </div>
                </form>
                <div class="tre mt-3">
                    <span class="text">Already a Member?</span>
                    <a class="but" href="../login_instructor/login_ins.php">Log In</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" 
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" 
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" 
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" 
        crossorigin="anonymous"></script>
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

