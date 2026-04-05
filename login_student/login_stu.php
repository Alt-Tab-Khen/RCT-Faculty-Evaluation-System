<?php
session_start();  // Start the session
include '../db_connect.php';

$email = "";
$password = "";
$loginError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute SQL query
    if ($conn) {
        $stmt = $conn->prepare("SELECT student_id, username, password, course, status FROM student WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
        
            // Check if the account is pending approval or rejected
            if ($user['status'] === 'pending') {
                $loginError = "Your account is still pending admin approval. Please wait for approval.";
            } elseif ($user['status'] === 'rejected') {
                $loginError = "Your account has been rejected. Please contact admin.";
            } else {
                // If the account is not pending or rejected, verify the password
                if (password_verify($password, $user['password'])) {
                    session_start();  // Make sure this is at the top of the login page
                    $_SESSION['student_id'] = $user['student_id'];  // Ensure this is set
                    $_SESSION['username'] = $user['username'];  // Any other session variables
                    $_SESSION['course'] = $user['course'];             
                    // Redirection based on course
                    switch ($user['course']) {
                        case 'bscs':
                            header('Location: ../student/bscs/admin.php');
                            break;
                        case 'bsba':
                            header('Location: ../student/bsba/admin.php');
                            break;
                        case 'bscrim':
                            header('Location: ../student/bscrim/admin.php');
                            break;
                        case 'beed':
                            header('Location: ../student/beed/admin.php');
                            break;
                        case 'bsed':
                            header('Location: ../student/bsed/admin.php');
                            break;
                        case 'bsa':
                            header('Location: ../student/bsa/admin.php');
                            break;
                        case 'act':
                            header('Location: ../student/act/admin.php');
                            break;
                        default:
                            $loginError = "Invalid course detected."; // Optional: handle invalid courses
                            break;
                    }
                    exit();
                } else {
                    $loginError = "Invalid password.";
                }
            }
        } else {
            $loginError = "No user found with that email.";
        }
        
    } else {
        $loginError = "Database connection failed.";
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style>
img{
    height: 80px;
    width: auto;
    border-radius: 50%;
    margin-right: 10px;
    
}
body{
    background: linear-gradient(rgb(0, 0, 0, 0.60), rgb(117, 117, 117, 0.75)), url(../photo/12.jpg);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
}
.rct{
    font-size: 25px;
    font-weight: bold;
}
.n1{
    font-size: 20px;
    font-weight: 500;
    margin-right: 20px;
}
.lg{
    font-size: 20px;
    font-weight: 500;
}
.fa-house{
    display: inline-block;
    align-self: center;
    margin-right: 5px;
}
.cont {
    width: 90%;
    max-width: 400px; /* Adjust as needed */
    height: auto; /* Allow height to adjust based on content */
    background: linear-gradient(to top, rgba(0, 0, 0, 0.75) 50%, rgba(0, 0, 0, 0.75) 50%);
    border-radius: 30px;
    padding: 30px;
    position: relative; /* Change from absolute to relative */
    margin: auto; /* Center horizontally in its container */
    top: 0; /* Remove top positioning */
    left: 0; /* Remove left positioning */
    transform: translateY(50%); /* Adjust vertical positioning */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: for a subtle shadow effect */
}
.log{
    padding-bottom: 20px;
}
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
.password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
        }



    </style>
    <nav class="navbar navbar-expand-lg " style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../photo/images.jpg" alt=""><span class="rct">RCT Faculty Evaluation System</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><i class="fa-solid fa-house"></i><span class="n1">Home</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="cont">      
        <div class="modal-header">
            <h1 class="log" id="exampleModalToggleLabel2" style="color: white;">Log in</h1>
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="exampleDropdownFormEmail1" class="form-label" style="color: white;">Email address</label>
                <input type="email" name="email" class="form-control" id="exampleDropdownFormEmail1" placeholder="Enter your Email">
            </div>
            <div class="mb-3 password-container">
                <label for="exampleDropdownFormPassword1" class="form-label" style="color: white;">Password</label>
                <input type="password" name="password" class="form-control" id="exampleDropdownFormPassword1" placeholder="Enter your Password">
                <i class="fa fa-eye toggle-password" id="togglePassword"></i>
            </div>  
            <div class="mb-3">
                <?php if (!empty($loginError)): ?>
                    <div class="alert alert-danger"><?php echo $loginError; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
        <div class="tre">
            <span class="text">New around here?</span>
            <a class="but" href="../signup_student/signup_stu.php">Sign up now</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#exampleDropdownFormPassword1');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>