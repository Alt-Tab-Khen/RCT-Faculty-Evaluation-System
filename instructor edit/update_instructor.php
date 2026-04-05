<?php
include '../db_connect.php';

// Initialize variables
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prof_id'])) {
    $prof_id = trim($_POST['prof_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $faculty = $_POST['faculty'];

    // Start preparing the query, only updating fields that are not empty
    $query = "UPDATE instructor SET first_name=?, last_name=?, faculty=?";
    $params = [$first_name, $last_name, $faculty];
    $types = "sss";  // Types for first_name, last_name, and faculty

    // Add username if provided
    if (!empty($username)) {
        $query .= ", username=?";
        $params[] = $username;
        $types .= "s";  // Append type for username
    }

    // Add email if provided
    if (!empty($email)) {
        $query .= ", email=?";
        $params[] = $email;
        $types .= "s";  // Append type for email
    }

    // Add password if provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password=?";
        $params[] = $hashed_password;
        $types .= "s";  // Append type for password
    }

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_types)) {
            $upload_dir = realpath(__DIR__ . "/../photo/") . '/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create directory if it doesn't exist
            }

            $image_name = uniqid() . "-" . basename($_FILES['profile_image']['name']);
            $image_path = $upload_dir . $image_name;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                $query .= ", profile_picture=?";
                $params[] = $image_name;
                $types .= "s";  // Append type for profile picture
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Add WHERE clause
    $query .= " WHERE prof_id=?";
    $params[] = $prof_id;
    $types .= "s";  // Append type for prof_id

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Use the spread operator to pass the params array to bind_param
    $stmt->bind_param($types, ...$params);

    // Execute the query
    if ($stmt->execute()) {
        $success = true;
    } else {
        $errors[] = "Error updating instructor: " . $stmt->error;
    }

    // Return JSON response
    echo json_encode(['success' => $success, 'errors' => $errors]);
}
?>
