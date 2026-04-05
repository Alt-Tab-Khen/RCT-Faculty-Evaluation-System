<?php
include 'db_connect.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the selected courses and validate
    if (isset($_POST['courses']) && is_array($_POST['courses'])) {
        $selected_courses = $_POST['courses'];

        // Get the faculty from the form
        $sending_faculty = $_POST['faculty'];

        // Query to get all faculty members from the current faculty
        $faculty_query = "SELECT last_name, first_name, middle_name, gender, age FROM instructor WHERE faculty = '$sending_faculty'";
        $faculty_result = mysqli_query($conn, $faculty_query);

        // Build the faculty list in HTML format
        $faculty_list = "<table class='table table-striped'>
                            <thead><tr><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Gender</th><th>Age</th></tr></thead>
                            <tbody>";

        while ($row = mysqli_fetch_assoc($faculty_result)) {
            $faculty_list .= "<tr>
                                <td>{$row['last_name']}</td>
                                <td>{$row['first_name']}</td>
                                <td>{$row['middle_name']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['age']}</td>
                              </tr>";
        }
        $faculty_list .= "</tbody></table>";

        // Query for the miscellaneous list in the same way
        $misc_query = "SELECT i.last_name, i.first_name, i.middle_name, i.gender, i.age, i.faculty
                       FROM instructor i
                       JOIN miscinstructor mi ON i.id = mi.instructor_id
                       WHERE mi.faculty = '$sending_faculty'";
        $misc_result = mysqli_query($conn, $misc_query);

        $misc_list = "<table class='table table-striped'>
                        <thead><tr><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Gender</th><th>Age</th><th>Faculty</th></tr></thead>
                        <tbody>";

        while ($row = mysqli_fetch_assoc($misc_result)) {
            $misc_list .= "<tr>
                                <td>{$row['last_name']}</td>
                                <td>{$row['first_name']}</td>
                                <td>{$row['middle_name']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['age']}</td>
                                <td>{$row['faculty']}</td>
                            </tr>";
        }
        $misc_list .= "</tbody></table>";

        // Now loop through each selected course
        foreach ($selected_courses as $course) {

            // Define the file path for the current course's `clfac.php`
            $file_path = "C:/xampp/htdocs/sheesh/student/department/$course/clfac.php";

            // Check if the file exists
            if (file_exists($file_path)) {

                // Read the current content of `clfac.php`
                $content = file_get_contents($file_path);

                // Check and preserve the placeholders
                if (strpos($content, "<!-- INSERT FACULTY LIST HERE -->") !== false) {
                    $content = preg_replace(
                        "/<!-- INSERT FACULTY LIST HERE -->.*<!-- END FACULTY LIST -->/s",
                        "<!-- INSERT FACULTY LIST HERE -->\n$faculty_list\n<!-- END FACULTY LIST -->",
                        $content
                    );
                } else {
                    echo "Faculty placeholder not found in $course/clfac.php<br>";
                }

                if (strpos($content, "<!-- INSERT MISCELLANEOUS LIST HERE -->") !== false) {
                    $content = preg_replace(
                        "/<!-- INSERT MISCELLANEOUS LIST HERE -->.*<!-- END MISCELLANEOUS LIST -->/s",
                        "<!-- INSERT MISCELLANEOUS LIST HERE -->\n$misc_list\n<!-- END MISCELLANEOUS LIST -->",
                        $content
                    );
                } else {
                    echo "Miscellaneous placeholder not found in $course/clfac.php<br>";
                }

                // Write the updated content back to `clfac.php`
                file_put_contents($file_path, $content);
            } else {
                echo "File not found for course: $course<br>";
            }
        }

        echo "Faculty list for $sending_faculty successfully sent to the selected courses.";
    } else {
        echo "No courses selected.";
    }
} else {
    echo "Invalid request.";
}
