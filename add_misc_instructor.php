<?php

include 'db_connect.php';

// Check if the form was submitted and if there are instructors selected
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['misc_instructors'])) {

    // Retrieve the selected miscellaneous instructors from the form
    $selected_instructors = $_POST['misc_instructors'];

    // Dynamically get the faculty from the form (passed from the modal)
    if (isset($_POST['faculty'])) {
        $faculty = $_POST['faculty'];
    } else {
        // If no faculty is passed, redirect back with an error
        header("Location: /sheesh/admin/faculty/business/clfac.php?error=NoFacultySelected");
        exit();
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO miscinstructor (instructor_id, faculty) VALUES (?, ?)");

    // Loop through each selected instructor and insert them into the `miscinstructor` table
    foreach ($selected_instructors as $instructor_id) {
        // Bind the parameters and execute the statement for each instructor
        $stmt->bind_param("is", $instructor_id, $faculty);

        // Execute the insertion
        if ($stmt->execute()) {
            // Success message or redirect can be added here if needed
            $success = true;
        } else {
            // Handle any errors
            echo "Error: " . $stmt->error;
        }
    }

    // Close the prepared statement
    $stmt->close();

    // Redirect back to the same faculty page after adding instructors
    header("Location: /sheesh/admin/faculty/" . strtolower($faculty) . "/clfac.php?faculty=" . $faculty);
    exit();
} else {
    // If no instructors were selected, redirect back with an error
    header("Location: /sheesh/admin/faculty/" . strtolower($faculty) . "/clfac.php?error=NoInstructorsSelected");

    exit();
}

?>
