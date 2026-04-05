<?php
include '../db_connect.php';

// Initialize variables
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $section = trim($_POST['section']);
    $year = trim($_POST['year']);
    $course = $_POST['course'];

    // Prepare the query, only updating fields that are not empty
    $query = "UPDATE student SET first_name=?, last_name=?, section=?, year=?, course=?";

    // Add username if provided
    if (!empty($username)) {
        $query .= ", username=?";
    }

    // Add email if provided
    if (!empty($email)) {
        $query .= ", email=?";
    }

    // Add password if provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password=?";
    }

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Define allowed image types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_types)) {
            $upload_dir = realpath(__DIR__ . "/../photo/") . '/';
            
            // Ensure the upload directory exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create directory if it doesn't exist
            }

            // Generate a unique file name
            $image_name = uniqid() . "-" . basename($_FILES['profile_image']['name']);
            $image_path = $upload_dir . $image_name;

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                $query .= ", image=?";
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    $query .= " WHERE student_id=?";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind the parameters based on the fields that are not empty
    if (!empty($username) && !empty($email) && !empty($password) && isset($image_name)) {
        $stmt->bind_param("ssssssssss", $first_name, $last_name, $section, $year, $course, $username, $email, $hashed_password, $image_name, $student_id);
    } elseif (!empty($username) && !empty($email) && !empty($password)) {
        $stmt->bind_param("sssssssss", $first_name, $last_name, $section, $year, $course, $username, $email, $hashed_password, $student_id);
    } elseif (!empty($username) && !empty($email)) {
        $stmt->bind_param("ssssssss", $first_name, $last_name, $section, $year, $course, $username, $email, $student_id);
    } elseif (isset($image_name)) {
        $stmt->bind_param("sssssss", $first_name, $last_name, $section, $year, $course, $image_name, $student_id);
    } else {
        $stmt->bind_param("ssssss", $first_name, $last_name, $section, $year, $course, $student_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        $success = true;
    } else {
        $errors[] = "Error updating student.";
    }

    // Return JSON response
    echo json_encode(['success' => $success, 'errors' => $errors]);
}
?>
